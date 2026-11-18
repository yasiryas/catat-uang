<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AdjustmentLog;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Mutation;
use App\Models\Period;
use App\Models\Transaction;
use App\Services\GoalService;
use App\Services\PeriodService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GoalService $goalService,
    ) {}

    public function index(Request $request, PeriodService $periodService)
    {
        $user = auth()->user();

        $periodService->ensureAllMonthsThisYear($user);

        $periods = Period::forUser($user)->latest('year')->latest('month')->get();
        $allowedPeriodIds = $periods->pluck('id')->map(fn (int $id): string => (string) $id)->push('all')->all();
        $selectedPeriodId = $request->validate([
            'period_id' => ['nullable', 'string', Rule::in($allowedPeriodIds)],
        ])['period_id'] ?? 'all';
        $period = $this->selectedPeriod($periods, $selectedPeriodId);

        $scopedTransactions = Transaction::forUser($user)
            ->when($period, fn ($query) => $query->where('period_id', $period->id));

        $scopedAdjustments = AdjustmentLog::forUser($user)
            ->when($period, fn ($query) => $query->where('period_id', $period->id));

        [$totalIncome, $totalExpense] = $this->sumIncomeAndExpense($scopedTransactions, $scopedAdjustments);
        $netBalance = $totalIncome - $totalExpense;
        $closingBalance = $period ? $period->opening_balance + $netBalance : 0;

        $incomeByCategory = $this->sumByCategory($scopedTransactions, 'income');
        $expenseByCategory = $this->sumByCategory($scopedTransactions, 'expense');

        $recentTransactions = (clone $scopedTransactions)
            ->with(['category', 'account'])
            ->latest('date')
            ->take(10)
            ->get();

        $totalTransactions = Transaction::forUser($user)->count();
        $totalCategories = Category::forUser(auth()->user())->count();
        $totalPeriods = $periods->count();

        $year = $period?->year ?? now()->year;
        [$chartMonths, $chartIncome, $chartExpense] = $this->monthlyChart($user, $selectedPeriodId, $year);

        $accounts = $this->accountsWithSaldo($user);
        $goals = $this->activeGoalsWithProgress($user);

        return view('dashboard', compact(
            'period', 'periods', 'selectedPeriodId',
            'totalIncome', 'totalExpense', 'netBalance', 'closingBalance',
            'incomeByCategory', 'expenseByCategory',
            'recentTransactions', 'totalTransactions', 'totalCategories', 'totalPeriods',
            'chartMonths', 'chartIncome', 'chartExpense', 'accounts', 'year', 'goals'
        ));
    }

    private function selectedPeriod(Collection $periods, ?string $selectedPeriodId): ?Period
    {
        if (! $selectedPeriodId || $selectedPeriodId === 'all') {
            return null;
        }

        return $periods->firstWhere('id', (int) $selectedPeriodId);
    }

    /**
     * @return array{0: float, 1: float} [income, expense]
     */
    private function sumIncomeAndExpense($transactions, $adjustments): array
    {
        $totals = (clone $transactions)
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            ->first();

        $adjustmentTotals = (clone $adjustments)
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            ->first();

        return [
            (float) $totals->income + (float) $adjustmentTotals->income,
            (float) $totals->expense + (float) $adjustmentTotals->expense,
        ];
    }

    private function sumByCategory($query, string $type): Collection
    {
        return (clone $query)
            ->where('type', $type)
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->sortByDesc('total')
            ->values();
    }

    /**
     * @return array{0: array, 1: array, 2: array} [labels, income, expense]
     */
    private function monthlyChart($user, ?string $selectedPeriodId, int $year): array
    {
        $rows = Transaction::forUser($user)
            ->whereYear('date', $year)
            ->when($selectedPeriodId && $selectedPeriodId !== 'all', fn ($q) => $q->where('period_id', $selectedPeriodId))
            ->get(['date', 'type', 'amount'])
            ->concat(
                AdjustmentLog::forUser($user)
                    ->whereYear('date', $year)
                    ->when($selectedPeriodId && $selectedPeriodId !== 'all', fn ($q) => $q->where('period_id', $selectedPeriodId))
                    ->get(['date', 'type', 'amount'])
            );

        $labels = [];
        $income = [];
        $expense = [];

        foreach (range(1, 12) as $month) {
            $monthRows = $rows->filter(fn ($row) => $row->date->month === $month);

            $labels[] = now()->setDate($year, $month, 1)->locale('id')->isoFormat('MMM');
            $income[] = (float) $monthRows->where('type', 'income')->sum('amount');
            $expense[] = (float) $monthRows->where('type', 'expense')->sum('amount');
        }

        return [$labels, $income, $expense];
    }

    private function accountsWithSaldo($user): Collection
    {
        $accounts = Account::forUser($user)->get();

        if ($accounts->isEmpty()) {
            return $accounts;
        }

        $accountIds = $accounts->modelKeys();

        $saldos = Transaction::query()
            ->whereIn('account_id', $accountIds)
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw("account_id, SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as saldo")
            ->groupBy('account_id')
            ->pluck('saldo', 'account_id');

        $mutations = Mutation::forUser($user)
            ->where(function ($query) use ($accountIds): void {
                $query->whereIn('from_account_id', $accountIds)
                    ->orWhereIn('to_account_id', $accountIds);
            })
            ->get(['from_account_id', 'to_account_id', 'amount']);

        foreach ($mutations as $mutation) {
            if (in_array($mutation->from_account_id, $accountIds, true)) {
                $saldos[$mutation->from_account_id] = ($saldos[$mutation->from_account_id] ?? 0) - (float) $mutation->amount;
            }

            if (in_array($mutation->to_account_id, $accountIds, true)) {
                $saldos[$mutation->to_account_id] = ($saldos[$mutation->to_account_id] ?? 0) + (float) $mutation->amount;
            }
        }

        return $accounts->each(fn (Account $account) => $account->saldo = (float) ($saldos[$account->id] ?? 0));
    }

    private function activeGoalsWithProgress($user): Collection
    {
        $goals = Goal::with(['period', 'category', 'account'])
            ->forUser($user)
            ->where('is_active', true)
            ->latest()
            ->limit(4)
            ->get();

        $progress = $this->goalService->progressForMany($goals);

        return $goals->map(fn (Goal $goal) => array_merge(
            ['id' => $goal->id, 'name' => $goal->name, 'type' => $goal->type],
            $progress[$goal->id]
        ));
    }
}

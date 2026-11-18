<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Goal;
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

        [$totalIncome, $totalExpense] = $this->sumIncomeAndExpense($scopedTransactions);
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
        $totalCategories = Category::count();
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
    private function sumIncomeAndExpense($query): array
    {
        $totals = (clone $query)
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            ->first();

        return [(float) $totals->income, (float) $totals->expense];
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
        $monthlyData = Transaction::forUser($user)
            ->whereYear('date', $year)
            ->when($selectedPeriodId && $selectedPeriodId !== 'all', fn ($q) => $q->where('period_id', $selectedPeriodId))
            ->selectRaw('MONTH(date) as month, type, SUM(amount) as total')
            ->groupBy('month', 'type')
            ->get()
            ->groupBy('month');

        $labels = [];
        $income = [];
        $expense = [];

        foreach (range(1, 12) as $month) {
            $data = $monthlyData->get($month);

            $labels[] = now()->setDate($year, $month, 1)->locale('id')->isoFormat('MMM');
            $income[] = (float) ($data?->firstWhere('type', 'income')?->total ?? 0);
            $expense[] = (float) ($data?->firstWhere('type', 'expense')?->total ?? 0);
        }

        return [$labels, $income, $expense];
    }

    private function accountsWithSaldo($user): Collection
    {
        $accounts = Account::forUser($user)->get();

        if ($accounts->isEmpty()) {
            return $accounts;
        }

        $saldos = Transaction::query()
            ->whereIn('account_id', $accounts->modelKeys())
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw("account_id, SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as saldo")
            ->groupBy('account_id')
            ->pluck('saldo', 'account_id');

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

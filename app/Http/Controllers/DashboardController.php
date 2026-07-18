<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Period;
use App\Models\Transaction;
use App\Services\PeriodService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, PeriodService $periodService)
    {
        $periodService->ensureAllMonthsThisYear(auth()->id());

        $userId = auth()->id();
        $periods = Period::where('user_id', $userId)->latest('year')->latest('month')->get();
        $selectedPeriodId = $request->get('period_id');

        $period = null;
        $totalIncome = 0;
        $totalExpense = 0;
        $netBalance = 0;
        $closingBalance = 0;
        $incomeByCategory = collect();
        $expenseByCategory = collect();
        $recentTransactions = collect();

        $baseQuery = Transaction::where('user_id', $userId);

        if ($selectedPeriodId && $selectedPeriodId !== 'all') {
            $period = $periods->firstWhere('id', (int) $selectedPeriodId);
            $baseQuery->where('period_id', $selectedPeriodId);
        } elseif (!$selectedPeriodId || $selectedPeriodId === 'all') {
            $period = null;
        }

        if ($period) {
            $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
            $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
            $netBalance = $totalIncome - $totalExpense;
            $closingBalance = $period->opening_balance + $netBalance;
        } else {
            $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
            $totalExpense = (clone $baseQuery)->where('type', 'expense')->sum('amount');
            $netBalance = $totalIncome - $totalExpense;
        }

        // Income breakdown by category
        $incomeByCategory = (clone $baseQuery)
            ->where('type', 'income')
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->sortByDesc('total');

        // Expense breakdown by category
        $expenseByCategory = (clone $baseQuery)
            ->where('type', 'expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->sortByDesc('total');

        // Recent transactions
        $recentTransactions = (clone $baseQuery)
            ->with(['category', 'account'])
            ->latest('date')
            ->take(10)
            ->get();

        // Summary counts (always global)
        $totalTransactions = Transaction::where('user_id', $userId)->count();
        $totalCategories = Category::count();
        $totalPeriods = $periods->count();

        // Monthly chart data (filtered by year)
        $year = $period ? $period->year : date('Y');
        $chartQuery = Transaction::where('user_id', $userId)->whereYear('date', $year);
        if ($selectedPeriodId && $selectedPeriodId !== 'all') {
            $chartQuery->where('period_id', $selectedPeriodId);
        }
        $monthlyData = (clone $chartQuery)
            ->selectRaw("MONTH(date) as month, type, SUM(amount) as total")
            ->groupBy('month', 'type')
            ->get()
            ->groupBy('month');

        $chartMonths = [];
        $chartIncome = [];
        $chartExpense = [];

        foreach (range(1, 12) as $m) {
            $monthName = \Carbon\Carbon::create($year, $m, 1)->locale('id')->isoFormat('MMM');
            $chartMonths[] = $monthName;
            $data = $monthlyData->get($m);
            $chartIncome[] = (float) ($data?->firstWhere('type', 'income')?->total ?? 0);
            $chartExpense[] = (float) ($data?->firstWhere('type', 'expense')?->total ?? 0);
        }

        // Saldo per dompet (always global)
        $accounts = Account::where('user_id', $userId)->get();

        return view('dashboard', compact(
            'period', 'periods', 'selectedPeriodId',
            'totalIncome', 'totalExpense', 'netBalance', 'closingBalance',
            'incomeByCategory', 'expenseByCategory',
            'recentTransactions', 'totalTransactions', 'totalCategories', 'totalPeriods',
            'chartMonths', 'chartIncome', 'chartExpense', 'accounts', 'year'
        ));
    }
}

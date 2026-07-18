<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Period;
use App\Models\Transaction;
use App\Services\PeriodService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(PeriodService $periodService)
    {
        $periodService->ensureAllMonthsThisYear(auth()->id());

        $period = Period::where('user_id', auth()->id())->current()->first();
        $totalIncome = 0;
        $totalExpense = 0;
        $netBalance = 0;
        $closingBalance = 0;

        if ($period) {
            $totalIncome = Transaction::income()->where('period_id', $period->id)->sum('amount');
            $totalExpense = Transaction::expense()->where('period_id', $period->id)->sum('amount');
            $netBalance = $totalIncome - $totalExpense;
            $closingBalance = $period->opening_balance + $netBalance;
        }

        // Recent transactions
        $recentTransactions = Transaction::where('user_id', auth()->id())
            ->with(['category', 'account'])
            ->latest('date')
            ->take(5)
            ->get();

        // Summary counts
        $totalTransactions = Transaction::where('user_id', auth()->id())->count();
        $totalCategories = Category::count();
        $totalPeriods = Period::where('user_id', auth()->id())->count();

        // Monthly chart data (current year)
        $year = date('Y');
        $monthlyData = Transaction::where('user_id', auth()->id())
            ->whereYear('date', $year)
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

        return view('dashboard', compact(
            'period', 'totalIncome', 'totalExpense', 'netBalance', 'closingBalance',
            'recentTransactions', 'totalTransactions', 'totalCategories', 'totalPeriods',
            'chartMonths', 'chartIncome', 'chartExpense'
        ));
    }
}

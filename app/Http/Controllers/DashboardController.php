<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\Transaction;
use App\Services\PeriodService;

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

        return view('dashboard', compact('period', 'totalIncome', 'totalExpense', 'netBalance', 'closingBalance'));
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Period;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCurrentPeriodExists
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $currentYear = date('Y');
            $currentMonth = date('m');
            $userId = Auth::id();

            $period = Period::where('year', $currentYear)
                ->where('month', $currentMonth)
                ->where('user_id', $userId)
                ->first();

            if (! $period) {
                Period::create([
                    'user_id' => $userId,
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'opening_balance' => 0,
                    'closing_balance' => 0,
                    'is_closed' => false,
                ]);
            }
        }

        return $next($request);
    }
}

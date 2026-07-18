<?php

namespace App\Services;

use App\Models\Period;
use Illuminate\Support\Facades\Date;

class PeriodService
{
    public function ensurePeriodExists(int $userId): Period
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;

        $period = Period::where('user_id', $userId)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->first();

        if ($period) {
            return $period;
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return Period::create([
            'user_id' => $userId,
            'name' => $monthNames[$month] . ' ' . $year,
            'start_date' => "{$year}-{$month}-01",
            'end_date' => "{$year}-{$month}-" . now()->daysInMonth,
            'year' => $year,
            'month' => $month,
            'is_closed' => false,
        ]);
    }

    public function ensureAllMonthsThisYear(int $userId): void
    {
        $now = now();
        $year = $now->year;

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        for ($month = 1; $month <= $now->month; $month++) {
            $exists = Period::where('user_id', $userId)
                ->whereYear('start_date', $year)
                ->whereMonth('start_date', $month)
                ->exists();

            if (!$exists) {
                $daysInMonth = now()->year($year)->month($month)->daysInMonth;
                Period::create([
                    'user_id' => $userId,
                    'name' => $monthNames[$month] . ' ' . $year,
                    'start_date' => "{$year}-{$month}-01",
                    'end_date' => "{$year}-{$month}-{$daysInMonth}",
                    'year' => $year,
                    'month' => $month,
                    'is_closed' => false,
                ]);
            }
        }
    }
}

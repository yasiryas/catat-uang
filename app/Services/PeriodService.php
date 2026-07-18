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

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return Period::firstOrCreate([
            'user_id' => $userId,
            'year' => $year,
            'month' => $month,
        ], [
            'name' => $monthNames[$month] . ' ' . $year,
            'start_date' => "{$year}-{$month}-01",
            'end_date' => "{$year}-{$month}-" . now()->daysInMonth,
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
            $daysInMonth = now()->year($year)->month($month)->daysInMonth;
            Period::firstOrCreate([
                'user_id' => $userId,
                'year' => $year,
                'month' => $month,
            ], [
                'name' => $monthNames[$month] . ' ' . $year,
                'start_date' => "{$year}-{$month}-01",
                'end_date' => "{$year}-{$month}-{$daysInMonth}",
                'is_closed' => false,
            ]);
        }
    }
}

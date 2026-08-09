<?php

namespace App\Services;

use App\Models\Period;
use App\Models\User;

class PeriodService
{
    public function ensurePeriodExists(User $user): Period
    {
        $now = now();
        $year = $now->year;
        $month = $now->month;

        $values = [
            'name' => $this->monthName($month).' '.$year,
            'start_date' => "{$year}-{$month}-01",
            'end_date' => "{$year}-{$month}-".now()->daysInMonth,
            'is_closed' => false,
        ];

        if ($user->belongsToFamily()) {
            return Period::firstOrCreate(
                ['family_id' => $user->family_id, 'year' => $year, 'month' => $month],
                $values + ['user_id' => $user->id]
            );
        }

        return Period::firstOrCreate(
            ['user_id' => $user->id, 'year' => $year, 'month' => $month],
            $values
        );
    }

    public function ensureAllMonthsThisYear(User $user): void
    {
        $now = now();
        $year = $now->year;

        for ($month = 1; $month <= $now->month; $month++) {
            $values = [
                'name' => $this->monthName($month).' '.$year,
                'start_date' => "{$year}-{$month}-01",
                'end_date' => "{$year}-{$month}-".now()->year($year)->month($month)->daysInMonth,
                'is_closed' => false,
            ];

            if ($user->belongsToFamily()) {
                Period::firstOrCreate(
                    ['family_id' => $user->family_id, 'year' => $year, 'month' => $month],
                    $values + ['user_id' => $user->id]
                );
            } else {
                Period::firstOrCreate(
                    ['user_id' => $user->id, 'year' => $year, 'month' => $month],
                    $values
                );
            }
        }
    }

    private function monthName(int $month): string
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ][$month];
    }
}

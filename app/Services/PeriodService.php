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

        $existingMonths = Period::query()
            ->when(
                $user->belongsToFamily(),
                fn ($query) => $query->where('family_id', $user->family_id),
                fn ($query) => $query->where('user_id', $user->id)
            )
            ->where('year', $year)
            ->pluck('month')
            ->flip();

        $missing = [];
        foreach (range(1, $now->month) as $month) {
            if ($existingMonths->has($month)) {
                continue;
            }

            $missing[] = [
                'user_id' => $user->id,
                'family_id' => $user->belongsToFamily() ? $user->family_id : null,
                'year' => $year,
                'month' => $month,
                'name' => $this->monthName($month).' '.$year,
                'start_date' => "{$year}-{$month}-01",
                'end_date' => "{$year}-{$month}-".$now->setDate($year, $month, 1)->daysInMonth,
                'is_closed' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($missing !== []) {
            Period::insert($missing);
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

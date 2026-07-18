<?php

namespace Database\Seeders;

use App\Models\Period;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrFail();

        $periods = [
            ['user_id' => $user->id, 'year' => 2025, 'month' => 1, 'opening_balance' => 5000000, 'closing_balance' => 7500000, 'is_closed' => true],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 2, 'opening_balance' => 7500000, 'closing_balance' => 6800000, 'is_closed' => true],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 3, 'opening_balance' => 6800000, 'closing_balance' => 8200000, 'is_closed' => true],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 4, 'opening_balance' => 8200000, 'closing_balance' => 9100000, 'is_closed' => true],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 5, 'opening_balance' => 9100000, 'closing_balance' => 8750000, 'is_closed' => true],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 6, 'opening_balance' => 8750000, 'closing_balance' => 10200000, 'is_closed' => true],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 7, 'opening_balance' => 10200000, 'closing_balance' => 0, 'is_closed' => false],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 8, 'opening_balance' => 0, 'closing_balance' => 0, 'is_closed' => false],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 9, 'opening_balance' => 0, 'closing_balance' => 0, 'is_closed' => false],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 10, 'opening_balance' => 0, 'closing_balance' => 0, 'is_closed' => false],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 11, 'opening_balance' => 0, 'closing_balance' => 0, 'is_closed' => false],
            ['user_id' => $user->id, 'year' => 2025, 'month' => 12, 'opening_balance' => 0, 'closing_balance' => 0, 'is_closed' => false],
        ];

        foreach ($periods as $period) {
            Period::updateOrCreate(
                ['user_id' => $user->id, 'year' => $period['year'], 'month' => $period['month']],
                $period
            );
        }
    }
}
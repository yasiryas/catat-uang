<?php

namespace Tests\Feature;

use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_derives_year_and_month_from_start_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('periods.store'), [
            'name' => 'Maret 2026',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-31',
            'is_closed' => false,
        ])->assertRedirect(route('periods.index'));

        $this->assertDatabaseHas('periods', [
            'user_id' => $user->id,
            'name' => 'Maret 2026',
            'year' => 2026,
            'month' => 3,
        ]);
    }

    public function test_update_derives_year_and_month_from_new_start_date(): void
    {
        $user = User::factory()->create();
        $period = Period::create([
            'user_id' => $user->id,
            'name' => 'Maret 2026',
            'year' => 2026,
            'month' => 3,
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-31',
        ]);

        $this->actingAs($user)->put(route('periods.update', $period), [
            'name' => 'Mei 2027',
            'start_date' => '2027-05-01',
            'end_date' => '2027-05-31',
            'is_closed' => false,
        ])->assertRedirect(route('periods.index'));

        $this->assertDatabaseHas('periods', [
            'id' => $period->id,
            'year' => 2027,
            'month' => 5,
        ]);
    }
}

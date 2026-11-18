<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AdjustmentLog;
use App\Models\Category;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdjustmentLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_adjustment_without_a_transaction(): void
    {
        $user = User::factory()->create();
        $period = $this->periodFor($user);

        $this->actingAs($user)
            ->postJson(route('adjustments.store'), [
                'period_id' => $period->id,
                'type' => 'expense',
                'amount' => 75000,
                'note' => 'Selisih kas kecil',
                'date' => '2026-02-28 17:00:00',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('adjustment_logs', [
            'user_id' => $user->id,
            'period_id' => $period->id,
            'note' => 'Selisih kas kecil',
        ]);
    }

    public function test_adjustment_requires_a_scoped_period(): void
    {
        $user = User::factory()->create();
        $otherPeriod = $this->periodFor(User::factory()->create());

        $this->actingAs($user)
            ->post(route('adjustments.store'), [
                'period_id' => $otherPeriod->id,
                'type' => 'income',
                'amount' => 75000,
                'date' => '2026-02-28 17:00:00',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('period_id');
    }

    public function test_adjustment_is_included_in_dashboard_totals(): void
    {
        $user = User::factory()->create();
        $period = $this->periodFor($user);
        $account = Account::create([
            'user_id' => $user->id,
            'name' => 'Tunai',
            'type' => 'cash',
            'description' => null,
        ]);
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Gaji',
            'type' => 'income',
            'budget_limit' => 0,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'period_id' => $period->id,
            'category_id' => $category->id,
            'account_id' => $account->id,
            'type' => 'income',
            'amount' => 1000000,
            'date' => '2026-02-01 08:00:00',
        ]);

        AdjustmentLog::create([
            'user_id' => $user->id,
            'period_id' => $period->id,
            'type' => 'income',
            'amount' => 300000,
            'date' => '2026-02-18 11:00:00',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('totalIncome', fn ($total) => (float) $total === 1300000.0);
    }

    private function periodFor(User $user): Period
    {
        return Period::create([
            'user_id' => $user->id,
            'name' => 'Februari 2026',
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-28',
            'year' => 2026,
            'month' => 2,
            'opening_balance' => 0,
            'closing_balance' => 0,
            'is_closed' => false,
        ]);
    }
}

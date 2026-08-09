<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\User;
use App\Services\GoalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    private function makePeriod(User $user, int $month = 7, int $year = 2026): Period
    {
        return Period::create([
            'user_id' => $user->id,
            'year' => $year,
            'month' => $month,
            'name' => 'Juli '.$year,
            'start_date' => $year.'-'.str_pad((string) $month, 2, '0').'-01',
            'end_date' => $year.'-'.str_pad((string) $month, 2, '0').'-28',
            'opening_balance' => 0,
            'closing_balance' => 0,
            'is_closed' => false,
        ]);
    }

    public function test_goals_page_requires_authentication(): void
    {
        $this->get('/goals')->assertRedirect('/login');
    }

    public function test_goals_page_is_displayed_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/goals');

        $response->assertOk()->assertSee('Target & Capaian');
    }

    public function test_user_can_create_a_savings_goal(): void
    {
        $user = User::factory()->create();
        $period = $this->makePeriod($user);

        $response = $this->actingAs($user)->post('/goals', [
            'name' => 'Tabungan Liburan',
            'type' => 'savings',
            'target_amount' => 1_000_000,
            'period_id' => $period->id,
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'name' => 'Tabungan Liburan',
            'type' => 'savings',
        ]);
    }

    public function test_goal_cannot_reference_another_users_period(): void
    {
        $other = User::factory()->create();
        $foreignPeriod = $this->makePeriod($other);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/goals', [
            'name' => 'Target',
            'type' => 'savings',
            'target_amount' => 500_000,
            'period_id' => $foreignPeriod->id,
        ]);

        $response->assertSessionHasErrors('period_id');
        $this->assertDatabaseMissing('goals', ['name' => 'Target']);
    }

    public function test_user_cannot_update_or_delete_others_goal(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $goal = Goal::create([
            'user_id' => $owner->id,
            'name' => 'Target Rahasia',
            'type' => 'savings',
            'target_amount' => 1_000_000,
            'is_active' => true,
        ]);

        $this->actingAs($attacker)
            ->put('/goals/'.$goal->id, [
                'name' => 'Diretas',
                'type' => 'savings',
                'target_amount' => 100,
            ])
            ->assertForbidden();

        $this->actingAs($attacker)
            ->delete('/goals/'.$goal->id)
            ->assertForbidden();

        $this->assertDatabaseHas('goals', ['id' => $goal->id, 'name' => 'Target Rahasia']);
    }

    public function test_progress_is_computed_from_transactions(): void
    {
        $user = User::factory()->create();
        $period = $this->makePeriod($user);
        $category = Category::create(['name' => 'Gaji', 'type' => 'income']);

        Transaction::create([
            'user_id' => $user->id,
            'period_id' => $period->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 3_000_000,
            'date' => now(),
        ]);
        Transaction::create([
            'user_id' => $user->id,
            'period_id' => $period->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 1_200_000,
            'date' => now(),
        ]);

        $goal = Goal::create([
            'user_id' => $user->id,
            'name' => 'Menabung 1 Juta',
            'type' => 'savings',
            'target_amount' => 1_000_000,
            'period_id' => $period->id,
            'is_active' => true,
        ]);

        $progress = app(GoalService::class)->progressFor($goal);

        $this->assertEquals(1_800_000, $progress['actual']);
        $this->assertTrue($progress['achieved']);
        $this->assertEquals(100, $progress['percentage']);
    }

    public function test_expense_goal_is_achieved_when_under_budget(): void
    {
        $user = User::factory()->create();
        $period = $this->makePeriod($user);
        $category = Category::create(['name' => 'Belanja', 'type' => 'expense']);

        Transaction::create([
            'user_id' => $user->id,
            'period_id' => $period->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 800_000,
            'date' => now(),
        ]);

        $goal = Goal::create([
            'user_id' => $user->id,
            'name' => 'Batas Belanja',
            'type' => 'category',
            'target_amount' => 1_000_000,
            'period_id' => $period->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $progress = app(GoalService::class)->progressFor($goal);

        $this->assertEquals(800_000, $progress['actual']);
        $this->assertTrue($progress['achieved']);
        $this->assertEquals(80, $progress['percentage']);
    }

    public function test_account_balance_goal_is_computed_per_account(): void
    {
        $user = User::factory()->create();
        $period = $this->makePeriod($user);
        $category = Category::create(['name' => 'Kategori', 'type' => 'income']);
        $accountA = Account::create(['user_id' => $user->id, 'name' => 'BCA', 'type' => 'bank']);
        $accountB = Account::create(['user_id' => $user->id, 'name' => 'Cash', 'type' => 'cash']);

        Transaction::create(['user_id' => $user->id, 'period_id' => $period->id, 'category_id' => $category->id, 'account_id' => $accountA->id, 'type' => 'income', 'amount' => 2_000_000, 'date' => now()]);
        Transaction::create(['user_id' => $user->id, 'period_id' => $period->id, 'category_id' => $category->id, 'account_id' => $accountA->id, 'type' => 'expense', 'amount' => 500_000, 'date' => now()]);
        Transaction::create(['user_id' => $user->id, 'period_id' => $period->id, 'category_id' => $category->id, 'account_id' => $accountB->id, 'type' => 'income', 'amount' => 9_000_000, 'date' => now()]);

        $goal = Goal::create([
            'user_id' => $user->id,
            'name' => 'Saldo BCA 1 Juta',
            'type' => 'account',
            'target_amount' => 1_000_000,
            'period_id' => $period->id,
            'account_id' => $accountA->id,
            'is_active' => true,
        ]);

        $progress = app(GoalService::class)->progressFor($goal);

        $this->assertEquals(1_500_000, $progress['actual']);
        $this->assertTrue($progress['achieved']);
    }
}

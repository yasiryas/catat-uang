<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_multiple_mutations_for_the_same_period(): void
    {
        $user = User::factory()->create();
        $period = $this->periodFor($user);
        $from = $this->accountFor($user, 'Dompet Asal');
        $to = $this->accountFor($user, 'Dompet Tujuan');

        foreach (['Mutasi pertama', 'Mutasi kedua'] as $note) {
            $this->actingAs($user)
                ->postJson(route('mutations.store'), [
                    'period_id' => $period->id,
                    'from_account_id' => $from->id,
                    'to_account_id' => $to->id,
                    'amount' => 250000,
                    'note' => $note,
                    'date' => '2026-02-10 10:00:00',
                ])
                ->assertCreated();
        }

        $this->assertDatabaseCount('mutations', 2);
    }

    public function test_mutation_rejects_account_outside_user_scope(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $period = $this->periodFor($user);
        $from = $this->accountFor($user, 'Dompet Asal');
        $to = $this->accountFor($otherUser, 'Dompet Orang Lain');

        $this->actingAs($user)
            ->post(route('mutations.store'), [
                'period_id' => $period->id,
                'from_account_id' => $from->id,
                'to_account_id' => $to->id,
                'amount' => 250000,
                'date' => '2026-02-10 10:00:00',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('to_account_id');
    }

    public function test_mutation_is_included_in_dashboard_account_balances(): void
    {
        $user = User::factory()->create();
        $period = $this->periodFor($user);
        $from = $this->accountFor($user, 'Dompet Asal');
        $to = $this->accountFor($user, 'Dompet Tujuan');
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Gaji',
            'type' => 'income',
            'budget_limit' => 0,
        ]);

        $from->transactions()->create([
            'user_id' => $user->id,
            'period_id' => $period->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1000000,
            'date' => '2026-02-01 08:00:00',
        ]);

        $this->actingAs($user)->postJson(route('mutations.store'), [
            'period_id' => $period->id,
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
            'amount' => 400000,
            'date' => '2026-02-10 10:00:00',
        ])->assertCreated();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('accounts', function ($accounts) use ($from, $to) {
                return (float) $accounts->firstWhere('id', $from->id)->saldo === 600000.0
                    && (float) $accounts->firstWhere('id', $to->id)->saldo === 400000.0;
            });
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

    private function accountFor(User $user, string $name): Account
    {
        return Account::create([
            'user_id' => $user->id,
            'name' => $name,
            'type' => 'cash',
            'description' => null,
        ]);
    }
}

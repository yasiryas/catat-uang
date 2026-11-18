<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Family;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_cannot_use_another_users_category(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $period = $this->periodFor($user);
        $foreign = Category::create([
            'name' => 'Pribadi',
            'type' => 'expense',
            'user_id' => $other->id,
        ]);

        $this->actingAs($user)->post(route('transactions.expense.store'), [
            'period_id' => $period->id,
            'category_id' => $foreign->id,
            'amount' => 10000,
            'date' => '2026-11-18',
        ])->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_transaction_can_use_global_category(): void
    {
        $user = User::factory()->create();
        $period = $this->periodFor($user);
        $global = Category::create(['name' => 'Makanan', 'type' => 'expense']);

        $this->actingAs($user)->post(route('transactions.expense.store'), [
            'period_id' => $period->id,
            'category_id' => $global->id,
            'amount' => 10000,
            'date' => '2026-11-18',
        ])->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $global->id,
        ]);
    }

    public function test_family_transaction_cannot_use_another_familys_category(): void
    {
        [$family, $admin] = $this->familyWithAdmin();
        $otherFamily = Family::create(['name' => 'Keluarga Lain', 'created_by' => $admin->id]);
        $period = $this->periodFor($admin, $family->id);
        $foreign = Category::create([
            'name' => 'Pribadi Keluarga Lain',
            'type' => 'expense',
            'family_id' => $otherFamily->id,
        ]);

        $this->actingAs($admin)->post(route('transactions.expense.store'), [
            'period_id' => $period->id,
            'category_id' => $foreign->id,
            'amount' => 10000,
            'date' => '2026-11-18',
        ])->assertSessionHasErrors('category_id');
    }

    public function test_admin_cannot_modify_private_category(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = User::factory()->create();
        $category = Category::create([
            'name' => 'Pribadi',
            'type' => 'expense',
            'user_id' => $owner->id,
        ]);

        $this->actingAs($admin)
            ->put(route('categories.update', $category), ['name' => 'Diubah', 'type' => 'expense'])
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Pribadi']);
    }

    private function periodFor(User $user, ?int $familyId = null): Period
    {
        return Period::create([
            'user_id' => $user->id,
            'family_id' => $familyId,
            'name' => 'November 2026',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'year' => 2026,
            'month' => 11,
        ]);
    }

    private function familyWithAdmin(): array
    {
        $admin = User::factory()->create();
        $family = Family::create(['name' => 'Keluarga Test', 'created_by' => $admin->id]);
        $admin->update([
            'family_id' => $family->id,
            'family_role' => 'admin',
        ]);

        return [$family, $admin];
    }
}

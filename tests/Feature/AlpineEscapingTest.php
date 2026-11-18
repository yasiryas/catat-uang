<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Family;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlpineEscapingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_rejects_injected_period_id(): void
    {
        $user = User::factory()->create();
        $period = Period::create([
            'user_id' => $user->id,
            'name' => 'November 2026',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'year' => 2026,
            'month' => 11,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['period_id' => "' + alert(1) + '"]))
            ->assertSessionHasErrors('period_id');

        $this->actingAs($user)
            ->get(route('dashboard', ['period_id' => $period->id]))
            ->assertOk()
            ->assertSee("period_id: '{$period->id}'", false)
            ->assertDontSee('alert(1)', false);
    }

    public function test_account_arguments_are_json_escaped(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'name' => '<script>alert(1)</script>',
            'type' => 'cash',
            'description' => "' onmouseover='alert(1)",
        ]);

        $this->actingAs($user)
            ->get(route('accounts.index'))
            ->assertOk()
            ->assertDontSee("edit({$account->id}, '", false)
            ->assertDontSee("confirmDelete({$account->id}, '", false)
            ->assertSee('u003Cscript', false);
    }

    public function test_period_arguments_are_json_escaped(): void
    {
        $user = User::factory()->create();
        $period = Period::create([
            'user_id' => $user->id,
            'name' => '<script>alert(1)</script>',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'year' => 2026,
            'month' => 11,
        ]);

        $this->actingAs($user)
            ->get(route('periods.index'))
            ->assertOk()
            ->assertDontSee("edit({$period->id}, '", false)
            ->assertDontSee("confirmDelete({$period->id}, '", false)
            ->assertSee('u003Cscript', false);
    }

    public function test_family_member_argument_is_json_escaped(): void
    {
        $admin = User::factory()->create();
        $family = Family::create(['name' => 'Keluarga Test', 'created_by' => $admin->id]);
        $admin->update([
            'family_id' => $family->id,
            'family_role' => 'admin',
        ]);
        $member = User::factory()->create([
            'name' => '<script>alert(1)</script>',
            'family_id' => $family->id,
            'family_role' => 'member',
        ]);

        $this->actingAs($admin)
            ->get(route('family.index'))
            ->assertOk()
            ->assertDontSee("confirmRemove({$member->id}, '", false)
            ->assertSee('u003Cscript', false);
    }
}

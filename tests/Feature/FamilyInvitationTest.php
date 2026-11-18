<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Family;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Notifications\FamilyInvitation as FamilyInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class FamilyInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_invitation_without_changing_membership_or_data(): void
    {
        [$family, $admin] = $this->familyWithAdmin();
        $invitee = User::factory()->create();
        $account = Account::create([
            'user_id' => $invitee->id,
            'name' => 'Dompet Pribadi',
            'type' => 'cash',
        ]);

        Notification::fake();

        $this->actingAs($admin)
            ->post(route('family.invite'), ['email' => $invitee->email])
            ->assertRedirect();

        $this->assertDatabaseHas('family_invitations', [
            'family_id' => $family->id,
            'email' => $invitee->email,
            'invited_by' => $admin->id,
            'decision' => null,
        ]);
        $this->assertDatabaseHas('users', ['id' => $invitee->id, 'family_id' => null]);
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'family_id' => null]);
        Notification::assertSentTo($invitee, FamilyInvitationNotification::class);
    }

    public function test_non_admin_cannot_invite(): void
    {
        [, $admin] = $this->familyWithAdmin();
        $member = User::factory()->create([
            'family_id' => $admin->family_id,
            'family_role' => 'member',
        ]);

        $this->actingAs($member)
            ->post(route('family.invite'), ['email' => User::factory()->create()->email])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('family_invitations', 0);
    }

    public function test_invitation_page_requires_valid_signature(): void
    {
        [, $admin] = $this->familyWithAdmin();
        $invitee = User::factory()->create();
        $invitation = $this->invitationFor($admin->family, $admin, $invitee);
        $url = $this->signedUrl('family.invitations.show', $invitation);

        $this->actingAs($invitee)->get($url)->assertOk();
        $this->actingAs($invitee)->get($url.'&token=wrong')->assertForbidden();
    }

    public function test_user_with_different_email_cannot_accept(): void
    {
        [, $admin] = $this->familyWithAdmin();
        $invitee = User::factory()->create();
        $other = User::factory()->create();
        $invitation = $this->invitationFor($admin->family, $admin, $invitee);

        $this->actingAs($other)
            ->post($this->signedUrl('family.invitations.accept', $invitation))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $other->id, 'family_id' => null]);
        $this->assertDatabaseHas('family_invitations', ['id' => $invitation->id, 'decision' => null]);
    }

    public function test_invitee_can_accept_without_data_relocation(): void
    {
        [$family, $admin] = $this->familyWithAdmin();
        $invitee = User::factory()->create();
        $account = Account::create([
            'user_id' => $invitee->id,
            'name' => 'Dompet Pribadi',
            'type' => 'cash',
        ]);
        $invitation = $this->invitationFor($family, $admin, $invitee);

        $this->actingAs($invitee)
            ->post($this->signedUrl('family.invitations.accept', $invitation))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $invitee->id,
            'family_id' => $family->id,
            'family_role' => 'member',
        ]);
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'family_id' => null]);
        $this->assertDatabaseHas('family_invitations', [
            'id' => $invitation->id,
            'decision' => 'accepted',
        ]);
        $this->assertNotNull($invitation->fresh()->used_at);
    }

    public function test_accepted_invitation_cannot_be_used_again(): void
    {
        [, $admin] = $this->familyWithAdmin();
        $invitee = User::factory()->create();
        $invitation = $this->invitationFor($admin->family, $admin, $invitee);
        $url = $this->signedUrl('family.invitations.accept', $invitation);

        $this->actingAs($invitee)->post($url)->assertSessionHas('success');
        $this->actingAs($invitee)->post($url)->assertSessionHas('error');
    }

    public function test_invitee_can_reject_invitation(): void
    {
        [, $admin] = $this->familyWithAdmin();
        $invitee = User::factory()->create();
        $invitation = $this->invitationFor($admin->family, $admin, $invitee);

        $this->actingAs($invitee)
            ->post($this->signedUrl('family.invitations.reject', $invitation))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $invitee->id, 'family_id' => null]);
        $this->assertDatabaseHas('family_invitations', [
            'id' => $invitation->id,
            'decision' => 'rejected',
        ]);
    }

    private function familyWithAdmin(): array
    {
        $admin = User::factory()->create();
        $family = Family::create([
            'name' => 'Keluarga Test',
            'created_by' => $admin->id,
        ]);
        $admin->update([
            'family_id' => $family->id,
            'family_role' => 'admin',
        ]);

        return [$family, $admin];
    }

    private function invitationFor(Family $family, User $admin, User $invitee): FamilyInvitation
    {
        return FamilyInvitation::create([
            'family_id' => $family->id,
            'email' => $invitee->email,
            'token_hash' => hash('sha256', 'plain-token'),
            'invited_by' => $admin->id,
            'expires_at' => now()->addDay(),
        ]);
    }

    private function signedUrl(string $route, FamilyInvitation $invitation): string
    {
        return URL::temporarySignedRoute($route, $invitation->expires_at, [
            'invitation' => $invitation,
            'token' => 'plain-token',
        ]);
    }
}

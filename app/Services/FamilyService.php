<?php

namespace App\Services;

use App\Models\Family;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Notifications\FamilyInvitation as FamilyInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FamilyService
{
    private const INVITATION_TTL_DAYS = 3;

    /**
     * Buat keluarga baru dan jadikan user sebagai kepala keluarga (admin).
     */
    public function create(User $user, string $name): Family
    {
        $family = Family::create([
            'name' => $name,
            'created_by' => $user->id,
        ]);

        $user->update([
            'family_id' => $family->id,
            'family_role' => 'admin',
        ]);

        return $family;
    }

    /**
     * Kirim undangan kepada akun yang sudah terdaftar.
     *
     * @return array{ok: bool, message: string, invitation?: FamilyInvitation}
     */
    public function inviteMember(User $admin, string $email): array
    {
        if (! $admin->isFamilyAdmin()) {
            return ['ok' => false, 'message' => 'Hanya kepala keluarga yang dapat mengundang anggota.'];
        }

        $email = strtolower($email);
        $invitee = User::where('email', $email)->first();

        if (! $invitee) {
            return ['ok' => false, 'message' => 'Tidak ada akun dengan email tersebut. Pastikan anggota sudah mendaftar.'];
        }

        if ($invitee->family_id === $admin->family_id) {
            return ['ok' => false, 'message' => 'Pengguna tersebut sudah menjadi anggota keluarga ini.'];
        }

        if ($invitee->family_id) {
            return ['ok' => false, 'message' => 'Pengguna tersebut sudah berada di keluarga lain.'];
        }

        $pending = FamilyInvitation::where('family_id', $admin->family_id)
            ->where('email', $email)
            ->pending()
            ->exists();

        if ($pending) {
            return ['ok' => false, 'message' => 'Sudah ada undangan menunggu untuk email tersebut.'];
        }

        $token = Str::random(64);
        $invitation = FamilyInvitation::create([
            'family_id' => $admin->family_id,
            'email' => $email,
            'token_hash' => hash('sha256', $token),
            'invited_by' => $admin->id,
            'expires_at' => now()->addDays(self::INVITATION_TTL_DAYS),
        ]);

        $invitee->notify(new FamilyInvitationNotification($invitation, $token));

        return ['ok' => true, 'message' => "Undangan dikirim ke {$invitee->name}.", 'invitation' => $invitation];
    }

    public function viewInvitation(User $invitee, FamilyInvitation $invitation, string $token): array
    {
        $error = $this->validateInvitation($invitee, $invitation, $token);

        if ($error !== null) {
            return ['ok' => false, 'message' => $error];
        }

        return ['ok' => true, 'invitation' => $invitation];
    }

    public function acceptInvitation(User $invitee, FamilyInvitation $invitation, string $token): array
    {
        $error = $this->validateInvitation($invitee, $invitation, $token);

        if ($error !== null) {
            return ['ok' => false, 'message' => $error];
        }

        if (! $this->consume($invitee, $invitation, 'accepted')) {
            return ['ok' => false, 'message' => 'Undangan ini sudah digunakan.'];
        }

        return ['ok' => true, 'message' => "Anda bergabung dengan {$invitation->family->name}."];
    }

    public function rejectInvitation(User $invitee, FamilyInvitation $invitation, string $token): array
    {
        $error = $this->validateInvitation($invitee, $invitation, $token);

        if ($error !== null) {
            return ['ok' => false, 'message' => $error];
        }

        if (! $this->consume($invitee, $invitation, 'rejected')) {
            return ['ok' => false, 'message' => 'Undangan ini sudah digunakan.'];
        }

        return ['ok' => true, 'message' => 'Undangan ditolak.'];
    }

    /**
     * Keluarkan anggota dari keluarga (hanya oleh admin; admin tidak bisa mengeluarkan dirinya sendiri).
     */
    public function removeMember(User $admin, User $member): array
    {
        if (! $admin->isFamilyAdmin()) {
            return ['ok' => false, 'message' => 'Hanya kepala keluarga yang dapat mengeluarkan anggota.'];
        }

        if ($member->id === $admin->id) {
            return ['ok' => false, 'message' => 'Kepala keluarga tidak dapat mengeluarkan dirinya sendiri. Gunakan tombol hapus keluarga.'];
        }

        if ($member->family_id !== $admin->family_id) {
            return ['ok' => false, 'message' => 'Anggota tidak berada di keluarga Anda.'];
        }

        $this->detachFromFamily($member);

        return ['ok' => true, 'message' => "{$member->name} dikeluarkan dari keluarga."];
    }

    /**
     * Anggota keluar sendiri dari keluarga (kepala keluarga harus menghapus keluarga).
     */
    public function leave(User $member): array
    {
        if ($member->isFamilyAdmin()) {
            return ['ok' => false, 'message' => 'Kepala keluarga tidak dapat keluar. Gunakan hapus keluarga bila ingin membubarkan.'];
        }

        $this->detachFromFamily($member);

        return ['ok' => true, 'message' => 'Anda keluar dari keluarga.'];
    }

    /**
     * Bubarkan keluarga (hanya oleh kepala keluarga).
     */
    public function destroy(User $admin): void
    {
        if (! $admin->isFamilyAdmin()) {
            abort(403);
        }

        Family::where('id', $admin->family_id)
            ->get()
            ->each(function (Family $family) {
                User::where('family_id', $family->id)->update(['family_id' => null, 'family_role' => null]);
                $family->delete();
            });
    }

    private function detachFromFamily(User $member): void
    {
        $member->update(['family_id' => null, 'family_role' => null]);
    }

    private function validateInvitation(User $invitee, FamilyInvitation $invitation, string $token): ?string
    {
        if (! hash_equals($invitation->token_hash, hash('sha256', $token))) {
            return 'Tautan undangan tidak valid.';
        }

        if (strcasecmp($invitee->email, $invitation->email) !== 0) {
            return 'Email Anda tidak sesuai dengan undangan ini.';
        }

        if ($invitation->used_at !== null || $invitation->decision !== null) {
            return 'Undangan ini sudah digunakan.';
        }

        if ($invitation->expires_at->isPast()) {
            return 'Tautan undangan sudah kedaluwarsa.';
        }

        $family = $invitation->family;

        if (! $family) {
            return 'Keluarga tidak ditemukan.';
        }

        $inviter = $invitation->invitedBy;

        if (! $inviter || ! $inviter->isFamilyAdmin() || $inviter->family_id !== $family->id) {
            return 'Undangan tidak lagi berlaku.';
        }

        if ($invitee->family_id === $family->id) {
            return 'Anda sudah menjadi anggota keluarga ini.';
        }

        if ($invitee->family_id !== null) {
            return 'Anda sudah berada di keluarga lain.';
        }

        return null;
    }

    private function consume(User $invitee, FamilyInvitation $invitation, string $decision): bool
    {
        return DB::transaction(function () use ($invitee, $invitation, $decision): bool {
            $updated = FamilyInvitation::whereKey($invitation->id)
                ->whereNull('used_at')
                ->whereNull('decision')
                ->update([
                    'used_at' => now(),
                    'decision' => $decision,
                ]);

            if (! $updated) {
                return false;
            }

            if ($decision === 'accepted') {
                $invitee->update([
                    'family_id' => $invitation->family_id,
                    'family_role' => 'member',
                ]);
            }

            return true;
        });
    }
}
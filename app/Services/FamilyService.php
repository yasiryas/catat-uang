<?php

namespace App\Services;

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FamilyService
{
    /**
     * Buat keluarga baru dan jadikan user sebagai kepala keluarga (admin).
     * Seluruh data historis user ikut dipindah ke buku bersama keluarga.
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

        $this->relocateDataToFamily($user, $family);

        return $family;
    }

    /**
     * Tambah anggota berdasarkan email akun yang sudah terdaftar.
     *
     * @return array{ok: bool, message?: string, member?: User}
     */
    public function inviteMember(User $admin, string $email): array
    {
        if (! $admin->isFamilyAdmin()) {
            return ['ok' => false, 'message' => 'Hanya kepala keluarga yang dapat mengundang anggota.'];
        }

        $member = User::where('email', $email)->first();

        if (! $member) {
            return ['ok' => false, 'message' => 'Tidak ada akun dengan email tersebut. Pastikan anggota sudah mendaftar.'];
        }

        if ($member->family_id === $admin->family_id) {
            return ['ok' => false, 'message' => 'Pengguna tersebut sudah menjadi anggota keluarga ini.'];
        }

        if ($member->family_id) {
            return ['ok' => false, 'message' => 'Pengguna tersebut sudah berada di keluarga lain.'];
        }

        $member->update([
            'family_id' => $admin->family_id,
            'family_role' => 'member',
        ]);

        $this->relocateDataToFamily($member, $admin->family);

        return ['ok' => true, 'message' => "{$member->name} berhasil ditambahkan.", 'member' => $member];
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

    private function relocateDataToFamily(User $user, Family $family): void
    {
        foreach (['accounts', 'periods', 'transactions', 'goals', 'mutations', 'adjustment_logs'] as $table) {
            DB::table($table)
                ->where('user_id', $user->id)
                ->whereNull('family_id')
                ->update(['family_id' => $family->id]);
        }
    }
}
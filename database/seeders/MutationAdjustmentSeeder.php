<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AdjustmentLog;
use App\Models\Mutation;
use App\Models\Period;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class MutationAdjustmentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();
        $accounts = Account::where('user_id', $user->id)->pluck('id', 'name');
        $periods = Period::where('user_id', $user->id)->pluck('id', 'month');

        $this->seedMutations($user, $periods, $accounts);
        $this->seedAdjustments($user, $periods);
    }

    private function seedMutations(User $user, Collection $periods, Collection $accounts): void
    {
        foreach ($this->mutationRows() as $row) {
            Mutation::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'period_id' => $this->periodId($periods, $row['month']),
                    'from_account_id' => $this->accountId($accounts, $row['from']),
                    'to_account_id' => $this->accountId($accounts, $row['to']),
                    'date' => $row['date'],
                    'note' => $row['note'],
                ],
                [
                    'family_id' => $user->family_id,
                    'amount' => $row['amount'],
                ]
            );
        }
    }

    private function seedAdjustments(User $user, Collection $periods): void
    {
        foreach ($this->adjustmentRows() as $row) {
            AdjustmentLog::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'period_id' => $this->periodId($periods, $row['month']),
                    'type' => $row['type'],
                    'amount' => $row['amount'],
                    'date' => $row['date'],
                    'note' => $row['note'],
                ],
                [
                    'family_id' => $user->family_id,
                    'old_amount' => 0,
                    'new_amount' => $row['amount'],
                    'reason' => $row['note'],
                ]
            );
        }
    }

    private function periodId(Collection $periods, int $month): int
    {
        $id = $periods->get($month);

        if ($id === null) {
            throw new RuntimeException("Periode bulan {$month} tidak ditemukan. Jalankan DemoDataSeeder terlebih dahulu.");
        }

        return (int) $id;
    }

    private function accountId(Collection $accounts, string $name): int
    {
        $id = $accounts->get($name);

        if ($id === null) {
            throw new RuntimeException("Dompet {$name} tidak ditemukan. Jalankan DemoDataSeeder terlebih dahulu.");
        }

        return (int) $id;
    }

    /** @return array<int, array<string, mixed>> */
    private function mutationRows(): array
    {
        return [
            ['month' => 2, 'from' => 'BCA', 'to' => 'Tunai', 'amount' => 1500000, 'date' => '2026-02-05 09:00:00', 'note' => 'Tarik tunai untuk belanja'],
            ['month' => 3, 'from' => 'BCA', 'to' => 'GoPay', 'amount' => 750000, 'date' => '2026-03-10 12:00:00', 'note' => 'Top up GoPay'],
            ['month' => 5, 'from' => 'Mandiri', 'to' => 'BCA', 'amount' => 2000000, 'date' => '2026-05-08 15:30:00', 'note' => 'Pindah dana tabungan'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function adjustmentRows(): array
    {
        return [
            ['month' => 2, 'type' => 'expense', 'amount' => 125000, 'date' => '2026-02-28 17:00:00', 'note' => 'Selisih kas kecil'],
            ['month' => 4, 'type' => 'income', 'amount' => 300000, 'date' => '2026-04-18 11:00:00', 'note' => 'Koreksi pembayaran freelance'],
            ['month' => 6, 'type' => 'expense', 'amount' => 75000, 'date' => '2026-06-25 10:00:00', 'note' => 'Pembulatan tagihan'],
        ];
    }
}

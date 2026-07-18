<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();

        // Bersihkan data lama
        Transaction::where('user_id', $user->id)->delete();
        Account::where('user_id', $user->id)->delete();
        Period::where('user_id', $user->id)->delete();
        Category::query()->delete();

        // ─── Categories ───────────────────────────────────────────
        $categories = collect();
        $catList = [
            ['name' => 'Gaji',          'type' => 'income',  'budget_limit' => 0],
            ['name' => 'Freelance',     'type' => 'income',  'budget_limit' => 0],
            ['name' => 'Investasi',     'type' => 'income',  'budget_limit' => 0],
            ['name' => 'Makanan',       'type' => 'expense', 'budget_limit' => 2000000],
            ['name' => 'Transportasi',  'type' => 'expense', 'budget_limit' => 1000000],
            ['name' => 'Hiburan',       'type' => 'expense', 'budget_limit' => 500000],
            ['name' => 'Tagihan',       'type' => 'expense', 'budget_limit' => 1500000],
            ['name' => 'Kesehatan',     'type' => 'expense', 'budget_limit' => 500000],
            ['name' => 'Lainnya',       'type' => 'expense', 'budget_limit' => 0],
        ];
        foreach ($catList as $data) {
            $categories->push(Category::create($data));
        }
        $categories = $categories->keyBy('name');

        // ─── Accounts ────────────────────────────────────────────
        $accList = [
            ['name' => 'Tunai',   'type' => 'cash',    'description' => 'Dompet fisik harian'],
            ['name' => 'BCA',     'type' => 'bank',    'description' => 'Rekening utama'],
            ['name' => 'Mandiri', 'type' => 'bank',    'description' => 'Rekening tabungan'],
            ['name' => 'GoPay',   'type' => 'ewallet', 'description' => 'E-wallet untuk transaksi digital'],
        ];
        $accounts = collect();
        foreach ($accList as $data) {
            $accounts->push(Account::create(['user_id' => $user->id] + $data));
        }
        $accounts = $accounts->keyBy('name');

        // ─── Periods ─────────────────────────────────────────────
        $periods = collect();
        foreach (range(1, 7) as $m) {
            $year = 2026;
            $start = "{$year}-".str_pad($m, 2, '0', STR_PAD_LEFT).'-01';
            $end = date('Y-m-t', strtotime($start));
            $monthName = Carbon::parse($start)->locale('id')->isoFormat('MMMM');

            $periods->push(Period::create([
                'user_id'         => $user->id,
                'name'            => "{$monthName} {$year}",
                'start_date'      => $start,
                'end_date'        => $end,
                'year'            => $year,
                'month'           => $m,
                'opening_balance' => $m === 1 ? 5000000 : 0,
                'closing_balance' => 0,
                'is_closed'       => $m < 7,
            ]));
        }

        // ─── Sample Transactions ─────────────────────────────────
        $sampleTx = [
            ['month' => 1, 'type' => 'income',  'cat' => 'Gaji',       'amount' => 8000000, 'date' => '2026-01-01 08:00:00', 'note' => 'Gaji Januari'],
            ['month' => 1, 'type' => 'expense', 'cat' => 'Makanan',    'amount' => 800000,  'date' => '2026-01-05 10:30:00', 'note' => 'Belanja bulanan'],
            ['month' => 1, 'type' => 'expense', 'cat' => 'Transportasi', 'amount' => 400000, 'date' => '2026-01-10 07:15:00', 'note' => 'Bensin'],
            ['month' => 2, 'type' => 'income',  'cat' => 'Gaji',       'amount' => 8000000, 'date' => '2026-02-01 08:00:00', 'note' => 'Gaji Februari'],
            ['month' => 2, 'type' => 'expense', 'cat' => 'Makanan',    'amount' => 900000,  'date' => '2026-02-03 11:00:00', 'note' => 'Belanja bulanan'],
            ['month' => 2, 'type' => 'expense', 'cat' => 'Tagihan',    'amount' => 750000,  'date' => '2026-02-20 14:30:00', 'note' => 'Internet'],
            ['month' => 3, 'type' => 'income',  'cat' => 'Gaji',       'amount' => 8500000, 'date' => '2026-03-01 08:00:00', 'note' => 'Gaji Maret'],
            ['month' => 3, 'type' => 'expense', 'cat' => 'Makanan',    'amount' => 750000,  'date' => '2026-03-05 10:00:00', 'note' => 'Belanja'],
            ['month' => 3, 'type' => 'income',  'cat' => 'Freelance',  'amount' => 1500000, 'date' => '2026-03-15 16:45:00', 'note' => 'Project website'],
            ['month' => 4, 'type' => 'income',  'cat' => 'Gaji',       'amount' => 8500000, 'date' => '2026-04-01 08:00:00', 'note' => 'Gaji April'],
            ['month' => 4, 'type' => 'expense', 'cat' => 'Transportasi', 'amount' => 420000, 'date' => '2026-04-12 07:30:00', 'note' => 'Bensin'],
            ['month' => 5, 'type' => 'income',  'cat' => 'Gaji',       'amount' => 8500000, 'date' => '2026-05-01 08:00:00', 'note' => 'Gaji Mei'],
            ['month' => 5, 'type' => 'expense', 'cat' => 'Makanan',    'amount' => 950000,  'date' => '2026-05-05 10:15:00', 'note' => 'Belanja'],
            ['month' => 6, 'type' => 'income',  'cat' => 'Gaji',       'amount' => 9000000, 'date' => '2026-06-01 08:00:00', 'note' => 'Gaji Juni'],
            ['month' => 6, 'type' => 'expense', 'cat' => 'Hiburan',    'amount' => 300000,  'date' => '2026-06-20 19:00:00', 'note' => 'Konser'],
            ['month' => 7, 'type' => 'income',  'cat' => 'Gaji',       'amount' => 9000000, 'date' => '2026-07-01 08:00:00', 'note' => 'Gaji Juli'],
            ['month' => 7, 'type' => 'expense', 'cat' => 'Makanan',    'amount' => 500000,  'date' => '2026-07-03 09:30:00', 'note' => 'Belanja mingguan'],
        ];

        $accountMap = [
            'Gaji' => 'BCA', 'Freelance' => 'BCA', 'Investasi' => 'Mandiri',
            'Makanan' => 'Tunai', 'Transportasi' => 'Tunai', 'Hiburan' => 'GoPay',
            'Tagihan' => 'BCA', 'Kesehatan' => 'Mandiri', 'Lainnya' => 'Tunai',
        ];

        foreach ($sampleTx as $tx) {
            $cat = $categories->get($tx['cat']);
            $period = $periods->get($tx['month'] - 1);
            $acc = $accounts->get($accountMap[$tx['cat']] ?? 'Tunai');

            Transaction::create([
                'user_id'     => $user->id,
                'period_id'   => $period->id,
                'category_id' => $cat->id,
                'account_id'  => $acc->id,
                'type'        => $tx['type'],
                'amount'      => $tx['amount'],
                'date'        => $tx['date'],
                'note'        => $tx['note'],
            ]);
        }

        // ─── Hitung closing balance ──────────────────────────────
        $runningBalance = 0;
        foreach ($periods as $p) {
            $incomeTotal  = Transaction::where('period_id', $p->id)->where('type', 'income')->sum('amount');
            $expenseTotal = Transaction::where('period_id', $p->id)->where('type', 'expense')->sum('amount');

            if ($p->month === 1) {
                $p->opening_balance = 5000000;
            } else {
                $p->opening_balance = $runningBalance;
            }
            $p->closing_balance = $p->opening_balance + $incomeTotal - $expenseTotal;
            $p->save();

            $runningBalance = $p->closing_balance;
        }
    }
}

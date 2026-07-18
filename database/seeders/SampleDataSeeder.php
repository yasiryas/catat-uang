<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AdjustmentLog;
use App\Models\Category;
use App\Models\Mutation;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();

        // Clean existing data (disable FK checks for clean truncate)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        AdjustmentLog::truncate();
        Mutation::truncate();
        Transaction::truncate();
        Period::truncate();
        Account::truncate();
        Category::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ─── Accounts ───────────────────────────────────────────
        $accountData = [
            ['name' => 'Tunai',   'type' => 'cash',    'description' => 'Dompet fisik harian'],
            ['name' => 'BCA',     'type' => 'bank',    'description' => 'Rekening utama'],
            ['name' => 'Mandiri', 'type' => 'bank',    'description' => 'Rekening tabungan'],
            ['name' => 'GoPay',   'type' => 'ewallet', 'description' => 'E-wallet untuk transaksi digital'],
        ];
        foreach ($accountData as $data) {
            Account::create(['user_id' => $user->id] + $data);
        }
        $accounts = Account::where('user_id', $user->id)->get()->keyBy('name');

        // ─── Categories ─────────────────────────────────────────
        $categoryData = [
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
        foreach ($categoryData as $data) {
            Category::create($data);
        }
        $categories = Category::all()->keyBy(fn ($c) => $c->name);
        $incomeCats  = $categories->where('type', 'income');
        $expenseCats = $categories->where('type', 'expense');

        // ─── Account mapping per category ───────────────────────
        $accountMap = [
            'Gaji'         => 'BCA',
            'Freelance'    => 'BCA',
            'Investasi'    => 'Mandiri',
            'Makanan'      => 'Tunai',
            'Transportasi' => 'Tunai',
            'Hiburan'      => 'GoPay',
            'Tagihan'      => 'BCA',
            'Kesehatan'    => 'Mandiri',
            'Lainnya'      => 'Tunai',
        ];

        // ─── Periods ────────────────────────────────────────────
        $periods = [];
        foreach (range(1, 7) as $m) {
            $year = 2026;
            $start = sprintf('%s-%02d-01', $year, $m);
            $end   = date('Y-m-t', strtotime($start));
            $monthName = \Carbon\Carbon::parse($start)->locale('id')->isoFormat('MMMM');

            $opening = match ($m) {
                1 => 5000000,
                default => null, // will be computed from previous closing
            };
            $isClosed = $m < 7;

            $periods[$m] = Period::create([
                'user_id'         => $user->id,
                'name'            => "{$monthName} {$year}",
                'start_date'      => $start,
                'end_date'        => $end,
                'year'            => $year,
                'month'           => $m,
                'opening_balance' => $opening ?? 0,
                'closing_balance' => 0,
                'is_closed'       => $isClosed,
            ]);
        }

        // ─── Transactions ───────────────────────────────────────
        $txData = [
            // [period_month, type, category, amount, date, note]
            // Jan
            [1, 'income',  'Gaji',      8000000, '2026-01-01 08:00:00', 'Gaji Januari'],
            [1, 'income',  'Freelance', 1500000, '2026-01-15 14:30:00', 'Project website'],
            [1, 'expense', 'Makanan',    800000, '2026-01-05 10:00:00', 'Belanja bulanan'],
            [1, 'expense', 'Transportasi', 400000, '2026-01-10 07:15:00', 'Bensin & parkir'],
            [1, 'expense', 'Hiburan',    300000, '2026-01-20 19:30:00', 'Nonton bioskop'],
            [1, 'expense', 'Tagihan',    600000, '2026-01-25 11:00:00', 'Listrik & air'],

            // Feb
            [2, 'income',  'Gaji',      8000000, '2026-02-01 08:00:00', 'Gaji Februari'],
            [2, 'income',  'Freelance', 2000000, '2026-02-10 10:00:00', 'Project mobile app'],
            [2, 'expense', 'Makanan',    900000, '2026-02-03 10:30:00', 'Belanja bulanan'],
            [2, 'expense', 'Transportasi', 450000, '2026-02-12 07:00:00', 'Bensin & parkir'],
            [2, 'expense', 'Hiburan',    250000, '2026-02-14 18:00:00', 'Valentine dinner'],
            [2, 'expense', 'Tagihan',    750000, '2026-02-20 14:00:00', 'Internet & pulsa'],

            // Mar
            [3, 'income',  'Gaji',      8500000, '2026-03-01 08:00:00', 'Gaji Maret + bonus'],
            [3, 'income',  'Freelance', 1000000, '2026-03-20 15:00:00', 'Maintenance project'],
            [3, 'expense', 'Makanan',    750000, '2026-03-05 09:45:00', 'Belanja bulanan'],
            [3, 'expense', 'Transportasi', 380000, '2026-03-10 07:30:00', 'Bensin & parkir'],
            [3, 'expense', 'Hiburan',    200000, '2026-03-15 20:00:00', 'Game baru'],
            [3, 'expense', 'Kesehatan',  350000, '2026-03-22 08:00:00', 'Cek dokter'],

            // Apr
            [4, 'income',  'Gaji',      8500000, '2026-04-01 08:00:00', 'Gaji April'],
            [4, 'income',  'Freelance', 2500000, '2026-04-18 13:00:00', 'Project e-commerce'],
            [4, 'expense', 'Makanan',    850000, '2026-04-03 10:15:00', 'Belanja bulanan'],
            [4, 'expense', 'Transportasi', 420000, '2026-04-12 07:45:00', 'Bensin & parkir'],
            [4, 'expense', 'Hiburan',    400000, '2026-04-20 09:00:00', 'Libur akhir pekan'],
            [4, 'expense', 'Tagihan',    800000, '2026-04-25 11:30:00', 'Listrik & air'],

            // Mei
            [5, 'income',  'Gaji',      8500000, '2026-05-01 08:00:00', 'Gaji Mei'],
            [5, 'income',  'Freelance', 1800000, '2026-05-15 16:00:00', 'Project API'],
            [5, 'expense', 'Makanan',    950000, '2026-05-05 10:00:00', 'Belanja bulanan + lebaran'],
            [5, 'expense', 'Transportasi', 600000, '2026-05-10 06:00:00', 'Mudik lebaran'],
            [5, 'expense', 'Hiburan',    350000, '2026-05-25 18:30:00', 'Keluarga berkumpul'],
            [5, 'expense', 'Lainnya',   1000000, '2026-05-18 12:00:00', 'THR keluarga'],

            // Jun
            [6, 'income',  'Gaji',      9000000, '2026-06-01 08:00:00', 'Gaji Juni + kenaikan'],
            [6, 'income',  'Freelance', 3000000, '2026-06-10 11:00:00', 'Project besar selesai'],
            [6, 'expense', 'Makanan',    800000, '2026-06-05 09:30:00', 'Belanja bulanan'],
            [6, 'expense', 'Transportasi', 400000, '2026-06-12 07:00:00', 'Bensin & parkir'],
            [6, 'expense', 'Hiburan',    300000, '2026-06-20 19:00:00', 'Konser'],
            [6, 'expense', 'Tagihan',    700000, '2026-06-25 14:30:00', 'Internet & pulsa'],

            // Jul (current, open)
            [7, 'income',  'Gaji',      9000000, '2026-07-01 08:00:00', 'Gaji Juli'],
            [7, 'income',  'Freelance', 1200000, '2026-07-10 15:30:00', 'Project maintenance'],
            [7, 'expense', 'Makanan',    500000, '2026-07-03 10:00:00', 'Belanja mingguan'],
            [7, 'expense', 'Transportasi', 250000, '2026-07-08 07:15:00', 'Bensin mingguan'],
            [7, 'expense', 'Hiburan',    150000, '2026-07-12 20:00:00', 'Nonton streaming'],
        ];

        foreach ($txData as [$pm, $type, $catName, $amount, $date, $note]) {
            $cat = $type === 'income'
                ? $incomeCats->firstWhere('name', $catName)
                : $expenseCats->firstWhere('name', $catName);
            if (!$cat) continue;

            $accountName = $accountMap[$catName] ?? 'Tunai';

            Transaction::create([
                'user_id'     => $user->id,
                'period_id'   => $periods[$pm]->id,
                'category_id' => $cat->id,
                'account_id'  => $accounts[$accountName]->id,
                'type'        => $type,
                'amount'      => $amount,
                'date'        => $date,
                'note'        => $note,
            ]);
        }

        // ─── Compute period closing balances ────────────────────
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

        // ─── Mutations ──────────────────────────────────────────
        Mutation::create([
            'user_id'        => $user->id,
            'period_id'      => $periods[7]->id,
            'from_account_id' => $accounts['BCA']->id,
            'to_account_id'   => $accounts['Tunai']->id,
            'from_account'   => 'BCA',
            'to_account'     => 'Tunai',
            'amount'         => 500000,
            'note'           => 'Tarik tunai',
            'date'           => '2026-07-05 10:00:00',
        ]);

        Mutation::create([
            'user_id'        => $user->id,
            'period_id'      => $periods[6]->id,
            'from_account_id' => $accounts['BCA']->id,
            'to_account_id'   => $accounts['GoPay']->id,
            'from_account'   => 'BCA',
            'to_account'     => 'GoPay',
            'amount'         => 200000,
            'note'           => 'Top up GoPay',
            'date'           => '2026-06-15 13:00:00',
        ]);

        // ─── Adjustment Logs ────────────────────────────────────
        $adjustTx = Transaction::where('period_id', $periods[7]->id)
            ->where('type', 'expense')
            ->first();

        if ($adjustTx) {
            AdjustmentLog::create([
                'period_id'      => $periods[7]->id,
                'transaction_id' => $adjustTx->id,
                'user_id'        => $user->id,
                'type'           => 'expense',
                'amount'         => 50000,
                'old_amount'     => $adjustTx->amount,
                'new_amount'     => $adjustTx->amount - 50000,
                'reason'         => 'Koreksi overbudget makan',
                'note'           => 'Adjustment via seeder',
                'date'           => '2026-07-13 11:00:00',
            ]);
        }
    }
}

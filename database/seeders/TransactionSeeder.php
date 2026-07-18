<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrFail();
        $periods = Period::where('user_id', $user->id)->get()->keyBy(fn ($p) => $p->month);
        $incomeCategories = Category::where('type', 'income')->get();
        $expenseCategories = Category::where('type', 'expense')->get();

        $transactions = [
            // January 2025
            ['period_month' => 1, 'type' => 'income', 'category' => 'Gaji', 'amount' => 8000000, 'date' => '2025-01-01', 'note' => 'Gaji Januari'],
            ['period_month' => 1, 'type' => 'income', 'category' => 'Freelance', 'amount' => 1500000, 'date' => '2025-01-15', 'note' => 'Project website'],
            ['period_month' => 1, 'type' => 'expense', 'category' => 'Makanan', 'amount' => 800000, 'date' => '2025-01-05', 'note' => 'Belanja bulanan'],
            ['period_month' => 1, 'type' => 'expense', 'category' => 'Transportasi', 'amount' => 400000, 'date' => '2025-01-10', 'note' => 'Bensin & parkir'],
            ['period_month' => 1, 'type' => 'expense', 'category' => 'Hiburan', 'amount' => 300000, 'date' => '2025-01-20', 'note' => 'Nonton bioskop'],

            // February 2025
            ['period_month' => 2, 'type' => 'income', 'category' => 'Gaji', 'amount' => 8000000, 'date' => '2025-02-01', 'note' => 'Gaji Februari'],
            ['period_month' => 2, 'type' => 'income', 'category' => 'Freelance', 'amount' => 2000000, 'date' => '2025-02-10', 'note' => 'Project mobile app'],
            ['period_month' => 2, 'type' => 'expense', 'category' => 'Makanan', 'amount' => 900000, 'date' => '2025-02-03', 'note' => 'Belanja bulanan'],
            ['period_month' => 2, 'type' => 'expense', 'category' => 'Transportasi', 'amount' => 450000, 'date' => '2025-02-12', 'note' => 'Bensin & parkir'],
            ['period_month' => 2, 'type' => 'expense', 'category' => 'Hiburan', 'amount' => 250000, 'date' => '2025-02-14', 'note' => 'Valentine dinner'],
            ['period_month' => 2, 'type' => 'expense', 'category' => 'Lainnya', 'amount' => 500000, 'date' => '2025-02-20', 'note' => 'Perawatan mobil'],

            // March 2025
            ['period_month' => 3, 'type' => 'income', 'category' => 'Gaji', 'amount' => 8500000, 'date' => '2025-03-01', 'note' => 'Gaji Maret + bonus'],
            ['period_month' => 3, 'type' => 'income', 'category' => 'Freelance', 'amount' => 1000000, 'date' => '2025-03-20', 'note' => 'Maintenance project'],
            ['period_month' => 3, 'type' => 'expense', 'category' => 'Makanan', 'amount' => 750000, 'date' => '2025-03-05', 'note' => 'Belanja bulanan'],
            ['period_month' => 3, 'type' => 'expense', 'category' => 'Transportasi', 'amount' => 380000, 'date' => '2025-03-10', 'note' => 'Bensin & parkir'],
            ['period_month' => 3, 'type' => 'expense', 'category' => 'Hiburan', 'amount' => 200000, 'date' => '2025-03-15', 'note' => 'Game baru'],

            // April 2025
            ['period_month' => 4, 'type' => 'income', 'category' => 'Gaji', 'amount' => 8500000, 'date' => '2025-04-01', 'note' => 'Gaji April'],
            ['period_month' => 4, 'type' => 'income', 'category' => 'Freelance', 'amount' => 2500000, 'date' => '2025-04-18', 'note' => 'Project e-commerce'],
            ['period_month' => 4, 'type' => 'expense', 'category' => 'Makanan', 'amount' => 850000, 'date' => '2025-04-03', 'note' => 'Belanja bulanan'],
            ['period_month' => 4, 'type' => 'expense', 'category' => 'Transportasi', 'amount' => 420000, 'date' => '2025-04-12', 'note' => 'Bensin & parkir'],
            ['period_month' => 4, 'type' => 'expense', 'category' => 'Hiburan', 'amount' => 400000, 'date' => '2025-04-20', 'note' => 'Libur akhir pekan'],

            // May 2025
            ['period_month' => 5, 'type' => 'income', 'category' => 'Gaji', 'amount' => 8500000, 'date' => '2025-05-01', 'note' => 'Gaji Mei'],
            ['period_month' => 5, 'type' => 'income', 'category' => 'Freelance', 'amount' => 1800000, 'date' => '2025-05-15', 'note' => 'Project API'],
            ['period_month' => 5, 'type' => 'expense', 'category' => 'Makanan', 'amount' => 950000, 'date' => '2025-05-05', 'note' => 'Belanja bulanan + lebaran'],
            ['period_month' => 5, 'type' => 'expense', 'category' => 'Transportasi', 'amount' => 600000, 'date' => '2025-05-10', 'note' => 'Mudik lebaran'],
            ['period_month' => 5, 'type' => 'expense', 'category' => 'Hiburan', 'amount' => 350000, 'date' => '2025-05-25', 'note' => 'Keluarga berkumpul'],
            ['period_month' => 5, 'type' => 'expense', 'category' => 'Lainnya', 'amount' => 1000000, 'date' => '2025-05-18', 'note' => 'THR keluarga'],

            // June 2025
            ['period_month' => 6, 'type' => 'income', 'category' => 'Gaji', 'amount' => 9000000, 'date' => '2025-06-01', 'note' => 'Gaji Juni + kenaikan'],
            ['period_month' => 6, 'type' => 'income', 'category' => 'Freelance', 'amount' => 3000000, 'date' => '2025-06-10', 'note' => 'Project besar selesai'],
            ['period_month' => 6, 'type' => 'expense', 'category' => 'Makanan', 'amount' => 800000, 'date' => '2025-06-05', 'note' => 'Belanja bulanan'],
            ['period_month' => 6, 'type' => 'expense', 'category' => 'Transportasi', 'amount' => 400000, 'date' => '2025-06-12', 'note' => 'Bensin & parkir'],
            ['period_month' => 6, 'type' => 'expense', 'category' => 'Hiburan', 'amount' => 300000, 'date' => '2025-06-20', 'note' => 'Konser'],

            // July 2025 (current month)
            ['period_month' => 7, 'type' => 'income', 'category' => 'Gaji', 'amount' => 9000000, 'date' => '2025-07-01', 'note' => 'Gaji Juli'],
            ['period_month' => 7, 'type' => 'income', 'category' => 'Freelance', 'amount' => 1200000, 'date' => '2025-07-10', 'note' => 'Project maintenance'],
            ['period_month' => 7, 'type' => 'expense', 'category' => 'Makanan', 'amount' => 500000, 'date' => '2025-07-03', 'note' => 'Belanja mingguan'],
            ['period_month' => 7, 'type' => 'expense', 'category' => 'Transportasi', 'amount' => 250000, 'date' => '2025-07-08', 'note' => 'Bensin mingguan'],
            ['period_month' => 7, 'type' => 'expense', 'category' => 'Hiburan', 'amount' => 150000, 'date' => '2025-07-12', 'note' => 'Nonton streaming'],
        ];

        foreach ($transactions as $t) {
            $period = $periods->get($t['period_month']);
            if (!$period) continue;

            $category = $t['type'] === 'income'
                ? $incomeCategories->where('name', $t['category'])->first()
                : $expenseCategories->where('name', $t['category'])->first();

            if (!$category) continue;

            Transaction::create([
                'user_id' => $user->id,
                'period_id' => $period->id,
                'category_id' => $category->id,
                'type' => $t['type'],
                'amount' => $t['amount'],
                'date' => $t['date'],
                'note' => $t['note'],
            ]);
        }
    }
}
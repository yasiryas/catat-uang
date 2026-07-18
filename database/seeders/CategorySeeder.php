<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Gaji', 'type' => 'income', 'budget_limit' => 0],
            ['name' => 'Freelance', 'type' => 'income', 'budget_limit' => 0],
            ['name' => 'Makanan', 'type' => 'expense', 'budget_limit' => 2000000],
            ['name' => 'Transportasi', 'type' => 'expense', 'budget_limit' => 1000000],
            ['name' => 'Hiburan', 'type' => 'expense', 'budget_limit' => 500000],
            ['name' => 'Lainnya', 'type' => 'expense', 'budget_limit' => 0],
        ];

        Category::insert($categories);
    }
}

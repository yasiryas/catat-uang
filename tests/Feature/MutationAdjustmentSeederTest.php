<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Database\Seeders\MutationAdjustmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutationAdjustmentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_is_idempotent(): void
    {
        User::factory()->create();
        $this->seed(DemoDataSeeder::class);

        $this->seed(MutationAdjustmentSeeder::class);
        $this->assertDatabaseCount('mutations', 3);
        $this->assertDatabaseCount('adjustment_logs', 3);

        $this->seed(MutationAdjustmentSeeder::class);
        $this->assertDatabaseCount('mutations', 3);
        $this->assertDatabaseCount('adjustment_logs', 3);
    }
}

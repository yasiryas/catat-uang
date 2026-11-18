<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adjustment_logs', function (Blueprint $table): void {
            $table->foreignId('transaction_id')->nullable()->change();
            $table->decimal('old_amount', 15, 2)->nullable()->change();
            $table->decimal('new_amount', 15, 2)->nullable()->change();
            $table->text('reason')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('adjustment_logs')->update([
            'old_amount' => DB::raw('COALESCE(old_amount, 0)'),
            'new_amount' => DB::raw('COALESCE(new_amount, 0)'),
            'reason' => DB::raw("COALESCE(reason, '')"),
        ]);

        Schema::table('adjustment_logs', function (Blueprint $table): void {
            $table->decimal('old_amount', 15, 2)->nullable(false)->change();
            $table->decimal('new_amount', 15, 2)->nullable(false)->change();
            $table->text('reason')->nullable(false)->change();
        });
    }
};

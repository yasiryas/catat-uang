<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Periode unik per keluarga (bukan per user), sehingga anggota berbagi periode yang sama.
        // Index unik lama juga dipakai FK user_id, jadi lepas FK dulu sebelum mengubah index.
        Schema::table('periods', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'year', 'month']);
            $table->index('user_id');
            $table->unique(['family_id', 'year', 'month']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Salin family_id user ke semua data historis miliknya.
        foreach (['accounts', 'periods', 'transactions', 'goals', 'mutations', 'adjustment_logs'] as $table) {
            DB::table($table)->whereNull('family_id')->update([
                'family_id' => DB::raw("(SELECT u.family_id FROM users u WHERE u.id = {$table}.user_id)"),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['family_id', 'year', 'month']);
            $table->dropIndex(['user_id']);
            $table->unique(['user_id', 'year', 'month']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};

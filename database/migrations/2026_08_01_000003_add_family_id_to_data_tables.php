<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('periods', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });

        Schema::table('periods', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });

        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('family_id');
        });
    }
};
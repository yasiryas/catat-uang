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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->dateTime('date')->nullable()->change();
        });

        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->dateTime('date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('date')->change();
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });

        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
        });
    }
};

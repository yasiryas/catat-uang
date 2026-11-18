<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mutations', function (Blueprint $table): void {
            $table->dropForeign(['period_id']);
            $table->dropUnique(['period_id']);
            $table->index('period_id');
            $table->foreign('period_id')->references('id')->on('periods')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table): void {
            $table->dropForeign(['period_id']);
            $table->dropIndex(['period_id']);
            $table->unique('period_id');
            $table->foreign('period_id')->references('id')->on('periods')->cascadeOnDelete();
        });
    }
};

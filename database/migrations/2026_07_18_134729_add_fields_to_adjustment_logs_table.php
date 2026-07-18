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
        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->constrained()->after('id');
            $table->enum('type', ['income', 'expense'])->nullable()->after('period_id');
            $table->decimal('amount', 15, 2)->default(0)->after('type');
            $table->text('note')->nullable()->after('amount');
            $table->date('date')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('adjustment_logs', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn(['period_id', 'type', 'amount', 'note', 'date']);
        });
    }
};

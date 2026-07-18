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
        Schema::table('mutations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->after('id');
            $table->string('from_account')->nullable()->after('period_id');
            $table->string('to_account')->nullable()->after('from_account');
            $table->decimal('amount', 15, 2)->default(0)->after('to_account');
            $table->text('note')->nullable()->after('amount');
            $table->date('date')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'from_account', 'to_account', 'amount', 'note', 'date']);
        });
    }
};

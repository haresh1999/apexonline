<?php

use App\Models\Transaction;
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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('mr_order_id');
        });

        DB::table('transactions')->update([
            'mr_order_id' => DB::raw('order_id')
        ]);

        Schema::table('transactions', function (Blueprint $table) {
            $table->unique(['user_id', 'mr_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['mr_order_id']);
            $table->dropColumn('mr_order_id');
        });
    }
};

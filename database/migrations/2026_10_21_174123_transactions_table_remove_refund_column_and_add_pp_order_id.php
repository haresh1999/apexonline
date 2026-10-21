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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_response']);
            $table->string('pp_order_id', 100)->unique()->nullable();
        });

        $tnxs = DB::table('transactions')->get();

        foreach ($tnxs as $tnx) {

            $response = json_decode($tnx->payment_response);

            $orderId = $response->order_id ?? null;

            DB::table('transactions')->where('id', $tnx->id)->update(['pp_order_id' => $orderId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};

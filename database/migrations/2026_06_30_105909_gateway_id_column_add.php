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
            $table->unsignedBigInteger('gateway_id');
        });

        $tnxs = DB::table('transactions')->get();

        foreach ($tnxs as $tnx) {
            $gateway = DB::table('gateways')->where('slug', $tnx->gateway)->first();
            DB::table('transactions')->where('id', $tnx->id)->update(['gateway_id' => $gateway->id]);
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

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
        Schema::table('bank_infos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });

        DB::table('bank_infos')->update(['user_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_infos', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};

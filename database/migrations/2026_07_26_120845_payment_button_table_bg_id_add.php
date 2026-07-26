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
        Schema::table('button_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('bg_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('button_payments', function (Blueprint $table) {
            $table->dropColumn('bg_id');
        });
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('client_id', 100)->nullable()->change();
            $table->string('client_secret')->nullable()->change();
            $table->string('sbx_client_id', 100)->nullable()->change();
            $table->string('sbx_client_secret')->nullable()->change();
            $table->string('callback_secret')->nullable()->change();
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

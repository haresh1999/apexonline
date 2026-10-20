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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('order_id')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('account_number', 50);
            $table->string('ifsc_code', 50);
            $table->string('holder_name', 100);
            $table->string('bank_name', 100);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};

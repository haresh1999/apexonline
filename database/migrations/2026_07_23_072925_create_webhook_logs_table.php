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
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('tnx_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->enum('env', ['sandbox', 'production']);
            $table->string('url');
            $table->string('signature');
            $table->json('payload');
            $table->longText('response');
            $table->smallInteger('status')->comment('200 = OK,201 = Created,400 = Bad Request,401 = Unauthorized,403 = Forbidden,404 = Not Found,422 = Validation Error,500 = Internal Server Error');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};

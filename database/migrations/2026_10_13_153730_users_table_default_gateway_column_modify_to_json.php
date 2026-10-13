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
        $row = DB::table('users')->get();

        foreach ($row as $value) {
            $data = [];
            $data[] = $value->default_gateway;

            DB::table('users')->where('id', $value->id)->update(['default_gateway' => json_encode($data)]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->json('default_gateway')->nullable()->change();
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

<?php

namespace Database\Seeders;

use App\Models\Gateway;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            'PhonePe',
            'RazorPay',
            'CashFree',
            'PayU',
            'EaseBuzz',
            'PayTm',
            'ZaaakaPay',
            'Ccavenue',
            'Zoho',
            'Instamojo',
            'SabPaisa',
            'HDFC'
        ];

        sort($methods);

        foreach ($methods as $value) {
            Gateway::updateOrCreate([
                'name' => $value,
            ], [
                'slug' => str()->slug($value),
                'status' => 1
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            'apexonline' => [
                'where' => [
                    'email' => 'ikondubai@gmail.com',
                    'mobile' => '8075088769',
                ],
                'data' => [
                    'name' => 'Apexonline',
                    'email_verified_at' => now(),
                    'client_id' => 'apexonline',
                    'client_secret' => 'c73ba053-bf3f-4c9e-88ae-9e49fd4534e4',
                    'sbx_client_id' => 'apexonline',
                    'sbx_client_secret' => '1890b383-345a-42d3-88c7-6e80efd08460',
                    'password' => bcrypt('apexonline@api'),
                    'env' => 'sandbox',
                    'callback_secret' => '09e820e3-b7b6-438d-93ed-f5cdb3c39d93',
                    'whitelist_ip' => null,
                    'default_gateway' => null
                ]
            ],
            'tradewill' => [
                'where' => [
                    'email' => 'tradewill@gmail.com',
                    'mobile' => '60138193428',
                ],
                'data' => [
                    'name' => 'Tradewill',
                    'email_verified_at' => now(),
                    'client_id' => 'tradewill',
                    'client_secret' => 'bb540801-2374-4afd-9061-b2ca6cce2743',
                    'sbx_client_id' => 'tradewill',
                    'sbx_client_secret' => '0609d1f5-d288-4a36-bb5d-13a26ebec06e',
                    'password' => bcrypt('tradewill@api'),
                    'env' => 'sandbox',
                    'callback_secret' => 'bf001636-822c-4dec-ba66-bb40cc468725',
                    'whitelist_ip' => null,
                    'default_gateway' => null
                ]
            ],
            'xs' => [
                'where' => [
                    'email' => 'giannis.kontogiannis@xs.com',
                    'mobile' => '35799967137',
                ],
                'data' => [
                    'name' => 'XS Forex',
                    'email_verified_at' => now(),
                    'client_id' => 'xs-forex',
                    'client_secret' => '859ab947-c0f0-46c6-94d9-01e5835341f3',
                    'sbx_client_id' => 'xs-forex',
                    'sbx_client_secret' => '97f4581f-5a75-41b9-8f34-8fa4da935e96',
                    'password' => bcrypt('xs@api'),
                    'env' => 'sandbox',
                    'callback_secret' => '17a89db4-4096-4d02-a3af-29ba3f259096',
                    'whitelist_ip' => null,
                    'default_gateway' => null
                ]
            ],
        ];

        foreach ($users as $user) {

            User::updateOrCreate($user['where'], $user['data']);
        }
    }
}

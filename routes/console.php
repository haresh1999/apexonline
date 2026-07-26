<?php

use App\Models\ButtonGateway;
use App\Models\ButtonPayment;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('update', function () {

    $data = [
        [
            'name' => 'cashfree',
            'url' => 'https://payments.cashfree.com/forms/apexonlinewigo',
            'status' => 1
        ],
        [
            'name' => 'instamojo',
            'url' => 'https://imjo.in/PkgdYq',
            'status' => 1
        ],
        [
            'name' => 'zoho',
            'url' => 'https://zohosecurepay.in/checkout/yr57e41t-rxinobe28njso/FEE-PARTIAL-PAYMENT',
            'status' => 1
        ],
        [
            'name' => 'payu',
            'url' => 'https://u.payu.in/ZIVbiPxUF7y5',
            'status' => 1
        ]
    ];

    foreach ($data as $key => $value) {

        $res = ButtonGateway::create($value);

        ButtonPayment::where('gateway', $value['name'])->update(['bg_id' => $res->id]);
    }
});

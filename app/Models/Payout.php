<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'order_id',
        'uuid',
        'amount',
        'account_number',
        'ifsc_code',
        'holder_name',
        'bank_name',
        'status',
        'response'
    ];
}

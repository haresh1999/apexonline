<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankInfo extends Model
{
    protected $fillable = [
        'account_number',
        'ifsc_code',
        'holder_name',
        'bank_name',
    ];
}

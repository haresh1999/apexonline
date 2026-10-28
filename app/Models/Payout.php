<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

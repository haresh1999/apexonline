<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ButtonPayment extends Model
{
    protected $fillable = [
        'gateway',
        'order_id,'
    ];
}

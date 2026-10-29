<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BankInfo extends Model
{
    protected $fillable = [
        'account_number',
        'ifsc_code',
        'holder_name',
        'bank_name',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeAuthUser($q)
    {
        if (Auth::id() != 1) {
            return $q->where('user_id', getUserId());
        }

        return $q;
    }
}

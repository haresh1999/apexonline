<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'payer_name',
        'payer_email',
        'payer_mobile',
        'status',
        'gateway',
        'env',
        'amount',
        'payment_response',
        'refund_amount',
        'refund_response',
        'redirect_url',
        'callback_url',
        'reference_id',
        'mr_order_id',
        'gateway_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->env = config('services.env');
            $model->reference_id = str()->uuid()->toString();
        });
    }

    public function scopeAuthTnx($q)
    {
        if (Auth::id() != 1) {
            return $q->where('user_id', Auth::id());
        }

        return $q;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

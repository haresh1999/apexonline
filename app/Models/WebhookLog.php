<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'url',
        'signature',
        'payload',
        'response',
        'user_id',
        'env',
        'status',
        'tnx_id'
    ];

    public function scopeAuthUser($q)
    {
        if (auth()->id() != 1) {

            return $q->where('user_id', getUserId());
        }

        return $q;
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'tnx_id', 'id');
    }
}

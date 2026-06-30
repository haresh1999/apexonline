<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'gateway_id')->where('env', 'production')->where('status', 'completed');
    }
}

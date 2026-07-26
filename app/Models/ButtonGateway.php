<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ButtonGateway extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'url',
        'status'
    ];

    public function transaction()
    {
        return $this->hasMany(ButtonPayment::class, 'bg_id');
    }
}

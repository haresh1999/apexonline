<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
        'email',
        'mobile',
        'client_id',
        'client_secret',
        'sbx_client_id',
        'sbx_client_secret',
        'whitelist_ip',
        'default_gateway',
        'callback_secret',
        'password',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getWhitelistIpAttribute($value)
    {
        if ($value) {
            return implode(', ', json_decode($value));
        }
        return null;
    }

    public function scopeAuthUser($q)
    {
        if (auth()->id() != 1) {

            return $q->where('user_id', getUserId());
        }

        return $q;
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function earning()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id')
            ->where('env', 'production')
            ->where('status', 'completed');
    }

    public function token()
    {
        return $this->hasMany(Token::class);
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $table = 'social_accounts';

    protected $fillable = [
        'user_id', 'provider_name', 'provider_id',
        'name', 'nickname', 'email', 'avatar',
        'access_token', 'refresh_token', 'expires_in'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

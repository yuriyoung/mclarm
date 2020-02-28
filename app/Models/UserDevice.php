<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device', 'ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function setUpdatedAt($value)
    {
        // Do nothing.
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

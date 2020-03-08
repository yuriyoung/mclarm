<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'avatar', 'qrcode', 'first_name', 'last_name', 'gender', 'birthday',
        'career', 'website', 'github', 'address_home', 'address_work', 'signature', 'about'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Return a concatenated result for the accounts full name.
     * TODO: swap first name and last name for chinese name
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim($this->last_name .''.$this->first_name);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

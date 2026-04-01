<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'has_seen_survey', 'district_id', 'district_regional_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function office()
    {
        return $this->belongsTo(\App\Models\Office::class, 'office_id', 'id');
    }

    /**
     * Scope to get user's full name (first_name + last_name)
     */
    public function scopeFullName($query)
    {
        return $query->selectRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) as full_name");
    }

    /**
     * Get the user's full name as an attribute
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}

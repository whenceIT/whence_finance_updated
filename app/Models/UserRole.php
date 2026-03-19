<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Roles\EloquentRole;

class UserRole extends Model
{
    protected $table = "role_users";

    public function role()
    {
        return $this->hasOne(EloquentRole::class, 'id', 'role_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $table = "group_users";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}

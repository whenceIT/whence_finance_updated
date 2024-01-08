<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientUser extends Model
{
    protected $table = "client_users";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
    public function client()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

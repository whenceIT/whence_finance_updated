<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupClient extends Model
{
    protected $table = "group_clients";

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

}

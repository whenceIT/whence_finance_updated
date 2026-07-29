<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAppUsers extends Model
{
    protected $table = "clientapp_users";
     public $timestamps = false;

    public function client()
{
    return $this->belongsTo(Client::class, 'client_id');
}


}

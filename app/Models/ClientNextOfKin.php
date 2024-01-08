<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientNextOfKin extends Model
{
    protected $table = "client_next_of_kin";

    public function relationship()
    {
        return $this->hasOne(ClientRelationship::class, 'id', 'client_relationship_id');
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientIdentification extends Model
{
    protected $table = "client_identifications";

    public function type()
    {
        return $this->hasOne(ClientIdentificationType::class, 'id', 'client_identification_type_id');
    }
}

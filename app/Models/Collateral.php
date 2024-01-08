<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    protected $table = "collateral";

    public function type()
    {
        return $this->hasOne(CollateralType::class, 'id', 'collateral_type_id');
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blockage extends Model
{
    use HasFactory;

    protected $table = 'blockages';

    protected $fillable = [
        'office_id'
    ];
}

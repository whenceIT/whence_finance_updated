<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashParameter extends Model
{
    use HasFactory;

    protected $table = 'cash_module_parameters';

    public $timestamps = false;

    protected $fillable = [
        'parameter_name',
        'value',
        'type',
    ];
}
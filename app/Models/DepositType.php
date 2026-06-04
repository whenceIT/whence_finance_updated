<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositType extends Model
{
    use HasFactory;

    protected $table = 'deposit_types';

    protected $fillable = [
        'name',
        'bank',
        'gl_account',
        'sort_order',
    ];

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'deposit_type', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'gl_account_code',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    const TYPE_ADMINISTRATION = 'administration';
    const TYPE_BANK_ACCOUNT = 'bank_account';

    public function administrationExpenses()
    {
        return $this->hasMany(AdministrationExpense::class, 'category_id');
    }

    public function bankAccountExpenses()
    {
        return $this->hasMany(BankAccountExpense::class, 'category_id');
    }
}
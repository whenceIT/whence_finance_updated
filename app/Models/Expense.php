<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = "expenses";

    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id', 'id');
    }
    protected $fillable = [
        'proof_of_payment'
    ];
    
}

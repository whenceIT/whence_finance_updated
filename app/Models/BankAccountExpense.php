<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccountExpense extends Model
{
    use HasFactory;

    protected $table = 'bank_account_expenses';

    protected $fillable = [
        'bank_account_id',
        'category_id',
        'reference_number',
        'comments',
        'amount',
        'gl_account_code',
        'transaction_date',
        'entered_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(Office::class, 'bank_account_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function getCategoryNameAttribute()
    {
        return $this->category?->name;
    }

    public function getEnteredByNameAttribute()
    {
        return $this->user?->first_name . ' ' . $this->user?->last_name;
    }
}
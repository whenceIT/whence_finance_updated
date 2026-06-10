<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrationExpense extends Model
{
    use HasFactory;

    protected $table = 'administration_expenses';

    protected $fillable = [
        'category_id',
        'reference_number',
        'comments',
        'amount',
        'gl_account_code',
        'expense_date',
        'entered_by',
        'bank_charge_type',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

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
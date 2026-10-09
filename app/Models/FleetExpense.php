<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetExpense extends Model
{
    // Allowed expense categories
    const CATEGORIES = [
        'Maintenance',
        'Repairs',
        'Accidents',
        'Tyres',
        'Insurance',
        'Spare Parts',
        'Other',
    ];

    protected $fillable = [
        'fleet_id',
        'expense_date',
        'category',
        'description',
        'amount',
        'reference_number',
        'document_path',
        'recorded_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }
}

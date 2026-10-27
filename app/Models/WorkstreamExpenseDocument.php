<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkstreamExpenseDocument extends Model
{
    protected $table = 'ws1_expense_documents';

    protected $fillable = [
        'expense_id',
        'uploaded_by_id',
        'original_name',
        'storage_path',
        'mime_type',
        'file_size',
    ];

    public function expense()
    {
        return $this->belongsTo(WorkstreamExpense::class, 'expense_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}

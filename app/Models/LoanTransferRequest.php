<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LoanTransferRequest extends Model
{
    protected $table = 'loan_transfer_requests';

    public $timestamps = false;

    public function newLoanConsultant()
    {
        return $this->belongsTo(User::class,'new_consultant_id');
    }

    public function oldLoanConsultant()
    {
        return $this->belongsTo(User::class,'old_consultant_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class,'done_by');
    }
}


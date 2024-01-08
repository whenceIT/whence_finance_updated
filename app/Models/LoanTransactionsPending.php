<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanTransactionsPending extends Model
{
    protected $table = "loan_transactions_requests";


    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id');
    }


    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function journal_entries()
    {
        return $this->hasMany(GlJournalEntry::class, 'loan_transaction_id', 'id');
    }
}

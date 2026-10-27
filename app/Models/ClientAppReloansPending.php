<?php

namespace App\Models;

use Barryvdh\Debugbar\DataCollector\ModelsCollector;
use Illuminate\Database\Eloquent\Model;

class ClientAppReloansPending extends Model
{
    protected $table = "pending_client_app_transactions";

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
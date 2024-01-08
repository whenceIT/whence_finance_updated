<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavingsTransaction extends Model
{
    protected $table = "savings_transactions";


    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id');
    }

    public function savings()
    {
        return $this->hasOne(Savings::class, 'id', 'savings_id');
    }
    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
    public function journal_entries()
    {
        return $this->hasMany(GlJournalEntry::class, 'savings_transaction_id', 'id');
    }

}

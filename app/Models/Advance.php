<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdvanceStatus {
    const Pending = 'pending';
    const Approved = 'approved';
    const Closed = 'closed';
}

class Advance extends Model
{
    protected $fillable = ['user_id', 'amount', 'installments', 'date_approved', 'expected_repayment_dates', 'remaining_amount', 'status']; 

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
    public function installments()
    {
        return $this->attributes['installments'];
    }

    public function isProcessedForToday()
    {
        return $this->last_update_date && Carbon::parse($this->last_update_date)->isToday();
    }    

    public function markAsProcessedForToday()
    {
        $this->last_update_date = Carbon::today();
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($advance) {
            $advance->status = 'pending';

        });
    }
    
    
    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceScreening extends Model
{
    protected $fillable = [
        'client_id',
        'motor_vehicle_loan_id',
        'pep_result',
        'sanctions_result',
        'screening_date',
        'screening_officer_id',
        'match_level',
        'comments',
        'supporting_evidence',
        'status'
    ];

    protected $casts = [
        'screening_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'motor_vehicle_loan_id');
    }

    public function screeningOfficer()
    {
        return $this->belongsTo(User::class, 'screening_officer_id');
    }
}

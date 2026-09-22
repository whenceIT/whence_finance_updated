<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $table = 'vacancies';

    /**
     * Recruitment workflow stages.
     */
    const RECRUITMENT_STATUSES = [
        'Open',
        'Advertising',
        'Shortlisting',
        'Interviewing',
        'Offer Made',
        'Filled',
        'Cancelled',
    ];

    /**
     * Interview stages.
     */
    const INTERVIEW_STATUSES = [
        'Not Started',
        'Scheduled',
        'In Progress',
        'Completed',
        'Cancelled',
    ];

    /**
     * Offer stages.
     */
    const OFFER_STATUSES = [
        'Not Made',
        'Pending',
        'Accepted',
        'Declined',
        'Withdrawn',
    ];

    protected $fillable = [
        'office_id',
        'position_id',
        'num_of_vacancies',
        'status',
        'notes',
        'date_arose',
        'reason',
        'recruitment_status',
        'num_of_applicants',
        'num_of_shortlisted',
        'interview_status',
        'selected_candidate',
        'offer_status',
        'expected_reporting_date',
        'actual_reporting_date',
    ];

    protected $casts = [
        'office_id' => 'integer',
        'position_id' => 'integer',
        'num_of_vacancies' => 'integer',
        'num_of_applicants' => 'integer',
        'num_of_shortlisted' => 'integer',
        'date_arose' => 'date',
        'expected_reporting_date' => 'date',
        'actual_reporting_date' => 'date',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Days the vacancy has remained open — from the day it arose up to the day
     * it was filled, or up to today when it is still open.
     *
     * @return int|null
     */
    public function getDaysVacantAttribute()
    {
        if (!$this->date_arose) {
            return null;
        }

        $start = $this->date_arose->copy()->startOfDay();
        $end = $this->actual_reporting_date
            ? $this->actual_reporting_date->copy()->startOfDay()
            : Carbon::now()->startOfDay();

        return (int) $start->diffInDays($end);
    }

    /**
     * @return bool
     */
    public function getIsFilledAttribute()
    {
        return $this->recruitment_status === 'Filled' || $this->actual_reporting_date !== null;
    }

    /**
     * @return bool
     */
    public function getIsOpenAttribute()
    {
        return !$this->is_filled && $this->recruitment_status !== 'Cancelled';
    }
}

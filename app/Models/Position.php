<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    protected $table = 'job_positions';

    protected $fillable = [
        'name',
        'status',
        'job_description',
        'date_added',
        'is_vacant',
        'num_of_vacancies',
        'num_of_active',
        'department_id',
        'posted_date'
    ];

    protected $casts = [
        'is_vacant' => 'boolean',
        'num_of_vacancies' => 'integer',
        'num_of_active' => 'integer',
        'department_id' => 'integer',
        'posted_date' => 'date',
        'date_added' => 'date',
        'deleted_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function uploads()
    {
        return $this->belongsToMany(GeneralUpload::class, 'general_upload_position')
                    ->withPivot(['created_at', 'updated_at'])
                    ->withTimestamps();
    }
}
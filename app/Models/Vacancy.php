<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $table = 'vacancies';

    protected $fillable = [
        'office_id',
        'position_id',
        'num_of_vacancies',
        'status',
        'notes',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}

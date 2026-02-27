<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLogs extends Model
{
    protected $table = "audit_log";

     protected $fillable = [
        'module',
        'action',
        'done_by',
        'details',
        'date'
    ];
      public $timestamps = false;

     public function user()
    {
        return $this->hasOne(User::class, 'id', 'done_by');
    }

    public function module()
    {
        return $this->hasOne(AuditModules::class, 'id', 'module');
    }

}
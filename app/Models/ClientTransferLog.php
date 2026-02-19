<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTransferLog extends Model
{
   protected $table = 'client_transfer_log';
     protected $fillable = [
        'client_id',
        'old_loan_officer_id',
        'new_loan_officer_id',
        'transferred_by',
        'transferred_at'
    ];
   public $timestamps = false;
}


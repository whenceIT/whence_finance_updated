<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTransferRequest extends Model
{
    protected $table = 'client_transfer_requests';

    public $timestamps = false;

    public function newOffice()
    {
        return $this->belongsTo(Office::class, 'new_office_id');
    }

    public function oldOffice()
    {
        return $this->belongsTo(Office::class, 'old_office_id');
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
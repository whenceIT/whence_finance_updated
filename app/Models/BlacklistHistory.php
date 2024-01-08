<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistHistory extends Model
{
    protected $table = "blacklist_history";

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function reason()
    {
        return $this->belongsTo(BlacklistReason::class, 'blacklist_reason_id');
    }
}

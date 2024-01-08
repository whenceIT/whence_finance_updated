<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Savings extends Model
{
    protected $table = "savings";

    public function charges()
    {
        return $this->hasMany(SavingsCharge::class, 'savings_id', 'id');
    }


    public function saving_transactions()
    {
        return $this->hasMany(SavingsTransaction::class, 'savings_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function savings_product()
    {
        return $this->hasOne(SavingsProduct::class, 'id', 'savings_product_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }


    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }


    public function field_officer()
    {
        return $this->hasOne(User::class, 'id', 'field_officer_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    protected $table = "expense_types";

    public function gl_account_asset()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_asset_id');
    }

    public function gl_account_expense()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_expense_id');
    }

    public function gl_account()
    {
        return $this->hasOne(GlAccount::class, 'id', 'gl_account_id');
    }

    public function gl_accounts()
    {
        return $this->belongsToMany(GlAccount::class, 'expense_type_gl_account', 'expense_type_id', 'gl_account_id');
    }
}

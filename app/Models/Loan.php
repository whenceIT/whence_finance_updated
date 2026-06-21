<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    use SoftDeletes;
    protected $table = "loans";

    protected $fillable = [
        'dormant_recovery',
        'unit_share_count',
        // ... other fillable fields
    ];

    protected $casts = [
        'dormant_recovery' => 'integer',
        'unit_share_count' => 'integer',
    ];

    public function charges()
    {
        return $this->hasMany(LoanCharge::class, 'loan_id', 'id');
    }

    public function repayment_schedules()
    {
        return $this->hasMany(LoanRepaymentSchedule::class, 'loan_id', 'id')->orderBy('due_date', 'asc');
    }

    public function group_allocation()
    {
        return $this->hasMany(GroupLoanAllocation::class, 'loan_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(LoanTransaction::class, 'loan_id', 'id')->orderBy('date', 'asc');;
    }

    public function payments()
    {
        return $this->hasMany(LoanRepayment::class, 'loan_id', 'id')->orderBy('collection_date', 'asc');;
    }

    public function collateral()
    {
        return $this->hasMany(Collateral::class, 'loan_id', 'id');
    }

    public function guarantors()
    {
        return $this->hasMany(Guarantor::class, 'loan_id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function fund()
    {
        return $this->hasOne(Fund::class, 'id', 'fund_id');
    }

    public function loan_purpose()
    {
        return $this->hasOne(LoanPurpose::class, 'id', 'loan_purpose_id');
    }

    public function loan_product()
    {
        return $this->hasOne(LoanProduct::class, 'id', 'loan_product_id');
    }

    public function office()
    {
        return $this->hasOne(Office::class, 'id', 'office_id');
    }


    public function loan_officer()
    {
        return $this->hasOne(User::class, 'id', 'loan_officer_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by_id');
    }


    public function vetted_by_field(){
        return $this->hasOne(User::class, 'id', 'vetted_by');
    }

    public function verified_by_field(){
        return $this->hasOne(User::class, 'id','verified_by');
    }

    public function rejected_by()
    {
        return $this->hasOne(User::class, 'id', 'rejected_by_id');
    }

    public function declined_by()
    {
        return $this->hasOne(User::class, 'id', 'declined_by_id');
    }
    public function withdrawn_by()
    {
        return $this->hasOne(User::class, 'id', 'withdrawn_by_id');
    }

    public function rescheduled_by()
    {
        return $this->hasOne(User::class, 'id', 'rescheduled_by_id');
    }

    public function closed_by()
    {
        return $this->hasOne(User::class, 'id', 'closed_by_id');
    }

    public function disbursed_by()
    {
        return $this->hasOne(User::class, 'id', 'disbursed_by_id');
    }

    public function motorVehicleLoan()
    {
        return $this->hasOne(MotorVehicleLoan::class);
    }

    public function repossessions()
    {
        return $this->hasMany(VehicleRepossession::class);
    }

    /**
     * Calculate the current balance for this loan based on transactions.
     * Returns an array with balance and days_in_arrears status.
     *
     * @return array
     */
    public function calculateBalance()
    {
        $debit_amount = 0;
        $credit_amount = 0;
        $days_in_arrears = null;

        foreach ($this->transactions as $transaction) {
            $debit_amount += $transaction->debit;
            $credit_amount += $transaction->credit;

            if ($transaction->payment_apply_to == 'reloan_payment') {
                $days_in_arrears = 0;
            }
        }

        $new_balance = $debit_amount - $credit_amount;

        return [
            'balance' => $new_balance,
            'debit_amount' => $debit_amount,
            'credit_amount' => $credit_amount,
            'days_in_arrears' => $days_in_arrears
        ];
    }

    public function overdue_loans($office_id = null)
    {
        $office_condition = $office_id ? "l.office_id = ?" : "1=1";
        $params = $office_id ? [$office_id] : [];

        $sql = "SELECT

            l.id,
            l.account_number,
            l.client_id,
            l.loan_officer_id,
            l.principal,
            l.approved_amount,
            l.disbursement_date,
            l.first_repayment_date,
            l.status,

            CONCAT(c.first_name, ' ', c.last_name) AS client_name,
            c.phone AS 'Client_Phone',

            CONCAT(u_lo.first_name, ' ', u_lo.last_name) AS loan_officer_name,

            CONCAT(u_created.first_name, ' ', u_created.last_name) AS created_by,
            CONCAT(u_approved.first_name, ' ', u_approved.last_name) AS approved_by,
            CONCAT(u_disbursed.first_name, ' ', u_disbursed.last_name) AS disbursed_by

        FROM loans l

        LEFT JOIN clients c
            ON c.id = l.client_id

        LEFT JOIN users u_lo
            ON u_lo.id = l.loan_officer_id

        LEFT JOIN users u_created
            ON u_created.id = l.created_by_id

        LEFT JOIN users u_approved
            ON u_approved.id = l.approved_by_id

        LEFT JOIN users u_disbursed
            ON u_disbursed.id = l.disbursed_by_id

        WHERE
            {$office_condition}
            AND l.status = 'disbursed'
            AND l.first_repayment_date IS NOT NULL
            AND l.first_repayment_date < CURRENT_DATE
            AND l.created_at >= '2025-01-01'

        ORDER BY
            l.first_repayment_date ASC";

        return DB::select($sql, $params);
    }

    public function loan_remainder($officer_id = null)
    {
        $officer_condition = $officer_id ? "l.loan_officer_id = ?" : "1=1";
        $params = [];

        if ($office_id) {
            $params[] = $office_id;
        }

        if ($officer_id) {
            $params[] = $officer_id;
        }

        $sql = "SELECT

            l.id,
            l.account_number,
            l.client_id,
            l.loan_officer_id,
            l.principal,
            l.approved_amount,
            l.disbursement_date,
            l.first_repayment_date,
            l.status,

            CONCAT(c.first_name, ' ', c.last_name) AS client_name,
            c.phone AS 'Client_Phone',

            CONCAT(u_lo.first_name, ' ', u_lo.last_name) AS loan_officer_name,

            CONCAT(u_created.first_name, ' ', u_created.last_name) AS created_by,
            CONCAT(u_approved.first_name, ' ', u_approved.last_name) AS approved_by,
            CONCAT(u_disbursed.first_name, ' ', u_disbursed.last_name) AS disbursed_by

        FROM loans l

        LEFT JOIN clients c
            ON c.id = l.client_id

        LEFT JOIN users u_lo
            ON u_lo.id = l.loan_officer_id

        LEFT JOIN users u_created
            ON u_created.id = l.created_by_id

        LEFT JOIN users u_approved
            ON u_approved.id = l.approved_by_id

        LEFT JOIN users u_disbursed
            ON u_disbursed.id = l.disbursed_by_id

        WHERE
            {$officer_condition}
            AND l.status = 'disbursed'
            AND l.first_repayment_date IS NOT NULL

        ORDER BY
            l.first_repayment_date ASC";

        return DB::select($sql, $params);
    }

}

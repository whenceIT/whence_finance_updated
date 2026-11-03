<?php

/**
 * Created by PhpStorm.
 * User: Tj
 * Date: 6/29/2016
 * Time: 3:11 PM
 */

namespace App\Helpers;

use App\Models\Asset;
use App\Models\AssetValuation;
use App\Models\AuditTrail;
use App\Models\Client;
use App\Models\Expense;
use App\Models\GlAccount;
use App\Models\GlJournalEntry;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\User;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\OtherIncome;
use App\Models\Payroll;
use App\Models\PayrollMeta;
use App\Models\Product;
use App\Models\Office;
use App\Models\ProductCheckin;
use App\Models\ProductCheckinItem;
use App\Models\ProductCheckout;
use App\Models\ProductCheckoutItem;
use App\Models\Savings;
use App\Models\SavingsProduct;
use App\Models\SavingsTransaction;
use App\Models\CycleDates;
use App\Models\Setting;
use App\Models\SmsGateway;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeneralHelper
{
    //get active theme
    public static function get_active_theme_directory($sep = '.')
    {
        return 'themes' . $sep . Setting::where('setting_key', 'active_theme')->first()->setting_value;
    }

    public static function loan_product($loan_product_id)
    {
        if (empty($loan_product_id)) {
            return null;
        }
        return LoanProduct::find($loan_product_id);
    }

    public static function payroll_loan_schedule($loan_amount, $tenure_months)
    {
        if (empty($loan_amount) || empty($tenure_months)) {
            return null;
        }
        $column = 'months_' . $tenure_months;
        $schedule = DB::table('payroll_loan_schedules')
            ->where('loan_amount', $loan_amount)
            ->first();
        if (!$schedule) {
            return null;
        }
        if (!property_exists($schedule, $column)) {
            return null;
        }
        return $schedule->$column;
    }

    /*
     * determine interest
     */
    public static function determine_interest_rate($id)
    {
        $loan = Loan::find($id);
        if ($loan->override_interest == 1) {
            return $loan->override_interest_rate / 100;
        }
        $interest = '';
        if ($loan->repayment_frequency_type == 'days') {
            //return the interest per year
            if ($loan->interest_rate_type == 'month') {
                $interest = $loan->interest_rate / 30;
            }
            if ($loan->interest_rate_type == 'year') {
                $interest = $loan->interest_rate / 365;
            }

        }
        if ($loan->repayment_frequency_type == 'weeks') {
            //return the interest per semi annually
            if ($loan->interest_rate_type == 'month') {
                $interest = $loan->interest_rate / 4;
            }
            if ($loan->interest_rate_type == 'year') {
                $interest = $loan->interest_rate / 52;
            }
        }
        if ($loan->repayment_frequency_type == 'months') {
            //return the interest per quaterly

            if ($loan->interest_rate_type == 'month') {
                $interest = $loan->interest_rate;
            }
            if ($loan->interest_rate_type == 'year') {
                $interest = $loan->interest_rate / 12;
            }
        }
        if ($loan->repayment_frequency_type == 'years') {
            //return the interest per bi-monthly
            if ($loan->interest_rate_type == 'month') {
                $interest = $loan->interest_rate * 12;
            }
            if ($loan->interest_rate_type == 'year') {
                $interest = $loan->interest_rate;
            }

        }
        return $interest * $loan->repayment_frequency / 100;
    }

    //determine monthly payment using amortization
    public static function amortized_payment($id, $balance, $period = "")
    {
        $loan = Loan::find($id);
        if (empty($period)) {
            $period = $loan->loan_term / $loan->repayment_frequency;
        }
        $interest_rate = GeneralHelper::determine_interest_rate($id);
        //calculate here
        $amount = ($interest_rate * $balance * pow((1 + $interest_rate), $period)) / (pow(
            (1 + $interest_rate),
            $period
        ) - 1);
        return $amount;
    }

    public static function loan_period($id)
    {
        $loan = Loan::find($id);
        $period = 0;
        if ($loan->repayment_cycle == 'annually') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 12);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 52);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 365);
            }
        }
        if ($loan->repayment_cycle == 'semi_annually') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 2);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 6);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 26);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 182.5);
            }
        }
        if ($loan->repayment_cycle == 'quarterly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 12);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 52);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 365);
            }
        }
        if ($loan->repayment_cycle == 'bi_monthly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 6);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration / 2);

            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 8);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 60);
            }
        }

        if ($loan->repayment_cycle == 'monthly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 12);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 4.3);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 30.4);
            }
        }
        if ($loan->repayment_cycle == 'weekly') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 52);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 4);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 1);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration * 7);
            }
        }
        if ($loan->repayment_cycle == 'daily') {
            if ($loan->loan_duration_type == 'year') {
                $period = ceil($loan->loan_duration * 365);
            }
            if ($loan->loan_duration_type == 'month') {
                $period = ceil($loan->loan_duration * 30.42);
            }
            if ($loan->loan_duration_type == 'week') {
                $period = ceil($loan->loan_duration * 7.02);
            }
            if ($loan->loan_duration_type == 'day') {
                $period = ceil($loan->loan_duration);
            }
        }
        return $period;
    }

    public static function time_ago($eventTime)
    {
        $totaldelay = time() - strtotime($eventTime);
        if ($totaldelay <= 0) {
            return '';
        } else {
            if ($days = floor($totaldelay / 86400)) {
                $totaldelay = $totaldelay % 86400;
                return $days . ' days ago';
            }
            if ($hours = floor($totaldelay / 3600)) {
                $totaldelay = $totaldelay % 3600;
                return $hours . ' hours ago';
            }
            if ($minutes = floor($totaldelay / 60)) {
                $totaldelay = $totaldelay % 60;
                return $minutes . ' minutes ago';
            }
            if ($seconds = floor($totaldelay / 1)) {
                $totaldelay = $totaldelay % 1;
                return $seconds . ' seconds ago';
            }
        }
    }

    public static function determine_due_date($id, $date)
    {
        $schedule = LoanRepaymentSchedule::where('due_date', ' >=', $date)->where('loan_id', $id)->orderBy(
            'due_date',
            'asc'
        )->first();
        if (!empty($schedule)) {
            return $schedule->due_date;
        } else {
            $schedule = LoanRepaymentSchedule::where(
                'loan_id',
                $id
            )->orderBy(
                    'due_date',
                    'desc'
                )->first();
            if ($date > $schedule->due_date) {
                return $schedule->due_date;
            } else {
                $schedule = LoanRepaymentSchedule::where('due_date', '>', $date)->where(
                    'loan_id',
                    $id
                )->orderBy(
                        'due_date',
                        'asc'
                    )->first();
                if (!empty($schedule->due_date)) {
                    return $schedule->due_date;
                }
            }

        }
    }

    public static function loan_total_interest($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('interest');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('interest');
        }
    }

    public static function loan_total_interest_waived($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('interest_waived');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('interest_waived');
        }
    }

    public static function loan_total_principal($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('principal');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('principal');
        }
    }

    public static function loan_total_fees($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('fees');
        } else {
            return LoanSchedule::where('loan_id', $id)->where(
                'due_date',
                '<=',
                $date
            )->sum('fees');
        }
    }

    public static function loan_total_penalty($id, $date = '')
    {
        if (empty($date)) {
            return LoanSchedule::where('loan_id', $id)->sum('penalty');
        } else {
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=', $date)->sum('penalty');
        }
    }

    public static function loan_total_paid($id, $date = '')
    {
        if (empty($date)) {
            return LoanTransaction::where('loan_id', $id)->where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->sum('credit');
        } else {
            return LoanTransaction::where('loan_id', $id)->where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->where('due_date', '<=', $date)->sum('credit');
        }

    }
    public static function new_loan_total_balance($id, $date = '')
    {
        if (empty($date)) {
            $loan = Loan::find($id);

            if (empty($loan)) {
                return 0;
            }

            // Aggregate through the existing relationship so relationship constraints
            // and LoanTransaction model scopes are preserved without loading every row.
            $totals = $loan->transactions()
                ->selectRaw('COALESCE(SUM(debit), 0) AS debit, COALESCE(SUM(credit), 0) AS credit')
                ->first();

            return $totals->debit - $totals->credit;
        }
    }


    public static function loan_balances_bulk(array $loanIds)
    {
        if (empty($loanIds)) {
            return collect();
        }

        return DB::table('loan_transactions')
            ->whereIn('loan_id', $loanIds)
            ->groupBy('loan_id')
            ->selectRaw('loan_id, COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS balance')
            ->pluck('balance', 'loan_id'); // [loan_id => balance]
    }


public static function new_new_loan_total_balance($id)
{
    return DB::table('loan_transactions')
        ->where('loan_id', $id)
        ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as balance')
        ->value('balance');
}




    public static function loan_total_balance($id, $date = '')
    {
        if (empty($date)) {
            $items = GeneralHelper::loan_items($id);
            return ($items['principal'] - $items['principal_paid'] - $items['principal_waived'] - $items['principal_written_off'])
                + ($items['interest'] - $items['interest_paid'] - $items['interest_waived'] - $items['interest_written_off'])
                + ($items['fees'] - $items['fees_paid'] - $items['fees_waived'] - $items['fees_written_off'])
                + ($items['penalty'] - $items['penalty_paid'] - $items['penalty_waived'] - $items['penalty_written_off']);
        }
    }

    public static function loan_arrears($id, $date)
    {
        $allocation = [];
        $amount_in_arrears = 0;
        $timely_repayments = 0;
        $total_repayments = 0;
        $days_in_arrears = 0;
        foreach (LoanRepaymentSchedule::where('loan_id', $id)->where('due_date', '<', $date)->orderBy('due_date', 'asc')->get() as $schedule) {
            $total_repayments = $total_repayments + 1;
            $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
            if (!empty($schedule->from_date)) {
                if (strtotime($schedule->due_date) > strtotime($schedule->from_date)) {
                    $timely_repayments = $timely_repayments + 1;
                }
            }
        }
        if ($amount_in_arrears > 0) {
            $date1 = new \DateTime(LoanRepaymentSchedule::where('loan_id', $id)->where('due_date', '<', date("Y-m-d"))->orderBy('due_date', 'desc')->first()->due_date);
            $date2 = new \DateTime($date);
            $days_in_arrears = $date2->diff($date1)->format("%a");
        }
        if ($total_repayments > 0) {
            $percentage = $timely_repayments * 100 / $total_repayments;
        } else {
            $percentage = 0;
        }
        $allocation["amount"] = $amount_in_arrears;
        $allocation["days"] = $days_in_arrears;
        $allocation["percentage"] = $percentage;
        return $allocation;

    }
    public static function loan_total_due_amount($id, $date = '')
    {
        // These five values came from five SUM queries. Calculate the same
        // formula in one aggregate query, retaining the original date filter.
        $query = LoanSchedule::where('loan_id', $id);

        if (!empty($date)) {
            $query->where('due_date', '<=', $date);
        }

        return $query->selectRaw('(
            COALESCE(SUM(penalty), 0) +
            COALESCE(SUM(fees), 0) +
            COALESCE(SUM(interest), 0) +
            COALESCE(SUM(principal), 0) -
            COALESCE(SUM(interest_waived), 0)
        ) AS total')->value('total') + 0;
    }

    public static function loan_total_due_period($id, $date)
    {
        // One aggregate query replaces five separate SUM queries.
        // Keep the existing formula exactly, including adding interest_waived.
        return LoanSchedule::where('loan_id', $id)
            ->where('due_date', $date)
            ->selectRaw('(
                COALESCE(SUM(penalty), 0) +
                COALESCE(SUM(fees), 0) +
                COALESCE(SUM(principal), 0) +
                COALESCE(SUM(interest), 0) +
                COALESCE(SUM(interest_waived), 0)
            ) AS total')
            ->value('total') + 0;
    }


    public static function loan_total_paid_period($id, $date)
    {
        return LoanRepayment::where('loan_id', $id)->where('due_date', $date)->sum('amount');

    }
    public static function loans_total_paid($start_date = '', $end_date = '')
    {
        // Preserve the original loan filters (including release_date filtering),
        // but aggregate repayments for all matching loans in one query.
        $loansQuery = Loan::whereIn('status', ['disbursed', 'closed', 'written_off']);

        if (!empty($start_date)) {
            $loansQuery->whereBetween('release_date', [$start_date, $end_date]);
        }

        $paymentsByLoan = LoanTransaction::selectRaw('loan_id, COALESCE(SUM(credit), 0) AS paid')
            ->whereIn('loan_id', $loansQuery->select('id'))
            ->where('transaction_type', 'repayment')
            ->where('reversed', 0)
            ->groupBy('loan_id')
            ->get();

        $paid = 0;
        foreach ($paymentsByLoan as $payment) {
            $paid += $payment->paid;
        }

        return $paid;
    }


    public static function diff_in_months(\DateTime $date1, \DateTime $date2)
    {
        $diff = $date1->diff($date2);

        $months = $diff->y * 12 + $diff->m + $diff->d / 30;

        return (int) round($months);
    }

    public static function addMonths($date, $months)
    {
        $orig_day = $date->format("d");
        $date->modify("+" . $months . " months");
        while ($date->format("d") < $orig_day && $date->format("d") < 5) {
            $date->modify("-1 day");
        }
    }


    public static function single_payroll_total_pay($id)
    {
        return PayrollMeta::where('payroll_id', $id)->where('position', 'bottom_left')->sum('value');
    }

    public static function single_payroll_total_deductions($id)
    {
        return PayrollMeta::where('payroll_id', $id)->where('position', 'bottom_right')->sum('value');
    }

    public static function single_payroll_pay($id)
    {
        return GeneralHelper::single_payroll_total_pay($id) - GeneralHelper::single_payroll_total_deductions($id);
    }


    public static function total_expenses($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return Expense::where('branch_id', session('branch_id'))->sum('amount');
        } else {
            return Expense::where('branch_id', session('branch_id'))->whereBetween(
                'date',
                [$start_date, $end_date]
            )->sum('amount');

        }

    }
    public static function total_payroll($start_date = '', $end_date = '')
    {
        // Replace one PayrollMeta SUM per payroll with a single SUM over the
        // same branch/date-filtered payroll set.
        $payrollQuery = Payroll::where('branch_id', session('branch_id'));

        if (!empty($start_date)) {
            $payrollQuery->whereBetween('date', [$start_date, $end_date]);
        }

        return PayrollMeta::whereIn('payroll_id', $payrollQuery->select('id'))
            ->where('position', 'bottom_left')
            ->sum('value') + 0;
    }
    public static function loans_total_principal($start_date = '', $end_date = '')
    {
        $loansQuery = Loan::where('branch_id', session('branch_id'))
            ->whereIn('status', ['disbursed', 'closed', 'written_off']);

        if (empty($start_date)) {
            // Keep the original calculation based on schedule principal, but
            // retrieve grouped totals in one query instead of one query per loan.
            $principalByLoan = LoanSchedule::selectRaw('loan_id, COALESCE(SUM(principal), 0) AS principal')
                ->whereIn('loan_id', $loansQuery->select('id'))
                ->groupBy('loan_id')
                ->get();

            $principal = 0;
            foreach ($principalByLoan as $row) {
                $principal += $row->principal;
            }

            return $principal;
        }

        // The original date-filtered branch sums loans.principal, not schedule principal.
        return $loansQuery->whereBetween('release_date', [$start_date, $end_date])
            ->sum('principal') + 0;
    }



    public static function total_other_income($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return OtherIncome::where('branch_id', session('branch_id'))->sum('amount');
        } else {
            return OtherIncome::where('branch_id', session('branch_id'))->whereBetween(
                'date',
                [$start_date, $end_date]
            )->sum('amount');

        }

    }

    public static function total_savings_interest($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where(
                'type',
                'interest'
            )->where('reversed', 0)->sum('debit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where(
                'type',
                'interest'
            )->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->sum('debit');

        }

    }

    public static function total_savings_deposits($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type', 'deposit')->where('reversed', 0)->sum('credit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where(
                'type',
                'deposit'
            )->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->sum('credit');

        }

    }
    public static function total_savings_transactions($id, $start_date = '', $end_date = '')
    {
        $query = SavingTransaction::where('savings_id', $id)->where('reversed', 0);

        if (!empty($start_date)) {
            $query->whereBetween('date', [$start_date, $end_date]);
        }

        // Preserve the original type-to-field mapping, including the existing
        // withdrawals calculation using debit rows where type = "interest".
        $totals = $query->selectRaw("            COALESCE(SUM(CASE WHEN type = 'interest' THEN credit ELSE 0 END), 0) AS interest,
            COALESCE(SUM(CASE WHEN type = 'deposit' THEN credit ELSE 0 END), 0) AS deposits,
            COALESCE(SUM(CASE WHEN type = 'interest' THEN debit ELSE 0 END), 0) AS withdrawals,
            COALESCE(SUM(CASE WHEN type = 'bank_fees' THEN credit ELSE 0 END), 0) AS fees,
            COALESCE(SUM(CASE WHEN type = 'guarantee' THEN credit ELSE 0 END), 0) AS guarantee
        ")
            ->first();

        return [
            'interest' => $totals->interest,
            'deposits' => $totals->deposits,
            'withdrawals' => $totals->withdrawals,
            'fees' => $totals->fees,
            'guarantee' => $totals->guarantee,
        ];
    }


    public static function total_savings_withdrawals($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where(
                'type',
                'withdrawal'
            )->where('reversed', 0)->sum('credit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where(
                'type',
                'withdrawal'
            )->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->sum('credit');

        }

    }

    public static function total_capital($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return Capital::where('branch_id', session('branch_id'))->where(
                'type',
                'deposit'
            )->sum('amount') - Capital::where('branch_id', session('branch_id'))->where(
                    'type',
                    'withdrawal'
                )->sum('amount');
        } else {
            return Capital::where('branch_id', session('branch_id'))->where(
                'type',
                'deposit'
            )->sum('amount') - Capital::where('branch_id', session('branch_id'))->where(
                    'type',
                    'withdrawal'
                )->sum('amount');

        }

    }

    public static function loans_total_paid_item($item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->whereBetween(
                    'release_date',
                    [$start_date, $end_date]
                )->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_product_total_paid_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->whereBetween(
                    'release_date',
                    [$start_date, $end_date]
                )->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_borrower_total_paid_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->whereBetween(
                    'release_date',
                    [$start_date, $end_date]
                )->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }
    public static function loans_total_due_item($item, $start_date = '', $end_date = '')
    {
        $columns = ['principal', 'interest', 'fees', 'penalty'];
        if (!in_array($item, $columns, true)) {
            return 0;
        }

        $loansQuery = Loan::where('branch_id', session('branch_id'))
            ->whereIn('status', ['disbursed', 'closed', 'written_off']);

        if (!empty($start_date)) {
            $loansQuery->whereBetween('release_date', [$start_date, $end_date]);
        }

        // One grouped schedule query replaces one SUM query per loan.
        $amountsByLoan = LoanSchedule::selectRaw(
            'loan_id, COALESCE(SUM(' . $item . '), 0) AS amount'
        )
            ->whereIn('loan_id', $loansQuery->select('id'))
            ->groupBy('loan_id')
            ->get();

        $amount = 0;
        foreach ($amountsByLoan as $row) {
            $amount += $row->amount;
        }

        return $amount;
    }
    public static function loans_product_total_due_items($id, $start_date = '', $end_date = '')
    {
        $query = Loan::query()
            ->where('loans.loan_product_id', $id)
            ->where('loans.branch_id', session('branch_id'))
            ->whereIn('loans.status', ['disbursed', 'closed', 'written_off'])
            ->join('loan_schedules', 'loans.id', '=', 'loan_schedules.loan_id')
            ->whereNull('loan_schedules.deleted_at');

        if (!empty($start_date)) {
            $query->whereBetween('loan_schedules.due_date', [$start_date, $end_date]);
        }

        $totals = $query->selectRaw('
            COALESCE(SUM(loan_schedules.interest), 0) AS interest,
            COALESCE(SUM(loan_schedules.principal), 0) AS principal,
            COALESCE(SUM(loan_schedules.penalty), 0) AS penalty,
            COALESCE(SUM(loan_schedules.fees), 0) AS fees
        ')->first();

        return [
            'interest' => $totals->interest,
            'principal' => $totals->principal,
            'penalty' => $totals->penalty,
            'fees' => $totals->fees,
        ];
    }
    public static function loans_product_total_paid_items($id, $start_date = '', $end_date = '')
    {
        $query = Loan::query()
            ->where('loans.loan_product_id', $id)
            ->where('loans.branch_id', session('branch_id'))
            ->whereIn('loans.status', ['disbursed', 'closed', 'written_off'])
            ->join('loan_repayments', 'loans.id', '=', 'loan_repayments.loan_id')
            ->whereNull('loan_repayments.deleted_at');

        if (!empty($start_date)) {
            $query->whereBetween('loan_repayments.collection_date', [$start_date, $end_date]);
        }

        $totals = $query->selectRaw('
            COALESCE(SUM(loan_repayments.interest), 0) AS interest,
            COALESCE(SUM(loan_repayments.principal), 0) AS principal,
            COALESCE(SUM(loan_repayments.penalty), 0) AS penalty,
            COALESCE(SUM(loan_repayments.fees), 0) AS fees
        ')->first();

        return [
            'interest' => $totals->interest,
            'principal' => $totals->principal,
            'penalty' => $totals->penalty,
            'fees' => $totals->fees,
        ];
    }

    public static function loans_borrower_total_due_item($id, $item, $start_date = '', $end_date = '')
    {
        $columns = ['principal', 'interest', 'fees', 'penalty'];
        if (!in_array($item, $columns, true)) {
            return 0;
        }

        $loansQuery = Loan::where('borrower_id', $id)
            ->where('branch_id', session('branch_id'))
            ->whereIn('status', ['disbursed', 'closed', 'written_off']);

        if (!empty($start_date)) {
            $loansQuery->whereBetween('release_date', [$start_date, $end_date]);
        }

        $amountsByLoan = LoanSchedule::selectRaw(
            'loan_id, COALESCE(SUM(' . $item . '), 0) AS amount'
        )
            ->whereIn('loan_id', $loansQuery->select('id'))
            ->groupBy('loan_id')
            ->get();

        $amount = 0;
        foreach ($amountsByLoan as $row) {
            $amount += $row->amount;
        }

        return $amount;
    }

    public static function loans_total_default($start_date = '', $end_date = '')
    {
        $loansQuery = Loan::where('branch_id', session('branch_id'))
            ->where('status', 'written_off');

        if (!empty($start_date)) {
            $loansQuery->whereBetween('release_date', [$start_date, $end_date]);
        }

        // Fetch the same written-off loans, then calculate their repayment totals
        // in one grouped query instead of calling loan_total_paid() per loan.
        $loans = $loansQuery->get(['id', 'principal']);
        if ($loans->isEmpty()) {
            return 0;
        }

        $paidByLoan = LoanTransaction::selectRaw('loan_id, COALESCE(SUM(credit), 0) AS paid')
            ->whereIn('loan_id', $loans->pluck('id'))
            ->where('transaction_type', 'repayment')
            ->where('reversed', 0)
            ->groupBy('loan_id')
            ->pluck('paid', 'loan_id');

        $principal = 0;
        foreach ($loans as $loan) {
            $principal += $loan->principal - ($paidByLoan[$loan->id] ?? 0);
        }

        return $principal;
    }
    public static function loans_total_due($start_date = '', $end_date = '')
    {
        $loansQuery = Loan::where('branch_id', session('branch_id'))
            ->whereIn('status', ['disbursed', 'closed', 'written_off']);

        if (!empty($start_date)) {
            $loansQuery->whereBetween('release_date', [$start_date, $end_date]);
        }

        // Match loan_total_due_amount() per loan while avoiding five SUM queries
        // for every loan. Grouping keeps the per-loan calculation intact.
        $dueByLoan = LoanSchedule::selectRaw('
            loan_id,
            COALESCE(SUM(penalty), 0) AS penalty,
            COALESCE(SUM(fees), 0) AS fees,
            COALESCE(SUM(interest), 0) AS interest,
            COALESCE(SUM(principal), 0) AS principal,
            COALESCE(SUM(interest_waived), 0) AS interest_waived
        ')
            ->whereIn('loan_id', $loansQuery->select('id'))
            ->groupBy('loan_id')
            ->get();

        $due = 0;
        foreach ($dueByLoan as $row) {
            $due += $row->penalty + $row->fees + $row->interest
                + $row->principal - $row->interest_waived;
        }

        return $due;
    }


    public static function loans_count($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $due = $due + Loan::where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->count();
            return $due;
        } else {
            $due = 0;
            $due = $due + Loan::where('branch_id', session('branch_id'))->whereIn(
                'status',
                ['disbursed', 'closed', 'written_off']
            )->whereBetween(
                    'release_date',
                    [$start_date, $end_date]
                )->count();
            return $due;

        }
    }

    public static function loans_product_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $due = $due + Loan::where('loan_product_id', $id)->where(
                'branch_id',
                session('branch_id')
            )->whereIn(
                    'status',
                    ['disbursed', 'closed', 'written_off']
                )->count();
            return $due;
        } else {
            $due = 0;
            $due = $due + Loan::where('loan_product_id', $id)->where(
                'branch_id',
                session('branch_id')
            )->whereIn(
                    'status',
                    ['disbursed', 'closed', 'written_off']
                )->whereBetween(
                    'release_date',
                    [$start_date, $end_date]
                )->count();
            return $due;

        }
    }

    public static function client_loans_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $client_ids = [];
            foreach (Sentinel::findUserById($id)->client_users as $key) {
                array_push($client_ids, $key->client_id);
            }
            $group_ids = [];
            foreach (Sentinel::findUserById($id)->group_users as $key) {
                array_push($group_ids, $key->group_id);
            }
            $due = Loan::where(function ($query) use ($client_ids, $group_ids) {
                $query->whereIn('client_id', $client_ids)
                    ->orWhereIn('group_id', $group_ids);
            })->count();
            return $due;
        } else {
            $due = 0;
            $client_ids = [];
            foreach (Sentinel::findUserById($id)->client_users as $key) {
                array_push($client_ids, $key->client_id);
            }
            $group_ids = [];
            foreach (Sentinel::findUserById($id)->group_users as $key) {
                array_push($group_ids, $key->group_id);
            }
            $due = Loan::where(function ($query) use ($client_ids, $group_ids) {
                $query->whereIn('client_id', $client_ids)
                    ->orWhereIn('group_id', $group_ids);
            })->whereBetween(
                    'created_at',
                    [$start_date, $end_date]
                )->count();
            return $due;

        }
    }
    public static function payments_product_count($id, $start_date = '', $end_date = '')
    {
        $loansQuery = Loan::where('loan_product_id', $id)
            ->where('branch_id', session('branch_id'))
            ->whereIn('status', ['disbursed', 'closed', 'written_off']);

        if (!empty($start_date)) {
            $loansQuery->whereBetween('release_date', [$start_date, $end_date]);
        }

        // Count matching repayment transactions across all matching loans at once.
        return LoanTransaction::whereIn('loan_id', $loansQuery->select('id'))
            ->where('transaction_type', 'repayment')
            ->where('reversed', 0)
            ->count();
    }
    public static function payments_borrower_count($id, $start_date = '', $end_date = '')
    {
        $loansQuery = Loan::where('borrower_id', $id)
            ->where('branch_id', session('branch_id'))
            ->whereIn('status', ['disbursed', 'closed', 'written_off']);

        if (!empty($start_date)) {
            $loansQuery->whereBetween('release_date', [$start_date, $end_date]);
        }

        return LoanTransaction::whereIn('loan_id', $loansQuery->select('id'))
            ->where('transaction_type', 'repayment')
            ->where('reversed', 0)
            ->count();
    }
    public static function borrower_loans_total_due($id)
    {
        $loansQuery = Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])
            ->where('borrower_id', $id);

        $dueByLoan = LoanSchedule::selectRaw('
            loan_id,
            COALESCE(SUM(penalty), 0) AS penalty,
            COALESCE(SUM(fees), 0) AS fees,
            COALESCE(SUM(interest), 0) AS interest,
            COALESCE(SUM(principal), 0) AS principal,
            COALESCE(SUM(interest_waived), 0) AS interest_waived
        ')
            ->whereIn('loan_id', $loansQuery->select('id'))
            ->groupBy('loan_id')
            ->get();

        $due = 0;
        foreach ($dueByLoan as $row) {
            $due += $row->penalty + $row->fees + $row->interest
                + $row->principal - $row->interest_waived;
        }

        return $due;
    }
    public static function borrower_loans_total_paid($id)
    {
        $loansQuery = Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])
            ->where('borrower_id', $id);

        $paymentsByLoan = LoanTransaction::selectRaw('loan_id, COALESCE(SUM(credit), 0) AS paid')
            ->whereIn('loan_id', $loansQuery->select('id'))
            ->where('transaction_type', 'repayment')
            ->where('reversed', 0)
            ->groupBy('loan_id')
            ->get();

        $paid = 0;
        foreach ($paymentsByLoan as $payment) {
            $paid += $payment->paid;
        }

        return $paid;
    }


    public static function audit_trail($action = "", $module = "", $notes = "")
    {
        $audit_trail = new AuditTrail();
        $audit_trail->user_id = Sentinel::getUser()->id;
        $audit_trail->office_id = Sentinel::getUser()->office_id;
        $audit_trail->name = Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name;
        $audit_trail->action = $action;
        $audit_trail->module = $module;
        $audit_trail->notes = $notes;
        $audit_trail->save();

    }

    public static function savings_account_balance($id, $end_date = "")
    {
        if (empty($end_date)) {
            $balance = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance'))->where('savings_id', $id)->where('reversed', 0)->first();

        } else {
            $balance = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance'))->where('savings_id', $id)->where('reversed', 0)->where('date', '<', $end_date)->first();
        }
        if (!empty($balance)) {
            return $balance->balance;
        } else {
            return 0;
        }

    }
    public static function client_savings_account_balance($id)
    {
        // Aggregate transactions across the client's savings accounts in one query.
        return SavingsTransaction::whereIn(
            'savings_id',
            Savings::where('client_id', $id)->select('id')
        )
            ->where('reversed', 0)
            ->selectRaw('COALESCE(SUM(credit), 0) - COALESCE(SUM(debit), 0) AS balance')
            ->value('balance') + 0;
    }
    public static function total_client_savings_account_balance($id)
    {
        $client_ids = [];
        foreach (Sentinel::findUserById($id)->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }

        $group_ids = [];
        foreach (Sentinel::findUserById($id)->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }

        // Preserve the same client/group account set, but calculate all balances
        // together instead of issuing one savings-transactions query per account.
        $savingsQuery = Savings::where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('client_id', $client_ids)
                ->orWhereIn('group_id', $group_ids);
        })->select('id');

        return SavingsTransaction::whereIn('savings_id', $savingsQuery)
            ->where('reversed', 0)
            ->selectRaw('COALESCE(SUM(credit), 0) - COALESCE(SUM(debit), 0) AS balance')
            ->value('balance') + 0;
    }

    public static function asset_valuation($id, $start_date = '')
    {
        $query = AssetValuation::where('asset_id', $id);

        if (!empty($start_date)) {
            $query->where('date', '<=', $start_date);
        }

        // Reuse the first result rather than executing the identical query twice.
        $valuation = $query->orderBy('date', 'desc')->first();

        return $valuation ? $valuation->amount : 0;
    }


    public static function asset_type_valuation($id, $start_date = '')
    {

        if (empty($start_date)) {
            $value = 0;
            foreach (Asset::where('asset_type_id', $id)->get() as $key) {
                if (!empty(AssetValuation::where('asset_id', $key->id)->orderBy('date', 'desc')->first())) {
                    $value = AssetValuation::where('asset_id', $key->id)->orderBy('date', 'desc')->first()->amount;
                }
            }
            return $value;
        } else {
            $value = 0;
            foreach (Asset::where('asset_type_id', $id)->get() as $key) {
                if (
                    !empty(AssetValuation::where('asset_id', $key->id)->where(
                        'date',
                        '<=',
                        $start_date
                    )->orderBy(
                            'date',
                            'desc'
                        )->first())
                ) {
                    $value = AssetValuation::where('asset_id', $key->id)->where(
                        'date',
                        '<=',
                        $start_date
                    )->orderBy(
                            'date',
                            'desc'
                        )->first()->amount;
                }
            }
            return $value;

        }

    }

    public static function bank_account_balance($id)
    {

        return Capital::where('bank_account_id', $id)->where('branch_id', session('branch_id'))->where(
            'type',
            'deposit'
        )->sum('amount') - Capital::where('bank_account_id', $id)->where(
                'branch_id',
                session('branch_id')
            )->where(
                    'type',
                    'withdrawal'
                )->sum('amount');
    }

    public static function send_sms($to, $msg)
    {
        if (Setting::where('setting_key', 'sms_enabled')->first()->setting_value == 1) {
            if (
                !empty(SmsGateway::find(Setting::where(
                    'setting_key',
                    'active_sms'
                )->first()->setting_value))
            ) {
                $active_sms = SmsGateway::find(Setting::where(
                    'setting_key',
                    'active_sms'
                )->first()->setting_value);
                $append = "&";
                $append .= $active_sms->to_name . "=" . $to;
                $append .= "&" . $active_sms->msg_name . "=" . urlencode($msg);
                $url = $active_sms->url . $append;
                //send sms here
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $curl_scraped_page = curl_exec($ch);
                curl_close($ch);
            }
        }

    }

    public static function buildTree($data, $parent = 0)
    {
        $tree = array();
        foreach ($data as $d) {
            if ($d['parent_id'] == $parent) {
                $children = GeneralHelper::buildTree($data, $d['id']);
                // set a trivial key
                if (!empty($children)) {
                    $d['_children'] = $children;
                }
                $tree[] = $d;
            }
        }
        return $tree;
    }

    public static function printTree($tree, $r = 0, $p = null)
    {
        foreach ($tree as $i => $t) {
            $dash = ($t['parent_id'] == 0) ? '' : str_repeat('-', $r) . ' ';
            printf("\t<option value='%d'>%s%s</option>\n", $t['id'], $dash, $t['name']);
            if (isset($t['_children'])) {
                GeneralHelper::printTree($t['_children'], $r + 1, $t['parent_id']);
            }
        }
    }

    public static function printTableTree($tree, $r = 0, $p = null)
    {
        $html = '';
        foreach ($tree as $i => $t) {
            $dash = ($t['parent_id'] == 0) ? '' : str_repeat('-', $r) . ' ';
            $html .= '<tr>';
            $html .= "<td>" . $dash . $t['name'] . '</td>';
            $html .= "<td>" . $t['slug'] . '</td>';
            if ($t['active'] == 1) {
                $html .= "<td><span class='label label-success'>" . trans_choice('general.yes', 1) . "</span></td>";
            } else {
                $html .= "<td><span class='label label-danger'>" . trans_choice('general.no', 1) . "</span></td>";
            }
            $html .= "<td>" . $t['notes'] . '</td>';
            $html .= "<td>" . count($t['products']) . '</td>';
            $html .= "<td> <div class='btn-group'>";
            $html .= '<button type="button" class="btn btn-info btn-xs dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">' . trans('general.choose');
            $html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>';
            $html .= '<ul class="dropdown-menu" role="menu">';
            if (Sentinel::hasAccess('stock.update')) {
                $html .= '<li><a href="' . url('product/category/' . $t['id'] . '/edit') . '"><i
                                                        class="fa fa-edit"></i>' . trans('general.edit') . '</a>
                                        </li>';
            }
            if (Sentinel::hasAccess('stock.delete')) {
                $html .= '<li><a href="' . url('product/category/' . $t['id'] . '/delete') . '" class="delete"><i
                                                        class="fa fa-trash"></i>' . trans('general.delete') . '</a>
                                        </li>';
            }
            $html .= '</ul></div></td>';
            $html .= '</tr>';
            if (isset($t['_children'])) {
                $html .= GeneralHelper::printTableTree($t['_children'], $r + 1, $t['parent_id']);
            }
        }
        return $html;
    }

    public static function getUniqueSlug($model, $value)
    {
        $slug = Str::slug($value);
        $slugCount = count($model->whereRaw("slug REGEXP '^{$slug}(-[0-9]+)?$' and id != '{$model->id}'")->get());

        return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
    }

    public static function limit_text($text, $limit)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    public static function check_in_total_amount($id)
    {

        return ProductCheckinItem::where('product_check_in_id', $id)->sum('total_cost');
    }

    public static function check_in_total_paid_amount($id)
    {

        return ProductCheckinItem::where('product_check_in_id', $id)->sum('total_cost');
    }

    public static function check_out_total_amount($id)
    {

        return ProductCheckoutItem::where('product_check_out_id', $id)->sum('total_cost');
    }
    public static function check_ins_total_amount($start_date = '', $end_date = '')
    {
        $checkinsQuery = ProductCheckin::where('branch_id', session('branch_id'));

        if (!empty($start_date)) {
            $checkinsQuery->whereBetween('date', [$start_date, $end_date]);
        }

        // Same parent set and same item total, without one child SUM per check-in.
        return ProductCheckinItem::whereIn(
            'product_check_in_id',
            $checkinsQuery->select('id')
        )->sum('total_cost') + 0;
    }
    public static function check_outs_total_amount($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            // Preserve the existing behavior: this method currently calls
            // check_in_total_amount() using each checkout ID.
            $checkoutsQuery = ProductCheckout::where('branch_id', session('branch_id'));

            return ProductCheckinItem::whereIn(
                'product_check_in_id',
                $checkoutsQuery->select('id')
            )->sum('total_cost') + 0;
        }

        $due = 0;
        foreach (ProductCheckout::where('branch_id', session('branch_id'))->whereBetween(
            'date',
            [$start_date, $end_date]
        )->get() as $key) {
            if ($key->type == 'cash') {
                $due = $due + GeneralHelper::check_in_total_amount($key->id);
            } else {
                if (!empty($key->loan)) {
                    $due = $due + GeneralHelper::loan_total_due_amount($key->loan_id);
                }
            }
        }

        return $due;
    }


    public static function stock_total_cost_amount()
    {
        $due = 0;
        foreach (Product::get() as $key) {
            $due = $due + ($key->qty * $key->cost_price);
        }
        return $due;
    }

    public static function stock_total_selling_amount()
    {
        $due = 0;
        foreach (Product::get() as $key) {
            $due = $due + ($key->qty * $key->selling_price);
        }
        return $due;
    }


    public static function loan_schedule_dtermine_paid_by($id)
    {
        $schedule = LoanSchedule::find($id);
        $amount = $schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty;
        $payments = 0;
        foreach (LoanRepayment::where('loan_id', $schedule->loan_id)->orderBy(
            'collection_date',
            'asc'
        )->get() as $payment) {
            $payments = $payments + $payment->amount;
        }
    }
    public static function loan_items($id, $start_date = '', $end_date = '')
    {
        $loan = Loan::find($id);

        // Keep the original relationship so any relationship constraints and
        // repayment-schedule model scopes remain in effect. Aggregate in SQL
        // instead of hydrating every repayment schedule into PHP.
        $totals = $loan->repayment_schedules()
            ->selectRaw('
                COALESCE(SUM(principal), 0) AS principal,
                COALESCE(SUM(interest), 0) AS interest,
                COALESCE(SUM(fees), 0) AS fees,
                COALESCE(SUM(penalty), 0) AS penalty,
                COALESCE(SUM(principal_paid), 0) AS principal_paid,
                COALESCE(SUM(interest_paid), 0) AS interest_paid,
                COALESCE(SUM(fees_paid), 0) AS fees_paid,
                COALESCE(SUM(penalty_paid), 0) AS penalty_paid,
                COALESCE(SUM(principal_waived), 0) AS principal_waived,
                COALESCE(SUM(interest_waived), 0) AS interest_waived,
                COALESCE(SUM(fees_waived), 0) AS fees_waived,
                COALESCE(SUM(penalty_waived), 0) AS penalty_waived,
                COALESCE(SUM(principal_written_off), 0) AS principal_written_off,
                COALESCE(SUM(interest_written_off), 0) AS interest_written_off,
                COALESCE(SUM(fees_written_off), 0) AS fees_written_off,
                COALESCE(SUM(penalty_written_off), 0) AS penalty_written_off
            ')
            ->first();

        return [
            'principal' => $totals->principal,
            'interest' => $totals->interest,
            'fees' => $totals->fees,
            'penalty' => $totals->penalty,
            'interest_waived' => $totals->interest_waived,
            'principal_waived' => $totals->principal_waived,
            'penalty_waived' => $totals->penalty_waived,
            'fees_waived' => $totals->fees_waived,
            'interest_paid' => $totals->interest_paid,
            'principal_paid' => $totals->principal_paid,
            'penalty_paid' => $totals->penalty_paid,
            'fees_paid' => $totals->fees_paid,
            'interest_written_off' => $totals->interest_written_off,
            'principal_written_off' => $totals->principal_written_off,
            'penalty_written_off' => $totals->penalty_written_off,
            'fees_written_off' => $totals->fees_written_off,
        ];
    }
    public static function loan_due_items($id, $start_date = '', $end_date = '')
    {
        $query = LoanSchedule::where('loan_id', $id);

        if (!empty($start_date)) {
            $query->whereBetween('due_date', [$start_date, $end_date]);
        }

        $totals = $query->selectRaw('
            COALESCE(SUM(principal), 0) AS principal,
            COALESCE(SUM(interest), 0) AS interest,
            COALESCE(SUM(fees), 0) AS fees,
            COALESCE(SUM(penalty), 0) AS penalty
        ')->first();

        return [
            'principal' => $totals->principal,
            'interest' => $totals->interest,
            'fees' => $totals->fees,
            'penalty' => $totals->penalty,
        ];
    }
    public static function schedule_due_amount($id)
    {
        $schedule = LoanSchedule::find($id);
        $amount = 0;

        $payments = LoanRepayment::where('loan_id', $schedule->loan_id)->sum('amount');

        // The former loop subtracted every other schedule's amount in the
        // selected loan/due-date range. Calculate that same total in MySQL.
        $previousSchedulesTotal = LoanSchedule::where('due_date', '<=', $schedule->due_date)
            ->where('loan_id', $schedule->loan_id)
            ->where('id', '!=', $id)
            ->selectRaw('COALESCE(SUM(
                COALESCE(interest, 0) +
                COALESCE(penalty, 0) +
                COALESCE(fees, 0) +
                COALESCE(principal, 0)
            ), 0) AS total')
            ->value('total');

        $payments -= $previousSchedulesTotal;
        $scheduleAmount = $schedule->interest + $schedule->penalty
            + $schedule->fees + $schedule->principal;

        if ($payments > 0 && $payments > $scheduleAmount) {
            $amount = 0;
        } elseif ($payments > 0 && $payments < $scheduleAmount) {
            $amount = $scheduleAmount - $payments;
        } else {
            $amount = $scheduleAmount;
        }

        return $amount;
    }

    public static function loans_paid_items($start_date = '', $end_date = '')
    {
        // Combine the five JournalEntry SUM queries into one grouped query.
        $query = JournalEntry::where('transaction_type', 'repayment')
            ->whereIn('transaction_sub_type', [
                'repayment_principal',
                'repayment_interest',
                'repayment_fees',
                'repayment_penalty',
                'overpayment',
            ])
            ->where('reversed', 0)
            ->where('branch_id', session('branch_id'));

        if (!empty($start_date)) {
            $query->whereBetween('date', [$start_date, $end_date]);
        }

        $totals = $query->selectRaw('transaction_sub_type, COALESCE(SUM(credit), 0) AS total')
            ->groupBy('transaction_sub_type')
            ->get()
            ->keyBy('transaction_sub_type');

        return [
            'principal' => $totals->get('repayment_principal')->total ?? 0,
            'interest' => $totals->get('repayment_interest')->total ?? 0,
            'fees' => $totals->get('repayment_fees')->total ?? 0,
            'penalty' => $totals->get('repayment_penalty')->total ?? 0,
            'over_payments' => $totals->get('overpayment')->total ?? 0,
        ];
    }

    public static function loans_due_items($start_date = '', $end_date = '')
    {
        // Keep the original Loan join, branch/status filters, and explicit
        // schedule soft-delete filter, but aggregate in MySQL rather than PHP.
        $query = Loan::query()
            ->where('loans.branch_id', session('branch_id'))
            ->whereIn('loans.status', ['disbursed', 'closed', 'written_off'])
            ->join('loan_schedules', 'loans.id', '=', 'loan_schedules.loan_id')
            ->whereNull('loan_schedules.deleted_at');

        if (!empty($start_date)) {
            $query->whereBetween('loan_schedules.due_date', [$start_date, $end_date]);
        }

        $totals = $query->selectRaw('
            COALESCE(SUM(loan_schedules.principal), 0) AS principal,
            COALESCE(SUM(loan_schedules.interest), 0) AS interest,
            COALESCE(SUM(loan_schedules.fees), 0) AS fees,
            COALESCE(SUM(loan_schedules.penalty), 0) AS penalty
        ')->first();

        return [
            'principal' => $totals->principal,
            'interest' => $totals->interest,
            'fees' => $totals->fees,
            'penalty' => $totals->penalty,
        ];
    }


    public static function determine_posting_days($id)
    {
        $savings_product = SavingsProduct::find($id);
        $interest_posting_days = [];
        if ($savings_product->interest_posting_period == "monthly") {
            array_push($interest_posting_days, Carbon::parse("last day of january")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of february")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of march")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of april")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of may")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of june")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of july")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of august")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of september")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of august")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of november")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of december")->format('Y-m-d'));
        }
        if ($savings_product->interest_posting_period == "quarterly") {
            array_push($interest_posting_days, Carbon::parse("last day of march")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of june")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of september")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of december")->format('Y-m-d'));
        }
        if ($savings_product->interest_posting_period == "biannual") {
            array_push($interest_posting_days, Carbon::parse("last day of june")->format('Y-m-d'));
            array_push($interest_posting_days, Carbon::parse("last day of december")->format('Y-m-d'));
        }
        if ($savings_product->interest_posting_period == "annually") {
            array_push($interest_posting_days, Carbon::parse("last day of december")->format('Y-m-d'));
        }
        return $interest_posting_days;
    }


    public static function determine_next_interest_calculation_date($id)
    {
        $savings_product = SavingsProduct::find($id);
        $next_calculation_date = "";
        if ($savings_product->interest_compounding_period == "daily") {
            $next_calculation_date = Carbon::tomorrow()->format("Y-m-d");
        }
        if ($savings_product->interest_compounding_period == "monthly") {
            $next_calculation_date = Carbon::parse("last day of this month")->format("Y-m-d");
        }
        if ($savings_product->interest_compounding_period == "quarterly") {
            if (Carbon::parse("last day of march")->gt(Carbon::today())) {
                $next_calculation_date = Carbon::parse("last day of march")->format('Y-m-d');
            } elseif (Carbon::parse("last day of june")->gt(Carbon::today())) {
                $next_calculation_date = Carbon::parse("last day of june")->format('Y-m-d');
            } elseif (Carbon::parse("last day of september")->gt(Carbon::today())) {
                $next_calculation_date = Carbon::parse("last day of september")->format('Y-m-d');
            } elseif (Carbon::parse("last day of december")->gt(Carbon::today())) {
                $next_calculation_date = Carbon::parse("last day of december")->format('Y-m-d');
            }
        }
        if ($savings_product->interest_compounding_period == "biannual") {
            if (Carbon::parse("last day of june")->gt(Carbon::today())) {
                $next_calculation_date = Carbon::parse("last day of june")->format('Y-m-d');
            } elseif (Carbon::parse("last day of december")->gt(Carbon::today())) {
                $next_calculation_date = Carbon::parse("last day of december")->format('Y-m-d');
            }
        }
        if ($savings_product->interest_compounding_period == "annually") {
            if (Carbon::parse("last day of december")->gt(Carbon::today())) {
                $next_calculation_date = Carbon::parse("last day of december")->format('Y-m-d');
            }
        }
        return $next_calculation_date;
    }

    public static function determine_next_interest_posting_date($id)
    {
        $savings_product = SavingsProduct::find($id);
        $next_posting_date = "";
        if ($savings_product->interest_posting_period == "monthly") {
            $next_posting_date = Carbon::parse("last day of this month")->format("Y-m-d");
        }
        if ($savings_product->interest_posting_period == "quarterly") {
            if (Carbon::parse("last day of march")->gt(Carbon::today())) {
                $next_posting_date = Carbon::parse("last day of march")->format('Y-m-d');
            } elseif (Carbon::parse("last day of june")->gt(Carbon::today())) {
                $next_posting_date = Carbon::parse("last day of june")->format('Y-m-d');
            } elseif (Carbon::parse("last day of september")->gt(Carbon::today())) {
                $next_posting_date = Carbon::parse("last day of september")->format('Y-m-d');
            } elseif (Carbon::parse("last day of december")->gt(Carbon::today())) {
                $next_posting_date = Carbon::parse("last day of december")->format('Y-m-d');
            }
        }
        if ($savings_product->interest_posting_period == "biannual") {
            if (Carbon::parse("last day of june")->gt(Carbon::today())) {
                $next_posting_date = Carbon::parse("last day of june")->format('Y-m-d');
            } elseif (Carbon::parse("last day of december")->gt(Carbon::today())) {
                $next_posting_date = Carbon::parse("last day of december")->format('Y-m-d');
            }
        }
        if ($savings_product->interest_posting_period == "annually") {
            if (Carbon::parse("last day of december")->gt(Carbon::today())) {
                $next_posting_date = Carbon::parse("last day of december")->format('Y-m-d');
            }
        }
        return $next_posting_date;
    }





    public static function gl_account_balance($id)
    {
        $transactions = GlJournalEntry::selectRaw(DB::raw('COALESCE(SUM(credit),0) credit, COALESCE(SUM(debit),0) debit'))->where('gl_account_id', $id)->where('reversed', 0)->groupBy('gl_account_id')->first();
        return $transactions;
    }

    public static function gl_account_unreconciled_balance($id)
    {
        $transactions = GlJournalEntry::selectRaw(DB::raw('COALESCE(SUM(credit),0) credit, COALESCE(SUM(debit),0) debit'))->where('gl_account_id', $id)->where('reversed', 0)->where('reconciled', 0)->groupBy('gl_account_id')->first();
        return $transactions;
    }




    //////////////////////////Disbursements////////////////                                                                                                                                                                            //////////////////////////////
    public static function total_disbursed_loans_amount($start_date = "", $end_date = "")
    {
        $amount = Loan::selectRaw(DB::raw('COALESCE(SUM(principal),0) principal'))->whereIn('status', ['disbursed', 'closed', 'written_off'])->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('disbursement_date', [$start_date, $end_date]);
        })->first();

        if (!empty($amount)) {
            return $amount->principal;
        } else {
            return 0;
        }
    }

    public static function officer_total_disbursed_loans_amount($start_date = "", $end_date = "")
    {
        // $user_ids = Sentinel::getUser()->id;
        $user_ids = Sentinel::getUser();
        $amount = Loan::selectRaw(DB::raw('COALESCE(SUM(principal),0) principal'))->whereIn('status', ['disbursed', 'closed', 'written_off'])->where('loan_officer_id', $user_ids->id)->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('disbursement_date', [$start_date, $end_date]);
        })->where(function ($query) use ($user_ids) {
            //  $query->whereIn('loan_officer_id', $user_ids->office);
            // $query->whereIn('loan_officer_id', '2');
        })->first();

        if (!empty($amount)) {
            return $amount->principal;
        } else {
            return 0;
        }
    }

    public static function branch_total_disbursed_loans_amount($start_date = "", $end_date = "")
    {
        $user_ids = Sentinel::getUser();
        $amount = Loan::selectRaw(DB::raw('COALESCE(SUM(principal),0) principal'))->whereIn('status', ['disbursed', 'closed', 'written_off'])->where('office_id', $user_ids->office_id)->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('disbursement_date', [$start_date, $end_date]);
        })->where(function ($query) use ($user_ids) {
            //$query->whereIn('office_id', $user_ids->office);

        })->first();

        if (!empty($amount)) {
            return $amount->principal;
        } else {
            return 0;
        }
    }


    // public static function get_province_loans($id){
    //     $province_transactions = [];
    //     $province_branches = Office::where('province_id',$id)->get();
    //     foreach($province_branches as $province_branch){
    //         $branch_loans = Loan::with('transactions')->where('office_id',$province_branch->id)->get();
    //         //foreach($branch_loans as $)
    //     }
    // }



    public static function client_total_disbursed_loans_amount($id, $start_date = "", $end_date = "")
    {
        $client_ids = [];
        foreach (Sentinel::findUserById($id)->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::findUserById($id)->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        $amount = Loan::selectRaw(DB::raw('COALESCE(SUM(principal),0) principal'))->whereIn('status', ['disbursed', 'closed', 'written_off'])->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('disbursement_date', [$start_date, $end_date]);
        })->where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('client_id', $client_ids)
                ->orWhereIn('group_id', $group_ids);
        })->first();

        if (!empty($amount)) {
            return $amount->principal;
        } else {
            return 0;
        }
    }

    ///////////////////////////////////////////////Repayments///////////////////////////////////////////////////
    public static function total_loans_repayments_amount($start_date = "", $end_date = "")
    {

        $amount = LoanTransaction::selectRaw(DB::raw('COALESCE(SUM(credit),0) amount'))->where('reversed', 0)->where('transaction_type', 'repayment')->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        })->first();

        if (!empty($amount)) {
            return $amount->amount;
        } else {
            return 0;
        }
    }

    public static function officer_total_loans_repayments_amount($start_date = "", $end_date = "")
    {
        $user_ids = Sentinel::getUser();
        $amount = DB::table("loan_transactions as lt")->selectRaw(DB::raw('COALESCE(SUM(credit),0) amount'))->where('reversed', 0)->where('transaction_type', 'repayment')->where('loan_officer_id', $user_ids->id)->join('loans as l', "l.id", "=", "lt.loan_id")->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        })->where(function ($query) use ($user_ids) {
            //$query->whereIn('l.loan_officer_id', $user_ids);

        })->first();

        if (!empty($amount)) {
            return $amount->amount;
        } else {
            return 0;
        }
    }


    public static function branch_total_loans_repayments_amount($start_date = "", $end_date = "")
    {
        $user_ids = Sentinel::getUser();
        $amount = DB::table("loan_transactions as lt")->selectRaw(DB::raw('COALESCE(SUM(credit),0) amount'))->where('reversed', 0)->where('transaction_type', 'repayment')->join('loans as l', "l.id", "=", "lt.loan_id")->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        })->where(function ($query) use ($user_ids) {
            $query->whereIn('l.office_id', $user_ids->office);

        })->first();

        if (!empty($amount)) {
            return $amount->amount;
        } else {
            return 0;
        }
    }

    public static function client_total_loans_repayments_amount($id, $start_date = "", $end_date = "")
    {
        $client_ids = [];
        foreach (Sentinel::findUserById($id)->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::findUserById($id)->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        $amount = LoanTransaction::selectRaw(DB::raw('COALESCE(SUM(credit),0) amount'))->where('reversed', 0)->where('transaction_type', 'repayment')->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        })->where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('client_id', $client_ids)
                ->orWhereIn('group_id', $group_ids);
        })->first();

        if (!empty($amount)) {
            return $amount->amount;
        } else {
            return 0;
        }
    }
    /////////////////////////////Outstandings/////////////////////////////////////////


    public static function total_loans_outstanding_amount($start_date = "", $end_date = "")
    {

        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }

    public static function officer_total_loans_outstanding_amount($start_date = "", $end_date = "")
    {
        $user_ids = Sentinel::getUser();
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->where('loan_officer_id', $user_ids->id)->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        })->where(function ($query) use ($user_ids) {
            // $query->whereIn('l.loan_officer_id', $user_ids);

        })->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }

    public static function branch_total_loans_outstanding_amount($start_date = "", $end_date = "")
    {
        $user_ids = Sentinel::getUser();
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        })->where(function ($query) use ($user_ids) {
            $query->whereIn('l.office_id', $user_ids->office);

        })->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }






    public static function client_total_loans_outstanding_amount($id, $start_date = "", $end_date = "")
    {
        $client_ids = [];
        foreach (Sentinel::findUserById($id)->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::findUserById($id)->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }

        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('l.client_id', $client_ids)
                ->orWhereIn('l.group_id', $group_ids);
        })->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }

    /**
     * Check if the current user is a specialist
     *
     * @return bool
     */
    public static function isSpecialist()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return false;
        }
        
        return \App\Models\Specialist::where('user_id', $user->id)->where('is_active', 1)->exists();
    }

    ///////////////////////Overdues//////////////////////////////////////////////////////////
    public static function total_loans_overdue_amount($start_date = "")
    {
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date) {
            $query->where('lr.due_date', '<', $start_date);
        }, function ($query) {
            $query->where('lr.due_date', '<', date("Y-m-d"));
        })->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }


    public static function officer_total_loans_overdue_amount($start_date = "")
    {
        $user_ids = Sentinel::getUser();
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->where('loan_officer_id', $user_ids->id)->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date) {
            $query->where('lr.due_date', '<', $start_date);
        }, function ($query) {
            $query->where('lr.due_date', '<', date("Y-m-d"));
        })->where(function ($query) use ($user_ids) {
            //$query->whereIn('loan_officer_id', $user_ids);

        })->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }


    public static function branch_total_loans_overdue_amount($start_date = "")
    {
        $user_ids = Sentinel::getUser();
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date) {
            $query->where('lr.due_date', '<', $start_date);
        }, function ($query) {
            $query->where('lr.due_date', '<', date("Y-m-d"));
        })->where(function ($query) use ($user_ids) {
            $query->whereIn('office_id', $user_ids->office);

        })->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }

    public static function client_total_loans_overdue_amount($id, $start_date = "")
    {
        $client_ids = [];
        foreach (Sentinel::findUserById($id)->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::findUserById($id)->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date) {
            $query->where('lr.due_date', '<', $start_date);
        }, function ($query) {
            $query->where('lr.due_date', '<', date("Y-m-d"));
        })->where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('l.client_id', $client_ids)
                ->orWhereIn('l.group_id', $group_ids);
        })->first();

        if (!empty($amount)) {
            return $amount->balance;
        } else {
            return 0;
        }
    }

    public static function client_numbers_graph()
    {
        $clients = [];
        $registered_prospects = Client::where('status', 'active')->whereNOTIn('id', function ($query) {
            $query->select('client_id')->from('loans');
        })->count();
        $total_clients = Client::where('status', 'active')->count();

        $funded_clients = Client::where('status', 'active')->whereIn('id', function ($query) {
            $query->select('l.client_id')->from('loans as l')->join("loan_repayment_schedules as lr", "l.id", '=', "lr.loan_id")->where('status', 'disbursed')->groupBy("l.id")->havingRaw('(COALESCE(SUM(lr.principal),0)+COALESCE(SUM(lr.interest),0)+COALESCE(SUM(lr.fees),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) >0')->distinct();
        })->count();
        array_push($clients, [
            "title" => "Registered Prospects",
            "value" => $registered_prospects,
        ]);
        array_push($clients, [
            "title" => "Funded Clients",
            "value" => $funded_clients,
        ]);
        array_push($clients, [
            "title" => "Total Clients",
            "value" => $total_clients,
        ]);

        return json_encode($clients, JSON_UNESCAPED_SLASHES);
    }

    public static function savings_balance_graph()
    {

        $savings = [];
        $transactions = DB::table('savings_transactions as st')->selectRaw(DB::raw('o.name name,(COALESCE(SUM(st.credit),0)- COALESCE(SUM(st.debit),0)) balance'))->join('offices as o', 'o.id', '=', 'st.office_id')->where('st.reversed', 0)->groupBy('st.office_id')->get();
        foreach ($transactions as $transaction) {
            array_push($savings, [
                "title" => $transaction->name,
                "value" => $transaction->balance,
            ]);
        }
        return json_encode($savings, JSON_UNESCAPED_SLASHES);
    }

    public static function loans_status_graph()
    {
        $loans = [];

        array_push($loans, [
            "title" => "Pending",
            "value" => Loan::where('status', 'pending')->count(),
        ]);

        array_push($loans, [
            "title" => "Approved",
            "value" => Loan::where('status', 'approved')->count(),
        ]);
        array_push($loans, [
            "title" => "Disbursed",
            "value" => Loan::where('status', 'disbursed')->count(),
        ]);
        array_push($loans, [
            "title" => "Declined",
            "value" => Loan::where('status', 'declined')->count(),
        ]);
        array_push($loans, [
            "title" => "Written Off",
            "value" => Loan::where('status', 'written_off')->count(),
        ]);
        array_push($loans, [
            "title" => "Withdrawn",
            "value" => Loan::where('status', 'withdrawn')->count(),
        ]);


        return json_encode($loans, JSON_UNESCAPED_SLASHES);
    }

    public static function collection_overview_graph()
    {
        $office_id = Sentinel::getUser()->office_id;
        $collection_overview = [];
        $date = date_format(
            date_sub(
                date_create(date("Y-m-d")),
                date_interval_create_from_date_string('1 years')
            ),
            'Y-m-d'
        );
        for ($i = 1; $i <= 13; $i++) {
            $d = explode('-', $date);
            $actual = 0;
            $expected = 0;
            $actual = $actual + LoanTransaction::where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->where(
                    'year',
                    $d[0]
                )->where(
                    'month',
                    $d[1]
                )->sum('credit');
            $repayment_schedules = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)) balance'))->where(
                'year',
                $d[0]
            )->where(
                    'month',
                    $d[1]
                )->first();
            if (!empty($repayment_schedules)) {
                $expected = $repayment_schedules->balance;
            }
            array_push($collection_overview, array(
                'month' => date_format(
                    date_create($date),
                    'M' . ' ' . $d[0]
                ),
                'actual' => $actual,
                'expected' => $expected
            ));
            $date = date_format(
                date_add(
                    date_create($date),
                    date_interval_create_from_date_string('1 months')
                ),
                'Y-m-d'
            );
        }


        return json_encode($collection_overview, JSON_UNESCAPED_SLASHES);
    }

    public static function fees_penalty_earned_paid()
    {

        $schedules = [];
        $fees = 0;
        $fees_paid = 0;
        $penalty = 0;
        $penalty_paid = 0;
        $transactions = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)) fees,(COALESCE(SUM(lr.fees_paid),0)) fees_paid ,(COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)) penalty,(COALESCE(SUM(lr.penalty_paid),0)) penalty_paid'))->first();
        if (!empty($transactions)) {
            $fees = $transactions->fees;
            $fees_paid = $transactions->fees_paid;
            $penalty = $transactions->penalty;
            $penalty_paid = $transactions->penalty_paid;
        }
        $schedules["fees"] = $fees;
        $schedules["fees_paid"] = $fees_paid;
        $schedules["penalty"] = $penalty;
        $schedules["penalty_paid"] = $penalty_paid;
        return $schedules;
    }

    public static function determine_savings_interest_earned($id)
    {
        $savings = Savings::find($id);
        $savings_product = $savings->savings_product;
        $total_balance = 0;
        if (Carbon::parse($savings->next_interest_calculation_date)->eq(Carbon::today())) {
            $previous_balance = GeneralHelper::savings_account_balance($id, Carbon::today()->format("Y-m-d")) + $savings->interest_earned;
            $total_balance = $total_balance + $previous_balance;
            if ($savings_product->interest_compounding_period == "daily") {
                $today_balance = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance'))->where('savings_id', $id)->where('reversed', 0)->where('date', Carbon::today()->format("Y-m-d"))->first();
                if (!empty($today_balance)) {
                    $total_balance = $today_balance->balance + $total_balance;
                }
                if ($total_balance >= $savings_product->minimum_balance) {
                    //calculate interest
                    $interest = $total_balance * ($savings_product->interest_rate / (100 * 365));
                    $savings->interest_earned = $savings->interest_earned + $interest;
                    $savings->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
                    $savings->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                    $savings->save();
                } else {
                    $savings->next_interest_calculation_date = Carbon::tomorrow()->format("Y-m-d");
                    $savings->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                    $savings->save();
                }
            }
            if ($savings_product->interest_compounding_period == "monthly") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . date("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . date("M"))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(1)->format("M"))->format("Y-m-d");
            }
            if ($savings_product->interest_compounding_period == "quarterly") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(2)->format("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(2))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(3)->format("M"))->format("Y-m-d");
            }
            if ($savings_product->interest_compounding_period == "biannual") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(5)->format("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(5))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(6)->format("M"))->format("Y-m-d");

            }
            if ($savings_product->interest_compounding_period == "annually") {
                if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of " . Carbon::today()->subMonths(11)->format("M")))) {
                    $start_date = $savings->start_interest_calculation_date;
                } else {
                    $start_date = Carbon::parse("first day of " . Carbon::today()->subMonths(11))->format("Y-m-d");
                }
                $next_interest_calculation_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(12)->format("M"))->format("Y-m-d");

            }
            if ($savings_product->interest_compounding_period == "monthly") {
                if ($savings_product->interest_calculation_type == "daily") {
                    $transactions = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date'))->where('savings_id', $savings->id)->where('reversed', 0)->whereBetween('date', [$start_date, Carbon::today()->format("Y-m-d")])->groupBy('date')->get();
                    $balance = GeneralHelper::savings_account_balance($savings->id, $start_date);
                    $interest = 0;
                    $total_days = 0;
                    if (empty($transactions)) {
                        if ($balance >= $savings_product->minimum_balance) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()->format("Y-m-d")) + 1;
                            $interest = $interest + ($balance * $days * $savings_product->interest_rate / (100 * 365));
                        }
                    } else {
                        foreach ($transactions as $transaction) {
                            if (Carbon::parse($start_date)->eq(Carbon::parse($transaction->date))) {
                                $days = 1;
                            } else {
                                $days = Carbon::parse($start_date)->diffInDays($transaction->date);
                            }
                            if ($balance >= $savings_product->minimum_balance) {
                                $interest = $interest + ($balance * $days * $savings_product->interest_rate / (100 * 365));
                            }
                            $start_date = Carbon::parse($start_date)->addDays($days)->format("Y-m-d");
                            $balance = $balance + $transaction->balance;
                            $total_days = $total_days + $days;
                        }
                        if (Carbon::parse($start_date)->notEqualTo(Carbon::today())) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()) + 1;
                            if ($balance >= $savings_product->minimum_balance) {
                                $interest = $interest + ($balance * $days * $savings_product->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + $days;
                        } else {
                            if ($balance >= $savings_product->minimum_balance) {
                                $interest = $interest + ($balance * $savings_product->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + 1;
                        }
                    }
                    $savings->interest_earned = $savings->interest_earned + $interest;
                    $savings->next_interest_calculation_date = $next_interest_calculation_date;
                    $savings->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                    $savings->save();
                }
                if ($savings_product->interest_calculation_type == "average") {
                    $transactions = SavingsTransaction::selectRaw(DB::raw('(COALESCE(SUM(credit),0)-COALESCE(SUM(debit),0)) as balance, date'))->where('savings_id', $savings->id)->where('reversed', 0)->whereBetween('date', [$start_date, Carbon::today()->format("Y-m-d")])->groupBy('date')->get();
                    $balance = GeneralHelper::savings_account_balance($savings->id, $start_date);
                    $interest = 0;
                    $total_days = 0;
                    if (empty($transactions)) {
                        if ($balance >= $savings_product->minimum_balance) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()->format("Y-m-d")) + 1;
                            $interest = $interest + ($balance * $days * $savings_product->interest_rate / (100 * 365));
                        }
                    } else {
                        $average_balance = 0;
                        foreach ($transactions as $transaction) {
                            if (Carbon::parse($start_date)->eq(Carbon::parse($transaction->date))) {
                                $days = 1;
                            } else {
                                $days = Carbon::parse($start_date)->diffInDays($transaction->date);
                            }
                            $interest = $interest + ($balance * $days * $savings_product->interest_rate / (100 * 365));
                            $average_balance = $average_balance + ($balance * $days);
                            $start_date = Carbon::parse($start_date)->addDays($days)->format("Y-m-d");
                            $balance = $balance + $transaction->balance;
                            $total_days = $total_days + $days;
                        }
                        if (Carbon::parse($start_date)->notEqualTo(Carbon::today())) {
                            $days = Carbon::parse($start_date)->diffInDays(Carbon::today()) + 1;
                            $average_balance = $average_balance + ($balance * $days);
                            if ($balance >= $savings_product->minimum_balance) {
                                $interest = $interest + ($balance * $days * $savings_product->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + $days;
                        } else {
                            $average_balance = $average_balance + ($balance * 1);
                            if ($balance >= $savings_product->minimum_balance) {
                                $interest = $interest + ($balance * $savings_product->interest_rate / (100 * 365));
                            }
                            $total_days = $total_days + 1;
                        }
                        $average_balance = $average_balance / $total_days;
                        if ($average_balance > $savings_product->minimum_balance) {
                            $interest = $interest + ($average_balance * $total_days * $savings_product->interest_rate / (100 * 365));
                        }
                    }
                    $savings->interest_earned = $savings->interest_earned + $interest;
                    $savings->next_interest_calculation_date = $next_interest_calculation_date;
                    $savings->last_interest_calculation_date = Carbon::today()->format("Y-m-d");
                    $savings->save();
                }

            }

        }

    }

    public static function post_savings_interest_earned($id)
    {
        $savings = Savings::find($id);
        $savings_product = $savings->savings_product;
        if (Carbon::parse($savings->next_interest_posting_date)->eq(Carbon::today())) {
            if ($savings->interest_earned > 0) {
                $date = date("Y-m-d");
                $savings_transaction = new SavingsTransaction();
                //$savings_transaction->created_by_id = Sentinel::getUser()->id;
                $savings_transaction->office_id = $savings->office_id;
                $savings_transaction->savings_id = $savings->id;
                $savings_transaction->transaction_type = "interest";
                $savings_transaction->reversible = 1;
                $savings_transaction->date = date("Y-m-d");
                $savings_transaction->time = date("H:i");
                $date = explode('-', date("Y-m-d"));
                $savings_transaction->year = $date[0];
                $savings_transaction->month = $date[1];
                $savings_transaction->credit = $savings->interest_earned;
                $savings_transaction->save();
                if (!empty($savings_product->gl_account_interest_on_savings)) {
                    $journal = new GlJournalEntry();
                    //$journal->created_by_id = Sentinel::getUser()->id;
                    $journal->gl_account_id = $savings_product->gl_account_interest_on_savings->id;
                    $journal->office_id = $savings->office_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->transaction_type = 'savings';
                    $journal->name = "Savings interest";
                    $journal->savings_transaction_id = $savings_transaction->id;
                    $journal->savings_id = $savings->id;
                    $journal->debit = $savings->interest_earned;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();
                }
                if (!empty($savings_product->gl_account_savings_reference)) {
                    $journal = new GlJournalEntry();
                    //$journal->created_by_id = Sentinel::getUser()->id;
                    $journal->gl_account_id = $savings_product->gl_account_savings_reference->id;
                    $journal->office_id = $savings->office_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->transaction_type = 'savings';
                    $journal->name = "Savings interest";
                    $journal->savings_transaction_id = $savings_transaction->id;
                    $journal->savings_id = $savings->id;
                    $journal->credit = $savings->interest_earned;
                    $journal->reference = $savings_transaction->id;
                    $journal->save();
                }
            }
            if ($savings_product->interest_posting_period == "monthly") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(1)->format("M"))->format("Y-m-d");
            }
            if ($savings_product->interest_posting_period == "quarterly") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(3)->format("M"))->format("Y-m-d");
            }
            if ($savings_product->interest_posting_period == "biannual") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(6)->format("M"))->format("Y-m-d");
            }
            if ($savings_product->interest_posting_period == "annually") {
                $savings->next_interest_posting_date = Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(12)->format("M"))->format("Y-m-d");
            }
            $savings->interest_earned = 0;
            $savings->last_interest_posting_date = Carbon::today()->format("Y-m-d");
            $savings->save();

        }


    }

    public static function get_user_info()
    {
        $user = Sentinel::getUser();
        if ($user) {
            return (object) [
               'user' => $user ?? '',
                'role' => $user->role ? $user->role->role_id : null,
                'office' => $user->office ? $user->office->id : null,
                'province_id' => $user->province_id ?? null,
                'other_role' => $user->other_role ?? null,
            ];
        }
        return (object) [
            'user' => null,
            'role' => null,
            'office' => null,
            'province_id' => null,
            'other_role' => null
        ];
    }

    public static function get_my_manager()
    {
        $currentUser = Sentinel::getUser();
        if (!$currentUser || !$currentUser->office) {
            return [
                'bm' => null,
                'admin' => null,
                'pm' => null,
                'dm' => null
            ];
        }

        $officeId = $currentUser->office->id;
        $managers = [];

        // Find BM (Branch Manager - role 4)
        $bm = \App\Models\User::where('office_id', $officeId)
            ->whereHas('roles', function ($query) {
                $query->where('roles.id', 4);
            })
            ->first();
        $managers['bm'] = $bm ? $bm->id : null;

        // Find Admin (role 1)
        $admin = \App\Models\User::where('office_id', $officeId)
            ->whereHas('roles', function ($query) {
                $query->where('roles.id', 1);
            })
            ->first();
        $managers['admin'] = $admin ? $admin->id : null;

        // Find PM (Province Manager - role 6)
        $pm = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('roles.id', 6);
            })->whereHas('office', function ($query) use ($currentUser) {
                $query->where('province_id', $currentUser->office->province_id);
            })
            ->first();
        $managers['pm'] = $pm ? $pm->id : null;

        // Find DM (District Manager - role 12)
        $dm = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('roles.id', 12);
            })->whereHas('office', function ($query) use ($currentUser) {
                $query->where('district_id', $currentUser->office->district_id);
            })
            ->first();
        $managers['dm'] = $dm ? $dm->id : null;

        // Find POA
        $poa = \App\Models\User::where('position_id',17)
            ->first();
        $managers['poa'] = $poa ? $poa->id : null;

        // Find MA
        $ma = \App\Models\User::where('position_id',8)
            ->first();
        $managers['ma'] = $ma ? $ma->id : null;

        // Find Executives
        $ma = \App\Models\User::where('position_id',20)
            ->first();
        $managers['ex'] = $ma ? $ma->id : null;

        // Find Risk
        $ma = \App\Models\User::where('position_id',14)
            ->first();
        $managers['rk'] = 8;
        
        return $managers;
    }

    public static function get_filtered_offices()
    {
        $user = Sentinel::getUser();
        if ($user->inRole(1)) {
            return \App\Models\Office::all();
        } elseif ($user->inRole(6)) {
            return \App\Models\Office::where('province_id', $user->province_id)->get();
        } elseif ($user->inRole(4) || $user->inRole(3) || $user->inRole(11)) {
            return \App\Models\Office::where('id', $user->office_id)->get();
        }else{
            return \App\Models\Office::all();
        }
        return collect();
    }

       public static function get_filtered_offices_new()
    {
   
        return \App\Models\Office::all();
        return collect();
    }

    public static function get_filtered_staffs($office_id = null)
    {
        $user = Sentinel::getUser();
        if ($user->inRole(1)) {
            if ($office_id) {
                return \App\Models\User::where('office_id', $office_id)->get();
            }
            return \App\Models\User::all();
        } elseif ($user->inRole(6)) {
            if ($office_id) {
                return \App\Models\User::where('office_id', $office_id)->where('province_id', $user->province_id)->get();
            }
            return \App\Models\User::where('province_id', $user->province_id)->get();
        } elseif ($user->inRole(4) || $user->inRole(3) || $user->inRole(11)) {
            return \App\Models\User::where('office_id', $user->office_id)->get();
        }else{
            if ($office_id) {
                return \App\Models\User::where('office_id', $office_id)->get();
            }
            return \App\Models\User::all();
        }
        return collect();
    }

    /**
     * Calculate view percentage based on view count and total users
     * 
     * @param int $viewCount The number of views
     * @param int|null $totalUsers Optional total users count. If null, will fetch from database
     * @return float The percentage value
     */
    public static function calculate_view_percentage($viewCount, $totalUsers = null)
    {
        if ($totalUsers === null) {
            $totalUsers = \App\Models\User::count();
        }
        
        if ($totalUsers == 0) {
            return 0;
        }
        
        return round(($viewCount / $totalUsers) * 100, 1);
    }

    /**
     * Get the count of pending recovery case approvals based on user role
     * 
     * @return int Count of pending recovery cases
     */
    public static function pending_recovery_case_approvals_count()
    {
        try {
            $user = Sentinel::getUser();
            $userId = $user->id;
            $role = \App\Models\UserRole::where('user_id', $userId)->first();
            $province_id = $user->province_id;
            $office_id = $user->office_id;

            if ($role && $role->role_id == "6") {
                // Count all pending cases for offices in the user's province
                // using a subquery instead of loading offices and counting one by one.
                return \App\Models\RecoveryCase::whereIn(
                    'origin_branch_id',
                    \App\Models\Office::where('province_id', $province_id)->select('id')
                )->whereNull('approved_date')->count();
            } elseif ($role == "10") {
                // Preserve the existing role comparison exactly.
                return \App\Models\RecoveryCase::whereNull('approved_date')->count();
            }

            return \App\Models\RecoveryCase::where('origin_branch_id', $office_id)
                ->whereNull('approved_date')
                ->count();
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    public static function pending_recoveries_approvals_count()
    {
        $user = Sentinel::getUser();
        $userId = $user->id;
        $role = \App\Models\UserRole::where('user_id', $userId)->first();
        $province_id = $user->province_id;
        $office_id = $user->office_id;

        if ($role && $role->role_id == "6") {
            // Replace per-office counts with one count over the province's offices.
            return \App\Models\LoanTransactionUnapproved::whereIn(
                'office_id',
                \App\Models\Office::where('province_id', $province_id)->select('id')
            )->where('payment_apply_to', 'debt_recovery')->count();
        } elseif ($user->hasAccess('settings')) {
            return \App\Models\LoanTransactionUnapproved::where('payment_apply_to', 'debt_recovery')->count();
        }

        return \App\Models\LoanTransactionUnapproved::where('office_id', $office_id)
            ->where('payment_apply_to', 'debt_recovery')
            ->count();
    }

    public static function formatUserDetails($user_id)
    {
        if (!$user_id) {
            return 'System';
        }

        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return 'System';
        }

        $role = $user->roles->first();
        $roleName = $role ? $role->name : 'N/A';
        $phone = $user->phone ?? 'N/A';

        return [
            'name'  => "{$user->first_name} {$user->last_name}",
            'phone' => $phone,
            'email' => $user->email,
            'role'  => $roleName,
        ];
    }

    /**
     * Merge one or more config('role.xxx') arrays into a deduplicated
     * list of user-ID strings.
     *
     * Usage:  GeneralHelper::mergedRoleIds('role.exec', 'role.goa', 'role.risk')
     */
    public static function mergedRoleIds(string ...$keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $val = config($key, []);
            if (is_array($val)) {
                $result = array_merge($result, $val);
            }
        }
        return array_unique(array_map('strval', $result));
    }

    /**
     * Return all office IDs that belong to the given province.
     *
     * @param  int|null  $provinceId
     * @return \Illuminate\Support\Collection<int>
     */
    public static function officeIdsByProvince(?int $provinceId): \Illuminate\Support\Collection
    {
        if (!$provinceId) {
            return collect();
        }
        return Office::where('province_id', $provinceId)->pluck('id');
    }

    /**
     * Return all office IDs that belong to the given district.
     *
     * @param  int|null  $districtId
     * @return \Illuminate\Support\Collection<int>
     */
    public static function officeIdsByDistrict(?int $districtId): \Illuminate\Support\Collection
    {
        if (!$districtId) {
            return collect();
        }

        return Office::where('district_id', $districtId)->pluck('id');
    }
}

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
        $amount = ($interest_rate * $balance * pow((1 + $interest_rate), $period)) / (pow((1 + $interest_rate),
                    $period) - 1);
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
        $schedule = LoanRepaymentSchedule::where('due_date', ' >=', $date)->where('loan_id', $id)->orderBy('due_date',
            'asc')->first();
        if (!empty($schedule)) {
            return $schedule->due_date;
        } else {
            $schedule = LoanRepaymentSchedule::where('loan_id',
                $id)->orderBy('due_date',
                'desc')->first();
            if ($date > $schedule->due_date) {
                return $schedule->due_date;
            } else {
                $schedule = LoanRepaymentSchedule::where('due_date', '>', $date)->where('loan_id',
                    $id)->orderBy('due_date',
                    'asc')->first();
                return $schedule->due_date;
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
            return LoanSchedule::where('loan_id', $id)->where('due_date', '<=',
                $date)->sum('fees');
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
            return LoanTransaction::where('loan_id', $id)->where('transaction_type',
                'repayment')->where('reversed', 0)->sum('credit');
        } else {
            return LoanTransaction::where('loan_id', $id)->where('transaction_type',
                'repayment')->where('reversed', 0)->where('due_date', '<=', $date)->sum('credit');
        }

    }

public static function new_loan_total_balance($id,$date = ''){
    if (empty($date)) {
        $loan = Loan::find($id);
        $someInfo = [];
        $debit = 0;
        $credit = 0; 
        if(!empty($loan)){
            foreach ($loan -> transactions as $transaction) {
                $debit = $debit + $transaction->debit;
                $credit = $credit + $transaction->credit;

            }
            return (($debit - $credit));

        } else {
            return 0;
        }

        }
    
}

public static function new_new_loan_total_balance($id,$date){
   
    $loan = Loan::find($id);
    $someInfo = [];
    $debit = 0;
    $credit = 0; 
    if(!empty($loan)){
        foreach ($loan -> transactions as $transaction) {
           if($transaction->date < $date) {
            $debit = $debit + $transaction->debit;
            $credit = $credit + $transaction->credit;
           }

        }
        return (($debit - $credit));

    } else {
        return 0;
    }


}



    public static function loan_total_balance($id, $date = '')
    {
        if (empty($date)) {
            $loan = Loan::find($id);
            $someInfo = [];
            $principal = 0;
            $principal_paid = 0;
            $principal_written_off = 0;
            $fees = 0;
            $fees_paid = 0;
            $penalty = 0;
            $penalty_paid = 0;
            $penalty_written_off = 0;
            $interest_waived = 0;
            $penalty_waived = 0;
            $fees_waived = 0;
            $fees_written_off = 0;
            $principal_waived = 0;
            $interest = 0;
            $interest_paid = 0;
            $interest_written_off = 0;
            if(!empty($loan)){
                foreach ($loan -> repayment_schedules as $schedule) {
                    $principal = $principal + $schedule->principal;
                    $interest = $interest + $schedule->interest;
                    $penalty = $penalty + $schedule->penalty;
                    $fees = $fees + $schedule->fees;
                    $principal_paid = $principal_paid + $schedule->principal_paid;
                    $interest_paid = $interest_paid + $schedule->interest_paid;
                    $penalty_paid = $penalty_paid + $schedule->penalty_paid;
                    $fees_paid = $fees_paid + $schedule->fees_paid;
                    $principal_waived = $principal_waived + $schedule->principal_waived;
                    $interest_waived = $interest_waived + $schedule->interest_waived;
                    $penalty_waived = $penalty_waived + $schedule->penalty_waived;
                    $fees_waived = $fees_waived + $schedule->fees_waived;
                    $principal_written_off = $principal_written_off + $schedule->principal_written_off;
                    $interest_written_off = $interest_written_off + $schedule->interest_written_off;
                    $penalty_written_off = $penalty_written_off + $schedule->penalty_written_off;
                    $fees_written_off = $fees_written_off + $schedule->fees_written_off;
    
                }
                return (($principal - $principal_paid - $principal_waived - $principal_written_off) + ($interest - $interest_paid - $interest_waived - $interest_written_off) + ($fees - $fees_paid - $fees_waived - $fees_written_off) + ($penalty - $penalty_paid - $penalty_waived - $penalty_written_off));
    
            } else {
                return 0;
            }

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
        if (empty($date)) {
            return (GeneralHelper::loan_total_penalty($id) + GeneralHelper::loan_total_fees($id) + GeneralHelper::loan_total_interest($id) + GeneralHelper::loan_total_principal($id) - GeneralHelper::loan_total_interest_waived($id));
        } else {
            return (GeneralHelper::loan_total_penalty($id, $date) + GeneralHelper::loan_total_fees($id,
                    $date) + GeneralHelper::loan_total_interest($id, $date) + GeneralHelper::loan_total_principal($id,
                    $date) - GeneralHelper::loan_total_interest_waived($id, $date));

        }

    }

    public static function loan_total_due_period($id, $date)
    {
        return (LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('penalty') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('fees') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('principal') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('interest') + LoanSchedule::where('loan_id', $id)->where('due_date',
                $date)->sum('interest_waived'));

    }

    public static function loan_total_paid_period($id, $date)
    {
        return LoanRepayment::where('loan_id', $id)->where('due_date', $date)->sum('amount');

    }

    public static function loans_total_paid($start_date = '', $end_date = '')
    {

        if (empty($start_date)) {
            $paid = 0;
            foreach (Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $paid = $paid + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->sum('credit');
            }
            return $paid;
        } else {
            $paid = 0;
            foreach (Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $paid = $paid + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->sum('credit');
            }
            return $paid;

        }

    }

    public static function diff_in_months(\DateTime $date1, \DateTime $date2)
    {
        $diff = $date1->diff($date2);

        $months = $diff->y * 12 + $diff->m + $diff->d / 30;

        return (int)round($months);
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
            return Expense::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->sum('amount');

        }

    }

    public static function total_payroll($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $payroll = 0;
            foreach (Payroll::where('branch_id', session('branch_id'))->get() as $key) {
                $payroll = $payroll + GeneralHelper::single_payroll_total_pay($key->id);
            }
            return $payroll;
        } else {
            $payroll = 0;
            foreach (Payroll::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $payroll = $payroll + GeneralHelper::single_payroll_total_pay($key->id);
            }
            return $payroll;

        }

    }

    public static function loans_total_principal($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $principal = $principal + LoanSchedule::where('loan_id', $key->id)->sum('principal');
            }
            return $principal;
        } else {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $principal = $principal + $key->principal;
            }
            return $principal;

        }

    }


    public static function total_other_income($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return OtherIncome::where('branch_id', session('branch_id'))->sum('amount');
        } else {
            return OtherIncome::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->sum('amount');

        }

    }

    public static function total_savings_interest($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'interest')->where('reversed', 0)->sum('debit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'interest')->where('reversed', 0)->whereBetween('date',
                [$start_date, $end_date])->sum('debit');

        }

    }

    public static function total_savings_deposits($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type', 'deposit')->where('reversed', 0)->sum('credit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'deposit')->where('reversed', 0)->whereBetween('date',
                [$start_date, $end_date])->sum('credit');

        }

    }

    public static function total_savings_transactions($id, $start_date = '', $end_date = '')
    {
        $interest = 0;
        $deposits = 0;
        $withdrawals = 0;
        $fees = 0;
        $guarantee = 0;
        $allocation = [];
        if (empty($start_date)) {
            foreach (SavingTransaction::where('savings_id', $id)->where('reversed', 0)->get() as $key) {
                if ($key->type == "interest") {
                    $interest = $interest + $key->credit;
                }
                if ($key->type == "deposit") {
                    $deposits = $deposits + $key->credit;
                }
                if ($key->type == "interest") {
                    $withdrawals = $withdrawals + $key->debit;
                }
                if ($key->type == "bank_fees") {
                    $fees = $fees + $key->credit;
                }
                if ($key->type == "guarantee") {
                    $guarantee = $guarantee + $key->credit;
                }
            }

        } else {
            foreach (SavingTransaction::where('savings_id', $id)->where('reversed', 0)->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                if ($key->type == "interest") {
                    $interest = $interest + $key->credit;
                }
                if ($key->type == "deposit") {
                    $deposits = $deposits + $key->credit;
                }
                if ($key->type == "interest") {
                    $withdrawals = $withdrawals + $key->debit;
                }
                if ($key->type == "bank_fees") {
                    $fees = $fees + $key->credit;
                }
                if ($key->type == "guarantee") {
                    $guarantee = $guarantee + $key->credit;
                }
            }

        }
        $allocation["interest"] = $interest;
        $allocation["deposits"] = $deposits;
        $allocation["withdrawals"] = $withdrawals;
        $allocation["fees"] = $fees;
        $allocation["guarantee"] = $guarantee;
        return $allocation;
    }

    public static function total_savings_withdrawals($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'withdrawal')->where('reversed', 0)->sum('credit');
        } else {
            return SavingTransaction::where('branch_id', session('branch_id'))->where('type',
                'withdrawal')->where('reversed', 0)->whereBetween('date',
                [$start_date, $end_date])->sum('credit');

        }

    }

    public static function total_capital($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            return Capital::where('branch_id', session('branch_id'))->where('type',
                    'deposit')->sum('amount') - Capital::where('branch_id', session('branch_id'))->where('type',
                    'withdrawal')->sum('amount');
        } else {
            return Capital::where('branch_id', session('branch_id'))->where('type',
                    'deposit')->sum('amount') - Capital::where('branch_id', session('branch_id'))->where('type',
                    'withdrawal')->sum('amount');

        }

    }

    public static function loans_total_paid_item($item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_product_total_paid_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_borrower_total_paid_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $amount = $amount + GeneralHelper::loan_terms_paid_item($key->id, $item);
            }
            return $amount;

        }

    }

    public static function loans_total_due_item($item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }

            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }
            }
            return $amount;
        }

    }

    public static function loans_product_total_due_items($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;

            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        } else {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->whereBetween('loan_schedules.due_date',
                [$start_date, $end_date])->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;
            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        }

    }

    public static function loans_product_total_paid_items($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_repayments', 'loans.id', '=',
                'loan_repayments.loan_id')->where('loan_repayments.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;

            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        } else {
            $interest = 0;
            $penalty = 0;
            $fees = 0;
            $principal = 0;
            foreach (Loan::where('loans.loan_product_id', $id)->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_repayments', 'loans.id', '=',
                'loan_repayments.loan_id')->whereBetween('loan_repayments.collection_date',
                [$start_date, $end_date])->where('loan_repayments.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;
            }
            return ["interest" => $interest, 'principal' => $principal, 'penalty' => $penalty, 'fees' => $fees];
        }

    }

    public static function loans_borrower_total_due_item($id, $item, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }

            }
            return $amount;
        } else {
            $amount = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                if ($item == 'principal') {
                    $amount = $amount + GeneralHelper::loan_total_principal($key->id);
                }
                if ($item == 'interest') {
                    $amount = $amount + GeneralHelper::loan_total_interest($key->id);
                }
                if ($item == 'fees') {
                    $amount = $amount + GeneralHelper::loan_total_fees($key->id);
                }
                if ($item == 'penalty') {
                    $amount = $amount + GeneralHelper::loan_total_penalty($key->id);
                }
            }
            return $amount;
        }

    }

    public static function loans_total_default($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->where('status', 'written_off')->get() as $key) {
                $principal = $principal + ($key->principal - GeneralHelper::loan_total_paid($key->id));
            }
            return $principal;
        } else {
            $principal = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->where('status',
                'written_off')->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $principal = $principal + ($key->principal - GeneralHelper::loan_total_paid($key->id));
            }
            return $principal;

        }

    }

    public static function loans_total_due($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $due = $due + GeneralHelper::loan_total_due_amount($key->id);
            }
            return $due;
        } else {
            $due = 0;
            foreach (Loan::where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + GeneralHelper::loan_total_due_amount($key->id);
            }
            return $due;

        }
    }

    public static function loans_count($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $due = $due + Loan::where('branch_id', session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->count();
            return $due;
        } else {
            $due = 0;
            $due = $due + Loan::where('branch_id', session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->count();
            return $due;

        }
    }

    public static function loans_product_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            $due = $due + Loan::where('loan_product_id', $id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->count();
            return $due;
        } else {
            $due = 0;
            $due = $due + Loan::where('loan_product_id', $id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->count();
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
            })->whereBetween('created_at',
                [$start_date, $end_date])->count();
            return $due;

        }
    }

    public static function payments_product_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;
        } else {
            $due = 0;
            foreach (Loan::where('loan_product_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;

        }
    }

    public static function payments_borrower_count($id, $start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;
        } else {
            $due = 0;
            foreach (Loan::where('borrower_id', $id)->where('branch_id', session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->count();
            }
            return $due;

        }
    }

    public static function borrower_loans_total_due($id)
    {

        $due = 0;
        foreach (Loan::whereIn('status',
            ['disbursed', 'closed', 'written_off'])->where('borrower_id', $id)->get() as $key) {
            $due = $due + GeneralHelper::loan_total_due_amount($key->id);
        }
        return $due;

    }

    public static function borrower_loans_total_paid($id)
    {

        $paid = 0;
        foreach (Loan::whereIn('status',
            ['disbursed', 'closed', 'written_off'])->where('borrower_id', $id)->get() as $key) {
            $paid = $paid + LoanTransaction::where('loan_id',
                    $key->id)->where('transaction_type',
                    'repayment')->where('reversed', 0)->sum('credit');
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

        $balance = 0;
        foreach (Savings::where('client_id', $id)->get() as $key) {
            $balance = $balance + GeneralHelper::savings_account_balance($key->id);
        }
        return $balance;

    }

    public static function total_client_savings_account_balance($id)
    {

        $balance = 0;
        $client_ids = [];
        foreach (Sentinel::findUserById($id)->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::findUserById($id)->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        foreach (Savings::where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('client_id', $client_ids)
                ->orWhereIn('group_id', $group_ids);
        })->get() as $key) {
            $balance = $balance + GeneralHelper::savings_account_balance($key->id);
        }
        return $balance;

    }


    public static function asset_valuation($id, $start_date = '')
    {

        if (empty($start_date)) {
            $value = 0;
            if (!empty(AssetValuation::where('asset_id', $id)->orderBy('date', 'desc')->first())) {
                $value = AssetValuation::where('asset_id', $id)->orderBy('date', 'desc')->first()->amount;
            }
            return $value;
        } else {
            $value = 0;
            if (!empty(AssetValuation::where('asset_id', $id)->where('date', '<=', $start_date)->orderBy('date',
                'desc')->first())
            ) {
                $value = AssetValuation::where('asset_id', $id)->where('date', '<=', $start_date)->orderBy('date',
                    'desc')->first()->amount;
            }
            return $value;

        }

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
                if (!empty(AssetValuation::where('asset_id', $key->id)->where('date', '<=',
                    $start_date)->orderBy('date',
                    'desc')->first())
                ) {
                    $value = AssetValuation::where('asset_id', $key->id)->where('date', '<=',
                        $start_date)->orderBy('date',
                        'desc')->first()->amount;
                }
            }
            return $value;

        }

    }

    public static function bank_account_balance($id)
    {

        return Capital::where('bank_account_id', $id)->where('branch_id', session('branch_id'))->where('type',
                'deposit')->sum('amount') - Capital::where('bank_account_id', $id)->where('branch_id',
                session('branch_id'))->where('type',
                'withdrawal')->sum('amount');
    }

    public static function send_sms($to, $msg)
    {
        if (Setting::where('setting_key', 'sms_enabled')->first()->setting_value == 1) {
            if (!empty(SmsGateway::find(Setting::where('setting_key',
                'active_sms')->first()->setting_value))
            ) {
                $active_sms = SmsGateway::find(Setting::where('setting_key',
                    'active_sms')->first()->setting_value);
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
        if (empty($start_date)) {
            $due = 0;
            foreach (ProductCheckin::where('branch_id', session('branch_id'))->get() as $key) {
                $due = $due + GeneralHelper::check_in_total_amount($key->id);
            }
            return $due;
        } else {
            $due = 0;
            foreach (ProductCheckin::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $due = $due + GeneralHelper::check_in_total_amount($key->id);
            }
            return $due;

        }
    }

    public static function check_outs_total_amount($start_date = '', $end_date = '')
    {
        if (empty($start_date)) {
            $due = 0;
            foreach (ProductCheckout::where('branch_id', session('branch_id'))->get() as $key) {
                $due = $due + GeneralHelper::check_in_total_amount($key->id);
            }
            return $due;
        } else {
            $due = 0;
            foreach (ProductCheckout::where('branch_id', session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
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
        foreach (LoanRepayment::where('loan_id', $schedule->loan_id)->orderBy('collection_date',
            'asc')->get() as $payment) {
            $payments = $payments + $payment->amount;
        }
    }

    public static function loan_items($id, $start_date = '', $end_date = '')
    {
        $allocation = [];
        $loan = Loan::find($id);
        $principal = 0;
        $principal_paid = 0;
        $principal_written_off = 0;
        $fees = 0;
        $fees_paid = 0;
        $penalty = 0;
        $penalty_paid = 0;
        $penalty_written_off = 0;
        $interest_waived = 0;
        $penalty_waived = 0;
        $fees_waived = 0;
        $fees_written_off = 0;
        $principal_waived = 0;
        $interest = 0;
        $interest_paid = 0;
        $interest_written_off = 0;
        foreach ($loan->repayment_schedules as $schedule) {
            $principal = $principal + $schedule->principal;
            $interest = $interest + $schedule->interest;
            $penalty = $penalty + $schedule->penalty;
            $fees = $fees + $schedule->fees;
            $principal_paid = $principal_paid + $principal_paid;
            $interest_paid = $interest_paid + $schedule->interest_paid;
            $penalty_paid = $penalty_paid + $schedule->penalty_paid;
            $fees_paid = $fees_paid + $schedule->fees_paid;
            $principal_waived = $principal_waived + $schedule->principal_waived;
            $interest_waived = $interest_waived + $schedule->interest_waived;
            $penalty_waived = $penalty_waived + $schedule->penalty_waived;
            $fees_waived = $fees_waived + $schedule->fees_waived;

            $principal_written_off = $principal_written_off + $schedule->principal_written_off;
            $interest_written_off = $interest_written_off + $schedule->interest_written_off;
            $penalty_written_off = $penalty_written_off + $schedule->penalty_written_off;
            $fees_written_off = $fees_written_off + $schedule->fees_written_off;

        }
        $allocation["principal"] = $principal;
        $allocation["interest"] = $interest;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;

        $allocation["interest_waived"] = $interest_waived;
        $allocation["principal_waived"] = $principal_waived;
        $allocation["penalty_waived"] = $penalty_waived;
        $allocation["fees_waived"] = $fees_waived;

        $allocation["interest_paid"] = $interest_paid;
        $allocation["principal_paid"] = $principal_paid;
        $allocation["penalty_paid"] = $penalty_paid;
        $allocation["fees_paid"] = $fees_paid;

        $allocation["interest_written_off"] = $interest_written_off;
        $allocation["principal_written_off"] = $principal_written_off;
        $allocation["penalty_written_off"] = $penalty_written_off;
        $allocation["fees_written_off"] = $fees_written_off;

        return $allocation;
    }

    public static function loan_due_items($id, $start_date = '', $end_date = '')
    {
        $allocation = [];
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest = 0;
        if (empty($start_date)) {
            $schedules = LoanSchedule::where('loan_id', $id)->get();
        } else {
            $schedules = LoanSchedule::where('loan_id', $id)->whereBetween('due_date',
                [$start_date, $end_date])->get();
        }
        foreach ($schedules as $schedule) {
            $interest = $interest + $schedule->interest;
            $penalty = $penalty + $schedule->penalty;
            $fees = $fees + $schedule->fees;
            $principal = $principal + $schedule->principal;
        }
        $allocation["principal"] = $principal;
        $allocation["interest"] = $interest;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;
        return $allocation;
    }

    public static function schedule_due_amount($id)
    {
        $schedule = LoanSchedule::find($id);
        $amount = 0;
        $payments = LoanRepayment::where('loan_id', $schedule->loan_id)->sum('amount');
        foreach (LoanSchedule::where('due_date', '<=', $schedule->due_date)->where('loan_id',
            $schedule->loan_id)->get() as $key) {
            if ($key->id != $id) {
                $payments = $payments - ($key->interest + $key->penalty + $key->fees + $key->principal);
            }
        }
        if ($payments > 0 && $payments > ($schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal)) {
            $amount = 0;
        } elseif ($payments > 0 && $payments < ($schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal)) {
            $amount = $schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal - $payments;
        } else {
            $amount = $schedule->interest + $schedule->penalty + $schedule->fees + $schedule->principal;
        }
        return $amount;
    }

    public static function loans_paid_items($start_date = '', $end_date = '')
    {
        $allocation = [];
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest = 0;
        $interest_waived = 0;
        $over_payments = 0;
        if (empty($start_date)) {
            $principal = $principal + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type', 'repayment_principal')->where('reversed',
                    0)->where('branch_id', session('branch_id'))->sum('credit');
            $interest = $interest + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_interest')->where('reversed', 0)->where('branch_id',
                    session('branch_id'))->sum('credit');
            $fees = $fees + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_fees')->where('reversed', 0)->where('branch_id', session('branch_id'))->sum('credit');
            $penalty = $penalty + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_penalty')->where('reversed', 0)->where('branch_id', session('branch_id'))->sum('credit');
            $over_payments = $over_payments + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type',
                    'overpayment')->where('reversed', 0)->where('branch_id', session('branch_id'))->sum('credit');
        } else {

            $principal = $principal + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type', 'repayment_principal')->where('reversed',
                    0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $interest = $interest + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_interest')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $fees = $fees + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_fees')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $penalty = $penalty + JournalEntry::where('transaction_type', 'repayment')->where('transaction_sub_type',
                    'repayment_penalty')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
            $over_payments = $over_payments + JournalEntry::where('transaction_type',
                    'repayment')->where('transaction_sub_type',
                    'overpayment')->where('reversed', 0)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id', session('branch_id'))->sum('credit');
        }

        $allocation["principal"] = $principal;
        $allocation["interest"] = $interest;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;
        $allocation["over_payments"] = $over_payments;
        return $allocation;
    }

    public static function loans_due_items($start_date = '', $end_date = '')
    {
        $allocation = [];
        $principal = 0;
        $fees = 0;
        $penalty = 0;
        $interest = 0;

        if (empty($start_date)) {
            foreach (Loan::select("loan_schedules.principal", "loan_schedules.interest", "loan_schedules.penalty",
                "loan_schedules.fees")->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;

            }

        } else {
            foreach (Loan::select("loan_schedules.principal", "loan_schedules.interest", "loan_schedules.penalty",
                "loan_schedules.fees")->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->whereBetween('loan_schedules.due_date',
                [$start_date, $end_date])->where('loan_schedules.deleted_at', NULL)->get() as $key) {
                $interest = $interest + $key->interest;
                $penalty = $penalty + $key->penalty;
                $fees = $fees + $key->fees;
                $principal = $principal + $key->principal;
            }
        }

        $allocation["principal"] = $principal;
        $allocation["interest"] = $interest;
        $allocation["fees"] = $fees;
        $allocation["penalty"] = $penalty;
        return $allocation;
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
        $amount = Loan::selectRaw(DB::raw('COALESCE(SUM(principal),0) principal'))->whereIn('status', ['disbursed', 'closed', 'written_off'])->where('loan_officer_id',$user_ids->id)->when($start_date, function ($query) use ($start_date, $end_date) {
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
        $amount = Loan::selectRaw(DB::raw('COALESCE(SUM(principal),0) principal'))->whereIn('status', ['disbursed', 'closed', 'written_off'])->where('office_id',$user_ids->office_id)->when($start_date, function ($query) use ($start_date, $end_date) {
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
        $amount = DB::table("loan_transactions as lt")->selectRaw(DB::raw('COALESCE(SUM(credit),0) amount'))->where('reversed', 0)->where('transaction_type', 'repayment')->where('loan_officer_id',$user_ids->id)->join('loans as l', "l.id", "=", "lt.loan_id")->when($start_date, function ($query) use ($start_date, $end_date) {
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
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->where('loan_officer_id',$user_ids->id)->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date, $end_date){
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
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date, $end_date){
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
        $amount = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) balance'))->where('loan_officer_id',$user_ids->id)->join('loans as l', "l.id", "=", "lr.loan_id")->where('l.status', 'disbursed')->when($start_date, function ($query) use ($start_date) {
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
        $date = date_format(date_sub(date_create(date("Y-m-d")),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        for ($i = 1; $i <= 13; $i++) {
            $d = explode('-', $date);
            $actual = 0;
            $expected = 0;
            $actual = $actual + LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->where('year',
                    $d[0])->where('month',
                    $d[1])->sum('credit');
            $repayment_schedules = DB::table("loan_repayment_schedules as lr")->selectRaw(DB::raw('(COALESCE(SUM(lr.interest),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)+COALESCE(SUM(lr.principal),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)+COALESCE(SUM(lr.fees),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.penalty_waived),0)-COALESCE(SUM(lr.penalty_written_off),0)) balance'))->where('year',
                $d[0])->where('month',
                $d[1])->first();
            if (!empty($repayment_schedules)) {
                $expected = $repayment_schedules->balance;
            }
            array_push($collection_overview, array(
                'month' => date_format(date_create($date),
                    'M' . ' ' . $d[0]),
                'actual' => $actual,
                'expected' => $expected
            ));
            $date = date_format(date_add(date_create($date),
                date_interval_create_from_date_string('1 months')),
                'Y-m-d');
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
}
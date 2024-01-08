<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\UpcomingRepaymentReminderEmail;
use App\Models\Client;
use App\Models\Group;
use App\Models\Loan;
use App\Models\LoanProductCharge;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\PaymentType;
use App\Models\Savings;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class TestController extends Controller
{
    public function __construct()
    {

    }


    public function index()
    {

        //Calculate savings interest
        echo Carbon::parse("last day of " . Carbon::today()->addMonthsNoOverflow(3)->format("M"))->format("Y-m-d").'<br>';
        foreach (Savings::with('savings_product')->get() as $savings) {
            $savings_product = $savings->savings_product;
            if (Carbon::parse($savings->start_interest_calculation_date)->gt(Carbon::parse("first day of ". Carbon::today()->subMonths(11)->format("M")))) {
                $start_date = $savings->start_interest_calculation_date;
            } else {
                $start_date = Carbon::parse("first day of ". Carbon::today()->subMonths(11))->format("Y-m-d");
            }
            echo $start_date.'<br>';
            echo GeneralHelper::determine_next_interest_calculation_date($savings->id);
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
                    $interest = $interest + ( $average_balance * $total_days * $savings_product->interest_rate / (100 * 365));
                }
            }

            echo $interest;

        }

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


}

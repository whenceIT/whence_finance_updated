@extends('layouts.master')
@section('title')
    Daily figures 
@endsection
@section('content')
<div class="row">
<?php
$use = date('Y-m-');
$todaysDate = date('Y-m-d');
$newTodaysDate =  date('Y-m-d', strtotime($todaysDate. ' + 3 months'));
$branchtargetDate = $use.'24';
$branchtargetDate = date('Y-m-d',strtotime($branchtargetDate));

if($todaysDate > $branchtargetDate){
    $branchtargetDate = date('Y-m-d',strtotime($branchtargetDate. ' + 1 months'));
}
$branchcompareDate = date('Y-m-d',strtotime($branchtargetDate. ' - 1 months'));
$branchzeroDate = date('Y-m-d', strtotime($branchtargetDate. ' - 3 months'));
$branchfirstDate = date('Y-m-d', strtotime($branchtargetDate. ' - 2 months'));
$provincefirstDate = date('Y-m-d', strtotime($branchtargetDate. ' - 2 months'));
$branchsecondDate = date('Y-m-d', strtotime($branchtargetDate. ' - 1 months'));


//BRANCH CYCLE OPENING UNCOLLECTED VARIABLES
$MoneyGivenOut = 0;
$MoneyCollected = 0;
$charges = 0;
$cycle_opening_uncollected_amount = 0;


//BRANCH COLLECTED TOTAL CALCULATIONS
$collected_total = 0;
$full_payments = 0;
$part_payments = 0;
$reloan_payments = 0;
$reloan_amount = 0;

// BRANCH COLLECTED TOTAL CALCULATIONS 1 MONTH AGO
$collected_total_1_months = 0;
$reloan_amount_1_months = 0;
$full_payments_1_months  = 0;
$part_payments_1_months  = 0;
$reloan_payments_1_months  = 0;

//BRANCH COLLECTED TOTAL CALCULATIONS 2 MONTHS AGO
$collected_total_2_months = 0;
$reloan_amount_2_months = 0;
$full_payments_2_months  = 0;
$part_payments_2_months  = 0;
$reloan_payments_2_months  = 0;

//GIVEN OUT AMOUNTS
$given_out_total = 0;
$new_loans_given_out = 0;
$reloans_given_out = 0;

//COUNTS
$bar_chart_count = 0;
$collections_count_fullpayment = 0;
$collections_count_reloan = 0;
$collections_count_partpayment = 0;
$givenout_count_newloan = 0;
$givenout_count_reloan = 0;
$given_out_count_total = 0;
$trans_id = 0;
$trans_id_int = 0;
?>

<?php
$collections = [];
$given_out = [];
$target_dates = [];
while($bar_chart_count < 5){


    $branchtargetDateAlgo = date('Y-m-d',strtotime($branchtargetDate. ' - '. $bar_chart_count.'months'));
    $branchcompareDateAlgo = date('Y-m-d',strtotime($branchcompareDate. ' - '. $bar_chart_count.'months'));
    $reloan_payments = 0;
    $reloan_amount = 0;
    $full_payments = 0;
    $part_payments = 0;
    $new_loans_given_out = 0;
    $reloans_given_out = 0;
    $interest = 0;
    $disbursement_interest = 0;
    $principal = 0;
   // {{date("jS M, Y", strtotime($compareDate))}} 
    array_push($target_dates,date("jS M, Y",strtotime($branchtargetDateAlgo)));

    foreach($allTransactions as $transaction){
        if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
            $full_payments = $full_payments + $transaction->credit;
            if($bar_chart_count == 4){
                $collections_count_fullpayment = $collections_count_fullpayment + 1;
            }
        }
        
        if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
            $part_payments = $part_payments + $transaction->credit;
            if($bar_chart_count == 4){
                $collections_count_partpayment = $collections_count_partpayment + 1;
            }
        }
        
        if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
        
            $reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
            $interest = $transaction->credit/0.4;
            $reloan_payments = $reloan_payments + $reloan_amount + $interest; 
            if($bar_chart_count == 4){
                $collections_count_reloan = $collections_count_reloan + 1;
            }
        }

        if($transaction->transaction_type == 'disbursement' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo){
            $disbursement_interest = $transaction->debit/0.4;
            $new_loans_given_out = $new_loans_given_out + $transaction->debit + $disbursement_interest ;
            if($bar_chart_count == 4){
                $givenout_count_newloan = $givenout_count_newloan + 1;
            }
        }
        
        if($transaction->transaction_type == 'interest' && $transaction->date > $branchcompareDateAlgo  && $transaction->date <= $branchtargetDateAlgo){
            $principal = $transaction->debit/0.4;
            $reloans_given_out = $reloans_given_out + $principal + $transaction->debit;
            if($bar_chart_count == 4){
                $givenout_count_reloan = $givenout_count_reloan + 1;
            }
        }
    }
    $given_out_count_total = 
    $collected_total = $reloan_payments + $part_payments + $full_payments;
    $given_out_total = $new_loans_given_out + $reloans_given_out;
    array_push($collections,$collected_total);
    array_push($given_out,$given_out_total);
    // $collections = array_reverse($collections);
    // $given_out = array_reverse($given_out);
    $bar_chart_count = $bar_chart_count + 1;

}
?>

</div>
@endsection
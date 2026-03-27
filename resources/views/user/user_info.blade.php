@extends('layouts.master')
@section('title')
    Staff Information
@endsection
@section('content')
<div>
<?php 
$reloansCount = 0; 
$todaysDate = date('Y-m-d');
$yesterdaysDate = date('Y-m-d',strtotime($todaysDate. ' - 1 days'));
$defaulterCount = 0;
//COLLECTIONS
$full_payments = 0;
$reloan_payments = 0;
$part_payments = 0;
$reloan_amount = 0;
$collected_today = 0;
$cycle_full_payments = 0;
$cycle_reloan_payments = 0;
$cycle_part_payments = 0;
$cycle_reloan_amount = 0;
$cycle_collected_total = 0;

//MONEY GIVEN OUT
 $new_loans_today = 0;
 $new_reloans_today = 0;
 $cycle_opening_uncollected_amount = 0;
 $new_loans_cycle = 0;
 $new_reloans_cycle = 0;
 //CYCLE END DATE
 //
 $projected_salary = 4000;
 $num = 1;
$use = date('Y-m-');
$todaysDate = date('Y-m-d');
$targetDate = $use.'24';
$targetDate = date('Y-m-d',strtotime($targetDate));
if($todaysDate > $targetDate){
    $targetDate = date('Y-m-d',strtotime($targetDate. ' + 1 months'));
}
$compareDate = date('Y-m-d',strtotime($targetDate. ' - 1 months'));
?>
<h2 style="display: flex;
    align-items: center;
    justify-content: center;
	font-size: 30px; 
    color: #2c3e50; 
    text-transform: uppercase; 
    letter-spacing: 2px; 
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); 
    font-family: 'Arial', sans-serif;
">{{ $user->first_name}} {{ $user->last_name}}</h2>
     
                            
                        <?php 
                        foreach($userLoans as $userLoan){

                            $MoneyCollected = 0;
                            $MoneyGivenOut = 0;
                            $charges = 0;
                            $balance = 0;

                            if($userLoan->status == 'disbursed' && $userLoan->first_repayment_date < $todaysDate){
                                $defaulterCount = $defaulterCount + 1;
                            }
                            foreach($userLoan->transactions as $transaction){
                                if($userLoan->status == 'disbursed' && $transaction->payment_apply_to == 'reloan_payment'){
                                    $reloansCount = $reloansCount + 1;
                                    break;
                                 }
            
                            }

                            foreach($userLoan->transactions as $transaction){
                                //COLLECTIONS
                                if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $yesterdaysDate && $transaction->date <= $todaysDate){
                                    $full_payments = $full_payments + $transaction->credit;
                                }
                                
                                if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $yesterdaysDate && $transaction->date <= $todaysDate){
                                    $part_payments = $part_payments + $transaction->credit;
                                }
                                
                                if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $yesterdaysDate && $transaction->date <= $todaysDate){
                                
                                    $reloan_amount = $transaction->balance_bf;
                                    $interest = $transaction->credit/0.4;
                                    $reloan_payments = $reloan_payments + $reloan_amount;  
                                }


                            }


                            foreach($userLoan->transactions as $transaction){
                                if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
                                    $cycle_full_payments = $cycle_full_payments + $transaction->credit;
                                }
                                
                                if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
                                    $cycle_part_payments = $cycle_part_payments + $transaction->credit;
                                }
                                
                                if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
                                
                                    $cycle_reloan_amount = $transaction->balance_bf;
                                    $cycle_interest = $transaction->credit/0.4;
                                    $cycle_reloan_payments = $cycle_reloan_payments + $cycle_reloan_amount;
                                }


                            }

                            foreach($userLoan->transactions as $transaction){
                                if($transaction->transaction_type == 'disbursement' && $transaction->date > $yesterdaysDate && $transaction->date <= $todaysDate){
                                    $new_loans_today = $new_loans_today + $transaction->debit;
                                }

                                if($transaction->transaction_type == 'interest' && $transaction->date > $yesterdaysDate && $transaction->date <= $todaysDate){
                                    $principal = $transaction->debit/0.4;
                                    $new_reloans_today = $new_reloans_today + $principal;
                                    $reloanTransAmount = $transaction->debit/0.4;
                                }
                            }

                            foreach($userLoan->transactions as $transaction){
                                if($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
                                    $new_loans_cycle = $new_loans_cycle + $transaction->debit;
                                }

                                if($transaction->transaction_type == 'interest' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
                                    $principal = $transaction->debit/0.4;
                                    $new_reloans_cycle = $new_reloans_cycle + $principal;
                                    $reloanTransAmount = $transaction->debit/0.4;
                                }
                            }

                            foreach($userLoan->transactions as $transaction){
                                if($transaction->date <= $compareDate && $transaction->transaction_type != 'specified_due_date_fee'){
                                    $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
                                }
                                
                                if($transaction->date <= $compareDate){
                                    $MoneyCollected = $MoneyCollected + $transaction->credit;
                                }
                                
                                
                                if($transaction->transaction_type == 'specified_due_date_fee' && $transaction->date <= $compareDate){
                                    $charges = $charges + $transaction->debit;
                                }
                                
                            }
                            $balance = $MoneyGivenOut - $MoneyCollected;
                            $cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
                        }
                        
                        ?>
                        <?php 
                           $target_total = $new_loans_today + $new_reloans_today;
                           $target_total_cycle = $new_loans_cycle + $new_reloans_cycle;
                           $collected_today = $full_payments + $part_payments + $reloan_payments;
                           $cycle_collected_total = $cycle_full_payments + $cycle_part_payments + $cycle_reloan_payments;
                        ?>
<?php
$cycle_opening_uncollected_amount = 0.0001;
?>

<!-- CALCULATING LOAN BALANCES FOR CYCLE OPENING UNCOLLECTED -->
<?php
$today = date('Y-m-d');
$currrent_date = date('Y-m');
$cycle_date = $currrent_date.'-'.'24';
if($today > $cycle_date){
    $cycle_date = date('Y-m-d',strtotime($cycle_date. '+ 1 months'));
}

$cycle_start = date('Y-m-d',strtotime($cycle_date. '- 1 months'));
$cycle_opening_uncollected_amounts = [];
for($x=24; $x>-1; $x--){
    $cycle_opening_uncollected_amount = 0;
    foreach($userLoans as $loan){
        $MoneyCollected = 0;
$MoneyGivenOut = 0;
$balance = 0;


        foreach($loan->transactions as $transaction){
            if($transaction->date <= date('Y-m-d',strtotime($cycle_start. '-' .$x. 'months')) && $transaction->transaction_type != 'specified_due_date_fee'){
                $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
            }
            
            if($transaction->date <= date('Y-m-d',strtotime($cycle_start. '-' .$x. 'months'))){
                $MoneyCollected = $MoneyCollected + $transaction->credit;
            }
            
            
         
        }

        $balance = $MoneyGivenOut - $MoneyCollected;
        $cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
    }

array_push($cycle_opening_uncollected_amounts ,$cycle_opening_uncollected_amount);

}
?>


                        
<!-- CALCULATING 12 MONTHS CYCLE COLLECTED USING TRANSACTIONS -->
                        <?php
                        $collected_amounts = [];
                        $today = date('Y-m-d');
                        $currrent_date = date('Y-m');
                        $cycle_date = $currrent_date.'-'.'24';
                        if($today > $cycle_date){
                            $cycle_date = date('Y-m-d',strtotime($cycle_date. '+ 1 months'));
                        }
                        $cycle_start = date('Y-m-d',strtotime($cycle_date. '- 1 months'));
                        
                        
                        for($x=24; $x>-1; $x--){
                            foreach($userTransactions as $transaction){
                                if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > date('Y-m-d',strtotime($cycle_start. '-' .$x. 'months')) && $transaction->date <= date('Y-m-d',strtotime($cycle_date.  '-' .$x. 'months'))){
                                    $full_payments = $full_payments + $transaction->credit;
                                }
                        
                                if($transaction->payment_apply_to == 'part_payment' && $transaction->date > date('Y-m-d',strtotime($cycle_start. '-' .$x. 'months')) && $transaction->date <= date('Y-m-d',strtotime($cycle_date.  '-' .$x. 'months'))){
                                    $part_payments = $part_payments + $transaction->credit;
                                }
                        
                                if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > date('Y-m-d',strtotime($cycle_start. '-' .$x. 'months')) && $transaction->date <= date('Y-m-d',strtotime($cycle_date.  '-' .$x. 'months'))){
                        
                                    $reloan_amount = $transaction->balance_bf; //+ ($transaction->credit/0.4);
                                    $interest = $transaction->credit/0.4;
                                    $reloan_payments = $reloan_payments + $reloan_amount;
                                
                                }
                            }
                        
                            array_push($collected_amounts,($full_payments + $part_payments + $reloan_payments));
                            $full_payments = 0;
                            $part_payments = 0;
                            $reloan_payments = 0;
                        
                        
                         }
                        ?>


<!-- CALCULATING 12 MONTHS GIVEN OUT USING TRANSACTIONS -->
<?php
$new_loan_total = 0;
$reloan_total = 0;
$targets = [];
$transaction_total = 0;
$carry_over = 0;
$today = date('Y-m-d');
$dates = [];
$colors = [];
$currrent_date = date('Y-m');
$cycle_date = $currrent_date.'-'.'24';
if($today > $cycle_date){
    $cycle_date = date('Y-m-d',strtotime($cycle_date. '+ 1 months'));
}
$cycle_start = date('Y-m-d',strtotime($cycle_date. '- 1 months'));
for($x=24; $x>-1; $x--){
    foreach($userTransactions as $transaction){
        if($transaction->transaction_type == 'disbursement' && $transaction->date > date('Y-m-d',strtotime($cycle_start. '-' .$x. 'months')) && $transaction->date <= date('Y-m-d',strtotime($cycle_date.  '-' .$x. 'months'))){
            $new_loan_total = $transaction->debit;
        }

        if($transaction->transaction_type == 'interest' && $transaction->date > date('Y-m-d',strtotime($cycle_start. '-' .$x. 'months')) && $transaction->date <= date('Y-m-d',strtotime($cycle_date.  '-' .$x. 'months'))){
            $principal = $transaction->debit/0.4;
            $reloan_total = $principal;
        }

        if($transaction_total + $new_loan_total + $reloan_total >= 40000 ){
            if($transaction_total == 40000){
                $carry_over = $carry_over + $new_loan_total + $reloan_total;
            }else{
                $carry_over = 40000 - ($transaction_total + $new_loan_total + $reloan_total);
                $transaction_total = 40000;
            }
           
        }else{
            $transaction_total = $transaction_total + $new_loan_total + $reloan_total; //+ $carry_over;
            $carry_over = 0;
        }
      
        $new_loan_total = 0;
        $reloan_total = 0;
    }

    array_push($targets,$transaction_total);
    if($transaction_total < 40000){
        array_push($colors,'#ff1c4b');
    }else{
        array_push($colors,'#57b7fa');
    }
    array_push($dates,date("jS M, Y", strtotime($cycle_date. '-' .$x. 'months')));
    $transaction_total = 0;
    $total = $reloan_total + $new_loan_total;
}

?>




<?php 
$uncollected_amounts = [];
for($x=0; $x<24; $x++){
    array_push($uncollected_amounts,($cycle_opening_uncollected_amounts[$x] - $collected_amounts[$x]));
}


$target_count = 0;
foreach($targets as $target){
    if($target >= 40000){
        $target_count = $target_count + 1;
    }
}

if($target_count >= 7){
    $projected_salary = 5000;
}

?>
                   
<div>

<div class="box box-primary">

    {{-- BOX HEADER --}}
    <div class="box-header with-border text-center">
        <h3 class="box-title">
             Performance Summary
            {{ date("jS M, Y", strtotime($start)) }}
            to
            {{ date("jS M, Y", strtotime($end)) }}
        </h3>
    </div>

    {{-- BOX BODY --}}
    <div class="box-body">

        {{-- FILTER FORM --}}
        <div style="background:#f9fafc; padding:25px; border-radius:16px; margin-bottom:25px;">
            <form method="GET" action="{{ url('user/'.$userId.'/staff_info') }}" class="form-horizontal">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">

                        {{-- Cycle Start --}}
                        <div class="form-group text-center">
                            <label class="control-label">Cycle Start</label>
                            <input type="month" name="start_month" class="form-control"
                                value="{{ substr($start, 0, 7) }}">
                        </div>

                        {{-- Cycle End --}}
                        <div class="form-group text-center">
                            <label class="control-label">Cycle End</label>
                            <input type="month" name="end_month" class="form-control"
                                value="{{ substr($end, 0, 7) }}">
                        </div>

                        {{-- Load Button --}}
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                Load
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <hr>

        <div class="text-center" style="margin-bottom: 20px;">
            <button type="button" class="btn btn-success" id="toggleView">
                <i class="fa fa-book"></i> Ledger
            </button>
        </div>

        {{-- SUMMARY --}}
        <div id="summaryView">

            @if(!$data)
                <p>No data available or failed to fetch.</p>
            @else

                <div class="row" style="margin-bottom: 25px;">

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ number_format($data['total_uncollected']) }}</h3>
                                <p>Cycle Opening Uncollected</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-warning"></i>
                            </div>
                        </div>
                    </div>

                            <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3>{{ number_format($data['uncollected_without_charges']) }}</h3>
                                <p>Cycle Opening Uncollected (without charges)</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-warning"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{ number_format($data['total_collected']) }}</h3>
                                <p>Total Collected</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                        <h3>{{ number_format(max(0, $data['still_uncollected'])) }}</h3>
                                <p>Still Uncollected</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>{{ number_format($data['given_out']) }}</h3>
                                <p>Given Out</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>


                        <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>{{ number_format($data['carry_over'] ?? 0) }}</h3>
                                <p>Carry Over</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>

        {{-- LEDGER VIEW --}}
        <div id="ledgerView" style="display: none;">

            {{-- TOGGLE SWITCH --}}
            <div class="ledger-toggle text-center">
                <div class="toggle-wrapper">
                    <div class="toggle-slider"></div>

                    <button class="toggle-btn active" data-target="collections">
                        Cycle Opening Uncollected
                    </button>
                    <button class="toggle-btn" data-target="disbursements">
                        Total Cycle Collected
                    </button>
                    <button class="toggle-btn" data-target="adjustments">
                        Total Cycle Given Out
                    </button>
                </div>
            </div>

            {{-- LEDGER SECTIONS --}}

            {{-- Collections --}}
            <div class="ledger-section" id="collections">
                <p class="text-muted text-center">Cycle Opening Uncollected</p>
                    <p class="text-muted text-center" style="margin-top: 8px;">
            <i class="fa fa-info-circle"></i>
These are the balances of all your loans as of  {{ date("jS M, Y", strtotime($start)) }}. Please note that any charges do not increase your uncollected balance, while loans with interest waivers reduce the uncollected amount accordingly.
        </p>

                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-bordered table-striped" id="cycleOpeningTable">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client Name</th>
                                <th>Amount Due</th>
                                <th>Balance</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Disbursements --}}
            <div class="ledger-section" id="disbursements" style="display:none;">
                <p class="text-muted text-center">Total Cycle Collected</p>

                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-bordered table-striped" id="totalCollectedTable">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client Name</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Adjustments --}}
            <div class="ledger-section" id="adjustments" style="display:none;">
                <p class="text-muted text-center">Total Cycle Given Out</p>

                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-bordered table-striped" id="givenOutTable">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client Name</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>

  <div style="margin-bottom:30px; margin-top:30px;">
                    <p
                        style="display: flex;
                                                                                                                                                                                                            align-items: center;
                                                                                                                                                                                                            justify-content: center; font-size:50px;">
                        PDUA%
                    </p>
                    <div
                        style="display: flex;
                                                                                                                                                                                                            align-items: center;
                                                                                                                                                                                                            justify-content: center;">

                        <div class="gauge"
                            style="width: 100%;
                                                                                                                                                                                                          max-width: 250px;
                                                                                                                                                                                                          font-size: 50px;
                                                                                                                                                                                                          color: #004033;">
                            <div class="gauge__body"
                                style=" width: 100%;
                                                                                                                                                                                                          height: 0;
                                                                                                                                                                                                          padding-bottom: 50%;
                                                                                                                                                                                                          background: #b4c0be;
                                                                                                                                                                                                          position: relative;
                                                                                                                                                                                                          border-top-left-radius: 100% 200%;
                                                                                                                                                                                                          border-top-right-radius: 100% 200%;
                                                                                                                                                                                                          overflow: hidden;">

                                @if(($data['pdua']) < 0.75)
                                    <div class="gauge__fill"
                                        style=" position: absolute;
                                                                                                                                                                                                                                                                                          top: 100%;
                                                                                                                                                                                                                                                                                          left: 0;
                                                                                                                                                                                                                                                                                          width: inherit;
                                                                                                                                                                                                                                                                                          height: 100%;
                                                                                                                                                                                                                                                                                          background: red;
                                                                                                                                                                                                                                                                                          transform-origin: center top;
                                                                                                                                                                                                                                                                                          transform: rotate(0.25turn);
                                                                                                                                                                                                                                                                                          transition: transform 0.2s ease-out;">
                                    </div>

                                @elseif(($data['pdua']) >= 0.90)
                                    <div class="gauge__fill"
                                        style=" position: absolute;
                                                                                                                                                                                                                                                                                          top: 100%;
                                                                                                                                                                                                                                                                                          left: 0;
                                                                                                                                                                                                                                                                                          width: inherit;
                                                                                                                                                                                                                                                                                          height: 100%;
                                                                                                                                                                                                                                                                                          background:#d4af37;
                                                                                                                                                                                                                                                                                          transform-origin: center top;
                                                                                                                                                                                                                                                                                          transform: rotate(0.25turn);
                                                                                                                                                                                                                                                                                          transition: transform 0.2s ease-out;">
                                    </div>

                                @else
                                    <div class="gauge__fill"
                                        style=" position: absolute;
                                                                                                                                                                                                                                                                                          top: 100%;
                                                                                                                                                                                                                                                                                          left: 0;
                                                                                                                                                                                                                                                                                          width: inherit;
                                                                                                                                                                                                                                                                                          height: 100%;
                                                                                                                                                                                                                                                                                          background:green;
                                                                                                                                                                                                                                                                                          transform-origin: center top;
                                                                                                                                                                                                                                                                                          transform: rotate(0.25turn);
                                                                                                                                                                                                                                                                                          transition: transform 0.2s ease-out;">
                                    </div>

                                @endif
                                <div class="gauge__cover"
                                    style="width: 75%;
                                                                                                                                                                                                          height: 150%;
                                                                                                                                                                                                          background: #f7f7f7;
                                                                                                                                                                                                          border-radius: 50%;
                                                                                                                                                                                                          position: absolute;
                                                                                                                                                                                                          top: 25%;
                                                                                                                                                                                                          left: 50%;
                                                                                                                                                                                                          transform: translateX(-50%);

                                                                                                                                                                                                          /* Text */
                                                                                                                                                                                                          display: flex;
                                                                                                                                                                                                          align-items: center;
                                                                                                                                                                                                          justify-content: center;
                                                                                                                                                                                                          padding-bottom: 25%;
                                                                                                                                                                                                          box-sizing: border-box;">
                                </div>

                            </div>
                        </div>


                    </div>

                    <div
                        style="display:flex; flex-direction:row; justify-content:space-between;
                                                                                                                                                                                                            align-items: center;
                                                                                                                                                                                                            justify-content: center;">
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div
                                style="background-color: red;  height: 10px;
                                                                                                                                                                                                          width: 20px;">
                            </div>
                            <p style="text-align: center; font-weight:bold;">Poor</p>
                        </div>

                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div
                                style="background-color: green;  height: 10px;
                                                                                                                                                                                                          width: 20px;">
                            </div>
                            <p style="text-align: center; font-weight:bold;">Fair</p>
                        </div>
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div
                                style="background-color: #d4af37;  height: 10px;
                                                                                                                                                                                                          width: 20px;">
                            </div>
                            <p style="text-align: center; font-weight:bold;">Good</p>
                        </div>

                    </div>
                </div>

</div>



<div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Loans Information</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">

                        <div class="col-md-4">
                            <span class="info-box-text">Cycle ends on</span>
                            @if(!empty($cycleDate->cycle_end_date))
                            <span class="info-box-number">{{date("jS M", strtotime($targetDate))}}</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <span class="info-box-text">Closed Loans</span>
                            <span class="info-box-number">{{\App\Models\Loan::where('loan_officer_id',$user->id)->where('status','closed')->count()}}</span>
                        </div>

                        <div class="col-md-4">
                            <span class="info-box-text">Active loans</span>
                            <span class="info-box-number">{{\App\Models\Loan::where('loan_officer_id',$user->id)->where('status','disbursed')->count()}}</span>
                        </div>

                        <div class="col-md-4">
                           <span class="info-box-text">Clients</span>
                            <span class="info-box-number">
                                <a href="{{ url('client/staff/'.$user->id.'/clients') }}">
                                    {{\App\Models\Client::where('staff_id',$user->id)->where('status','active')->count()}}
                                </a>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <span class="info-box-text">Current Reloans</span>
                            <span class="info-box-number">{{$reloansCount}}</span>
                        </div>

                        <div class="col-md-4">
                            <span class="info-box-text">Defaulters</span>
                            <span class="info-box-number">{{$defaulterCount}}</span>
                        </div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

                <div>


<!-- Advances Section -->
<div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Advances</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date Approved</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($advances as $advance)
                        <tr>
                            <td>{{ $advance->date_approved }}</td>
                            <td>{{ $advance->amount }}</td>
                            <td>{{ $advance->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Leave Records Section -->
    <div class="col-md-6">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Leave Records</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Commencement Date</th>
                            <th>Return Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leave_days as $leave)
                        <tr>
                            <td>{{ $leave->commencement_date }}</td>
                            <td>{{ $leave->return_date }}</td>
                            <td>{{ $leave->reason }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>



<canvas id='graph'></canvas>
<canvas id='target_graph'></canvas>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('.table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
        });
    });
</script>
</div>
@endsection
@section('footer-scripts')
<script>
const ctx = document.getElementById('graph');
const ctx_new = document.getElementById('target_graph');

var collections = <?php echo json_encode($collected_amounts); ?>;
var uncollections = <?php echo json_encode($uncollected_amounts); ?>;
var dates = <?php echo json_encode($dates); ?>;

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: dates,
    datasets: [
        {
    label: 'Your uncollected as at end of cycle',
      data:uncollections,
      borderWidth: 1,
      backgroundColor:'#ff1c4b'

},
        {
      label: 'Your collected as at end of cycle',
      data: collections,
      borderWidth: 1,
      backgroundColor:'#57b7fa'

    }
]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});

var targets = <?php echo json_encode($targets); ?>;
var colors = <?php echo json_encode($colors); ?>;

new Chart(ctx_new,{
    type:'bar',
    data: {
        labels: dates,
        datasets: [{
             label:'Cycle given out for last 12 months',
             data:targets,
             backgroundColor:colors,
             borderWidth:1
            }]
    },
    options: {
        scales: {
            y: {
                beginAtZero:true
            }
        },
    }
});


              $(document).ready(function () {

    var showingLedger = false;

    $('#toggleView').on('click', function () {

        if (!showingLedger) {
            $('#summaryView').hide();
            $('#ledgerView').show();
            $(this).html('<i class="fa fa-bar-chart"></i> Summary');

            // Fetch initial collections table
            fetchCycleOpeningTable();

        } else {
            $('#ledgerView').hide();
            $('#summaryView').show();
            $(this).html('<i class="fa fa-book"></i> Ledger');
        }

        showingLedger = !showingLedger;
    });

    $('.toggle-btn').on('click', function () {

        var target = $(this).data('target');

        // Toggle active button
        $('.toggle-btn').removeClass('active');
        $(this).addClass('active');

        // Move slider
        $('.toggle-wrapper').attr('data-active', target);

        // Show correct section
        $('.ledger-section').hide();
        $('#' + target).fadeIn(200);

        // Fetch data for specific section
        if(target === 'collections') {
            fetchCycleOpeningTable();
        }
        if(target === 'disbursements') {
            fetchTotalCollectedTable();
        }
        if(target === 'adjustments') {
            fetchGivenOutTable();
        }
    });

    // --- FETCH FUNCTIONS ---

   function fetchCycleOpeningTable() {
    var $tableBody = $('#cycleOpeningTable tbody');
    $tableBody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

    $.ajax({
        url: 'https://lms2backend.whencefinancesystem.com/cycle-opening-uncollected-table',
        method: 'GET',
        data: {
            user_id: '{{ $userId }}',
            start_date: '{{ $start }}',
            end_date: '{{ $end }}'
        },
        success: function(response) {
            $tableBody.empty();

            if (!response.loans_uncollected || response.loans_uncollected.length === 0) {
                $tableBody.html('<tr><td colspan="5" class="text-center">No uncollected loans</td></tr>');
                return;
            }

            response.loans_uncollected.forEach(function(loan) {
                $tableBody.append(`
                    <tr>
                        <td>${loan.loan_id}</td>
                        <td>${loan.client_name}</td>
                        <td>${Number(loan.amount_due).toLocaleString()}</td>
                        <td>${Number(loan.balance).toLocaleString()}</td>
                        <td>${loan.due_date ? new Date(loan.due_date).toISOString().slice(0, 10) : '-'}</td>

                    </tr>
                `);
            });
        },
        error: function(err) {
            $tableBody.html('<tr><td colspan="5" class="text-center text-danger">Failed to load data</td></tr>');
            console.error(err);
        }
    });
}

function fetchTotalCollectedTable() {
    var $tableBody = $('#totalCollectedTable tbody');
    $tableBody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

    $.ajax({
        url: 'https://lms2backend.whencefinancesystem.com/total-collected-table',
        method: 'GET',
        data: {
            user_id: '{{ $userId }}',
            start_date: '{{ $start }}',
            end_date: '{{ $end }}'
        },
        success: function(response) {
            $tableBody.empty();

            if (!response.collected_transactions || response.collected_transactions.length === 0) {
                $tableBody.html('<tr><td colspan="4" class="text-center">No collected transactions</td></tr>');
                return;
            }

            response.collected_transactions.forEach(function(tx) {
                $tableBody.append(`
                    <tr>
                        <td>${tx.loan_id}</td>
                        <td>${tx.client_name}</td>
                        <td>${tx.transaction_type}</td>
                        <td>${Number(tx.amount).toLocaleString()}</td>
                    </tr>
                `);
            });
        },
        error: function(err) {
            $tableBody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>');
            console.error(err);
        }
    });
}

function fetchGivenOutTable() {
    var $tableBody = $('#givenOutTable tbody');
    $tableBody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

    $.ajax({
        url: 'https://lms2backend.whencefinancesystem.com/given-out-table',
        method: 'GET',
        data: {
            user_id: '{{ $userId }}',
            start_date: '{{ $start }}',
            end_date: '{{ $end }}'
        },
        success: function(response) {
            $tableBody.empty();

            if (!response.given_out_breakdown || response.given_out_breakdown.length === 0) {
                $tableBody.html('<tr><td colspan="4" class="text-center">No given out transactions</td></tr>');
                return;
            }

            response.given_out_breakdown.forEach(function(tx) {
                $tableBody.append(`
                    <tr>
                        <td>${tx.loan_id !== null ? tx.loan_id : '-'}</td>
                        <td>${tx.client_name}</td>
                        <td>${tx.transaction_type}</td>
                        <td>${Number(tx.amount).toLocaleString()}</td>
                    </tr>
                `);
            });
        },
        error: function(err) {
            $tableBody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>');
            console.error(err);
        }
    });
}


});

   const gaugeElement = document.querySelector(".gauge");

                function setGaugeValue(gauge, value) {
                    if (value < 0 || value > 1) {
                        return;
                    }

                    gauge.querySelector(".gauge__fill").style.transform = `rotate(${value / 2
                        }turn)`;
                    gauge.querySelector(".gauge__cover").textContent = `${Math.round(
                        value * 100
                    )}%`;
                }


    setGaugeValue(gaugeElement, "{{ $data['pdua'] }}");

</script>
@endsection

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
 $num = 1;
$use = date('Y-m-');
$todaysDate = date('Y-m-d');
if($cycleDate != null){
    $targetDate = $use.$cycleDate->cycle_end_date;
}else{
    $targetDate = $use.$num;
}
$targetDate = date('Y-m-d',strtotime($targetDate));
if($todaysDate > $targetDate){
    $targetDate = date('Y-m-d',strtotime($targetDate. ' + 1 months'));
}
$compareDate = date('Y-m-d',strtotime($targetDate. ' - 1 months'));
?>
<h2 style="display: flex;
    align-items: center;
    justify-content: center;">{{ $user->first_name}} {{ $user->last_name}}</h2>
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
                           <span class="info-box-text"># of Clients</span>
                            <span class="info-box-number">{{\App\Models\Client::where('staff_id',$user->id)->where('status','active')->count()}}</span>
                        </div>

                        <div class="col-md-4">
                            <span class="info-box-text"># of Active loans</span>
                            <span class="info-box-number">{{\App\Models\Loan::where('loan_officer_id',$user->id)->where('status','disbursed')->count()}}</span>
                        </div>

                        <div class="col-md-4">
                            <span class="info-box-text"># of Closed Loans</span>
                            <span class="info-box-number">{{\App\Models\Loan::where('loan_officer_id',$user->id)->where('status','closed')->count()}}</span>
                        </div>


                        <div class="col-md-4">
                            <span class="info-box-text">Cycle ends on</span>
                            @if(!empty($cycleDate->cycle_end_date))
                            <span class="info-box-number">{{date("jS M", strtotime($targetDate))}}</span>
                            @endif
                        </div>

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
                                
                                    $reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
                                    $interest = $transaction->credit/0.4;
                                    $reloan_payments = $reloan_payments + $reloan_amount + $interest;  
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
                                
                                    $cycle_reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
                                    $cycle_interest = $transaction->credit/0.4;
                                    $cycle_reloan_payments = $cycle_reloan_payments + $cycle_reloan_amount + $cycle_interest;  
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
                                if($transaction->date <= $compareDate){
                                    $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
                                }
                                
                                if($transaction->transaction_type != 'interest_waiver' && $transaction->date <= $compareDate){
                                    $MoneyCollected = $MoneyCollected + $transaction->credit;
                                }
                                
                                
                                if($transaction->transaction_type == 'specified_due_date_fee' && $transaction->date <= $compareDate){
                                    $charges = $charges + $transaction->debit;
                                }
                                
                            }
                            $balance = ($MoneyGivenOut - $MoneyCollected - $charges);
                            if($balance < 0){
                                $balance = 0;
                            }
                            $cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
                        }
                        
                        ?>
                        <?php 
                           $target_total = $new_loans_today + $new_reloans_today;
                           $target_total_cycle = $new_loans_cycle + $new_reloans_cycle;
                           $collected_today = $full_payments + $part_payments + $reloan_payments;
                           $cycle_collected_total = $cycle_full_payments + $cycle_part_payments + $cycle_reloan_payments;
                        ?>

                        <div class="col-md-4">
                            <span class="info-box-text"># of Current Reloans</span>
                            <span class="info-box-number">{{$reloansCount}}</span>
                        </div>

                        <div class="col-md-4">
                            <span class="info-box-text"># of Defaulters</span>
                            <span class="info-box-number">{{$defaulterCount}}</span>
                        </div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

                <div>

<!-- COLLECTIONS TODAY IS OF VALUE 1 -->  
<a href="{{url('user/'.$user->id.'/collections_today/collections_stats')}}">
<div class="col-lg-4 col-xs-12">
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">Collected today</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($collected_today,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
</a>

<a href="{{url('user/'.$user->id.'/given_out_today/given_out_stats')}}">
<div class="col-lg-4 col-xs-12">
<div class="small-box bg-yellow">
<div class="inner">
<p style="font-weight: bold;">Given out today</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($target_total,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
</a>

<a href="{{url('user/'.$user->id.'/uncollected_today/uncollected_stats')}}">
<div class="col-lg-4 col-xs-12">
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">Still Uncollected Today</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($cycle_opening_uncollected_amount - $cycle_collected_total,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
</a>

<a href="{{url('user/'.$user->id.'/collections_cycle/collections_stats')}}">
<div class="col-lg-4 col-xs-12">
<div class="small-box bg-purple">
<div class="inner">
<p style="font-weight: bold;">Total Cycle Collected</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($cycle_collected_total,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
</a>

<a href="{{url('user/'.$user->id.'/given_out_cycle/given_out_stats')}}">
<div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Total Cycle Given out</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($target_total_cycle,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
</a>

<a href="{{url('user/'.$user->id.'/uncollected_cycle/uncollected_stats')}}">
<div class="col-lg-4 col-xs-12">
<div class="small-box bg-blue">
<div class="inner">
<p style="font-weight: bold;">Cycle Opening Uncollected</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($cycle_opening_uncollected_amount,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
</a>

</div>
@endsection
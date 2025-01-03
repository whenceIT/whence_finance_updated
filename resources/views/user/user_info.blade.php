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
                   
<div class="row">
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

<div class="col-lg-4 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Projected salary</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($projected_salary)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
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
                            <span class="info-box-number">{{\App\Models\Client::where('staff_id',$user->id)->where('status','active')->count()}}</span>
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
</script>
@endsection

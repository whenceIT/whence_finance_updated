
<?php 
function compare($a,$b){
    return $a->first_repayment_date <=> $b->first_repayment_date;
}

function compareTwo($a,$b){
    return $b->first_repayment_date <=>  $a->first_repayment_date;
}

usort($LoanArray,"compare");
usort($LoanArrayTwo,"compareTwo")
?>
<?php
 $thisWeekTotal = 0;
 $allTimeTotal = 0;
 $today = date('Y-m-d');
 $last_month = date('Y-m',strtotime($today. '- 1 month'));
 $cycle_date = $last_month.'-'.'31';
 $period_start = '2023-01-01';
 $collected_total = 0;
?>

@extends('layouts.master')
@section('title')
Branch Uncollected
@endsection
@section('content')
<div class="box box-primary">
<div class="box-header with-border">
            <h3 class="box-title">
        Branch Uncollected
            @if(!empty($compareDate))
                    for period: <b> {{date("jS M, Y", strtotime($period_start))}} to {{date("jS M, Y", strtotime($compareDate))}}</b>
            @endif
            </h3>
            <div class="box-tools pull-right">

            </div>
        </div>
    <div class="box-body hidden-print">
    <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
{{csrf_field()}}
   

<div class="form-group">
    <label for="start_date" class="control-label col-md-2"
        >{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}
    </label>
    <div class="col-md-3">
        <input type="text" name="start_date" class="form-control date-picker" required id="start_date" value="{{$compareDate}}">
    </div>
</div>


<div class="form-group">
    <label for="end_date" class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}
        
    </label>
        <div class="col-md-3">
            <input type="text" name="end_date" class="form-control date-picker" required id="end_date" value="{{$targetDate}}">
        </div>
</div>

<div class="form-group">
        <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">


                        @if($role->role_id == '1')
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif

                        @if($role->role_id == '6')
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::where('province_id',$userProvince)->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif


                        @if($role->role_id == '4')
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::where('id',$userBranch)->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif



                    </div>
</div>                   




<div class="form-group">
<label for=""
class="control-label col-md-2"></label>
<div class="col-md-4"> 
<button type="submit" class="btn btn-success">Go!
</button>
</div>
</div>
     

   
</form>
    </div>
</div>




<?php 
$balance = 0;
$interest = 0;
$amount = 0;
$total = 0;
$transactions = [];
?>



<?php
foreach($LoanArray as $Loan){

    $OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
$reloansCount = 0;

foreach($Loan->transactions as $transaction){

if($Loan->first_repayment_date >= $period_start && $Loan->first_repayment_date <= $cycle_date){
    if($transaction->date >= $cycle_date){
        $collected_total = $collected_total + $transaction->credit;
        array_push($transactions,$transaction);
    }
}

  
$out = $out + $transaction->debit;


    $in = $in + $transaction->credit;



if($transaction->payment_apply_to == 'reloan_payment'){
    $reloansCount = $reloansCount + 1;
 }

}

$OutIn = $out - $in;


if($Loan->first_repayment_date >= $compareDate && $Loan->first_repayment_date <= $targetDate){
    $thisWeekTotal = $thisWeekTotal + $OutIn;
}

if($Loan->first_repayment_date >= $period_start && $Loan->first_repayment_date <= $cycle_date){
$allTimeTotal = $allTimeTotal + $OutIn;
}
}
?>

<?php

$OutIn2 = 0;
$OutIn3 = 0;

foreach($full_loans as $full_loan){
    $out2 = 0;
    $in2 = 0;
    foreach($full_loan->transactions as $transaction){
        $out2 = $out2 + $transaction->debit;
        $in2 = $in2 + $transaction->credit;
    }
    $diff2 =  $out2 - $in2;
     if($diff2 != 0 && $diff2 > 0){
        $OutIn2 = $OutIn2 + $diff2;
    }
}

foreach($LoanArray as $loan){
    $out3 = 0;
    $in3 = 0;
    $part_payment = 0;

    foreach($loan->transactions as $transaction){
        if($transaction->payment_apply_to == 'part_payment'){
            $part_payment = 1;
        }

    }

    foreach($loan->transactions as $transaction){
    if($part_payment == 1){
        $out3 = $out3 + $transaction->debit;
        $in3 = $in3 + $transaction->credit;
    }

    }

    $diff3 =  $out3 - $in3;

    if($diff3 != 0 && $diff3 > 0){
    $OutIn3 = $OutIn3 + $diff3;  
}

}

?>

@if($office_id != 0)
<div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/expected_collections_report/pdf?compareDate='.$compareDate.'&targetDate='.$targetDate.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a>
                                    <a href="{{url('report/loan_report/expected_collections_report/excel?compareDate='.$compareDate.'&targetDate='.$targetDate.'&office_id='.$office_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a>
                                </li>
                              
                            </ul>
</div>

<div class="row">
    
<div class="col-lg-6 col-xs-12">
<div class="small-box bg-red">
<div class="inner">
<p style="font-weight: bold;">Uncollected balance as at {{date("jS M, Y",strtotime($cycle_date))}}</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($allTimeTotal,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>

<!-- <div class="col-lg-4 col-xs-12">
<div class="small-box bg-aqua">
<div class="inner">
<p style="font-weight: bold;">Uncollected between {{date("jS M, Y",strtotime($compareDate))}} and {{date("jS M, Y",strtotime($targetDate))}}</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($thisWeekTotal,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div> -->

<a href="javascript:;" onmousedown="toggleCOUA();" >
<div class="col-lg-6 col-xs-12">
<div class="small-box bg-green">
<div class="inner">
<p style="font-weight: bold;">Collected</p>
<div class="icon">
<i class="fa fa-usd"></i>
</div>
<h3>{{number_format($collected_total,2)}}</h3>
</div>
<div class="small-box-footer">
    <p></p>
</div>
</div>
</div>
</a>

<a href="javascript:;" onmousedown="toggleCOUA();" style="margin: 10px;">
<span class="label label-primary" style="font-size: 15px;">COUA</span>
<!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
</a>



<p>With part payments: {{$OutIn3}}</p>
<p>Without part payments or reloans: {{$OutIn2}}</p>

</div>


<div style="padding-top: 10px;">

<div id='defaulted' style="display:block" class="box box-primary">
<div class="box-body table-responsive" >

<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Client Name</th>
    <th>Loan Consultant</th>
    <th>Balance</th>
    <th>Due Date</th>
    </tr>
</thead>
<tbody>
    <?php
     $collected_total = 0;
    ?>
    @foreach($LoanArray as $Loan)
    <?php
      $OutIn = 0;
      $out = 0;
      $in = 0;
    ?>
    @foreach($Loan->transactions as $transaction)
<?php
    $collected_total = $collected_total + $transaction->credit;
    $out = $out + $transaction->debit;
    
    // if($transaction->transaction_type != 'interest_waiver'){
    //     $in = $in + $transaction->credit;
    // }

    $in = $in + $transaction->credit;
    
?>
    @endforeach
    <?php
    $OutIn = $out - $in;
    ?>
   @if($OutIn != 0 && $OutIn > 0)
    <tr>
    <td>
        @if($reloansCount > 0)
        <p>{{$Loan->id}}<span style="color: blue;">(Reloan)</span></p>
        @else
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a>
        </td>
        @endif
        <td>
        @if(!empty($Loan->client->first_name))
            {{$Loan->client->first_name}}
        @endif      
        @if(!empty($Loan->client->last_name)) 
            {{$Loan->client->last_name}}</td>
        @endif   
        <td>
        @if(!empty($Loan->loan_officer->first_name))
            {{$Loan->loan_officer->first_name}}
        @endif  
        @if(!empty($Loan->loan_officer->last_name))   
            {{$Loan->loan_officer->last_name}}
        @endif      
        </td>
        <td>{{number_format($OutIn,2)}}</td>
        <td style="font-weight: bold;">{{date("jS M, Y",strtotime($Loan->first_repayment_date))}}</td>
    </tr>
    @endif
   
    @endforeach
</tbody>
</table>
       
</div>
</div>  



<div class="box box-primary">
<div class="box-body table-responsive" >

<table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Client Name</th>
    <th>Amount</th>
    <th>Transaction Type</th>
    <th>Date</th>
    </tr>
</thead>
<tbody>
    <?php
     $collected_total = 0;
    ?>
    @foreach($transactions as $transaction)
    <tr>
    <td>
    <a href="{{ url('loan/'.$transaction->loan_id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$transaction->loan_id}}</a>
    </td>
    <td>{{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}</td>
    <td>{{$transaction->payment_apply_to}}</td>
    <td>{{number_format($transaction->credit,2)}}</td>
    <td>{{date("jS M, Y",strtotime($transaction->date))}}</td>
    </tr>
    @endforeach
</tbody>
</table>
       
</div>
</div>  
@endif

@endsection
@section('footer-scripts')
<script>
    $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[5, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });           
        
const chart = document.getElementById('myChart');

var xValues = ["Italy", "France", "Spain", "USA", "Argentina"];
var yValues = [55, 49, 44, 24, 15];
var barColors = [
  "#b91d47",
  "#00aba9",
  "#2b5797",
  "#e8c3b9",
  "#1e7145"
];

var txt = new Chart(chart, {
  type: "line",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "World Wide Wine Production 2018"
    }
  }
});

var collected = document.getElementById('collected');
var defaulted = document.getElementById('defaulted');

function toggleCOUA(){
    console.log(collected.style.display);
    if(collected.style.display == 'none'){
            defaulted.style.display = 'none'
            collected.style.display = 'block'  
    }else{
        collected.style.display = 'none'
        defaulted.style.display = 'block'
    }
}

</script>
@endsection

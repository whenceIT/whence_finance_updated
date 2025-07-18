
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


@extends('layouts.master')
@section('title')
Expected collections
@endsection
@section('content')
<div class="box box-primary">
<div class="box-header with-border">
            <h3 class="box-title">
          Expected collection's
            @if(!empty($compareDate))
                    for period: <b> {{date("jS M, Y", strtotime($compareDate))}} to {{date("jS M, Y", strtotime($targetDate))}}</b>
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
?>

@if(!empty($targetDate))
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

<div style="padding-top: 10px;">

<div class="box box-primary">
<div class="box-body table-responsive" >

<table class="table  table-bordered table-hover table-striped" id="data-table">
<?php
 $thisWeekTotal = 0;
 $allTimeTotal = 0;
 $today = date('Y-m-d');
 $last_month = date('Y-m',strtotime($today. '- 1 month'));
 $cycle_date = $last_month.'-'.'31';
?>
<p>{{$cycle_date}}</p>
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
    @foreach($LoanArray as $Loan)
    <?php
$OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
$reloansCount = 0;
// $total = 0;
// $totalIn = 0;
// $totalOut = 0;
?>
    @foreach($Loan->transactions as $transaction)
<?php

$out = $out + $transaction->debit;

if($transaction->transaction_type != 'interest_waiver'){
    $in = $in + $transaction->credit;
}


if($transaction->payment_apply_to == 'reloan_payment'){
    $reloansCount = $reloansCount + 1;
 }
?>
@endforeach
<?php
$OutIn = $out - $in;

if($Loan->first_repayment_date <= $cycle_date){
    $thisWeekTotal = $thisWeekTotal + $OutIn;
}


// if($Loan->first_repayment_date >= $compareDate && $Loan->first_repayment_date <= $targetDate){
//     $thisWeekTotal = $thisWeekTotal + $OutIn;
// }

$allTimeTotal = $allTimeTotal + $OutIn
?>
    @if($Loan->first_repayment_date >= $compareDate && $Loan->first_repayment_date <= $targetDate)
    <tr>
    <td>
        @if($reloansCount > 0)
        <p>{{$Loan->id}}<span style="color: blue;">(Reloan)</span></p>
        @else
        <p>{{$Loan->id}}</p>
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
<p style="font-weight: bold; font-size:large">TOTAL: K{{number_format($allTimeTotal,2)}}</p>
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

</script>
@endsection

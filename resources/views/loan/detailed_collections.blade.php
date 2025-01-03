@extends('layouts.master')
@section('title')
{{$branch_name->name}} collections between {{date("jS M, Y", strtotime($compareDate))}} and {{date("jS M, Y", strtotime($targetDate))}}
@endsection
@section('content')
<div>
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
<form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
{{csrf_field()}}
    <div class="form-group">

    <label for="start_date"
        class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}
    </label>
    <div class="col-md-3">
        <input type="text" name="start_date" class="form-control date-picker" required id="start_date" >
    </div>



    <label for="end_date" class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}
        
    </label>
        <div class="col-md-3">
            <input type="text" name="end_date" class="form-control date-picker" required id="end_date"  >
        </div>


        <button type="submit" class="btn btn-success">Go!
                        </button>

    </div>
   
</form>

<div style="padding-top: 10px;">

<div class="box box-primary">
<div class="box-body table-responsive" >

<table class="table  table-bordered table-hover table-striped" id="data-table">
<?php
 $thisWeekTotal = 0
?>
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
if($Loan->first_repayment_date >= $compareDate && $Loan->first_repayment_date <= $targetDate){
    $thisWeekTotal = $thisWeekTotal + $OutIn;
}
?>
    @if($Loan->first_repayment_date >= $compareDate && $Loan->first_repayment_date <= $targetDate)
    <tr>
    <td>
        @if($reloansCount > 0)
        <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a><span style="color: blue;">(Reloan)</span>
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
<p style="font-weight: bold; font-size:large">TOTAL: K{{number_format($thisWeekTotal,2)}}</p>
</div>


</div>
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

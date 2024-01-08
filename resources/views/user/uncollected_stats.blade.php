@extends('layouts.master')
@section('title')
 {{$user->first_name}}'s loan balances as at {{date("jS M, Y", strtotime($targetDate))}}
@endsection
@section('content')
<div>
<form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
{{csrf_field()}}
    <div class="form-group">
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
<?php
$cycle_opening_uncollected_amount = 0;

foreach($userLoans as $userLoan){

    
    $MoneyCollected = 0;
    $MoneyGivenOut = 0;
    $charges = 0;
    $balance = 0;

    foreach($userLoan->transactions as $transaction){
        if($transaction->date <= $targetDate){
            $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
        }
        
        if($transaction->transaction_type != 'interest_waiver' && $transaction->date <= $targetDate){
            $MoneyCollected = $MoneyCollected + $transaction->credit;
        }
        
        
        if($transaction->transaction_type == 'specified_due_date_fee' && $transaction->date <= $targetDate){
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
        <table class="table  table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Name</th>
    <th>Balance</th>
    <th># of Reloans</th>
    </tr>
</thead>
<tbody>
@foreach($userLoans as $loan)
<?php
$reloansCount = 0;
$OutIn = 0;
$out = 0;
$in = 0;
$newout = 0;
?>
@foreach($loan->transactions as $transaction)
<?php
if($transaction->date <= $targetDate){
    $out = $out + $transaction->debit;
}

if($transaction->date <= $targetDate && $transaction->transaction_type != 'interest_waiver'){
    $in = $in + $transaction->credit;
}

if($transaction->date <= $targetDate && $transaction->transaction_type == 'specified_due_date_fee'){
    $newout = $newout + $transaction->debit;
}

if($loan->status == 'disbursed' && $transaction->payment_apply_to == 'reloan_payment'){
    $reloansCount = $reloansCount + 1;
 }
?>
@endforeach

<?php
$OutIn = $out - $in;
$OutIn = $OutIn - $newout;
if($OutIn < 0){
    $OutIn = 0;
}
?>
<tr>
   @if($OutIn != 0)
    <td>{{$loan->id}}</td>
    <td>
    @if(!empty($loan->client->first_name))
        {{$loan->client->first_name}} 
    @endif
    @if(!empty($loan->client->last_name))
        {{$loan->client->last_name}}
    @endif
        @if($loan->defaulted == 'yes')
        <span style="color: red;">(Defaulted)</span>
        @endif
    </td>
    <td>{{number_format($OutIn,2)}}</td>
    <td style="font-weight: bold;">{{$reloansCount}}</td>
    @endif
</tr>
@endforeach
</tbody>
</table>
</div>
</div>  

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
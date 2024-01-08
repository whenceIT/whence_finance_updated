@extends('layouts.master')
@section('title')
    {{$user->first_name}}'s collections between {{date("jS M, Y", strtotime($compareDate))}} and {{date("jS M, Y", strtotime($targetDate))}} 
@endsection
@section('content')
<div>
<?php
$full_payment_count = 0;
$part_payments_count = 0;
$reloan_payments_count = 0;
$total = 0;
$reloan_total = 0;
$part_payment_total = 0;
$full_payment_total = 0;
foreach($userTransactions as $transaction){
    if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
        $full_payment_count = $full_payment_count + 1;
        $full_payment_total = $full_payment_total + $transaction->credit;
    }
    
    if($transaction->payment_apply_to == 'part_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
        $part_payments_count = $part_payments_count + 1;
        $part_payment_total = $part_payment_total +  $transaction->credit;
    }
    
    if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate){
        $reloan_payments_count = $reloan_payments_count + 1;  
        $interest = $transaction->credit/0.4;
        $reloan_amount = $transaction->credit; + ($transaction->credit/0.4);
        $reloan_total = $reloan_total +  $reloan_amount + $interest;
    }
}

$total = $full_payment_total + $part_payment_total + $reloan_total;
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

        <div class="col-md-4">
                           <span class="info-box-text" style="font-weight: bold; font-size: 18px;">Reloans</span>
                            <span class="info-box-number">{{$reloan_payments_count}}</span>
        </div>

        <div class="col-md-4">
                           <span class="info-box-text" style="font-weight: bold; font-size: 18px;">Part Payments</span>
                            <span class="info-box-number">{{$part_payments_count}}</span>
                        </div>

        <div class="col-md-4">
                           <span class="info-box-text" style="font-weight: bold; font-size: 18px;">Full Payments</span>
                            <span class="info-box-number">{{$full_payment_count}}</span>
        </div>

        <div class="col-md-4">
                           <span class="info-box-text" style="font-weight: bold; font-size: 18px;">Total</span>
                            <span class="info-box-number">{{$total}}</span>
        </div>

        <table class="table  table-bordered table-hover table-striped" id="data-table">
            <thead>
                <tr>
                <th>Loan ID</th>
                    <th>Name</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            @foreach($userTransactions as $transaction)
            @if($transaction->transaction_type != 'interest_waiver' && $transaction->date > $compareDate &&  $transaction->date <= $targetDate && $transaction->credit)
         <tr>
    <td>{{$transaction->loan_id}}</td>
    <td>{{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}</td>
    <td>{{$transaction->payment_apply_to}}</td>
    <td>{{number_format($transaction->credit,2)}}</td>
</tr>       
@endif
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
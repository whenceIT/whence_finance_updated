@extends('layouts.master')
@section('title')
{{$user->first_name}}'s given out between {{date("jS M, Y", strtotime($compareDate))}} and {{date("jS M, Y", strtotime($targetDate))}}
@endsection
@section('content')
<div>
<?php
$reloan_count = 0;
$new_loan_count = 0;
$total = 0;
$new_loan_total = 0;
$reloan_total = 0;
$nums = [];
$targets = [];
$transaction_total = 0;
$carry_over = 0;
for($x=12; $x>-1; $x--){
    foreach($userTransactions as $transaction){
        if($transaction->transaction_type == 'disbursement' && $transaction->date > date('Y-m-d',strtotime($compareDate. '-' .$x. 'months')) && $transaction->date <= date('Y-m-d',strtotime($targetDate.  '-' .$x. 'months'))){
            $new_loan_total = $transaction->debit;
            $new_loan_count = $new_loan_count + 1;
        }

        if($transaction->transaction_type == 'interest' && $transaction->date > date('Y-m-d',strtotime($compareDate. '-' .$x. 'months')) && $transaction->date <= date('Y-m-d',strtotime($targetDate.  '-' .$x. 'months'))){
            $principal = $transaction->debit/0.4;
            $reloan_total = $principal;
            $reloan_count = $reloan_count + 1;
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

    array_push($nums,$x);
    array_push($targets,$transaction_total);
    $transaction_total = 0;
    $total = $reloan_total + $new_loan_total;
}
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
                            <span class="info-box-number">{{$reloan_count}}</span>
        </div>

        <div class="col-md-4">
                           <span class="info-box-text" style="font-weight: bold; font-size: 18px;">New Loans</span>
                            <span class="info-box-number">{{$new_loan_count}}</span>
                        </div>

        <div class="col-md-4">
                           <span class="info-box-text" style="font-weight: bold; font-size: 18px;">Total</span>
                            <span class="info-box-number">{{$targets[12]}}</span>
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
            @if(($transaction->transaction_type == 'disbursement' || $transaction->transaction_type == 'interest') && $transaction->date > $compareDate &&  $transaction->date <= $targetDate)
         <tr>
    <td>{{$transaction->loan_id}}</td>
    <td>{{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}</td>
    @if($transaction->transaction_type == 'disbursement')
    <td>New Loan</td>
    @elseif($transaction->transaction_type == 'interest')
    <td>Reloan</td>
    @endif
    @if($transaction->transaction_type == 'interest')
    <td>{{number_format(($transaction->debit/0.4),2)}}</td>
    @else
    <td>{{number_format($transaction->debit,2)}}</td>
    @endif
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

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
<table class="table  table-bordered table-hover table-striped" id="data-table-2">
    <thead>
        
    </thead>
</table>

<table class="table table-bordered table-hover table-striped" id="data-table">
<thead>
    <tr>
    <th>Loan ID</th>
    <th>Client Name</th>
    <th>Loan Consultant</th>
    <th>Type</th>
    <th>Balance</th>
    <th>Due Date</th>

    </tr>
</thead>
<tbody>
    @foreach($transactionList as $transaction)
    <tr>
@if(!empty($transaction->Loan->id))
<td>
<a href="{{ url('loan/'.$transaction->Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$transaction->Loan->id}}</a>
</td>
@endif
<td>
@if(!empty($transaction->Loan->client->first_name))
    {{$transaction->Loan->client->first_name}} 
@endif    
@if(!empty($transaction->Loan->client->last_name))
    {{$transaction->Loan->client->last_name}}</td>
@endif    
<td>@if(!empty($transaction->Loan->loan_officer->first_name))
    {{$transaction->Loan->loan_officer->first_name}} 
@endif
@if(!empty($transaction->Loan->loan_officer->last_name))
    {{$transaction->Loan->loan_officer->last_name}}</td>
@endif    
@if($transaction->payment_apply_to == 'reloan_payment')
<td>Reloan</td>
@elseif($transaction->transaction_type == 'disbursement')
<td>New Loan</td>
@else
<td></td>
@endif
<?php 
if($transaction->transaction_type == 'disbursement'){
    $interest = $transaction->debit * 0.4;
    $balance = $transaction->debit + $interest;
}elseif($transaction->payment_apply_to == 'reloan_payment'){
    $amount = $transaction->balance_bf - $transaction->credit;
    $interest = $amount * 0.4;
    $balance = $amount + $interest;
}
?>
<td>{{$balance}}</td>
<?php
$due_date = date('Y-m-d',strtotime($transaction->date. '+ 1 months'))
?>
<td>{{date("jS M, Y",strtotime($due_date))}}</td>
    
    </tr>
    <?php
    $total = $total + $balance;
    $interest = 0;
    $balance = 0;
    $amount = 0;
    ?>
    @endforeach
</tbody>
</table>
</div>
</div> 
 
<p style="font-weight: bold; font-size:large">UNCOLLECTED TOTAL: K{{number_format($total,2)}}</p>
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

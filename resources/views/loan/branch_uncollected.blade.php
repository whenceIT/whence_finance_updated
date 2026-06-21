
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

<section class="content">

    {{-- MODERN FILTER TOOLBAR --}}
    <div class="box box-primary">
        <div class="box-body">
            <form method="post" action="{{Request::url()}}" id="filterForm" enctype="multipart/form-data">
                {{csrf_field()}}
                
                {{-- Quick Date Filters --}}
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                    <label style="margin: 0; font-weight: 600;">
                        <i class="fa fa-calendar"></i> Quick Filters:
                    </label>
                    <button type="button" class="btn btn-default" onclick="setDateRange('today')">Today</button>
                    <button type="button" class="btn btn-default" onclick="setDateRange('week')">This Week</button>
                    <button type="button" class="btn btn-default" onclick="setDateRange('month')">This Month</button>
                    <button type="button" class="btn btn-default" onclick="setDateRange('circle')">This Circle</button>
                    <button type="button" class="btn btn-default" onclick="setDateRange('quarter')">This Quarter</button>
                    <button type="button" class="btn btn-default" onclick="setDateRange('semi')">Semi-Annual</button>
                    <button type="button" class="btn btn-default" onclick="setDateRange('year')">This Year</button>
                </div>

                {{-- Date Range and Office Selector --}}
                <div style="display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="margin: 0; white-space: nowrap; font-weight: 600;">Start Date:</label>
                        <input type="text" name="start_date" class="form-control date-picker" required id="start_date" value="{{$compareDate}}" style="width: 150px;">
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="margin: 0; white-space: nowrap; font-weight: 600;">End Date:</label>
                        <input type="text" name="end_date" class="form-control date-picker" required id="end_date" value="{{$targetDate}}" style="width: 150px;">
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="margin: 0; white-space: nowrap; font-weight: 600;">
                            <i class="fa fa-building"></i> Office:
                        </label>
                        @if($role->role_id == '1')
                        <select name="office_id" class="form-control select2" id="office_id" required style="width: 200px;">
                            <option value="0" @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}" @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif

                        @if($role->role_id == '6')
                        <select name="office_id" class="form-control select2" id="office_id" required style="width: 200px;">
                            <option value="0" @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::where('province_id',$userProvince)->get() as $key)
                                <option value="{{$key->id}}" @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif

                        @if($role->role_id == '4')
                        <select name="office_id" class="form-control select2" id="office_id" required style="width: 200px;">
                            <option value="0" @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::where('id',$userBranch)->get() as $key)
                                <option value="{{$key->id}}" @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Province and Office Breakdown Info --}}
    @if($office_id != 0)
    <?php
        $selectedOffice = \App\Models\Office::find($office_id);
        $province = $selectedOffice ? $selectedOffice->province : null;
    ?>
    <div class="row">
        <div class="col-md-6">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-map-marker"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Province</span>
                    <span class="info-box-number">{{ $province ? $province->name : 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Office</span>
                    <span class="info-box-number">{{ $selectedOffice ? $selectedOffice->name : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

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
    
    {{-- Download Reports --}}
    <div style="margin-bottom: 15px;">
        <div class="btn-group">
            <button type="button" class="btn bg-blue dropdown-toggle legitRipple" data-toggle="dropdown">
                <i class="fa fa-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="{{url('report/loan_report/expected_collections_report/pdf?compareDate='.$compareDate.'&targetDate='.$targetDate.'&office_id='.$office_id)}}" target="_blank">
                        <i class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                    </a>
                </li>
                <li>
                    <a href="{{url('report/loan_report/expected_collections_report/excel?compareDate='.$compareDate.'&targetDate='.$targetDate.'&office_id='.$office_id)}}" target="_blank">
                        <i class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{number_format($allTimeTotal,2)}}</h3>
                    <p>Uncollected balance as at {{date("jS M, Y",strtotime($cycle_date))}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <a href="javascript:;" onclick="toggleCOUA();">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{number_format($collected_total,2)}}</h3>
                        <p>Collected</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ count($LoanArray) }}</h3>
                    <p>Total Loans</p>
                </div>
                <div class="icon">
                    <i class="fa fa-list"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Info --}}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    <p><strong>With part payments:</strong> {{number_format($OutIn3,2)}}</p>
                    <p><strong>Without part payments or reloans:</strong> {{number_format($OutIn2,2)}}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Uncollected Loans Table --}}
    <div id='defaulted' style="display:block" class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-list"></i> Uncollected Loans
            </h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-hover table-striped" id="data-table-uncollected">
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
                        $reloansCount = 0;
                    ?>
                    @foreach($Loan->transactions as $transaction)
                    <?php
                        $out = $out + $transaction->debit;
                        $in = $in + $transaction->credit;
                        
                        if($transaction->payment_apply_to == 'reloan_payment'){
                            $reloansCount++;
                        }
                    ?>
                    @endforeach
                    <?php
                        $OutIn = $out - $in;
                    ?>
                    @if($OutIn != 0 && $OutIn > 0)
                    <tr>
                        <td>
                            @if($reloansCount > 0)
                                <p>{{$Loan->id}} <span class="label label-info">Reloan</span></p>
                            @else
                                <a href="{{ url('loan/'.$Loan->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$Loan->id}}</a>
                            @endif
                        </td>
                        <td>
                            @if(!empty($Loan->client->first_name))
                                {{$Loan->client->first_name}}
                            @endif      
                            @if(!empty($Loan->client->last_name)) 
                                {{$Loan->client->last_name}}
                            @endif   
                        </td>
                        <td>
                            @if(!empty($Loan->loan_officer->first_name))
                                {{$Loan->loan_officer->first_name}}
                            @endif  
                            @if(!empty($Loan->loan_officer->last_name))   
                                {{$Loan->loan_officer->last_name}}
                            @endif      
                        </td>
                        <td><strong>{{number_format($OutIn,2)}}</strong></td>
                        <td>{{date("jS M, Y",strtotime($Loan->first_repayment_date))}}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Collected Transactions Table --}}
    <div id="collected" style="display:none" class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-check"></i> Collected Transactions
            </h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-hover table-striped" id="data-table-collected">
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>Client Name</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>
                            <a href="{{ url('loan/'.$transaction->loan_id.'/show') }}" data-toggle="tooltip" title="Click to view">{{$transaction->loan_id}}</a>
                        </td>
                        <td>{{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}</td>
                        <td>{{$transaction->payment_apply_to}}</td>
                        <td><strong>{{number_format($transaction->credit,2)}}</strong></td>
                        <td>{{date("jS M, Y",strtotime($transaction->date))}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</section>

@endsection

@section('footer-scripts')
<script>
    // Initialize DataTables
    $('#data-table-uncollected, #data-table-collected').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        "paging": true,
        "lengthChange": true,
        "displayLength": 15,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "order": [[4, "desc"]],
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
        responsive: true
    });

    // Toggle between Collected and Uncollected views
    function toggleCOUA(){
        var collected = document.getElementById('collected');
        var defaulted = document.getElementById('defaulted');
        
        if(collected.style.display == 'none'){
            defaulted.style.display = 'none'
            collected.style.display = 'block'  
        }else{
            collected.style.display = 'none'
            defaulted.style.display = 'block'
        }
    }

    // Quick date range setter
    function setDateRange(period) {
        var today = new Date();
        var startDate, endDate;
        
        switch(period) {
            case 'today':
                startDate = endDate = today;
                break;
            case 'week':
                var firstDay = today.getDate() - today.getDay();
                startDate = new Date(today.setDate(firstDay));
                endDate = new Date();
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'circle':
                // Assuming circle is monthly cycle ending on 31st
                var lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                startDate = new Date(lastMonth.getFullYear(), lastMonth.getMonth(), 1);
                endDate = new Date(lastMonth.getFullYear(), lastMonth.getMonth() + 1, 0);
                break;
            case 'quarter':
                var quarter = Math.floor(today.getMonth() / 3);
                startDate = new Date(today.getFullYear(), quarter * 3, 1);
                endDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                break;
            case 'semi':
                var half = today.getMonth() < 6 ? 0 : 6;
                startDate = new Date(today.getFullYear(), half, 1);
                endDate = new Date(today.getFullYear(), half + 6, 0);
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date(today.getFullYear(), 11, 31);
                break;
        }
        
        // Format dates as YYYY-MM-DD
        document.getElementById('start_date').value = formatDate(startDate);
        document.getElementById('end_date').value = formatDate(endDate);
    }

    function formatDate(date) {
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
</script>
@endsection

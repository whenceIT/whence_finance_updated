@extends('layouts.master')

@section('title')
    {{ trans_choice('general.loan', 1) }} {{ trans_choice('general.book', 1) }} {{ trans_choice('general.report', 1) }}
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            {{ trans_choice('general.loan', 1) }} {{ trans_choice('general.book', 2) }} {{ trans_choice('general.report', 2) }}
            @if(!empty($start_date))
                for period: <b>{{ $start_date }} to {{ $end_date }}</b>
            @else
                as of <b>{{ $end_date }}</b>
            @endif
        </h3>
        <div class="box-tools pull-right"></div>
    </div>

    <!-- Hidden Section for the Report Filters -->
    <div class="box-body hidden-print">
        <form method="post" action="{{ Request::url() }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="end_date" class="control-label col-md-2">{{ trans_choice('general.end', 1) }} {{ trans_choice('general.date', 1) }}</label>
                <div class="col-md-3">
                    <input type="text" name="end_date" class="form-control date-picker" value="{{ $end_date }}" required id="end_date">
                </div>
            </div>

            <div class="form-group">
    <label for="office_id" class="control-label col-md-2">{{ trans_choice('general.office', 1) }}</label>
    <div class="col-md-3">
        <select name="office_id" class="form-control select2" id="office_id" required>
            <option value="0">View All</option> <!-- Add "View All" option -->
            @foreach(\App\Models\Office::all() as $key)
                <option value="{{ $key->id }}">{{ $key->name }}</option>
            @endforeach
        </select>
    </div>
</div>


            <!-- Additional Filter Fields -->
            <div class="form-group">
                <div class="col-md-4 col-md-offset-2">
                    <button type="submit" class="btn btn-success">{{ trans_choice('general.search', 1) }}!</button>
                    <a href="{{ Request::url() }}" class="btn btn-danger">{{ trans_choice('general.reset', 1) }}!</a>

                    <!-- Download Options -->
                    <div class="btn-group">
                        <button type="button" class="btn bg-blue dropdown-toggle legitRipple" data-toggle="dropdown">
                            {{ trans_choice('general.download', 1) }} {{ trans_choice('general.report', 1) }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="{{ url('report/loan_report/loan_book/pdf?end_date='.$end_date.'&office_id='.$office_id) }}" target="_blank">
                                    <i class="icon-file-pdf"></i> {{ trans_choice('general.download', 1) }} {{ trans_choice('general.to', 1) }} {{ trans_choice('general.pdf', 1) }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('report/loan_report/loan_book/excel?end_date='.$end_date.'&office_id='.$office_id) }}" target="_blank">
                                    <i class="icon-file-excel"></i> {{ trans_choice('general.download', 1) }} {{ trans_choice('general.to', 1) }} {{ trans_choice('general.excel', 1) }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(!empty($end_date))
    <div class="box box-primary">
        <div class="panel-body table-responsive">
            <style>
                /* General Report Styling */
                @media print {
                    @page {
                        size: A4 landscape;
                        margin: 20mm;
                    }
                }
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    color: #333;
                }
                h3 {
                    text-align: center;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .table th, .table td {
                    padding: 8px;
                    text-align: left; /* Align text to the left */
                }
                .table th {
                    font-weight: bold;
                    border-bottom: 2px solid #000; /* Top rule for header */
                }
                .table tbody tr {
                    border-bottom: 1px solid #ddd; /* Bottom rule between rows */
                }
                .table tfoot th {
                    border-top: 2px solid #000; /* Bottom rule for totals */
                    font-weight: bold;
                }
                /* Footer for multi-page reports */
                .footer {
                    position: fixed;
                    bottom: 0;
                    width: 100%;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                }
            </style>

            <!-- Financial Report Table -->
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>{{ trans_choice('general.account', 1) }} #</th>
                        <th>{{ trans_choice('general.client', 1) }} {{ trans_choice('general.name', 1) }}</th>
                        <th>{{ trans_choice('general.office', 1) }}</th>
                        <th>{{ trans_choice('general.product', 1) }}</th>
                        <th>{{ trans_choice('general.disbursed', 1) }} {{ trans_choice('general.date', 1) }}</th>
                        <th>Disbursed Amount</th> 
                        <th>{{ trans_choice('general.principal', 1) }} {{ trans_choice('general.balance', 1) }}</th>
                        <th>{{ trans_choice('general.interest', 1) }} {{ trans_choice('general.balance', 1) }}</th>
                        <th>{{ trans_choice('general.fee', 1) }} {{ trans_choice('general.balance', 1) }}</th>
                        <th>Book Balance</th>
                        <th>{{ trans_choice('general.maturity', 1) }} {{ trans_choice('general.date', 1) }}</th>
                      
                    </tr>
                </thead>
                <tbody>
                @php
    $total_principal = 0;
    $total_outstanding = 0;
    $loan_count = 0;
@endphp
@foreach($data as $key)
    @php
        $principal = $key->principal;
        $balance = $key->true_balance; // Use balance calculated as of the selected date
        $intbalance = $key->interest_balance;
        foreach ($key->repayment_schedules as $schedule) {
       
        }
        $total_principal += $principal;
        $total_outstanding += $balance;
        $loan_count++;
    @endphp
    <tr>
        <td>{{ $key->client->account_no}}</td>
        <td>
            @if($key->client_type == "client")
                @if(!empty($key->client))
                    @if($key->client->client_type == "individual")
                        {{ $key->client->first_name }} {{ $key->client->middle_name }} {{ $key->client->last_name }}
                    @else
                        {{ $key->client->full_name }}
                    @endif
                @endif
            @else
                @if(!empty($key->group))
                    {{ $key->group->name }}
                @endif
            @endif
        </td>
        <td>{{ $key->office->name ?? '-' }}</td>
        <td>{{ $key->loan_product->name ?? '-' }}</td>
        <td>{{ $key->disbursement_date }}</td>
        <td>{{ number_format($principal, 2) }}</td>      
        <td>{{ number_format($key->principal_balance, 2) }}</td>
        <td>{{ number_format($intbalance, 2) }}</td>   
        <td>{{ number_format($key->fees_balance, 2) }}</td>         
        <td>{{ number_format($balance, 2) }}</td>
        <td>{{ $key->expected_maturity_date ?? '-' }}</td>
       
    </tr>
@endforeach

                </tbody>
                <tfoot>
                    <tr>
        @php
        $total_principal = $data->sum('principal');
        $total_principal_paid = $data->sum('principal_paid');
        $total_principal_balance = $data->sum('principal_balance');

        $total_interest_due = $data->sum('total_interest');
        $total_interest_paid = $data->sum('interest_paid');
        $total_interest_balance = $data->sum('interest_balance');

        $total_fees_due = $data->sum('total_fees');
        $total_fees_paid = $data->sum('fees_paid');
        $total_fees_balance = $data->sum('fees_balance');
    @endphp             <th colspan="5">Total</th>
                        <th>{{ number_format($total_principal, 2) }}</th>
                        <th>{{ number_format($total_principal_balance, 2) }}</th>
                        <th>{{ number_format($total_interest_balance, 2) }}</th>
                        <th>{{ number_format($total_fees_balance, 2) }}</th>
                        <th>{{ number_format($total_outstanding, 2) }}</th>
                        <th colspan="5"></th>
                    </tr>
                    <tr>
                        <th colspan="12">Total Number of Loans: {{ $loan_count }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif

<!-- Report Footer -->
<div class="footer">
    Generated by  on {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}.
</div>
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function () {
        $("body").addClass('sidebar-xs sidebar-collapse');
    });
</script>
@endsection

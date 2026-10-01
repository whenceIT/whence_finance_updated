@extends('layouts.master')
@section('title')
    Recovery Ledger
@endsection
@section('content')

<style>
.bento-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}
.bento-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}
.bento-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
.bento-card.total-amount {
    background: linear-gradient(135deg, #1d12af 0%, #388def 100%);
}
.bento-card.total-cases {
    background: linear-gradient(135deg, #3a78eb 0%, #3892f9 100%);
}
.bento-card.total-clients {
    background: linear-gradient(135deg, #23b883 0%, #28a745 100%);
}
.bento-card.net-amount {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
.bento-icon {
    font-size: 3.5rem;
    opacity: 0.8;
    margin-right: 25px;
}
.bento-content {
    flex: 1;
}
.bento-title {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
    font-weight: 500;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.bento-value {
    font-size: 30px;
    font-weight: 800;
    letter-spacing: -0.5px;
    line-height: 1;
}
.bento-footer {
    font-size: 12px;
    opacity: 0.85;
    margin-top: 8px;
}
.filter-section {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
}
.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}
.filter-form .form-group {
    margin-bottom: 0;
}
.filter-form label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
}
.filter-form .btn {
    margin-top: 20px;
}
.case-section {
    margin-bottom: 30px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.case-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.case-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
}
.case-stats {
    font-size: 14px;
    opacity: 0.95;
}
.case-table {
    background: white;
    padding: 0;
}
.daily-breakdown table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.daily-breakdown th {
    background: #e9ecef;
    padding: 8px 10px;
    text-align: right;
}
.daily-breakdown td {
    padding: 8px 10px;
    border-bottom: 1px solid #eee;
    text-align: right;
}
.daily-breakdown td:first-child {
    text-align: left;
    font-weight: 600;
}
@media (max-width: 768px) {
    .bento-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    .bento-value {
        font-size: 24px;
    }
    .bento-icon {
        font-size: 2.5rem;
        margin-right: 15px;
    }
    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-form .btn {
        margin-top: 10px;
    }
}
</style>

<!-- Summary Stats -->
<div class="bento-stats">
    <div class="bento-card total-amount">
        <div class="bento-icon">
            <i class="fa fa-money"></i>
        </div>
        <div class="bento-content">
            <div class="bento-title">Total Credits</div>
            <div class="bento-value">K{{ number_format($totalAmount + $funds, 2) }}</div>
            <div class="bento-footer">{{ $totalTransactions }} transactions</div>
        </div>
    </div>
    
    <div class="bento-card total-cases">
        <div class="bento-icon">
            <i class="fa fa-folder-open"></i>
        </div>
        <div class="bento-content">
            <div class="bento-title">Total Cases</div>
            <div class="bento-value">{{ $totalCases }}</div>
            <div class="bento-footer">Unique recovery cases</div>
        </div>
    </div>
    
    <div class="bento-card total-clients">
        <div class="bento-icon">
            <i class="fa fa-users"></i>
        </div>
        <div class="bento-content">
            <div class="bento-title">Unique Clients</div>
            <div class="bento-value">{{ $uniqueClients }}</div>
            <div class="bento-footer">Clients with recoveries</div>
        </div>
    </div>
    
    <div class="bento-card net-amount">
        <div class="bento-icon">
            <i class="fa fa-pie-chart"></i>
        </div>
        <div class="bento-content">
            <div class="bento-title">Net Amount</div>
            <div class="bento-value">K{{ number_format($netAmount + $funds, 2) }}</div>
            <div class="bento-footer">Credits - Debits</div>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="filter-section">
    <form method="GET" action="{{ url('loan/recovery/ledger') }}" class="filter-form">
        <div class="form-group">
            <label>Period</label>
            <select name="period" class="form-control" style="min-width: 120px;">
                <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        
        <div class="form-group" id="customDateFields" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        
        <div class="form-group" id="customDateFields2" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        
        <div class="form-group">
            <label>Office</label>
            <select name="office_id" class="form-control" style="min-width: 180px;">
                <option value="">All Offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ $officeFilter == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Recovery Case</label>
            <select name="case_id" class="form-control" style="min-width: 180px;">
                <option value="">All Cases</option>
                @foreach($recoveryCases as $rcase)
                    <option value="{{ $rcase->id }}" {{ $caseFilter == $rcase->id ? 'selected' : '' }}>
                        {{ $rcase->case_number ?? 'Case #' . $rcase->id }} - {{ $rcase->loan->client->first_name ?? '' }} {{ $rcase->loan->client->last_name ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group" style="margin-top: 20px;">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa fa-filter"></i> Apply Filters
            </button>
            <a href="{{ url('loan/recovery/ledger') }}" class="btn btn-sm btn-default">
                <i class="fa fa-refresh"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- All Transactions Table -->
@if(count($transactions) > 0)
    <div class="box box-primary" style="margin-top: 0; border-radius: 8px;">
        <div class="box-header" style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
            <h3 class="box-title" style="padding: 10px;">
                <i class="fa fa-table"></i> All Transactions
                <small class="text-muted" style="font-weight: 400;">
                    @php
                        $periodLabels = [
                            'daily' => 'Today',
                            'weekly' => 'This Week',
                            'monthly' => 'This Month',
                            'yearly' => 'This Year',
                        ];
                    @endphp
                    @if($period === 'custom' && $startDate && $endDate)
                        ({{ date('d M Y', strtotime($startDate)) }} &ndash; {{ date('d M Y', strtotime($endDate)) }})
                    @else
                        ({{ $periodLabels[$period] ?? 'All Time' }})
                    @endif
                </small>
            </h3>
            <div class="box-tools">
                <span class="text-muted">
                    Showing {{ $transactions->count() }} transactions
                </span>
            </div>
        </div>
        <div class="box-body table-responsive" style="padding: 0;">
            <table class="table table-bordered table-hover table-striped" style="margin-bottom: 0;" id="ledgerTable">
                <thead>
                <tr>
                    <th>Trans ID</th>
                    <th>Loan ID</th>
                    <th>Client</th>
                    <th>Loan Officer</th>
                    <th>Recovery Specialist</th>
                    <th>Amount</th>
                    <th>Transaction Type</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Receipt #</th>
                    <th>Created By</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>
                            <strong>{{ $transaction->id }}</strong>
                        </td>
                        <td>
                            @if($transaction->loan)
                                <a href="{{ url('loan/'.$transaction->loan->id.'/show') }}" 
                                   data-toggle="tooltip" title="Click to view loan">
                                    {{ $transaction->loan->id }}
                                </a>
                                <br>
                                <small class="text-muted">{{ $transaction->loan->account_number ?? '' }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->loan && $transaction->loan->client)
                                <strong>{{ $transaction->loan->client->first_name }} {{ $transaction->loan->client->last_name }}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fa fa-phone"></i> {{ $transaction->loan->client->phone ?? 'N/A' }}
                                </small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->loan && $transaction->loan->loan_officer)
                                {{ $transaction->loan->loan_officer->first_name }} {{ $transaction->loan->loan_officer->last_name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($transaction->recovery_case && $transaction->recovery_case->assignedSpecialist)
                                <strong>{{ $transaction->recovery_case->assignedSpecialist->first_name }} 
                                {{ $transaction->recovery_case->assignedSpecialist->last_name }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $transaction->recovery_case->case_number ?? '' }}
                                </small>
                            @else
                                <span class="text-muted">Not Assigned</span>
                            @endif
                        </td>
                        <td>
                            <strong>K{{ number_format($transaction->credit ?? 0, 2) }}</strong>
                            @if($transaction->debit > 0)
                                <br>
                                <small class="text-danger">-K{{ number_format($transaction->debit, 2) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($transaction->transaction_type)
                                <span class="label label-{{ $transaction->transaction_type == 'repayment' ? 'success' : 'info' }}">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                </span>
                            @else
                                <span class="label label-default">N/A</span>
                            @endif
                            @if($transaction->payment_apply_to)
                                <br>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $transaction->payment_apply_to)) }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $transaction->date ? date('d M Y', strtotime($transaction->date)) : 'N/A' }}
                            <br>
                            <small class="text-muted">{{ $transaction->created_at ? $transaction->created_at->format('h:i A') : '' }}</small>
                        </td>
                        <td>
                            @if($transaction->payment_detail)
                                <span class="label label-primary">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->payment_detail->payment_type ?? 'N/A')) }}
                                </span>
                                @if($transaction->payment_detail->receipt)
                                    <br>
                                    <small>{{ $transaction->payment_detail->receipt }}</small>
                                @endif
                            @else
                                <span class="label label-default">N/A</span>
                            @endif
                        </td>
                        <td>
                            {{ $transaction->receipt_number ?? ($transaction->payment_detail ? $transaction->payment_detail->receipt : '-') }}
                        </td>
                        <td>
                            @if($transaction->created_by)
                                {{ $transaction->created_by->first_name }} {{ $transaction->created_by->last_name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="box box-primary">
        <div class="box-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No recovery transactions found for the selected period.
            </div>
        </div>
    </div>
@endif

@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    // Initialize ledger table with DataTable
    if ($('#ledgerTable').length > 0) {
        $('#ledgerTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-xs',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-xs',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-info btn-xs',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                }
            ],
            "paging": true,
            "lengthChange": true,
            "displayLength": 25,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "order": [[7, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [0]}
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
    }
    
    // Toggle custom date fields
    $('select[name="period"]').change(function() {
        if ($(this).val() === 'custom') {
            $('#customDateFields, #customDateFields2').show();
        } else {
            $('#customDateFields, #customDateFields2').hide();
        }
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
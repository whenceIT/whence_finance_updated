@extends('layouts.master')
@section('title')
    Approved Recovery Transactions
@endsection
@section('content')

<style>
.bento-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
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
    font-size: 36px;
    font-weight: 800;
    letter-spacing: -0.5px;
    line-height: 1;
}
.bento-footer {
    font-size: 12px;
    opacity: 0.85;
    margin-top: 8px;
}
.office-section {
    margin-bottom: 30px;
}
.office-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    margin-bottom: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.office-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}
.office-stats {
    font-size: 14px;
    opacity: 0.95;
}
@media (max-width: 768px) {
    .bento-stats {
        grid-template-columns: 1fr;
    }
    .bento-value {
        font-size: 28px;
    }
    .bento-icon {
        font-size: 2.5rem;
        margin-right: 15px;
    }
}
</style>

<!-- Bento Stats Cards -->
<div class="bento-stats">
    <div class="bento-card total-amount">
        <div class="bento-icon">
            <i class="fa fa-money"></i>
        </div>
        <div class="bento-content">
            <div class="bento-title">Total Amount Recovered</div>
            <div class="bento-value">K{{ number_format($totalAmount + $funds, 2) }}</div>
            <div class="bento-footer">Sum of all approved recovery transactions</div>
        </div>
    </div>
    
    <div class="bento-card total-cases">
        <div class="bento-icon">
            <i class="fa fa-folder-open"></i>
        </div>
        <div class="bento-content">
            <div class="bento-title">Total Cases</div>
            <div class="bento-value">{{ $totalCases }}</div>
            <div class="bento-footer">Unique recovery cases with transactions</div>
        </div>
    </div>
</div>

<!-- Transactions grouped by Office -->
@if(count($transactionsByOffice) > 0)
    @foreach($transactionsByOffice as $officeName => $officeTransactions)
    <div class="office-section">
        <div class="office-header">
            <h3 class="office-title">
                <i class="fa fa-building"></i> {{ $officeName }}
            </h3>
            <div class="office-stats">
                <strong>{{ $officeTransactions->count() }}</strong> Transactions | 
                <strong>K{{ number_format($officeTransactions->sum('credit'), 2) }}</strong>
            </div>
        </div>
        
        <div class="box box-primary" style="margin-top: 0; border-radius: 0 0 8px 8px;">
            <div class="box-body table-responsive" style="padding: 0;">
                <table class="table table-bordered table-hover table-striped office-table" style="margin-bottom: 0;">
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
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($officeTransactions as $transaction)
                        <tr>
                            <td>
                                <strong>{{ $transaction->id }}</strong>
                                @if($transaction->recovery_case)
                                    <br>
                                    <button type="button" class="btn btn-xs btn-info" data-toggle="modal" 
                                            data-target="#caseModal{{$transaction->id}}" title="View Case Details">
                                        <i class="fa fa-eye"></i> Case
                                    </button>
                                @endif
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
                            <td>
                                <a href="{{ url('loan/'.$transaction->loan_id.'/show') }}" 
                                   class="btn btn-xs btn-primary" title="View Loan">
                                    <i class="fa fa-eye"></i> Loan
                                </a>
                                @if($transaction->recovery_case)
                                    <a href="{{ url('recovery/case/'.$transaction->recovery_case->id.'/show') }}" 
                                       class="btn btn-xs btn-success" title="View Recovery Case" target="_blank">
                                        <i class="fa fa-file-text"></i> Case
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="box box-primary">
        <div class="box-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No approved recovery transactions found.
            </div>
        </div>
    </div>
@endif

<!-- Recovery Case Details Modals -->
@foreach($transactions as $transaction)
@if($transaction->recovery_case)
<div class="modal fade" id="caseModal{{$transaction->id}}" tabindex="-1" role="dialog" aria-labelledby="caseModalLabel{{$transaction->id}}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="caseModalLabel{{$transaction->id}}">
                    Recovery Case Details - {{ $transaction->recovery_case->case_number ?? 'N/A' }}
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><strong>Case Information</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Case Number:</strong></td>
                                <td>{{ $transaction->recovery_case->case_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>{{ $transaction->recovery_case->category_label ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="label label-{{ $transaction->recovery_case->status_color ?? 'default' }}">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->recovery_case->status ?? 'N/A')) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created Date:</strong></td>
                                <td>{{ $transaction->recovery_case->created_at ? $transaction->recovery_case->created_at->format('d M Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Approved Date:</strong></td>
                                <td>{{ $transaction->recovery_case->approved_date ? $transaction->recovery_case->approved_date->format('d M Y') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><strong>Financial Details</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Loan Outstanding:</strong></td>
                                <td>K{{ number_format($transaction->recovery_case->loan_outstanding_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount Recovered:</strong></td>
                                <td>K{{ number_format($transaction->recovery_case->amount_recovered ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Recovery Costs:</strong></td>
                                <td>K{{ number_format($transaction->recovery_case->recovery_costs ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Net Recovery:</strong></td>
                                <td>K{{ number_format($transaction->recovery_case->net_recovery ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Recovery Rate:</strong></td>
                                <td>{{ $transaction->recovery_case->recovery_rate ?? 0 }}%</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5><strong>Branch Information</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Origin Branch:</strong></td>
                                <td>{{ $transaction->recovery_case->originBranch->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Supporting Branch:</strong></td>
                                <td>{{ $transaction->recovery_case->supportingBranch->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Assigned Specialist:</strong></td>
                                <td>{{ $transaction->recovery_case->assignedSpecialist ? $transaction->recovery_case->assignedSpecialist->first_name . ' ' . $transaction->recovery_case->assignedSpecialist->last_name : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><strong>Attribution Percentages</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Recoveries Dept:</strong></td>
                                <td>{{ $transaction->recovery_case->recoveries_dept_attribution_pct ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td><strong>Origin Branch:</strong></td>
                                <td>{{ $transaction->recovery_case->origin_branch_attribution_pct ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td><strong>Supporting Branch:</strong></td>
                                <td>{{ $transaction->recovery_case->supporting_branch_attribution_pct ?? 0 }}%</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($transaction->recovery_case->notes)
                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Notes</strong></h5>
                        <p>{{ $transaction->recovery_case->notes }}</p>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <h5><strong>Transaction Details</strong></h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Transaction ID:</strong></td>
                                <td>{{ $transaction->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount:</strong></td>
                                <td>K{{ number_format($transaction->credit ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Transaction Date:</strong></td>
                                <td>{{ $transaction->date ? date('d M Y', strtotime($transaction->date)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Notes:</strong></td>
                                <td>{{ $transaction->notes ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a href="{{ url('recovery/case/'.$transaction->recovery_case->id.'/show') }}" 
                   class="btn btn-primary" target="_blank">
                    View Full Case Details
                </a>
                <a href="{{ url('loan/'.$transaction->loan_id.'/show') }}" 
                   class="btn btn-info" target="_blank">
                    View Loan Details
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    // Initialize each office table separately
    $('.office-table').each(function() {
        $(this).DataTable({
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
            "displayLength": 10,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "order": [[7, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [11]}
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
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection

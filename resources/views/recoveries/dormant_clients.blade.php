@extends('layouts.master')
@section('title')
    {{ trans_choice('general.client', 2) }} - Recovery Clients
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-users"></i> Recovery Clients
            </h3>
            <div class="box-tools pull-right">
                <ul class="nav nav-tabs">
                 
                    <li class="{{ $type == 'dormant' ? 'active' : '' }}">
                        <a href="{{ route('recovery.clients', ['type' => 'dormant']) }}">
                            <i class="fa fa-users"></i> Find Dormant Clients
                        </a>
                    </li>
                    <li class="{{ $type == 'overdue' ? 'active' : '' }}">
                        <a href="{{ route('recovery.clients', ['type' => 'overdue']) }}">
                            <i class="fa fa-exclamation-triangle"></i>Find Defaulted (Escalated) Clients
                        </a>
                    </li>
                    <li class="{{ $type == 'escalated' ? 'active' : '' }}">
                        <a href="{{ route('recovery.clients', ['type' => 'escalated']) }}">
                            <i class="fa fa-users"></i> Never Loaned
                        </a>
                    </li>
                    <li class="{{ $type == 'recovered-defults' ? 'active' : '' }}">
                        <a href="{{ route('recovery.clients', ['type' => 'recovered-defults']) }}" style="background-color: #5cb85c; color: white;">
                            <i class="fa fa-check-circle"></i> Recovered Defaulted Clients
                        </a>
                    </li>   
                    <li class="{{ $type == 'recovered' ? 'active' : '' }}">
                        <a href="{{ route('recovery.clients', ['type' => 'recovered']) }}" style="background-color: #5cb85c; color: white;">
                            <i class="fa fa-check-circle"></i> Recovered Dormant Clients
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="box-body" id="clients-container">
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-6">
                    <form method="GET" class="form-inline">
                        <input type="text" name="search" class="form-control input-sm" placeholder="Search by name or NRC..." value="{{ request()->get('search') }}" style="width: 250px;">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        @if(request()->get('search'))
                            <a href="{{ route('recovery.clients', ['type' => $type]) }}" class="btn btn-default btn-sm">Clear</a>
                        @endif
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" onclick="window.print()" class="btn btn-info btn-sm">
                        <i class="fa fa-print"></i> Print to PDF
                    </button>
                </div>
            </div>
            
            @if($type == 'dormant')
                <div class="alert alert-warning">
                    <i class="fa fa-info-circle"></i> 
                    Search for <strong>Dormant clients</strong> who haven't taken a loan in the last 3 months, and mark them as recovered to start unit share deduction in their next 3 loans
                </div>
            @elseif($type == 'escalated')
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Never Loaned Clients</strong> are active clients who have never taken a loan.
                </div>
            @elseif($type == 'overdue')
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Clients</strong> who have defaulted their last loans (Escalated Clients)
                </div>
            @elseif($type == 'recovered-defults')
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> 
                    <strong>Recovered Defaulted Clients</strong> are clients whose loans have been marked as recovered (esc_recovered = 1).
                </div>
            @endif

            <div id="clients-table-container">
                @if(count($clientsData) === 0)
                    <div class="alert alert-success text-center" style="padding: 40px;">
                        <i class="fa fa-check-circle" style="font-size: 48px; color: #5cb85c; margin-bottom: 15px;"></i>
                        <h4>
                            @if($type === 'recovered')
                                No Recovered Clients Found
                            @elseif($type === 'overdue')
                                No Clients with Overdue Loans Found
                            @elseif($type === 'escalated')
                                No Never Loaned Clients Found
                            @elseif($type === 'recovered-defults')
                                No Recovered Defaulted Clients Found
                            @else
                                No Dormant Clients Found
                            @endif
                        </h4>
                        <p class="text-muted">
                            @if($type === 'escalated')
                                All clients have taken at least one loan.
                            @elseif($type === 'recovered-defults')
                                No loans have been marked as recovered yet.
                            @else
                                All clients are up to date.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="clients-table" data-type="{{ $type }}">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client Name</th>
                                    <th>Mobile</th>
                                    <th>Office</th>
                                    <th>Loan Officer</th>
                                    
                                    @if($type === 'overdue' || $type === 'recovered-defults')
                                        <th>Loan ID</th>
                                        <th>Loan Amount</th>
                                        <th>First Repayment Date</th>
                                        <th>Days Overdue</th>
                                    @else
                                        <th>Last Loan Date</th>
                                        <th>Days Since Last Loan</th>
                                    @endif
                                    
                                    <th>Total Shared Loans</th>
                                    <th>Has Never Taken Loan</th>
                                    <th class="action-column">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            @foreach($clientsData as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td><strong>{{ $client->first_name }} {{ $client->last_name }}</strong></td>
                                        <td>{{ $client->mobile }}</td>
                                        <td>{{ $client->office ? $client->office->name : '-' }}</td>
                                        <td>{{ $client->staff ? $client->staff->first_name . ' ' . $client->staff->last_name : '-' }}</td>
                                        
                                        @if($type === 'overdue')
                                            @php
                                                $lastLoan = $client->loans->first();
                                                $overdueLoan = $lastLoan && $lastLoan->first_repayment_date && $lastLoan->first_repayment_date < \Carbon\Carbon::now()->toDateString() ? $lastLoan : null;
                                            @endphp
                                            @if($overdueLoan)
                                                <td><a href="{{ url('loan/' . $overdueLoan->id . '/show') }}">{{ $overdueLoan->id }}</a></td>
                                                <td>K{{ number_format($overdueLoan->principal ?? 0, 2) }}</td>
                                                <td>{{ $overdueLoan->first_repayment_date ? \Carbon\Carbon::parse($overdueLoan->first_repayment_date)->format('d M Y') : 'N/A' }}</td>
                                                <td><span class="badge bg-red">{{ $overdueLoan->first_repayment_date ? \Carbon\Carbon::parse($overdueLoan->first_repayment_date)->diffInDays(\Carbon\Carbon::now()) : 0 }} days</span></td>
                                            @else
                                                <td colspan="4">No overdue loan</td>
                                            @endif
                                        @elseif($type === 'recovered-defults')
                                            @php
                                                $recoveredLoan = $client->loans->where('esc_recovered', 1)->first();
                                            @endphp
                                            @if($recoveredLoan)
                                                <td><a href="{{ url('loan/' . $recoveredLoan->id . '/show') }}">{{ $recoveredLoan->id }}</a></td>
                                                <td>K{{ number_format($recoveredLoan->principal ?? 0, 2) }}</td>
                                                <td>{{ $recoveredLoan->first_repayment_date ? \Carbon\Carbon::parse($recoveredLoan->first_repayment_date)->format('d M Y') : 'N/A' }}</td>
                                                <td><span class="badge bg-green">Recovered</span></td>
                                            @else
                                                <td colspan="4">No recovered loan</td>
                                            @endif
                                        @else
                                            @php
                                                $lastLoan = $client->loans->first();
                                            @endphp
                                            <td>{{ $lastLoan ? $lastLoan->created_at->format('d M Y') : 'Never' }}</td>
                                            <td>{{ $lastLoan ? \Carbon\Carbon::now()->diffInDays($lastLoan->created_at) . ' days' : 'N/A' }}</td>
                                        @endif
                                            
                                            <td><span class="badge bg-gray">{{ $client->loans->where('shared', 1)->count() }}</span></td>
                                            <td>
                                                @php
                                                    $hasNeverTakenLoan = $client->loans->isEmpty();
                                                @endphp
                                                @if($type === 'escalated')
                                                    <span class="badge bg-green">Yes</span>
                                                @elseif($type === 'dormant')
                                                    @if($hasNeverTakenLoan)
                                                        <span class="badge bg-yellow">No (Never Loaned)</span>
                                                    @else
                                                        <span class="badge bg-red">No</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="action-column">
                                                <a href="{{ url('client/' . $client->id . '/show') }}" class="btn btn-xs btn-primary">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                
@if($type === 'overdue' || $type === 'recovered-defults')
                                                 @php
                                                     $loanToMark = $type === 'overdue' ? $client->loans->where('status', 'disbursed')->whereNotNull('first_repayment_date')->where('first_repayment_date', '<', \Carbon\Carbon::now()->toDateString())->first() : $client->loans->where('esc_recovered', 1)->first();
                                                 @endphp
                                                 @if($loanToMark && $loanToMark->esc_recovered != 1)
                                                     <button class="btn btn-xs btn-success" onclick="markEscRecovered({{ $client->id }}, {{ $loanToMark->id }})">
                                                         <i class="fa fa-check"></i> Mark Recovered
                                                     </button>
                                                 @elseif($loanToMark && $loanToMark->esc_recovered == 1)
                                                     <span class="badge bg-green">Recovered</span>
                                                 @endif
                                             @elseif($type === 'dormant')
                                                 <button class="btn btn-xs btn-success" onclick="markRecovered({{ $client->id }})">
                                                     <i class="fa fa-check"></i> Mark Recovered
                                                 </button>
                                             @endif
                                            </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

<style>
.nav-tabs > li > a {
    cursor: pointer !important;
}
.nav-tabs > li.active > a {
    background-color: #667eea !important;
    color: white !important;
    border-color: #667eea !important;
}
</style>

<style media="print">
    @page {
        size: landscape;
        margin: 20mm;
    }
    
    body {
        background: white !important;
    }
    
    .box {
        border: 1px solid #ddd;
        box-shadow: none;
    }
    
    .box-header {
        background: #f5f5f5;
        border-bottom: 2px solid #ddd;
    }
    
    .box-header h3 {
        color: #333;
    }
    
    .box-body {
        padding: 15px;
    }
    
    .table {
        border-collapse: collapse;
        width: 100%;
    }
    
    .table th, .table td {
        border: 1px solid #ddd !important;
        padding: 8px;
        text-align: left;
    }
    
    .table th {
        background-color: #f5f5f5;
        font-weight: bold;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
    
    .table-bordered {
        border: 1px solid #ddd;
    }
    
    .table-bordered th, .table-bordered td {
        border: 1px solid #ddd;
    }
    
    .nav-tabs {
        display: none;
    }
    
    .box-tools {
        display: none;
    }
    
    .alert {
        page-break-inside: avoid;
    }
    
    button {
        display: none;
    }
    
    .pagination, .dataTables_info, .dataTables_filter, .dataTables_length {
        display: none;
    }
    
    .content {
        padding: 0;
    }
    
    .content-header {
        display: none;
    }
    
    .row {
        margin: 0;
    }
    
    .col-md-6, .col-md-12 {
        width: 100%;
        max-width: 100%;
    }
    
    .btn-xs {
        display: none;
    }
    
    .action-column {
        display: none !important;
    }
    
    #clients-container {
        padding: 0;
    }
    
    #clients-table-container {
        border: 1px solid #ddd;
        box-shadow: none;
    }
    
    #clients-table-container > *:not(.box-body) {
        display: none !important;
    }
    
    #clients-table-container .box-body {
        padding: 15px;
        border: none;
    }
    
    .table-responsive {
        overflow: visible;
    }
</style>
<script>
    $(document).ready(function() {
        // Initialize DataTable for the clients table
        var type = $('#clients-table').data('type');
        
        $('#clients-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-xs',
                    title: type.charAt(0).toUpperCase() + type.slice(1) + ' Clients'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-xs',
                    orientation: 'landscape',
                    title: type.charAt(0).toUpperCase() + type.slice(1) + ' Clients'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-info btn-xs',
                    title: type.charAt(0).toUpperCase() + type.slice(1) + ' Clients'
                }
            ],
            "paging": true,
            "lengthChange": true,
            "displayLength": 20,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "order": [[0, "asc"]],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "Showing _START_ to _END_ of _TOTAL_ clients",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "(filtered from _MAX_ total clients)",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: true
        });
    });

    function markRecovered(clientId) {
        if (!confirm('Mark this client as recovered?')) {
            return;
        }

        $.ajax({
            url: '{{ route('client.mark_recovered', ['id' => '__CLIENT_ID__']) }}'.replace('__CLIENT_ID__', clientId),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Client marked as recovered!');
                    window.location.href = '{{ route('recovery.clients', ['type' => 'dormant']) }}';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please try again.');
            }
        });
    }

    function markEscRecovered(clientId, loanId) {
        if (!confirm('Mark this loan as recovered?')) {
            return;
        }

        $.ajax({
            url: '{{ route('client.mark_esc_recovered', ['clientId' => '__CLIENT_ID__', 'loanId' => '__LOAN_ID__']) }}'.replace('__CLIENT_ID__', clientId).replace('__LOAN_ID__', loanId),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Loan marked as recovered!');
                    window.location.href = '{{ route('recovery.clients', ['type' => 'recovered-defults']) }}';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please try again.');
            }
        });
    }
</script>
@endsection
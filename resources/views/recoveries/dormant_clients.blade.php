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
                            <i class="fa fa-users"></i> Dormant Clients
                        </a>
                    </li>
                    <li class="{{ $type == 'recovered' ? 'active' : '' }}">
                        <a href="{{ route('recovery.clients', ['type' => 'recovered']) }}">
                            <i class="fa fa-check-circle"></i> Recovered Clients
                        </a>
                    </li>
                    <li class="{{ $type == 'overdue' ? 'active' : '' }}">
                        <a href="{{ route('recovery.clients', ['type' => 'overdue']) }}">
                            <i class="fa fa-exclamation-triangle"></i> Overdue Loans
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
            </div>
            
            @if($type == 'dormant')
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> 
                    <strong>Dormant clients</strong> are active clients who either:
                    <ul style="margin-bottom: 0; margin-top: 5px;">
                        <li>Have never taken a loan, OR</li>
                        <li>Haven't taken a loan in the last 3 months</li>
                    </ul>
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
                            @else
                                No Dormant Clients Found
                            @endif
                        </h4>
                        <p class="text-muted">All clients are up to date.</p>
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
                                    
                                    @if($type === 'overdue')
                                        <th>Loan ID</th>
                                        <th>Loan Amount</th>
                                        <th>First Repayment Date</th>
                                        <th>Days Overdue</th>
                                    @else
                                        <th>Last Loan Date</th>
                                        <th>Days Since Last Loan</th>
                                    @endif
                                    
                                    <th>Total Shared Loans</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
@foreach($clientsData as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td><strong>{{ $client->first_name }} {{ $client->last_name }}</strong></td>
                                        <td>{{ $client->mobile }}</td>
                                        <td>{{ $client->office->name }}</td>
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
                                        @else
                                            @php
                                                $lastLoan = $client->loans->first();
                                            @endphp
                                            <td>{{ $lastLoan ? $lastLoan->created_at->format('d M Y') : 'Never' }}</td>
                                            <td>{{ $lastLoan ? \Carbon\Carbon::now()->diffInDays($lastLoan->created_at) . ' days' : 'N/A' }}</td>
                                        @endif
                                        
                                        <td><span class="badge bg-gray">{{ $client->loans->where('shared', 1)->count() }}</span></td>
                                        <td>
                                            <a href="{{ url('client/' . $client->id . '/show') }}" class="btn btn-xs btn-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            
                                            @if($type === 'dormant')
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
            url: '{{ url('client') }}/' + clientId + '/mark-recovered',
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
</script>
@endsection
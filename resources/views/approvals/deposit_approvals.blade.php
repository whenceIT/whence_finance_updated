@extends('layouts.master')
@section('title', 'Deposit Approvals')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Deposit Approvals</h3>
        </div>
        <div class="box-body">
            <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                <div class="form-group">
                    <label for="deposit_type" class="control-label">Deposit Type:</label>
                    <select name="deposit_type" id="deposit_type" class="form-control">
                        <option value="all">All Types</option>
                        @foreach($depositTypes as $type)
                            <option value="{{ $type->id }}" {{ request('deposit_type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Office</th>
                        <th>Deposit Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Bank Deposit Log ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                        <tr>
                            <td>{{ $deposit->date }}</td>
                            <td>{{ $deposit->office }}</td>
                            <td>{{ $deposit->deposit_type_name ?? 'N/A' }}</td>
                            <td>{{ number_format($deposit->amount, 2) }}</td>
                            <td>{{ $deposit->bank_deposit_log_method ?? 'N/A' }}</td>
                            <td>{{ $deposit->bank_deposit_log_reference_number ?? 'N/A' }}</td>
                            <td>{{ $deposit->bank_deposit_log_id ?? 'N/A' }}</td>
                            <td>
                                @if($deposit->status == 1)
                                    <span class="label label-success">Approved</span>
                                @elseif($deposit->status == 0)
                                    <span class="label label-danger">Declined</span>
                                @else
                                    <span class="label label-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($deposit->status !== 1)
                                    <button type="button" class="btn btn-success btn-xs approve-btn" 
                                            data-id="{{ $deposit->id }}" 
                                            data-action="approve"
                                            data-message="Are you sure you want to APPROVE this deposit?">
                                        <i class="fa fa-check"></i> Approve
                                    </button>
                                @endif
                                @if($deposit->status !== 0)
                                    <button type="button" class="btn btn-danger btn-xs decline-btn" 
                                            data-id="{{ $deposit->id }}" 
                                            data-action="decline"
                                            data-message="Are you sure you want to DECLINE this deposit?">
                                        <i class="fa fa-times"></i> Decline
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No deposits found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-center">
                {{ $deposits->links() }}
            </div>
        </div>
    </div>

    <script>
    $(document).on('click', '.approve-btn, .decline-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var action = btn.data('action');
        var message = btn.data('message');
        
        if (confirm(message)) {
            $.post("{{ route('approvals.deposit-approvals.action', ['', '']) }}".replace('', btn.data('id')).replace('', action), {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                location.reload();
            }).fail(function() {
                alert('Action failed. Please try again.');
            });
        }
    });
    </script>
@endsection
@extends('layouts.master')
@section('title')
    Pending Transactions - Provincial Ledger
@endsection
@include('components.kilo-alert')
@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-clock-o"></i> Pending Transactions</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-success btn-sm" id="bulk-approve-btn">
                <i class="fa fa-check"></i> Approve Selected
            </button>
            <button type="button" class="btn btn-success btn-sm" id="approve-all-btn">
                <i class="fa fa-check-circle"></i> Approve All
            </button>
            <button type="button" class="btn btn-danger btn-sm" id="bulk-decline-btn">
                <i class="fa fa-times"></i> Decline Selected
            </button>
            <button type="button" class="btn btn-danger btn-sm" id="decline-all-btn">
                <i class="fa fa-times-circle"></i> Decline All
            </button>
            <a href="{{ url('provincial-transactions/approved') }}" class="btn btn-default btn-sm">View Approved</a>
        </div>
    </div>
    <div class="box-body no-padding">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 30px;">
                        <input type="checkbox" id="select-all">
                    </th>
            <th>Date</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Province</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>
                        <input type="checkbox" class="select-tx" value="{{ $tx->id }}">
                    </td>
                    <td>{{ $tx->transaction_date ?? $tx->created_at->format('d M Y') }}</td>
                    <td><strong>{{ $tx->title }}</strong></td>
                    <td>
                        <span class="label {{ $tx->type == 'income' ? 'label-success' : 'label-danger' }}">
                            {{ ucfirst($tx->type) }}
                        </span>
                    </td>
                    <td>K{{ number_format($tx->amount, 2) }}</td>
                    <td>{{ $tx->province->name ?? 'N/A' }}</td>
                    <td>
                        <button class="btn btn-xs btn-primary approve-tx" data-id="{{ $tx->id }}">
                            <i class="fa fa-check"></i> Approve
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                        <i class="fa fa-check-circle" style="font-size: 48px; color: #5cb85c; margin-bottom: 10px;"></i>
                        <h4>No Pending Transactions</h4>
                        <p>All transactions have been processed.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#select-all').on('click', function() {
        $('.select-tx').prop('checked', this.checked);
    });

    $('#bulk-approve-btn').on('click', function() {
        var selected = $('.select-tx:checked').map(function() {
            return this.value;
        }).get();
        
        if (selected.length === 0) {
            window.KiloAlert.warning('Please select at least one transaction to approve.');
            return;
        }
        
        if (confirm('Approve ' + selected.length + ' transaction(s)?')) {
            $.post('{{ url("provincial-transactions/bulk-approve") }}', {
                _token: "{{ csrf_token() }}",
                ids: selected
            }, function(response) {
                if (response.success) {
                    window.KiloAlert.success('Transactions approved successfully');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.KiloAlert.error('Error: ' + response.message);
                }
            }).fail(function() {
                window.KiloAlert.error('Action failed. Please try again.');
            });
        }
    });

    $('#approve-all-btn').on('click', function() {
        if (confirm('Approve ALL pending transactions?')) {
            $.post('{{ url("provincial-transactions/approve-all") }}', {
                _token: "{{ csrf_token() }}"
            }, function(response) {
                if (response.success) {
                    window.KiloAlert.success('All transactions approved successfully');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.KiloAlert.error('Error: ' + response.message);
                }
            }).fail(function() {
                window.KiloAlert.error('Action failed. Please try again.');
            });
        }
    });

    $('#bulk-decline-btn').on('click', function() {
        var selected = $('.select-tx:checked').map(function() {
            return this.value;
        }).get();
        
        if (selected.length === 0) {
            window.KiloAlert.warning('Please select at least one transaction to decline.');
            return;
        }
        
        if (confirm('Decline ' + selected.length + ' transaction(s)?')) {
            $.ajax({
                url: '{{ url("provincial-transactions/bulk-decline") }}',
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: selected
                },
                success: function(response) {
                    if (response.success) {
                        window.KiloAlert.success('Transactions declined successfully');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        window.KiloAlert.error('Error: ' + response.message);
                    }
                },
                error: function() {
                    window.KiloAlert.error('Action failed. Please try again.');
                }
            });
        }
    });

    $('#decline-all-btn').on('click', function() {
        if (confirm('Decline ALL pending transactions?')) {
            $.post('{{ url("provincial-transactions/decline-all") }}', {
                _token: "{{ csrf_token() }}",
                province_id: $('#province_id').val()
            }, function(response) {
                if (response.success) {
                    window.KiloAlert.success(response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.KiloAlert.error('Error: ' + response.message);
                }
            }).fail(function() {
                window.KiloAlert.error('Action failed. Please try again.');
            });
        }
    });

    $('.approve-tx').on('click', function() {
        var id = $(this).data('id');
        if (confirm('Approve this transaction?')) {
            $.post('{{ url("provincial-transactions") }}/' + id + '/approve', {
                _token: "{{ csrf_token() }}"
            }, function(response) {
                if (response.success) {
                    window.KiloAlert.success('Transaction approved');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.KiloAlert.error('Error: ' + response.message);
                }
            }).fail(function() {
                window.KiloAlert.error('Action failed. Please try again.');
            });
        }
    });
});
</script>
@endsection
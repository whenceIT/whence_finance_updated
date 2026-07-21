@extends('layouts.master')

@section('title')
    Setup Debt Management
@endsection
@php
    $blockerUser = Sentinel::getUser();
    $debtBlocker = \App\Helpers\BlockerHelper::debt_blocker($blockerUser);
@endphp

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Setup Debt Management</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-sm btn-success" onclick="openCostModal()">
                        <i class="fa fa-plus"></i> Add Setup Debt Cost
                    </button>
                </div>
            </div>
            <div class="box-body">
                <p class="text-muted">Manage setup debt costs for branches and record payment transactions.</p>

                <!-- Summary Cards -->
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-md-3">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ number_format(array_sum(array_column($rows, 'amount')), 0) }}</h3>
                                <p>Total Debt</p>
                            </div>
                            <div class="icon"><i class="fa fa-file-text"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{ number_format(array_sum(array_column($rows, 'total_paid')), 0) }}</h3>
                                <p>Total Paid</p>
                            </div>
                            <div class="icon"><i class="fa fa-check-circle"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>{{ number_format(array_sum(array_column($rows, 'balance')), 0) }}</h3>
                                <p>Total Balance</p>
                            </div>
                            <div class="icon"><i class="fa fa-exclamation-circle"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>{{ count($rows) }}</h3>
                                <p>Branches</p>
                            </div>
                            <div class="icon"><i class="fa fa-building"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Debt Costs Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table" id="costs-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Branch</th>
                                <th>Description</th>
                                <th style="width:120px;">Debt Amount</th>
                                <th style="width:120px;">Paid</th>
                                <th style="width:120px;">Balance</th>
                                <th style="width:100px;">Created</th>
                                <th style="width:150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $index => $row)
                            @php
                                $progress = $row['amount'] > 0 ? ($row['total_paid'] / $row['amount']) * 100 : 0;
                                $statusClass = $row['balance'] <= 0 ? 'success' : ($progress > 50 ? 'warning' : 'danger');
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $row['office']->name }}</strong>
                                    @if($row['office']->external_id)
                                        <br><small class="text-muted">{{ $row['office']->external_id }}</small>
                                    @endif
                                </td>
                                <td>{{ $row['description'] ?: '—' }}</td>
                                <td style="font-weight:700;">{{ number_format($row['amount'], 2) }}</td>
                                <td style="color:#27ae60;font-weight:600;">{{ number_format($row['total_paid'], 2) }}</td>
                                <td style="font-weight:700;color:{{ $row['balance'] > 0 ? '#e74c3c' : '#27ae60' }};">
                                    {{ number_format($row['balance'], 2) }}
                                    <div class="progress" style="height:4px;margin-top:4px;margin-bottom:0;">
                                        <div class="progress-bar progress-bar-{{ $statusClass }}" 
                                             style="width:{{ min($progress, 100) }}%"></div>
                                    </div>
                                </td>
                                <td><small>{{ $row['created_at']->format('d M Y') }}</small></td>
                                <td>
                                    <button class="btn btn-xs btn-primary" onclick="openTransactionModal({{ $row['id'] }}, '{{ $row['office']->name }}', {{ $row['office']->id }})" title="Add Payment">
                                        <i class="fa fa-money"></i> Pay
                                    </button>
                                    <button class="btn btn-xs btn-info" onclick="viewTransactions({{ $row['id'] }}, '{{ $row['office']->name }}')" title="View Payments">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <button class="btn btn-xs btn-warning" onclick="editCost({{ $row['id'] }})" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    @if($row['balance'] > 0)
                                    <button class="btn btn-xs btn-danger" onclick="blockOffice({{ $row['office']->id }}, '{{ addslashes($row['office']->name) }}')" title="Block">
                                        <i class="fa fa-ban"></i> Block
                                    </button>
                                    @endif
                                    <button class="btn btn-xs btn-danger" onclick="deleteCost({{ $row['id'] }})" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center" style="padding:24px;color:#888;">No setup debt costs recorded yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Cost Modal -->
<div class="modal fade" id="costModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="costModalTitle">Add Setup Debt Cost</h4>
            </div>
            <form id="costForm">
                <div class="modal-body">
                    <input type="hidden" id="cost_id" name="cost_id">
                    
                    <div class="form-group">
                        <label>Branch <span class="text-danger">*</span></label>
                        <select class="form-control" id="cost_office_id" name="office_id" required>
                            <option value="">Select Branch</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Debt Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cost_amount" name="amount" 
                               step="0.01" min="0" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="cost_description" name="description" 
                                  rows="3" placeholder="Optional description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="transactionModalTitle">Record Payment</h4>
            </div>
            <form id="transactionForm">
                <div class="modal-body">
                    <input type="hidden" id="trans_cost_id" name="setup_debt_cost_id">
                    <input type="hidden" id="trans_office_id" name="office_id">
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Recording payment for: <strong id="trans_office_name"></strong>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="trans_amount" name="amount" 
                               step="0.01" min="0" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="trans_date" name="transaction_date" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Reference Number</label>
                        <input type="text" class="form-control" id="trans_reference_number" name="reference_number" maxlength="100" placeholder="Optional reference or receipt number">
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" id="trans_notes" name="notes" 
                                  rows="3" placeholder="Optional payment notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .shimmer-trans-wrapper {
        padding: 15px;
    }
    .shimmer-trans-row {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
        align-items: center;
    }
    .shimmer-trans-cell {
        height: 20px;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        border-radius: 4px;
        animation: shimmer-trans 1.5s infinite;
    }
    @keyframes shimmer-trans {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
<!-- View Transactions Modal -->
<div class="modal fade" id="viewTransactionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="viewTransactionsTitle">Payment History</h4>
            </div>
            <div class="modal-body">
                <div id="transactionsContent">
                    <div class="shimmer-trans-wrapper">
                        <div class="shimmer-trans-row"><div class="shimmer-trans-cell" style="width:40px;"></div><div class="shimmer-trans-cell"></div><div class="shimmer-trans-cell" style="width:120px;"></div><div class="shimmer-trans-cell" style="width:80px;"></div><div class="shimmer-trans-cell" style="width:100px;"></div><div class="shimmer-trans-cell" style="width:60px;"></div></div>
                        <div class="shimmer-trans-row"><div class="shimmer-trans-cell" style="width:40px;"></div><div class="shimmer-trans-cell" style="width:80%;"></div><div class="shimmer-trans-cell" style="width:120px;"></div><div class="shimmer-trans-cell" style="width:80px;"></div><div class="shimmer-trans-cell" style="width:100px;"></div><div class="shimmer-trans-cell" style="width:60px;"></div></div>
                        <div class="shimmer-trans-row"><div class="shimmer-trans-cell" style="width:40px;"></div><div class="shimmer-trans-cell" style="width:80%;"></div><div class="shimmer-trans-cell" style="width:120px;"></div><div class="shimmer-trans-cell" style="width:80px;"></div><div class="shimmer-trans-cell" style="width:100px;"></div><div class="shimmer-trans-cell" style="width:60px;"></div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#costs-table').DataTable({
        order: [[5, 'desc']], // Sort by balance descending
        pageLength: 25,
    });
});

function openCostModal() {
    $('#costModalTitle').text('Add Setup Debt Cost');
    $('#costForm')[0].reset();
    $('#cost_id').val('');
    $('#costModal').modal('show');
}

function editCost(costId) {
    $.get('/risk/setup-debt-costs/' + costId, function(data) {
        $('#costModalTitle').text('Edit Setup Debt Cost');
        $('#cost_id').val(data.id);
        $('#cost_office_id').val(data.office_id);
        $('#cost_amount').val(data.amount);
        $('#cost_description').val(data.description);
        $('#costModal').modal('show');
    }).fail(function() {
        alert('Failed to load cost data');
    });
}

function deleteCost(costId) {
    if (!confirm('Are you sure you want to delete this setup debt cost? All associated transactions will also be deleted.')) {
        return;
    }
    
    $.ajax({
        url: '/risk/setup-debt-costs/' + costId,
        type: 'DELETE',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function() {
            alert('Failed to delete cost');
        }
    });
}

$('#costForm').on('submit', function(e) {
    e.preventDefault();
    
    const costId = $('#cost_id').val();
    const url = costId ? '/risk/setup-debt-costs/' + costId : '/risk/setup-debt-costs';
    const method = costId ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        type: method,
        data: $(this).serialize(),
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            alert(response.message);
            $('#costModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Error: ' + (xhr.responseJSON?.message || 'Failed to save'));
        }
    });
});

function openTransactionModal(costId, officeName, officeId) {
    $('#transactionForm')[0].reset();
    $('#trans_cost_id').val(costId);
    $('#trans_office_id').val(officeId);
    $('#trans_office_name').text(officeName);
    $('#trans_date').val('{{ date("Y-m-d") }}');
    $('#trans_reference_number').val('');
    $('#transactionModal').modal('show');
}

$('#transactionForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '/risk/setup-debt-transactions',
        type: 'POST',
        data: $(this).serialize(),
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            alert(response.message);
            $('#transactionModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Error: ' + (xhr.responseJSON?.message || 'Failed to record transaction'));
        }
    });
});

function viewTransactions(costId, officeName) {
    $('#viewTransactionsTitle').text('Payment History - ' + officeName);
    $('#viewTransactionsModal').modal('show');
    
    $.get('/risk/setup-debt-costs/' + costId + '/transactions', function(data) {
        let html = '';
        
        if (data.transactions.length === 0) {
            html = '<div class="alert alert-info">No payments recorded yet.</div>';
        } else {
            html = '<table class="table table-bordered table-striped"><thead><tr>' +
                   '<th style="width:40px;">#</th>' +
                   '<th>Date</th>' +
                   '<th>Amount</th>' +
                   '<th>Notes</th>' +
                   '<th>Created By</th>' +
                   '<th style="width:60px;">Action</th>' +
                   '</tr></thead><tbody>';
            
            data.transactions.forEach(function(trans, index) {
                html += '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + trans.transaction_date + '</td>' +
                        '<td style="font-weight:700;color:#27ae60;">' + parseFloat(trans.amount).toLocaleString() + '</td>' +
                        '<td>' + (trans.notes || '—') + '</td>' +
                        '<td><small>' + (trans.creator ? trans.creator.first_name + ' ' + trans.creator.last_name : 'N/A') + '</small></td>' +
                        '<td><button class="btn btn-xs btn-danger" onclick="deleteTransaction(' + trans.id + ')" title="Delete"><i class="fa fa-trash"></i></button></td>' +
                        '</tr>';
            });
            
            html += '</tbody></table>';
            html += '<div class="alert alert-success"><strong>Total Paid:</strong> ' + 
                    parseFloat(data.total_paid).toLocaleString() + '</div>';
        }
        
        $('#transactionsContent').html(html);
    }).fail(function() {
        $('#transactionsContent').html('<div class="alert alert-danger">Failed to load transactions</div>');
    });
}

function blockOffice(officeId, officeName) {
    if (!confirm('Block ' + officeName + ' due to unpaid setup debt (minimum 5,000)?')) {
        return;
    }
    
    $.ajax({
        url: '{{ route("blockages.store") }}',
        type: 'POST',
        data: {
            office_id: officeId,
            reason: 'You have not paid 5,000 minimum towards set up cost',
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Office blocked successfully');
                location.reload();
            } else {
                alert(response.message || 'Failed to block office');
            }
        },
        error: function() {
            alert('Failed to block office');
        }
    });
}

function deleteTransaction(transId) {
    if (!confirm('Are you sure you want to delete this transaction?')) {
        return;
    }
    
    $.ajax({
        url: '/risk/setup-debt-transactions/' + transId,
        type: 'DELETE',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            alert(response.message);
            $('#viewTransactionsModal').modal('hide');
            location.reload();
        },
        error: function() {
            alert('Failed to delete transaction');
        }
    });
}
</script>
@endsection

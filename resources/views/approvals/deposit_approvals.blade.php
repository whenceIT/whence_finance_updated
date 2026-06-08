@extends('layouts.master')
@section('title', 'Deposit Approvals')
@include('components.kilo-alert')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Deposit Approvals</h3>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-6">
                    <button type="button" class="btn btn-success btn-sm" id="bulk-approve-btn">
                        <i class="fa fa-check"></i> Approve Selected
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="approve-all-btn">
                        <i class="fa fa-check-circle"></i> Approve All
                    </button>
                </div>
                <div class="col-md-6 text-right">
                    <input type="text" id="search-input" class="form-control input-sm" placeholder="Search..." style="width: 200px;">
                </div>
            </div>
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
                        <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                        <th>Date</th>
                        <th>Office</th>
                        <th>Deposit Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Log ID</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                        <tr>
                            <td><input type="checkbox" class="row-select" value="{{ $deposit->id }}"></td>
                            <td>{{ $deposit->date }}</td>
                            <td>{{ $deposit->office_name ?? $deposit->office }}</td>
                            <td>{{ $deposit->deposit_type_name ?? 'N/A' }}</td>
                            <td>{{ number_format($deposit->amount, 2) }}</td>
                            <td>{{ $deposit->bank_deposit_log_method ?? 'N/A' }}</td>
                            <td>{{ $deposit->bank_deposit_log_reference_number ?? 'N/A' }}</td>
                            <td>{{ $deposit->bank_deposit_log_id ?? 'N/A' }}</td>
                            <td>
                                @if($deposit->bank_deposit_log_user_first_name)
                                    {{ $deposit->bank_deposit_log_user_first_name }} {{ $deposit->bank_deposit_log_user_last_name }}
                                @elseif($deposit->bank_deposit_log_user_id)
                                    {{ $deposit->bank_deposit_log_user_id }}
                                @else
                                    N/A
                                @endif
                            </td>
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
                                            data-url="{{ url('approvals/deposit-approvals/'.$deposit->id.'/1') }}"
                                            data-message="Are you sure you want to APPROVE this deposit?">
                                        <i class="fa fa-check"></i> Approve
                                    </button>
                                @endif
                                @if($deposit->status !== 0)
                                    <button type="button" class="btn btn-danger btn-xs decline-btn" 
                                            data-id="{{ $deposit->id }}" 
                                            data-url="{{ url('approvals/deposit-approvals/'.$deposit->id.'/0') }}"
                                            data-message="Are you sure you want to DECLINE this deposit?">
                                        <i class="fa fa-times"></i> Decline
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">No deposits found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="text-center">
                {{ $deposits->links() }}
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        $('#select-all').on('click', function() {
            $('.row-select').prop('checked', this.checked);
        });

        $('#bulk-approve-btn').on('click', function() {
            var selected = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            if (selected.length === 0) {
                window.KiloAlert.warning('Please select at least one deposit to approve.');
                return;
            }
            
            if (confirm('Are you sure you want to APPROVE all selected deposits?')) {
                $.post('{{ url("approvals/deposit-approvals/bulk-approve") }}', {
                    _token: "{{ csrf_token() }}",
                    ids: selected
                }, function(response) {
                    if (response.success) {
                        window.KiloAlert.success(response.message);
                    } else {
                        window.KiloAlert.error(response.message || 'Action failed.');
                    }
                    setTimeout(() => location.reload(), 1500);
                }).fail(function() {
                    window.KiloAlert.error('Action failed. Please try again.');
                });
            }
        });

        $('#approve-all-btn').on('click', function() {
            if (confirm('Are you sure you want to APPROVE ALL deposits matching the current filters?')) {
                $.post('{{ url("approvals/deposit-approvals/approve-all") }}', {
                    _token: "{{ csrf_token() }}",
                    deposit_type: $('#deposit_type').val(),
                    office_id: $('#office_id').val()
                }, function(response) {
                    if (response.success) {
                        window.KiloAlert.success(response.message);
                    } else {
                        window.KiloAlert.error(response.message || 'Action failed.');
                    }
                    setTimeout(() => location.reload(), 1500);
                }).fail(function() {
                    window.KiloAlert.error('Action failed. Please try again.');
                });
            }
        });

        $('#search-input').on('keyup', function() {
            var searchVal = this.value.toLowerCase();
            $('table tbody tr').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(searchVal) > -1);
            });
        });

        $(document).on('click', '.approve-btn, .decline-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var url = btn.data('url');
            var message = btn.data('message');
            
            if (confirm(message)) {
                $.post(url, {
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    if (response.success) {
                        window.KiloAlert.success(response.message);
                    } else {
                        window.KiloAlert.error(response.message || 'Action failed.');
                    }
                    setTimeout(() => location.reload(), 1500);
                }).fail(function() {
                    window.KiloAlert.error('Action failed. Please try again.');
                });
            }
        });
    });
    </script>
@endsection
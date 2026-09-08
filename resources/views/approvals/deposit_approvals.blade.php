@extends('layouts.master')
@section('title', 'Deposit Approvals')
@include('components.kilo-alert')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Deposit Approvals</h3>
        </div>

        <br>
        <style>
            .deposit-tabs .nav-tabs {
                border-bottom: 2px solid #ddd;
                margin-bottom: 15px;
            }
            .deposit-tabs .nav-tabs > li {
                background: #f8f8f8;
                border: 1px solid #ddd;
                border-bottom: none;
                border-top-left-radius: 6px;
                border-top-right-radius: 6px;
                margin-right: 5px;
            }
            .deposit-tabs .nav-tabs > li > a {
                color: #6c757d;
                font-weight: 600;
                padding: 10px 20px;
                border: none;
                background: transparent;
            }
            .deposit-tabs .nav-tabs > li > a:hover {
                background: #e9ecef;
                color: #495057;
            }
            .deposit-tabs .nav-tabs > li.active > a,
            .deposit-tabs .nav-tabs > li.active > a:hover {
                background: #28a745;
                color: #fff;
                border: none;
            }
        </style>
        <ul class="nav nav-tabs deposit-tabs" style="margin: 0 0 15px 0;">
            <li class="active"><a data-toggle="tab" href="#mandatory-deposits"><i class="fa fa-check"></i> Mandatory Deposit Approvals</a></li>
            <li><a data-toggle="tab" href="#setup-debt-deposits"><i class="fa fa-money"></i> Setup Debt Deposits</a></li>
        </ul>

        <div class="tab-content">
            <div id="mandatory-deposits" class="tab-pane in active">

                <div class="box-body">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success btn-sm" id="bulk-approve-btn">
                                <i class="fa fa-check"></i> Approve Selected
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="approve-all-btn">
                                <i class="fa fa-check-circle"></i> Approve All
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="bulk-decline-btn">
                                <i class="fa fa-times"></i> Decline Selected
                            </button>
                            <!-- <button type="button" class="btn btn-danger btn-sm" id="decline-all-btn">
                                <i class="fa fa-times-circle"></i> Decline All
                            </button> -->
                        </div>
                        <div class="col-md-6 text-right">
                            <input type="text" id="search-input" class="form-control input-sm" placeholder="Search..." style="width: 200px;">
                        </div>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <p class="text-muted">View and approved or decline the mandatory fees for branches.</p>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                                <th>For</th>
                                <th>Office</th>
                                <th>Deposit Type</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Branch Manager</th>
                                <th>Recorded on</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $deposit)
                                <tr>
                                    <td><input type="checkbox" class="row-select" value="{{ $deposit->id }}"></td>
                                    <td>{{ \Carbon\Carbon::parse($deposit->date)->format('F Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::title(App\Models\Office::officeName($deposit->office)->name) }}</td>
                                    <td>{{ $deposit->depositTypeInfo ? $deposit->depositTypeInfo->name : 'Invalid Entry' }}</td>
                                    <td>{{ number_format($deposit->amount, 2) }}</td>
                                    <td>{{ $deposit->bankDepositLog ? $deposit->bankDepositLog->deposit_method : 'Invalid Entry' }}</td>
                                    <td>{{ $deposit->bankDepositLog ? $deposit->bankDepositLog->reference_number : 'Invalid Entry' }}</td>
                                    <td>
                                        @if($deposit->bankDepositLog && $deposit->bankDepositLog->user && $deposit->bankDepositLog->user->first_name)
                                            {{\Illuminate\Support\Str::title( $deposit->bankDepositLog->user->first_name.' '.$deposit->bankDepositLog->user->last_name ) }} 
                                        @elseif($deposit->bankDepositLog && $deposit->bankDepositLog->user_id)
                                            {{ $deposit->bankDepositLog->user_id }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        {{ optional($deposit->bankDepositLog)->created_date ? \Carbon\Carbon::parse($deposit->bankDepositLog->created_date)->format('d M Y, H:i') : '—' }}
                                    </td>
                                    <td>
                                        @if($deposit->status == 1)
                                            <span class="label label-success">Approved</span>
                                        @elseif($deposit->status == NULL)
                                            <span class="label label-warning">Pending</span>
                                        @else
                                            <span class="label label-danger">Declined</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deposit->status !== 1 && $deposit->bankDepositLog != null)
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
                                <tr><td colspan="11" class="text-center">
                                    <div class="empty-state">
                                        <img src="https://img.magnific.com/free-vector/illustration-character-saving-money-safe_53876-37248.jpg" alt="No deposits found" style="max-width: 200px; opacity: 0.6;">
                                        <p class="text-muted" style="margin-top: 10px;">No deposits found.</p>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $deposits->links() }}
                    </div>
                </div>
            </div>

            <div id="setup-debt-deposits" class="tab-pane">
                <div class="box-body">

                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success btn-sm" id="bulk-approve-debt-btn">
                                <i class="fa fa-check"></i> Approve Selected
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="bulk-decline-debt-btn">
                                <i class="fa fa-times"></i> Decline Selected
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <input type="text" id="search-input-debt" class="form-control input-sm" placeholder="Search..." style="width: 200px;">
                        </div>
                    </div>

                    <p class="text-muted" style="padding-top: 12px;">View and approved or decline setup debt transactions for branches.</p>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="select-all-debt"></th>
                                <th>Branch</th>
                                <th>Description</th>
                                <th style="width: 120px;">Amount</th>
                                <th style="width: 120px;">Date</th>
                                <th style="width: 120px;">Reference</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;">Created</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($setupDebtDeposits ?? [] as $index => $transaction)
                                <tr>
                                    <td><input type="checkbox" class="row-select-debt" value="{{ $transaction->id }}"></td>
                                    <td>{{ $transaction->office->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->setupDebtCost->description ?: '—' }}</td>
                                    <td style="font-weight:700;">{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') : '—' }}</td>
                                    <td>{{ $transaction->reference_number ?: '—' }}</td>
                                    
                                    <td>
                                        @if($transaction->status == 1)
                                            <span class="label label-success">Approved</span>
                                        @elseif($transaction->status == NULL || $transaction->status == 0)
                                            <span class="label label-warning">Pending</span>
                                        @else
                                            <span class="label label-danger">Declined</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $transaction->created_at ? $transaction->created_at->format('d M Y') : '—' }}</small></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-xs approve-debt-btn" 
                                                data-id="{{ $transaction->id }}" 
                                                data-url="{{ url('approvals/setup-debt/'.$transaction->id.'/1') }}"
                                                data-message="Are you sure you want to APPROVE this setup debt transaction?">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs decline-debt-btn" 
                                                data-id="{{ $transaction->id }}" 
                                                data-url="{{ url('approvals/setup-debt/'.$transaction->id.'/0') }}"
                                                data-message="Are you sure you want to DECLINE this setup debt transaction?">
                                            <i class="fa fa-times"></i> Decline
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">
                                    <div class="empty-state">
                                        <img src="https://img.magnific.com/free-vector/illustration-character-saving-money-safe_53876-37248.jpg" alt="No setup debt transactions found" style="max-width: 200px; opacity: 0.6;">
                                        <p class="text-muted" style="margin-top: 10px;">No setup debt transactions found.</p>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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

        $('#bulk-decline-btn').on('click', function() {
            var selected = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            if (selected.length === 0) {
                window.KiloAlert.warning('Please select at least one deposit to decline.');
                return;
            }
            
            if (confirm('Are you sure you want to DECLINE all selected deposits?')) {
                $.post('{{ url("approvals/deposit-approvals/bulk-decline") }}', {
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

        $('#decline-all-btn').on('click', function() {
            if (confirm('Are you sure you want to DECLINE ALL deposits matching the current filters?')) {
                $.post('{{ url("approvals/deposit-approvals/decline-all") }}', {
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
            $('#mandatory-deposits table tbody tr').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(searchVal) > -1);
            });
        });

        $('#search-input-debt').on('keyup', function() {
            var searchVal = this.value.toLowerCase();
            $('#setup-debt-deposits table tbody tr').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(searchVal) > -1);
            });
        });

        $('#select-all-debt').on('click', function() {
            $('.row-select-debt').prop('checked', this.checked);
        });

        $('#bulk-approve-debt-btn').on('click', function() {
            var selected = $('.row-select-debt:checked').map(function() {
                return this.value;
            }).get();

            if (selected.length === 0) {
                window.KiloAlert.warning('Please select at least one setup debt transaction to approve.');
                return;
            }

            if (confirm('Are you sure you want to APPROVE all selected setup debt transactions?')) {
                $.post('{{ url("approvals/setup-debt/bulk-approve") }}', {
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

        $('#bulk-decline-debt-btn').on('click', function() {
            var selected = $('.row-select-debt:checked').map(function() {
                return this.value;
            }).get();

            if (selected.length === 0) {
                window.KiloAlert.warning('Please select at least one setup debt transaction to decline.');
                return;
            }

            if (confirm('Are you sure you want to DECLINE all selected setup debt transactions?')) {
                $.post('{{ url("approvals/setup-debt/bulk-decline") }}', {
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

        $(document).on('click', '.approve-debt-btn, .decline-debt-btn', function(e) {
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
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
           
        </div>
        <div class="box-body table-responsive">
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
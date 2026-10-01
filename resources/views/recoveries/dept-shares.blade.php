@extends('layouts.master')

@section('title')
    Recovery Department - Unit Shares
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#unitShareModal">
                        <i class="fa fa-plus"></i> Record Unit Share
                    </button>
                </div>
                <h3 class="box-title"><i class="fa fa-share"></i> Unit Shares</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Unit Share</span>
                                <span class="info-box-number">K {{ number_format($totalUnitShare, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-share"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Records</span>
                                <span class="info-box-number">{{ $unitShares->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-purple"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Linked to Loans</span>
                                <span class="info-box-number">{{ $withLoan }} <small>/ {{ $unitShares->count() }}</small></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-navy"><i class="fa fa-database"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Distinct Loans Covered</span>
                                <span class="info-box-number">{{ $totalLoans }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Details</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="unitShareTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Loan</th>
                                        <th>Client</th>
                                        <th>Case Category</th>
                                        <th>Office</th>
                                        <th>Recorded By</th>
                                        <th>Unit</th>
                                        <th class="text-right">Amount</th>
                                        <th>Notes</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($unitShares as $share)
                                        @php
                                            $client = $share->loan?->client;
                                            $case   = $share->loan?->recoveryCase;
                                        @endphp
                                        <tr>
                                            <td>{{ $share->id }}</td>
                                            <td>
                                                @if($share->loan)
                                                    <a href="{{ url('loan/'.$share->loan->id.'/show') }}" data-toggle="tooltip" title="View loan">
                                                        #{{ $share->loan->id }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $share->loan->account_number ?? '' }}</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($client)
                                                    <strong>{{ $client->first_name }} {{ $client->last_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fa fa-phone"></i> {{ $client->phone ?? '—' }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($case)
                                                    <a href="{{ url('recovery/case/'.$case->id.'/show') }}" data-toggle="tooltip" title="View recovery case">
                                                        <span class="badge bg-{{ ['cross_branch' => 'primary', 'escalated' => 'warning', 'dormant' => 'default', 'legal' => 'danger', 'skip_trace' => 'success'][$case->category] ?? 'light' }}">
                                                            {{ $case->category_label }}
                                                        </span>
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $case->case_number ?? '' }}</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $share->office?->name ?? '—' }}</td>
                                            <td>
                                                @if($share->user)
                                                    {{ $share->user->first_name }} {{ $share->user->last_name }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->unit)
                                                    <span class="badge bg-blue">{{ $share->unit }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-right"><strong>K {{ number_format($share->amount, 2) }}</strong></td>
                                            <td>
                                                @if($share->notes)
                                                    {{ \Illuminate\Support\Str::limit($share->notes, 40) }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $share->created_at ? \Carbon\Carbon::parse($share->created_at)->format('d/m/Y H:i') : '--' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No unit shares found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($unitShares->count() > 0)
                                    <tfoot>
                                        <tr>
                                            <th colspan="7" class="text-right">Total</th>
                                            <th class="text-right">K {{ number_format($totalUnitShare, 2) }}</th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Unit Share Modal -->
<div class="modal fade" id="unitShareModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Record Unit Share</h4>
            </div>
            <form id="unitShareForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Loan <span class="text-muted">(optional)</span></label>
                        <select name="loan_id" id="loan_id" class="form-control select2" style="width:100%">
                            <option value="">— Select Loan —</option>
                            @foreach($loans as $loanOption)
                                @php
                                    $loanClientName = $loanOption->client
                                        ? trim($loanOption->client->first_name . ' ' . $loanOption->client->last_name)
                                        : '';
                                @endphp
                                <option value="{{ $loanOption->id }}">
                                    #{{ $loanOption->id }}
                                    {{ $loanOption->account_number ? '('.$loanOption->account_number.')' : '' }}
                                    @if($loanClientName !== '') — {{ $loanClientName }} @endif
                                    @if($loanOption->office) — {{ $loanOption->office->name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit" id="unit" class="form-control">
                            <option value="unit_share">Unit Share</option>
                            <!-- <option value="recoveries_dept_share">Recoveries Dept Share</option>
                            <option value="dormant_client_unit_share">Dormant Client Unit Share</option> -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
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

<script>
$('#unitShareForm').on('submit', function(e) {
    e.preventDefault();

    var $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true);

    $.ajax({
        url: '{{ route("recovery.dept-shares.store") }}',
        type: 'POST',
        data: $(this).serialize(),
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            alert(response.message || 'Unit share saved successfully');
            $('#unitShareModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            var msg = 'Failed to save';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON.errors) {
                    msg = $.map(xhr.responseJSON.errors, function(v) { return v[0]; }).join(' ');
                }
            }
            alert('Error: ' + msg);
        },
        complete: function() {
            $btn.prop('disabled', false);
        }
    });
});

$(function() {
    $('[data-toggle="tooltip"]').tooltip();

    if ($.fn.DataTable && $('#unitShareTable').length) {
        $('#unitShareTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "displayLength": 25,
            "searching": true,
            "ordering": true,
            "info": true,
            "order": [[9, "desc"]],
            "columnDefs": [{"orderable": false, "targets": [1, 3]}]
        });
    }
});

var $loanSelect = $('#loan_id');

$('#unitShareModal').on('shown.bs.modal', function() {
    if (typeof $.fn.select2 !== 'undefined' && !$loanSelect.hasClass('select2-hidden-accessible')) {
        $loanSelect.select2({
            placeholder: 'Search by loan #, account no., client or branch...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#unitShareModal .modal-body'),
            minimumInputLength: 0
        });
    }
});

$('#unitShareModal').on('hidden.bs.modal', function() {
    if ($loanSelect.hasClass('select2-hidden-accessible')) {
        $loanSelect.select2('destroy');
    }
});
</script>
@endsection

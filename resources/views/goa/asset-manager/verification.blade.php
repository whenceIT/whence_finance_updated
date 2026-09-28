@extends('layouts.master')
@section('title')
    GOA Manager - Periodic Verification
@endsection
@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-8">
            <h2 style="font-weight:700;color:#1e293b;margin-bottom:4px;"><i class="fa fa-check-square-o" style="color:#27ae60;"></i> Periodic Verification</h2>
            <p class="text-muted" style="margin:0;">Request and track branch asset inventory verifications</p>
        </div>
        <div class="col-md-4 text-right" style="padding-top:10px;">
            <a href="{{ route('goa.asset-manager.dashboard') }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Dashboard</a>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#requestVerificationModal">
                <i class="fa fa-plus"></i> Request Verification
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-orange" style="height:70px;line-height:70px;"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number">{{ $pendingVerifications->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-green" style="height:70px;line-height:70px;"><i class="fa fa-check"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Submitted</span>
                    <span class="info-box-number">{{ $submittedVerifications->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-yellow" style="height:70px;line-height:70px;"><i class="fa fa-question"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Never Verified</span>
                    <span class="info-box-number">{{ $neverVerifiedOffices->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" style="margin-bottom:0;">
        <li class="active"><a href="#tab-pending" data-toggle="tab"><i class="fa fa-clock-o"></i> Pending</a></li>
        <li><a href="#tab-submitted" data-toggle="tab"><i class="fa fa-check"></i> Submitted</a></li>
        <li><a href="#tab-never" data-toggle="tab"><i class="fa fa-exclamation-circle"></i> Not Yet Verified</a></li>
    </ul>

    <div class="tab-content" style="border:1px solid #ddd;border-top:none;padding:20px;background:#fff;">

        {{-- Pending --}}
        <div class="tab-pane active" id="tab-pending">
            <table class="table table-bordered table-hover" style="margin:0;">
                <thead style="background:#f1f5f9;">
                    <tr>
                        <th>Branch</th>
                        <th>Period</th>
                        <th>Requested By</th>
                        <th>Requested At</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingVerifications as $v)
                    <tr>
                        <td>{{ optional($v->office)->name }}</td>
                        <td>{{ $v->period }}</td>
                        <td>{{ optional($v->requester)->first_name }} {{ optional($v->requester)->last_name }}</td>
                        <td>{{ $v->created_at->format('d M Y H:i') }}</td>
                        <td class="text-center"><span class="label label-warning">{{ $v->status }}</span></td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-success submit-verification-btn"
                                data-id="{{ $v->id }}"
                                data-branch="{{ optional($v->office)->name }}"
                                data-period="{{ $v->period }}"
                                data-inventory="{{ json_encode($v->branchInventorySnapshot ?? []) }}"
                                data-toggle="modal" data-target="#submitVerificationModal">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted" style="padding:20px;">No pending verification requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Submitted --}}
        <div class="tab-pane" id="tab-submitted">
            <table class="table table-bordered table-hover" style="margin:0;">
                <thead style="background:#f1f5f9;">
                    <tr>
                        <th>Branch</th>
                        <th>Period</th>
                        <th>Requested By</th>
                        <th>Submitted By</th>
                        <th>Submitted At</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submittedVerifications as $v)
                    <tr>
                        <td>{{ optional($v->office)->name }}</td>
                        <td>{{ $v->period }}</td>
                        <td>{{ optional($v->requester)->first_name }} {{ optional($v->requester)->last_name }}</td>
                        <td>{{ optional($v->submitter)->first_name }} {{ optional($v->submitter)->last_name }}</td>
                        <td>{{ $v->submitted_at ? $v->submitted_at->format('d M Y H:i') : '—' }}</td>
                        <td class="text-center"><span class="label label-success">{{ $v->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted" style="padding:20px;">No submitted verifications yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Never Verified --}}
        <div class="tab-pane" id="tab-never">
            @if($neverVerifiedOffices->isEmpty())
                <div class="alert alert-success"><i class="fa fa-check-circle"></i> All branches have been verified at least once.</div>
            @else
                <table class="table table-bordered table-hover" style="margin:0;">
                    <thead style="background:#f1f5f9;">
                        <tr><th>Branch</th><th>Province</th><th class="text-center">Total Assets</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($neverVerifiedOffices as $office)
                        <tr>
                            <td>{{ $office->name }}</td>
                            <td>{{ optional($office->province)->name ?? '—' }}</td>
                            <td class="text-center">{{ $office->inventories_count ?? 0 }}</td>
                            <td>
                                <a href="{{ route('goa.asset-manager.inventory', ['office_id' => $office->id]) }}" class="btn btn-xs btn-default">View Inventory</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>

{{-- Request Verification Modal --}}
<div class="modal fade" id="requestVerificationModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('goa.asset-manager.verification.request') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title"><i class="fa fa-plus"></i> Request Asset Verification</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Period <span class="text-danger">*</span></label>
                        <input type="text" name="period" class="form-control" placeholder="e.g. October 2026" required value="{{ date('F Y') }}">
                        <span class="help-block">Enter the month and year for this verification cycle.</span>
                    </div>
                    <div class="form-group">
                        <label>Select Branches <span class="text-danger">*</span></label>
                        <div style="max-height:200px;overflow-y:auto;border:1px solid #ddd;padding:8px;border-radius:4px;">
                            <label style="font-weight:400;cursor:pointer;margin-bottom:6px;">
                                <input type="checkbox" id="selectAllBranches"> <strong>Select All</strong>
                            </label>
                            <hr style="margin:4px 0 6px;">
                            @foreach($offices as $o)
                            <div>
                                <label style="font-weight:400;cursor:pointer;">
                                    <input type="checkbox" name="office_ids[]" value="{{ $o->id }}" class="branch-checkbox"> {{ $o->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any instructions for branch managers..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Send Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Submit Verification Modal --}}
<div class="modal fade" id="submitVerificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="submitVerificationForm" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success" style="color:#fff;">
                    <h4 class="modal-title"><i class="fa fa-check-square-o"></i> Submit Verification — <span id="submitVLabel"></span></h4>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Review and confirm the current inventory for your branch for the period: <strong id="submitVPeriod"></strong></p>
                    <div id="submitVInventorySnapshot"></div>
                    <div class="form-group">
                        <label>Notes / Comments</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirm & Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    $('#selectAllBranches').on('change', function () {
        $('.branch-checkbox').prop('checked', this.checked);
    });

    $(document).on('click', '.submit-verification-btn', function () {
        var id = $(this).data('id');
        var branch = $(this).data('branch');
        var period = $(this).data('period');
        var inventory = $(this).data('inventory');

        $('#submitVLabel').text(branch);
        $('#submitVPeriod').text(period);
        $('#submitVerificationForm').attr('action', '/goa_dashboard/asset-manager/verification/' + id + '/submit');

        // Render inventory snapshot
        var html = '';
        if (inventory && inventory.length > 0) {
            html = '<table class="table table-sm table-bordered"><thead style="background:#f1f5f9;">'
                 + '<tr><th>Asset</th><th class="text-center">Total</th><th class="text-center">Working</th>'
                 + '<th class="text-center">Damaged</th><th class="text-center">Missing</th><th class="text-center">Under Repair</th></tr></thead><tbody>';
            $.each(inventory, function (i, row) {
                html += '<tr><td>' + row.category_name + '</td>'
                      + '<td class="text-center">' + row.total + '</td>'
                      + '<td class="text-center">' + row.working + '</td>'
                      + '<td class="text-center">' + row.damaged + '</td>'
                      + '<td class="text-center">' + row.missing + '</td>'
                      + '<td class="text-center">' + row.under_repair + '</td></tr>';
            });
            html += '</tbody></table>';
        } else {
            html = '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No inventory records found for this branch. You can still confirm the verification.</div>';
        }
        $('#submitVInventorySnapshot').html(html);
    });
});
</script>
@endsection

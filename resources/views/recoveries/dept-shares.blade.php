@extends('layouts.master')

@section('title')
    Department Shares
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-tools pull-right">
                    @if(Sentinel::getUser() && Sentinel::getUser()->role && Sentinel::getUser()->role->role_id == 1)
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#shareModal">
                        <i class="fa fa-plus"></i> Record Reconciling Entry
                    </button>
                    @endif
                </div>
                <h3 class="box-title"><i class="fa fa-share"></i> Department Shares Summary</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-share"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Recovery Dept Share</span>
                                <span class="info-box-number">K {{ number_format($totalDeptShare, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Unit Share</span>
                                <span class="info-box-number">K {{ number_format($totalUnitShare, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Details</h3>
                    </div>
                    <div class="box-body">
                        <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin-right: 10px;">
                                <label for="type" style="margin-right: 5px;">Filter:</label>
                                <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <option value="dept_share" {{ request('type') == 'dept_share' ? 'selected' : '' }}>Recoveries Dept Share</option>
                                    <option value="unit_share" {{ request('type') == 'unit_share' ? 'selected' : '' }}>Unit Share</option>
                                </select>
                            </div>
                            @if(request()->hasAny(['type']))
                                <a href="{{ route('dept.shares') }}" class="btn btn-default btn-sm">Clear Filter</a>
                            @endif
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Case</th>
                                        <th>Staff</th>
                                        <th>Amount</th>
                                        <th>Created At</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($allShares) > 0)
                                        @foreach($allShares as $share)
                                        <tr>
                                            <td>{{ $share->id }}</td>
                                            <td>
                                                @if($share->type === 'dept_share' && $share->recoveryCase)
                                                    <a href="{{ url('recovery/case/' . $share->recoveryCase->id . '/show') }}">{{ $share->recoveryCase->case_number ?? '0' }}</a>
                                                @elseif($share->office)
                                                    {{ $share->office->name }}
                                                @else
                                                    Recociliation Entry
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->type === 'dept_share')
                                                    @if($share->recoveryCase && $share->recoveryCase->assignedSpecialist)
                                                        {{ $share->recoveryCase->assignedSpecialist->first_name ?? '0' }} {{ $share->recoveryCase->assignedSpecialist->last_name ?? '0' }}
                                                    @elseif($share->createdBy)
                                                        {{ $share->createdBy->first_name ?? '0' }} {{ $share->createdBy->last_name ?? '0' }}
                                                    @else
                                                        Recoveries Cordinator
                                                    @endif
                                                @else
                                                    @if($share->user)
                                                        {{ $share->user->first_name ?? '0' }} {{ $share->user->last_name ?? '0' }}
                                                    @else
                                                        Recoveries Cordinator
                                                    @endif
                                                @endif
                                            </td>
                                            <td>K {{ number_format($share->type === 'dept_share' ? $share->dept_share_amount : $share->amount, 2) }}</td>
                                            <td>{{ $share->created_at ? \Carbon\Carbon::parse($share->created_at)->format('d/m/Y H:i') : '--' }}</td>
                                            <td>
                                                @if($share->type === 'dept_share')
                                                    <span class="badge bg-blue">Recovery Dept Share</span>
                                                @else
                                                    <span class="badge bg-green">Unit Share</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No data found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Record Reconciling Entry</h4>
            </div>
            <form id="shareForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Share Type <span class="text-danger">*</span></label>
                        <select name="share_type" id="share_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="dept_share">Recovery Dept Share</option>
                            <option value="unit_share">Unit Share</option>
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
$('#shareForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '{{ route("recovery.dept-shares.store") }}',
        type: 'POST',
        data: $(this).serialize(),
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            alert(response.message || 'Share saved successfully');
            $('#shareModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Error: ' + (xhr.responseJSON?.message || 'Failed to save'));
        }
    });
});
</script>
@endsection
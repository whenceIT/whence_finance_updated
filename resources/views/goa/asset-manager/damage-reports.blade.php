@extends('layouts.master')
@section('title')
    GOA Manager - Damage Reports
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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 style="font-weight:700;color:#1e293b;margin-bottom:4px;"><i class="fa fa-exclamation-triangle" style="color:#e74c3c;"></i> Damage Reports</h2>
            <p class="text-muted" style="margin:0;">Track and manage asset damage across branches</p>
        </div>
        <div class="col-md-4 text-right" style="padding-top:10px;">
            <a href="{{ route('goa.asset-manager.dashboard') }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Dashboard</a>
            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reportDamageModal">
                <i class="fa fa-plus"></i> Report Damage
            </button>
        </div>
    </div>

    {{-- Summary stat --}}
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-red" style="height:70px;line-height:70px;"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Open Reports</span>
                    <span class="info-box-number">{{ $openCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-orange" style="height:70px;line-height:70px;"><i class="fa fa-wrench"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Sent for Repair</span>
                    <span class="info-box-number">{{ $sentForRepairCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-green" style="height:70px;line-height:70px;"><i class="fa fa-check"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Repaired / Closed</span>
                    <span class="info-box-number">{{ $closedCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-blue" style="height:70px;line-height:70px;"><i class="fa fa-list"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Total Reports</span>
                    <span class="info-box-number">{{ $reports->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="box box-default" style="margin-bottom:10px;">
        <div class="box-body" style="padding:10px 15px;">
            <form method="GET" action="{{ route('goa.asset-manager.damage-reports') }}" class="form-inline">
                <div class="form-group" style="margin-right:10px;">
                    <label class="mr-1">Branch:</label>
                    <select name="office_id" class="form-control form-control-sm">
                        <option value="">All Branches</option>
                        @foreach($offices as $o)
                            <option value="{{ $o->id }}" {{ request('office_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right:10px;">
                    <label class="mr-1">Category:</label>
                    <select name="category_id" class="form-control form-control-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-right:10px;">
                    <label class="mr-1">Status:</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All Statuses</option>
                        @foreach(['Reported','Assessed','Sent for Repair','Repaired','Closed'] as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Filter</button>
                <a href="{{ route('goa.asset-manager.damage-reports') }}" class="btn btn-default btn-sm"><i class="fa fa-times"></i> Clear</a>
            </form>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="box box-default">
        <div class="box-body p-0">
            <table class="table table-bordered table-hover" style="margin:0;">
                <thead style="background:#f1f5f9;">
                    <tr>
                        <th>#</th>
                        <th>Branch</th>
                        <th>Asset</th>
                        <th class="text-center">Qty</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-center">Status</th>
                        <th>Reporter</th>
                        <th class="text-center">Photo</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    @php
                        $statusColors = [
                            'Reported'      => 'danger',
                            'Assessed'      => 'warning',
                            'Sent for Repair' => 'info',
                            'Repaired'      => 'success',
                            'Closed'        => 'default',
                        ];
                        $sc = $statusColors[$report->status] ?? 'default';
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($reports->currentPage() - 1) * $reports->perPage() }}</td>
                        <td>{{ optional($report->office)->name }}</td>
                        <td>{{ optional($report->category)->name }}</td>
                        <td class="text-center"><strong>{{ $report->quantity_affected }}</strong></td>
                        <td>{{ $report->reported_date ? $report->reported_date->format('d M Y') : '—' }}</td>
                        <td style="max-width:200px;">{{ mb_strimwidth($report->description, 0, 83, '…') }}</td>
                        <td class="text-center"><span class="label label-{{ $sc }}">{{ $report->status }}</span></td>
                        <td>{{ optional($report->reporter)->first_name }} {{ optional($report->reporter)->last_name }}</td>
                        <td class="text-center">
                            @if($report->photo)
                                <a href="{{ asset('storage/' . $report->photo) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $report->photo) }}" style="height:32px;width:40px;object-fit:cover;border-radius:3px;">
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-primary update-status-btn"
                                data-id="{{ $report->id }}"
                                data-status="{{ $report->status }}"
                                data-notes="{{ $report->notes }}"
                                data-toggle="modal" data-target="#updateStatusModal">
                                <i class="fa fa-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted" style="padding:20px;">No damage reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="box-footer text-center">
            {{ $reports->appends(request()->all())->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Report Damage Modal --}}
<div class="modal fade" id="reportDamageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('goa.asset-manager.damage-reports.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger" style="color:#fff;">
                    <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Report Asset Damage</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Branch <span class="text-danger">*</span></label>
                                <select name="office_id" class="form-control" required>
                                    <option value="">-- Select Branch --</option>
                                    @foreach($offices as $o)
                                        <option value="{{ $o->id }}" {{ old('office_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Asset Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- Select Asset --</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity Affected <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_affected" class="form-control" min="1" value="{{ old('quantity_affected', 1) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date Reported <span class="text-danger">*</span></label>
                                <input type="date" name="reported_date" class="form-control" value="{{ old('reported_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Photo (optional)</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Describe the damage...">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-save"></i> Submit Report</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Update Status Modal --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="updateStatusForm" action="">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title">Update Report Status</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="updateStatus" class="form-control">
                            @foreach(['Reported','Assessed','Sent for Repair','Repaired','Closed'] as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" id="updateNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    $(document).on('click', '.update-status-btn', function () {
        var id = $(this).data('id');
        $('#updateStatus').val($(this).data('status'));
        $('#updateNotes').val($(this).data('notes'));
        $('#updateStatusForm').attr('action', '/goa_dashboard/asset-manager/damage-reports/' + id);
    });
});
</script>
@endsection

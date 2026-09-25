@extends('layouts.master')
@section('title')
    GOA Manager - Repair Tracking
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

    <div class="row mb-3">
        <div class="col-md-8">
            <h2 style="font-weight:700;color:#1e293b;margin-bottom:4px;"><i class="fa fa-wrench" style="color:#e67e22;"></i> Repair Tracking</h2>
            <p class="text-muted" style="margin:0;">Log and track repairs for damaged branch assets</p>
        </div>
        <div class="col-md-4 text-right" style="padding-top:10px;">
            <a href="{{ route('goa.asset-manager.dashboard') }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Dashboard</a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-orange" style="height:70px;line-height:70px;"><i class="fa fa-wrench"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Pending Repairs</span>
                    <span class="info-box-number">{{ $pendingRepairs->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-green" style="height:70px;line-height:70px;"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Completed Repairs</span>
                    <span class="info-box-number">{{ $completedRepairs->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-yellow" style="height:70px;line-height:70px;"><i class="fa fa-money"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">Total Repair Cost</span>
                    <span class="info-box-number">K{{ number_format($totalRepairCost, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box" style="min-height:70px;">
                <span class="info-box-icon bg-blue" style="height:70px;line-height:70px;"><i class="fa fa-calendar"></i></span>
                <div class="info-box-content" style="padding:8px 10px;">
                    <span class="info-box-text">This Month's Cost</span>
                    <span class="info-box-number">K{{ number_format($thisMonthCost, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs" style="margin-bottom:0;">
        <li class="active"><a href="#tab-pending" data-toggle="tab"><i class="fa fa-clock-o"></i> Pending / Active</a></li>
        <li><a href="#tab-history" data-toggle="tab"><i class="fa fa-history"></i> Repair History</a></li>
        <li><a href="#tab-costs" data-toggle="tab"><i class="fa fa-bar-chart"></i> Cost Breakdown</a></li>
    </ul>

    <div class="tab-content" style="border:1px solid #ddd;border-top:none;padding:20px;background:#fff;">

        {{-- Pending Repairs --}}
        <div class="tab-pane active" id="tab-pending">
            <table class="table table-bordered table-hover" style="margin:0;">
                <thead style="background:#f1f5f9;">
                    <tr>
                        <th>Branch</th>
                        <th>Asset</th>
                        <th class="text-center">Qty</th>
                        <th>Reported</th>
                        <th>Description</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRepairs as $report)
                    <tr>
                        <td>{{ optional($report->office)->name }}</td>
                        <td>{{ optional($report->category)->name }}</td>
                        <td class="text-center">{{ $report->quantity_affected }}</td>
                        <td>{{ $report->reported_date ? $report->reported_date->format('d M Y') : '—' }}</td>
                        <td>{{ mb_strimwidth($report->description, 0, 73, '…') }}</td>
                        <td class="text-center"><span class="label label-info">{{ $report->status }}</span></td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-success log-repair-btn"
                                data-id="{{ $report->id }}"
                                data-branch="{{ optional($report->office)->name }}"
                                data-asset="{{ optional($report->category)->name }}"
                                data-qty="{{ $report->quantity_affected }}"
                                data-toggle="modal" data-target="#logRepairModal">
                                <i class="fa fa-wrench"></i> Log Repair
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted" style="padding:20px;">No pending repairs. Items marked "Sent for Repair" will appear here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Repair History --}}
        <div class="tab-pane" id="tab-history">
            <table class="table table-bordered table-hover" style="margin:0;">
                <thead style="background:#f1f5f9;">
                    <tr>
                        <th>Branch</th>
                        <th>Asset</th>
                        <th>Repair Date</th>
                        <th>Provider</th>
                        <th class="text-right">Cost (K)</th>
                        <th>Date Returned</th>
                        <th>Condition After</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedRepairs as $repair)
                    <tr>
                        <td>{{ optional(optional($repair->damageReport)->office)->name }}</td>
                        <td>{{ optional(optional($repair->damageReport)->category)->name }}</td>
                        <td>{{ $repair->repair_date ? $repair->repair_date->format('d M Y') : '—' }}</td>
                        <td>{{ $repair->repair_provider ?: '—' }}</td>
                        <td class="text-right">{{ $repair->repair_cost ? number_format($repair->repair_cost, 2) : '—' }}</td>
                        <td>{{ $repair->date_returned ? $repair->date_returned->format('d M Y') : '—' }}</td>
                        <td>
                            <span class="label label-{{ $repair->condition_after === 'Working' ? 'success' : 'danger' }}">
                                {{ $repair->condition_after }}
                            </span>
                        </td>
                        <td>
                            @if($repair->invoice_path)
                                <a href="{{ asset('storage/' . $repair->invoice_path) }}" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-file"></i></a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted" style="padding:20px;">No repair history yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Cost Breakdown --}}
        <div class="tab-pane" id="tab-costs">
            <div class="row">
                <div class="col-md-6">
                    <h5>By Branch</h5>
                    <table class="table table-sm table-bordered">
                        <thead style="background:#f1f5f9;"><tr><th>Branch</th><th class="text-right">Total Cost (K)</th><th class="text-center">Repairs</th></tr></thead>
                        <tbody>
                            @forelse($costByBranch as $row)
                            <tr>
                                <td>{{ $row->office_name }}</td>
                                <td class="text-right">{{ number_format($row->total_cost, 2) }}</td>
                                <td class="text-center">{{ $row->repair_count }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>By Asset Category</h5>
                    <table class="table table-sm table-bordered">
                        <thead style="background:#f1f5f9;"><tr><th>Category</th><th class="text-right">Total Cost (K)</th><th class="text-center">Repairs</th></tr></thead>
                        <tbody>
                            @forelse($costByCategory as $row)
                            <tr>
                                <td>{{ $row->category_name }}</td>
                                <td class="text-right">{{ number_format($row->total_cost, 2) }}</td>
                                <td class="text-center">{{ $row->repair_count }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Log Repair Modal --}}
<div class="modal fade" id="logRepairModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('goa.asset-manager.repairs.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="damage_report_id" id="repairDamageReportId">
            <div class="modal-content">
                <div class="modal-header bg-success" style="color:#fff;">
                    <h4 class="modal-title"><i class="fa fa-wrench"></i> Log Repair — <span id="repairAssetLabel"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Repair Date</label>
                                <input type="date" name="repair_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date Returned</label>
                                <input type="date" name="date_returned" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Condition After Repair</label>
                                <select name="condition_after" class="form-control">
                                    <option value="Working">Working</option>
                                    <option value="Damaged Beyond Repair">Damaged Beyond Repair</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Repair Cost (K)</label>
                                <input type="number" step="0.01" name="repair_cost" class="form-control" placeholder="0.00" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Repair Provider</label>
                                <input type="text" name="repair_provider" class="form-control" placeholder="Company / Person">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description of Repair</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="What was done..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Invoice / Receipt (optional)</label>
                        <input type="file" name="invoice" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Repair</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    $(document).on('click', '.log-repair-btn', function () {
        $('#repairDamageReportId').val($(this).data('id'));
        $('#repairAssetLabel').text($(this).data('branch') + ' — ' + $(this).data('asset'));
    });
});
</script>
@endsection

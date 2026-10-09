@extends('layouts.master')
@section('title')
    GOA Manager - Fleet Vehicle Details
@endsection

@section('content')
<div class="container-fluid" style="padding-top:15px;">
    <!-- Sleek Professional Header (Bootstrap 3 friendly) -->
    <div class="fleet-header" style="background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-radius:6px; padding:18px 22px; margin-bottom:20px; border-left:6px solid #007bff;">
        <div class="row">
            <div class="col-sm-8">
                <div style="display:flex; align-items:center;">
                    <div style="width:68px; height:68px; border-radius:50%; background:linear-gradient(135deg,#007bff,#0056b3); display:flex; align-items:center; justify-content:center; margin-right:18px; box-shadow:0 3px 10px rgba(0,123,255,0.35);">
                        <i class="fa fa-truck" style="color:#fff; font-size:32px;"></i>
                    </div>
                    <div>
                        <div style="display:flex; align-items:center;">
                            <h3 style="margin:0; font-weight:700; color:#222;">{{ $fleet->vehicle_id }}</h3>
                            <span style="margin-left:12px;">
                                @if($fleet->vehicle_status == 'Active')
                                    <span class="label label-success" style="font-size:13px; padding:5px 12px; border-radius:12px;">ACTIVE</span>
                                @elseif($fleet->vehicle_status == 'Maintenance')
                                    <span class="label label-warning" style="font-size:13px; padding:5px 12px; border-radius:12px;">MAINTENANCE</span>
                                @else
                                    <span class="label label-danger" style="font-size:13px; padding:5px 12px; border-radius:12px;">{{ strtoupper($fleet->vehicle_status) }}</span>
                                @endif
                            </span>
                        </div>
                        <div style="color:#666; margin-top:4px; font-size:15px;">
                            <i class="fa fa-car"></i> {{ $fleet->vehicle_type ?: 'Unknown Type' }} &nbsp;•&nbsp; {{ $fleet->vehicle_model ?: 'Unknown Model' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 text-right" style="padding-top:6px;">
                <div style="color:#888; font-size:12px;">Last Updated</div>
                <div style="font-weight:600; color:#333;">
                    {{ $fleet->updated_at ? $fleet->updated_at->diffForHumans() : 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats - Colorful Iconic Cards -->
    <div class="row" style="margin-bottom:18px;">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="background:#fff; border-left:4px solid #17a2b8; border-radius:6px; padding:14px; box-shadow:0 2px 6px rgba(0,0,0,0.06); min-height:78px;">
                <div style="display:flex; align-items:center;">
                    <div style="width:42px; height:42px; background:#17a2b8; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                        <i class="fa fa-user" style="font-size:18px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:11px; color:#888; text-transform:uppercase;">Assigned Driver</div>
                        <div style="font-weight:600; color:#222; font-size:14.5px; line-height:1.2;">
                            {{ $fleet->user ? $fleet->user->first_name . ' ' . $fleet->user->last_name : 'Unassigned' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="background:#fff; border-left:4px solid #28a745; border-radius:6px; padding:14px; box-shadow:0 2px 6px rgba(0,0,0,0.06); min-height:78px;">
                <div style="display:flex; align-items:center;">
                    <div style="width:42px; height:42px; background:#28a745; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                        <i class="fa fa-building" style="font-size:18px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:11px; color:#888; text-transform:uppercase;">Office / Location</div>
                        <div style="font-weight:600; color:#222; font-size:14.5px; line-height:1.2;">
                            {{ $fleet->office ? $fleet->office->name : 'No Office' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="background:#fff; border-left:4px solid #ffc107; border-radius:6px; padding:14px; box-shadow:0 2px 6px rgba(0,0,0,0.06); min-height:78px;">
                <div style="display:flex; align-items:center;">
                    <div style="width:42px; height:42px; background:#ffc107; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                        <i class="fa fa-calendar" style="font-size:18px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:11px; color:#888; text-transform:uppercase; display:flex; align-items:center; justify-content:space-between;">
                            <span>Insurance Expiry</span>
                            <a href="#" id="editInsuranceBtn" style="color:#007bff; font-size:12px; margin-left:6px;" title="Update insurance expiry date">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </div>
                        <div style="font-weight:600; color:#222; font-size:14.5px; line-height:1.2;" id="insuranceExpiryValue">
                            {{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('d M Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card" style="background:#fff; border-left:4px solid #007bff; border-radius:6px; padding:14px; box-shadow:0 2px 6px rgba(0,0,0,0.06); min-height:78px;">
                <div style="display:flex; align-items:center;">
                    <div style="width:42px; height:42px; background:#007bff; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                        <i class="fa fa-money" style="font-size:18px;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:11px; color:#888; text-transform:uppercase;">Current Value</div>
                        <div style="font-weight:600; color:#222; font-size:14.5px; line-height:1.2;">
                            {{ $fleet->current_value ? 'ZMW ' . number_format($fleet->current_value, 0) : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Details - Two Columns -->
    <div class="row">
        <!-- Vehicle Details -->
        <div class="col-md-6">
            <div class="panel panel-default" style="border:none; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-radius:6px; margin-bottom:18px;">
                <div class="panel-heading" style="background:#f8f9fa; border-bottom:2px solid #eee; padding:10px 16px; border-top-left-radius:6px; border-top-right-radius:6px;">
                    <i class="fa fa-info-circle text-primary"></i> <strong style="font-size:15px;">Vehicle Details</strong>
                </div>
                <div class="panel-body" style="padding:12px 18px 6px;">
                    <div class="row" style="margin-bottom:8px; padding-bottom:7px; border-bottom:1px solid #f0f0f0;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-hashtag text-primary"></i> Vehicle ID</div>
                        <div class="col-sm-7" style="font-weight:600;">{{ $fleet->vehicle_id }}</div>
                    </div>
                    <div class="row" style="margin-bottom:8px; padding-bottom:7px; border-bottom:1px solid #f0f0f0;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-truck text-primary"></i> Type</div>
                        <div class="col-sm-7" style="font-weight:600;">{{ $fleet->vehicle_type ?: 'N/A' }}</div>
                    </div>
                    <div class="row" style="margin-bottom:8px; padding-bottom:7px; border-bottom:1px solid #f0f0f0;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-tag text-primary"></i> Model</div>
                        <div class="col-sm-7" style="font-weight:600;">{{ $fleet->vehicle_model ?: 'N/A' }}</div>
                    </div>
                    <div class="row" style="margin-bottom:8px; padding-bottom:7px; border-bottom:1px solid #f0f0f0;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-paint-brush text-primary"></i> Color</div>
                        <div class="col-sm-7" style="font-weight:600;">
                            <span style="background:#f8f9fa; border:1px solid #e0e0e0; padding:2px 9px; border-radius:3px;">{{ $fleet->color ?: 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="row" style="padding-top:2px;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-file-text text-primary"></i> White Book</div>
                        <div class="col-sm-7" style="font-weight:600;">{{ ucfirst($fleet->white_book ?? 'N/A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment & Status -->
        <div class="col-md-6">
            <div class="panel panel-default" style="border:none; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-radius:6px; margin-bottom:18px;">
                <div class="panel-heading" style="background:#f8f9fa; border-bottom:2px solid #eee; padding:10px 16px; border-top-left-radius:6px; border-top-right-radius:6px;">
                    <i class="fa fa-users text-success"></i> <strong style="font-size:15px;">Assignment & Status</strong>
                </div>
                <div class="panel-body" style="padding:12px 18px 6px;">
                    <div class="row" style="margin-bottom:8px; padding-bottom:7px; border-bottom:1px solid #f0f0f0;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-user text-success"></i> Assigned To</div>
                        <div class="col-sm-7" style="font-weight:600;">{{ $fleet->user ? $fleet->user->first_name . ' ' . $fleet->user->last_name : 'Unassigned' }}</div>
                    </div>
                    <div class="row" style="margin-bottom:8px; padding-bottom:7px; border-bottom:1px solid #f0f0f0;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-building text-success"></i> Office</div>
                        <div class="col-sm-7" style="font-weight:600;">{{ $fleet->office ? $fleet->office->name : 'N/A' }}</div>
                    </div>
                    <div class="row" style="margin-bottom:8px; padding-bottom:7px; border-bottom:1px solid #f0f0f0;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-calendar-check text-success"></i> Last Maintenance</div>
                        <div class="col-sm-7" style="font-weight:600;">{{ $fleet->last_maintenance ? $fleet->last_maintenance->format('d M Y') : 'N/A' }}</div>
                    </div>
                    <div class="row" style="padding-top:2px;">
                        <div class="col-sm-5 text-muted" style="padding-right:0;"><i class="fa fa-toggle-on text-success"></i> Status</div>
                        <div class="col-sm-7">
                            @if($fleet->vehicle_status == 'Active')
                                <span class="label label-success" style="padding:4px 11px;">Active &amp; Operational</span>
                            @elseif($fleet->vehicle_status == 'Maintenance')
                                <span class="label label-warning" style="padding:4px 11px;">Under Maintenance</span>
                            @else
                                <span class="label label-danger" style="padding:4px 11px;">{{ $fleet->vehicle_status }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financials & Documents -->
    <div class="panel panel-default" style="border:none; box-shadow:0 2px 8px rgba(0,0,0,0.06); border-radius:6px; margin-bottom:20px;">
        <div class="panel-heading" style="background:#f8f9fa; border-bottom:2px solid #eee; padding:10px 16px; border-top-left-radius:6px; border-top-right-radius:6px;">
            <i class="fa fa-file-text text-warning"></i> <strong style="font-size:15px;">Financials &amp; Documents</strong>
        </div>
        <div class="panel-body" style="padding:14px 18px 4px;">
            <div class="row">
                <div class="col-md-4">
                    <div style="margin-bottom:10px;">
                        <div style="font-size:12px; color:#888;"><i class="fa fa-calendar"></i> Date Purchased</div>
                        <div style="font-weight:600; font-size:14.5px;">{{ $fleet->date_purchased ? $fleet->date_purchased->format('d M Y') : 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="margin-bottom:10px;">
                        <div style="font-size:12px; color:#888;"><i class="fa fa-money"></i> Current Value</div>
                        <div style="font-weight:600; font-size:14.5px; color:#28a745;">
                            {{ $fleet->current_value ? 'ZMW ' . number_format($fleet->current_value, 2) : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="margin-bottom:10px;">
                        <div style="font-size:12px; color:#888;"><i class="fa fa-file-text"></i> Insurance Expires</div>
                        <div style="font-weight:600; font-size:14.5px;">
                            <span id="insuranceExpiryDetail">{{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('d M Y') : 'N/A' }}</span>
                            @if($fleet->insurance_expire_date && $fleet->insurance_expire_date->isPast())
                                <span class="label label-danger" style="margin-left:6px; padding:2px 7px;">Expired</span>
                            @elseif($fleet->insurance_expire_date && $fleet->insurance_expire_date->diffInDays() < 30)
                                <span class="label label-warning" style="margin-left:6px; padding:2px 7px;">Expiring Soon</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin:8px 4px 20px;">
        <a href="{{ route('goa.fleet-management') }}" class="btn btn-default btn-lg" style="padding-left:18px; padding-right:18px;">
            <i class="fa fa-arrow-left"></i> Back to Fleet Management
        </a>
        <a href="{{ route('fleets.edit', $fleet->id) }}" class="btn btn-primary btn-lg" style="padding-left:28px; padding-right:28px; box-shadow:0 2px 6px rgba(0,123,255,0.3);">
            <i class="fa fa-edit"></i> Edit Vehicle Details
        </a>
    </div>

    {{-- ================================================================
         VEHICLE LIFECYCLE HISTORY — 6-tab section
         ================================================================ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('error') }}</div>
    @endif

    <div class="panel panel-default" style="border:none; box-shadow:0 2px 12px rgba(0,0,0,0.08); border-radius:8px; margin-bottom:30px;">
        <div class="panel-heading" style="background:linear-gradient(135deg,#1e293b 0%,#334155 100%); border-radius:8px 8px 0 0; padding:14px 20px;">
            <h4 style="margin:0; color:#fff; font-weight:700; font-size:1.15rem;">
                <i class="fa fa-history" style="margin-right:8px;"></i> Vehicle Lifecycle History
                <small style="color:#94a3b8; font-weight:400; font-size:.85rem; margin-left:10px;">{{ $fleet->vehicle_id }}</small>
            </h4>
        </div>

        {{-- Tab nav --}}
        <div style="background:#f8fafc; border-bottom:2px solid #e2e8f0; padding:0 20px;">
            <ul class="nav nav-tabs" id="lifecycleTabs" style="border:none; margin-bottom:0;">
                <li class="active"><a href="#tab-accidents"   data-toggle="tab" style="border:none; border-bottom:3px solid transparent; padding:12px 18px; font-weight:600; color:#64748b;"><i class="fa fa-car-crash fa-car" style="color:#dc2626;"></i>&nbsp; Accidents <span class="badge" style="background:#dc2626;">{{ $fleet->accidents->count() }}</span></a></li>
                <li><a href="#tab-service"    data-toggle="tab" style="border:none; border-bottom:3px solid transparent; padding:12px 18px; font-weight:600; color:#64748b;"><i class="fa fa-wrench" style="color:#d97706;"></i>&nbsp; Maintenance &amp; Repairs <span class="badge" style="background:#d97706;">{{ $fleet->serviceRecords->count() }}</span></a></li>
                <li><a href="#tab-expenses"   data-toggle="tab" style="border:none; border-bottom:3px solid transparent; padding:12px 18px; font-weight:600; color:#64748b;"><i class="fa fa-money" style="color:#059669;"></i>&nbsp; Expenses <span class="badge" style="background:#059669;">{{ $fleet->expenses->count() }}</span></a></li>
                <li><a href="#tab-documents"  data-toggle="tab" style="border:none; border-bottom:3px solid transparent; padding:12px 18px; font-weight:600; color:#64748b;"><i class="fa fa-paperclip" style="color:#7c3aed;"></i>&nbsp; Documents</a></li>
                <li><a href="#tab-schedule"   data-toggle="tab" style="border:none; border-bottom:3px solid transparent; padding:12px 18px; font-weight:600; color:#64748b;"><i class="fa fa-calendar" style="color:#0284c7;"></i>&nbsp; Schedule <span class="badge" style="background:#0284c7;">{{ $fleet->maintenanceSchedules->where('status','pending')->count() }}</span></a></li>
                <li><a href="#tab-costsummary" data-toggle="tab" style="border:none; border-bottom:3px solid transparent; padding:12px 18px; font-weight:600; color:#64748b;"><i class="fa fa-bar-chart" style="color:#1e293b;"></i>&nbsp; Cost Summary</a></li>
            </ul>
        </div>

        <div class="tab-content" style="padding:20px;">

            {{-- ─────────────────────────────────────────────────────
                 TAB 1 — ACCIDENT HISTORY
                 ───────────────────────────────────────────────────── --}}
            <div class="tab-pane active" id="tab-accidents">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <h5 style="margin:0; font-weight:700; color:#dc2626;"><i class="fa fa-exclamation-triangle"></i> Accident History</h5>
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#addAccidentModal">
                        <i class="fa fa-plus"></i> Record Accident
                    </button>
                </div>

                @forelse($fleet->accidents as $acc)
                <div style="background:#fff; border:1px solid #fecaca; border-left:4px solid #dc2626; border-radius:6px; padding:14px 18px; margin-bottom:12px; position:relative;">
                    <div class="row">
                        <div class="col-sm-9">
                            <div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                <span style="background:#dc2626; color:#fff; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                    {{ $acc->accident_date->format('d M Y') }}
                                </span>
                                @if($acc->location)
                                    <span style="color:#64748b; font-size:13px;"><i class="fa fa-map-marker"></i> {{ $acc->location }}</span>
                                @endif
                                @if($acc->driver)
                                    <span style="color:#64748b; font-size:13px;"><i class="fa fa-user"></i> {{ $acc->driver }}</span>
                                @endif
                            </div>
                            @if($acc->description)
                                <p style="margin:0 0 6px; color:#374151; font-size:14px;">{{ $acc->description }}</p>
                            @endif
                            <div style="display:flex; flex-wrap:wrap; gap:16px; font-size:13px; color:#64748b;">
                                @if($acc->police_report_number)
                                    <span><i class="fa fa-file-text"></i> Police Ref: <strong>{{ $acc->police_report_number }}</strong></span>
                                @endif
                                @if($acc->insurance_claim_number)
                                    <span><i class="fa fa-shield"></i> Insurance Claim: <strong>{{ $acc->insurance_claim_number }}</strong></span>
                                @endif
                            </div>
                            @if($acc->repair_details)
                                <p style="margin:6px 0 0; font-size:13px; color:#64748b;"><i class="fa fa-wrench"></i> <em>{{ $acc->repair_details }}</em></p>
                            @endif
                        </div>
                        <div class="col-sm-3 text-right">
                            <div style="font-size:1.3rem; font-weight:700; color:#dc2626;">K{{ number_format($acc->cost, 2) }}</div>
                            <small style="color:#94a3b8;">Accident Cost</small>
                            <div style="margin-top:8px;">
                                @if($acc->document_path)
                                    <a href="{{ asset('storage/' . $acc->document_path) }}" target="_blank" class="btn btn-xs btn-default" title="View Document"><i class="fa fa-paperclip"></i> Doc</a>
                                @endif
                                <form action="{{ route('fleets.accidents.destroy', [$fleet->id, $acc->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this accident record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                            @if($acc->recorded_by)
                                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">By: {{ $acc->recorded_by }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                    <div style="text-align:center; padding:30px; color:#94a3b8; background:#f8fafc; border-radius:6px;">
                        <i class="fa fa-check-circle fa-2x" style="color:#22c55e; display:block; margin-bottom:8px;"></i>
                        No accidents recorded for this vehicle.
                    </div>
                @endforelse
            </div>

            {{-- ─────────────────────────────────────────────────────
                 TAB 2 — MAINTENANCE & REPAIR HISTORY
                 ───────────────────────────────────────────────────── --}}
            <div class="tab-pane" id="tab-service">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <h5 style="margin:0; font-weight:700; color:#d97706;"><i class="fa fa-wrench"></i> Maintenance &amp; Repair History</h5>
                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#addServiceModal">
                        <i class="fa fa-plus"></i> Add Service Record
                    </button>
                </div>

                @forelse($fleet->serviceRecords as $svc)
                <div style="background:#fff; border:1px solid #fde68a; border-left:4px solid #d97706; border-radius:6px; padding:14px 18px; margin-bottom:12px;">
                    <div class="row">
                        <div class="col-sm-9">
                            <div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                <span style="background:#d97706; color:#fff; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                    {{ $svc->service_date->format('d M Y') }}
                                </span>
                                <strong style="font-size:15px; color:#1e293b;">{{ $svc->service_type }}</strong>
                                @if($svc->odometer_reading)
                                    <span style="color:#64748b; font-size:13px;"><i class="fa fa-tachometer"></i> {{ number_format($svc->odometer_reading) }} km</span>
                                @endif
                            </div>
                            @if($svc->description)
                                <p style="margin:0 0 5px; color:#374151; font-size:14px;">{{ $svc->description }}</p>
                            @endif
                            <div style="display:flex; flex-wrap:wrap; gap:16px; font-size:13px; color:#64748b;">
                                @if($svc->workshop)
                                    <span><i class="fa fa-building"></i> {{ $svc->workshop }}</span>
                                @endif
                                @if($svc->parts_replaced)
                                    <span><i class="fa fa-cogs"></i> Parts: {{ $svc->parts_replaced }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-3 text-right">
                            <div style="font-size:1.3rem; font-weight:700; color:#d97706;">K{{ number_format($svc->cost, 2) }}</div>
                            <small style="color:#94a3b8;">Service Cost</small>
                            <div style="margin-top:8px;">
                                @if($svc->document_path)
                                    <a href="{{ asset('storage/' . $svc->document_path) }}" target="_blank" class="btn btn-xs btn-default" title="View Invoice"><i class="fa fa-paperclip"></i> Invoice</a>
                                @endif
                                <form action="{{ route('fleets.service-records.destroy', [$fleet->id, $svc->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this service record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                            @if($svc->recorded_by)
                                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">By: {{ $svc->recorded_by }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                    <div style="text-align:center; padding:30px; color:#94a3b8; background:#f8fafc; border-radius:6px;">
                        <i class="fa fa-wrench fa-2x" style="display:block; margin-bottom:8px;"></i>
                        No service records yet.
                    </div>
                @endforelse
            </div>

            {{-- ─────────────────────────────────────────────────────
                 TAB 3 — EXPENSE HISTORY
                 ───────────────────────────────────────────────────── --}}
            <div class="tab-pane" id="tab-expenses">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <h5 style="margin:0; font-weight:700; color:#059669;"><i class="fa fa-money"></i> Vehicle Expenses</h5>
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addExpenseModal">
                        <i class="fa fa-plus"></i> Add Expense
                    </button>
                </div>

                @php
                    $categoryColors = [
                        'Maintenance'  => '#d97706',
                        'Repairs'      => '#dc2626',
                        'Accidents'    => '#991b1b',
                        'Tyres'        => '#374151',
                        'Insurance'    => '#0284c7',
                        'Spare Parts'  => '#7c3aed',
                        'Other'        => '#64748b',
                    ];
                @endphp

                @forelse($fleet->expenses as $exp)
                @php $catColor = $categoryColors[$exp->category] ?? '#64748b'; @endphp
                <div style="background:#fff; border:1px solid #d1fae5; border-left:4px solid {{ $catColor }}; border-radius:6px; padding:12px 18px; margin-bottom:10px;">
                    <div class="row">
                        <div class="col-sm-9">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
                                <span style="background:{{ $catColor }}; color:#fff; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ $exp->category }}</span>
                                <span style="color:#64748b; font-size:13px;">{{ $exp->expense_date->format('d M Y') }}</span>
                                @if($exp->reference_number)
                                    <span style="color:#94a3b8; font-size:12px;">Ref: {{ $exp->reference_number }}</span>
                                @endif
                            </div>
                            @if($exp->description)
                                <p style="margin:0; color:#374151; font-size:14px;">{{ $exp->description }}</p>
                            @endif
                        </div>
                        <div class="col-sm-3 text-right">
                            <div style="font-size:1.25rem; font-weight:700; color:{{ $catColor }};">K{{ number_format($exp->amount, 2) }}</div>
                            <div style="margin-top:6px;">
                                @if($exp->document_path)
                                    <a href="{{ asset('storage/' . $exp->document_path) }}" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-paperclip"></i> Doc</a>
                                @endif
                                <form action="{{ route('fleets.expenses.destroy', [$fleet->id, $exp->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this expense?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                            @if($exp->recorded_by)
                                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">By: {{ $exp->recorded_by }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                    <div style="text-align:center; padding:30px; color:#94a3b8; background:#f8fafc; border-radius:6px;">
                        <i class="fa fa-money fa-2x" style="display:block; margin-bottom:8px;"></i>
                        No expenses recorded yet.
                    </div>
                @endforelse
            </div>

            {{-- ─────────────────────────────────────────────────────
                 TAB 4 — DOCUMENTS
                 ───────────────────────────────────────────────────── --}}
            <div class="tab-pane" id="tab-documents">
                <h5 style="font-weight:700; color:#7c3aed; margin-bottom:16px;"><i class="fa fa-paperclip"></i> Supporting Documents</h5>

                @php
                    $allDocs = collect();
                    foreach($fleet->accidents as $a)    { if($a->document_path)   $allDocs->push(['label'=>'Accident — '.$a->accident_date->format('d M Y'),   'path'=>$a->document_path, 'color'=>'#dc2626']); }
                    foreach($fleet->serviceRecords as $s) { if($s->document_path) $allDocs->push(['label'=>'Service — '.$s->service_date->format('d M Y').' — '.$s->service_type, 'path'=>$s->document_path, 'color'=>'#d97706']); }
                    foreach($fleet->expenses as $e)     { if($e->document_path)   $allDocs->push(['label'=>'Expense — '.$e->expense_date->format('d M Y').' — '.$e->category,        'path'=>$e->document_path, 'color'=>'#059669']); }
                @endphp

                @forelse($allDocs as $doc)
                <div style="display:flex; align-items:center; justify-content:space-between; background:#fff; border:1px solid #e2e8f0; border-left:4px solid {{ $doc['color'] }}; border-radius:6px; padding:10px 16px; margin-bottom:8px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <i class="fa fa-file-pdf-o fa-lg" style="color:{{ $doc['color'] }};"></i>
                        <span style="font-size:14px; color:#374151;">{{ $doc['label'] }}</span>
                    </div>
                    <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" class="btn btn-xs btn-default">
                        <i class="fa fa-download"></i> Open
                    </a>
                </div>
                @empty
                    <div style="text-align:center; padding:30px; color:#94a3b8; background:#f8fafc; border-radius:6px;">
                        <i class="fa fa-folder-open fa-2x" style="display:block; margin-bottom:8px;"></i>
                        No documents attached. Upload supporting files when adding accident, service, or expense records.
                    </div>
                @endforelse
            </div>

            {{-- ─────────────────────────────────────────────────────
                 TAB 5 — MAINTENANCE SCHEDULE
                 ───────────────────────────────────────────────────── --}}
            <div class="tab-pane" id="tab-schedule">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <h5 style="margin:0; font-weight:700; color:#0284c7;"><i class="fa fa-calendar"></i> Maintenance Schedule</h5>
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#addScheduleModal">
                        <i class="fa fa-plus"></i> Schedule Maintenance
                    </button>
                </div>

                @forelse($fleet->maintenanceSchedules->sortBy('due_date') as $sched)
                @php
                    $days = $sched->due_date ? now()->diffInDays($sched->due_date, false) : null;
                    $rowBorder = '#0284c7';
                    if($sched->status === 'completed') $rowBorder = '#22c55e';
                    elseif($days !== null && $days < 0) $rowBorder = '#dc2626';
                    elseif($days !== null && $days <= 5) $rowBorder = '#f59e0b';
                @endphp
                <div style="background:#fff; border:1px solid #e2e8f0; border-left:4px solid {{ $rowBorder }}; border-radius:6px; padding:12px 18px; margin-bottom:10px;">
                    <div class="row">
                        <div class="col-sm-8">
                            <strong style="font-size:15px;">{{ $sched->maintenance_type }}</strong>
                            @if($sched->technician) <span style="color:#64748b; font-size:13px; margin-left:10px;"><i class="fa fa-user"></i> {{ $sched->technician }}</span> @endif
                            @if($sched->notes) <p style="margin:4px 0 0; color:#64748b; font-size:13px;">{{ $sched->notes }}</p> @endif
                        </div>
                        <div class="col-sm-4 text-right">
                            @if($sched->status === 'completed')
                                <span class="label label-success">Completed</span>
                                @if($sched->amount) <div style="font-size:13px; color:#22c55e; font-weight:600;">K{{ number_format($sched->amount, 2) }}</div> @endif
                            @else
                                <span style="font-weight:700; font-size:14px; color:{{ $rowBorder }};">
                                    Due: {{ $sched->due_date ? $sched->due_date->format('d M Y') : 'TBD' }}
                                </span>
                                @if($days !== null)
                                    <div style="font-size:12px; color:{{ $rowBorder }};">
                                        @if($days > 0) {{ $days }} day(s) to go
                                        @elseif($days < 0) {{ abs($days) }} day(s) overdue
                                        @else Due today @endif
                                    </div>
                                @endif
                                <form method="POST" action="{{ route('maintenance.complete', $sched->id) }}" style="display:inline-flex; align-items:center; gap:6px; margin-top:6px;">
                                    @csrf
                                    <input type="number" name="amount" step="0.01" placeholder="Amount" class="form-control input-xs" style="width:100px;" required>
                                    <button type="submit" class="btn btn-xs btn-success"><i class="fa fa-check"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                    <div style="text-align:center; padding:30px; color:#94a3b8; background:#f8fafc; border-radius:6px;">
                        <i class="fa fa-calendar fa-2x" style="display:block; margin-bottom:8px;"></i>
                        No maintenance scheduled.
                    </div>
                @endforelse
            </div>

            {{-- ─────────────────────────────────────────────────────
                 TAB 6 — COST SUMMARY
                 ───────────────────────────────────────────────────── --}}
            <div class="tab-pane" id="tab-costsummary">
                <h5 style="font-weight:700; color:#1e293b; margin-bottom:20px;"><i class="fa fa-bar-chart"></i> Total Expenditure — {{ $fleet->vehicle_id }}</h5>

                <div class="row">
                    {{-- Expense categories --}}
                    <div class="col-md-7">
                        <div style="background:#f8fafc; border-radius:8px; padding:20px;">
                            <h6 style="color:#64748b; text-transform:uppercase; font-size:12px; letter-spacing:.05em; margin-bottom:16px;">Expenses by Category</h6>
                            @foreach(\App\Models\FleetExpense::CATEGORIES as $cat)
                            @php $catAmt = $expenseSummary->get($cat, 0); @endphp
                            <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #e2e8f0;">
                                <span style="color:#374151;">
                                    @php $catIcons = ['Maintenance'=>'fa-wrench','Repairs'=>'fa-tools fa-wrench','Accidents'=>'fa-car','Tyres'=>'fa-circle-o','Insurance'=>'fa-shield','Spare Parts'=>'fa-cogs','Other'=>'fa-ellipsis-h']; @endphp
                                    <i class="fa {{ $catIcons[$cat] ?? 'fa-tag' }}" style="width:18px; color:{{ $categoryColors[$cat] ?? '#64748b' }};"></i>
                                    {{ $cat }}
                                </span>
                                <strong style="color:{{ $catAmt > 0 ? ($categoryColors[$cat] ?? '#374151') : '#94a3b8' }};">
                                    K{{ number_format($catAmt, 2) }}
                                </strong>
                            </div>
                            @endforeach

                            {{-- Accident costs (from accident records) --}}
                            <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #e2e8f0;">
                                <span style="color:#374151;"><i class="fa fa-exclamation-triangle" style="width:18px; color:#dc2626;"></i> Accident Records (direct costs)</span>
                                <strong style="color:{{ $accidentTotal > 0 ? '#dc2626' : '#94a3b8' }};">K{{ number_format($accidentTotal, 2) }}</strong>
                            </div>

                            {{-- Service record costs --}}
                            <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 0;">
                                <span style="color:#374151;"><i class="fa fa-wrench" style="width:18px; color:#d97706;"></i> Service Record Costs</span>
                                <strong style="color:{{ $serviceTotal > 0 ? '#d97706' : '#94a3b8' }};">K{{ number_format($serviceTotal, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Total card --}}
                    <div class="col-md-5">
                        <div style="background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%); border-radius:10px; padding:28px 24px; color:#fff; text-align:center; height:100%; display:flex; flex-direction:column; justify-content:center;">
                            <div style="font-size:13px; text-transform:uppercase; letter-spacing:.08em; color:#94a3b8; margin-bottom:10px;">Total Vehicle Expenditure</div>
                            <div style="font-size:2.4rem; font-weight:800; color:#fbbf24; line-height:1.1;">
                                K{{ number_format($totalExpenditure, 2) }}
                            </div>
                            <div style="margin-top:18px; font-size:13px; color:#64748b;">
                                <div>{{ $fleet->accidents->count() }} accident record(s)</div>
                                <div>{{ $fleet->serviceRecords->count() }} service record(s)</div>
                                <div>{{ $fleet->expenses->count() }} expense record(s)</div>
                            </div>
                            <div style="margin-top:18px; padding-top:14px; border-top:1px solid #334155; font-size:12px; color:#64748b;">
                                Purchase Value: {{ $fleet->current_value ? 'K' . number_format($fleet->current_value, 2) : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /.tab-content --}}
    </div>{{-- /.panel --}}

</div>{{-- /.container-fluid --}}

{{-- ================================================================
     MODALS
     ================================================================ --}}

{{-- ADD ACCIDENT MODAL --}}
<div class="modal fade" id="addAccidentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header" style="background:#dc2626; color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
            <h4 class="modal-title"><i class="fa fa-car"></i> Record Accident</h4>
        </div>
        <form method="POST" action="{{ route('fleets.accidents.store', $fleet->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group"><label>Accident Date <span class="text-danger">*</span></label><input type="date" name="accident_date" class="form-control" required></div>
                        <div class="form-group"><label>Driver</label><input type="text" name="driver" class="form-control" placeholder="Name of driver at the time"></div>
                        <div class="form-group"><label>Location</label><input type="text" name="location" class="form-control" placeholder="Where the accident occurred"></div>
                        <div class="form-group"><label>Cost (K)</label><input type="number" name="cost" class="form-control" step="0.01" min="0" placeholder="0.00"></div>
                        <div class="form-group"><label>Supporting Document</label><input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3" placeholder="Brief description of the accident"></textarea></div>
                        <div class="form-group"><label>Police Report / Reference #</label><input type="text" name="police_report_number" class="form-control" placeholder="e.g. POL/2026/001234"></div>
                        <div class="form-group"><label>Insurance Claim / Reference #</label><input type="text" name="insurance_claim_number" class="form-control" placeholder="e.g. INS/CLM/2026/0456"></div>
                        <div class="form-group"><label>Repair Details</label><textarea name="repair_details" class="form-control" rows="2" placeholder="Summary of repairs carried out"></textarea></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"><i class="fa fa-save"></i> Save Accident Record</button>
            </div>
        </form>
    </div></div>
</div>

{{-- ADD SERVICE RECORD MODAL --}}
<div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header" style="background:#d97706; color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
            <h4 class="modal-title"><i class="fa fa-wrench"></i> Add Service / Repair Record</h4>
        </div>
        <form method="POST" action="{{ route('fleets.service-records.store', $fleet->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group"><label>Service Date <span class="text-danger">*</span></label><input type="date" name="service_date" class="form-control" required></div>
                        <div class="form-group"><label>Service Type <span class="text-danger">*</span></label><input type="text" name="service_type" class="form-control" placeholder="e.g. Oil Change, Brake Service, Repair" required></div>
                        <div class="form-group"><label>Workshop / Service Provider</label><input type="text" name="workshop" class="form-control" placeholder="Name of garage / workshop"></div>
                        <div class="form-group"><label>Odometer Reading (km)</label><input type="number" name="odometer_reading" class="form-control" min="0" placeholder="e.g. 52000"></div>
                        <div class="form-group"><label>Cost (K)</label><input type="number" name="cost" class="form-control" step="0.01" min="0" placeholder="0.00"></div>
                        <div class="form-group"><label>Invoice / Document</label><input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"><label>Description / Work Done</label><textarea name="description" class="form-control" rows="4" placeholder="Describe the work carried out"></textarea></div>
                        <div class="form-group"><label>Parts Replaced</label><textarea name="parts_replaced" class="form-control" rows="3" placeholder="List any parts that were replaced"></textarea></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Save Service Record</button>
            </div>
        </form>
    </div></div>
</div>

{{-- ADD EXPENSE MODAL --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header" style="background:#059669; color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
            <h4 class="modal-title"><i class="fa fa-money"></i> Add Vehicle Expense</h4>
        </div>
        <form method="POST" action="{{ route('fleets.expenses.store', $fleet->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Expense Date <span class="text-danger">*</span></label><input type="date" name="expense_date" class="form-control" required></div>
                <div class="form-group">
                    <label>Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        @foreach(\App\Models\FleetExpense::CATEGORIES as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Description</label><input type="text" name="description" class="form-control" placeholder="Brief description"></div>
                <div class="form-group"><label>Amount (K) <span class="text-danger">*</span></label><input type="number" name="amount" class="form-control" step="0.01" min="0" placeholder="0.00" required></div>
                <div class="form-group"><label>Reference Number</label><input type="text" name="reference_number" class="form-control" placeholder="Receipt / Invoice reference"></div>
                <div class="form-group"><label>Supporting Document</label><input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Expense</button>
            </div>
        </form>
    </div></div>
</div>

{{-- SCHEDULE MAINTENANCE MODAL --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header" style="background:#0284c7; color:#fff;">
            <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
            <h4 class="modal-title"><i class="fa fa-calendar"></i> Schedule Maintenance</h4>
        </div>
        <form method="POST" action="{{ route('maintenance.store') }}">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="maintenanceVehicleId" value="{{ $fleet->vehicle_id }}">
                <div class="form-group"><label>Maintenance Type <span class="text-danger">*</span></label><input type="text" name="maintenanceType" class="form-control" placeholder="e.g. Oil Change, Service" required></div>
                <div class="form-group"><label>Technician</label><input type="text" name="maintenanceTechnician" class="form-control" placeholder="Assigned technician"></div>
                <div class="form-group"><label>Due Date</label><input type="date" name="maintenanceDueDate" class="form-control"></div>
                <div class="form-group"><label>Notes</label><textarea name="maintenanceNotes" class="form-control" rows="3" placeholder="Additional notes"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info"><i class="fa fa-calendar-plus-o"></i> Schedule</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Insurance Expiry Update Modal -->
<div class="modal fade" id="insuranceModal" tabindex="-1" role="dialog" aria-labelledby="insuranceModalLabel">
    <div class="modal-dialog" role="document" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#f8f9fa; padding:12px 18px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="insuranceModalLabel" style="font-size:16px; font-weight:600;">
                    <i class="fa fa-calendar text-warning"></i> Update Insurance Expiry
                </h4>
            </div>
            <div class="modal-body" style="padding:18px 20px;">
                <div style="margin-bottom:14px;">
                    <div style="font-size:12px; color:#666; margin-bottom:3px;">Current Expiry</div>
                    <div id="modalCurrentDate" style="font-weight:600; font-size:15px; color:#333;">
                        {{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('d M Y') : 'N/A' }}
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label for="newInsuranceDate" style="font-size:12px; color:#666; margin-bottom:4px;">New Expiry Date (must be today or later)</label>
                    <input type="date" id="newInsuranceDate" class="form-control" style="height:36px; font-size:14px;"
                           value="{{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('Y-m-d') : '' }}">
                </div>
            </div>
            <div class="modal-footer" style="padding:10px 16px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveInsuranceBtn">
                    <i class="fa fa-save"></i> Save New Date
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        // Open modal
        $('#editInsuranceBtn').on('click', function (e) {
            e.preventDefault();
            $('#modalCurrentDate').text($('#insuranceExpiryValue').text().trim());
            var currentIso = '{{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format("Y-m-d") : "" }}';
            $('#newInsuranceDate').val(currentIso);
            $('#insuranceModal').modal('show');
        });

        // Save via AJAX
        $('#saveInsuranceBtn').on('click', function () {
            var newDate = $('#newInsuranceDate').val();
            if (!newDate) {
                alert('Please select a valid future date.');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: '{{ route("fleets.update-insurance", $fleet->id) }}',
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    insurance_expire_date: newDate
                },
                success: function (res) {
                    if (res.success) {
                        $('#insuranceModal').modal('hide');
                        // Reload to refresh date + any expiry warning labels
                        location.reload();
                    } else {
                        alert(res.message || 'Update failed.');
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update date. Please try again.';
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save New Date');
                }
            });
        });
    });
</script>

@endsection

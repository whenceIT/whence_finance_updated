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
                        <div style="font-size:11px; color:#888; text-transform:uppercase;">Insurance Expiry</div>
                        <div style="font-weight:600; color:#222; font-size:14.5px; line-height:1.2;">
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
                            {{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('d M Y') : 'N/A' }}
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
</div>

@endsection

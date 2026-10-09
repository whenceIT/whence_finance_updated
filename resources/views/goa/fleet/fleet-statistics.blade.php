@extends('layouts.master')
@section('title')
    GOA Manager - Fleet Statistics
@endsection

@section('content')
<div class="container-fluid">

    {{-- Page header --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="mb-0" style="font-weight:700; color:#1e293b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#06b6d4" class="bi bi-pie-chart-fill me-2" viewBox="0 0 16 16">
                            <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
                        </svg>
                        Fleet Statistics
                    </h3>
                    <p class="text-muted mb-0 mt-1">Overview of fleet status breakdown.</p>
                </div>
                {{-- Sub-page nav --}}
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('goa.fleet.vehicle-inventory') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-car me-1"></i> Vehicle Inventory
                    </a>
                    <a href="{{ route('goa.fleet.statistics') }}" class="btn btn-info btn-sm">
                        <i class="fa fa-pie-chart me-1"></i> Fleet Statistics
                    </a>
                    <a href="{{ route('goa.fleet.upcoming-maintenance') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fa fa-wrench me-1"></i> Upcoming Maintenance
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="info-box" style="border-left:4px solid #667eea;">
                <span class="info-box-icon" style="background:linear-gradient(135deg,#667eea,#764ba2);"><i class="fa fa-car"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Vehicles</span>
                    <span class="info-box-number">{{ $totalVehicles }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="info-box" style="border-left:4px solid #48bb78;">
                <span class="info-box-icon" style="background:linear-gradient(135deg,#48bb78,#38a169);"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active</span>
                    <span class="info-box-number">{{ $activeVehicles }}</span>
                    <span class="progress-description">{{ $totalVehicles > 0 ? round(($activeVehicles / $totalVehicles) * 100) : 0 }}% of fleet</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="info-box" style="border-left:4px solid #ed8936;">
                <span class="info-box-icon" style="background:linear-gradient(135deg,#ed8936,#dd6b20);"><i class="fa fa-wrench"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Under Maintenance</span>
                    <span class="info-box-number">{{ $maintenanceVehicles }}</span>
                    <span class="progress-description">{{ $totalVehicles > 0 ? round(($maintenanceVehicles / $totalVehicles) * 100) : 0 }}% of fleet</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="info-box" style="border-left:4px solid #e53e3e;">
                <span class="info-box-icon" style="background:linear-gradient(135deg,#e53e3e,#c53030);"><i class="fa fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Out of Service</span>
                    <span class="info-box-number">{{ $outOfServiceVehicles }}</span>
                    <span class="progress-description">{{ $totalVehicles > 0 ? round(($outOfServiceVehicles / $totalVehicles) * 100) : 0 }}% of fleet</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Operational rate banner --}}
    <div class="alert alert-light text-center mb-4" style="border:1px solid #e2e8f0; border-radius:10px;">
        <span class="text-muted">Fleet Operational Rate:&nbsp;</span>
        <strong class="text-success">{{ $totalVehicles > 0 ? round(($activeVehicles / $totalVehicles) * 100) : 0 }}%</strong>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span class="text-muted">Non-Operational Rate:&nbsp;</span>
        <strong class="text-warning">{{ $totalVehicles > 0 ? round((($maintenanceVehicles + $outOfServiceVehicles) / $totalVehicles) * 100) : 0 }}%</strong>
    </div>

    {{-- Collapsible breakdown list --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h4 class="box-title" style="display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#06b6d4 0%,#0891b2 100%);color:#fff;padding:10px 24px;border-radius:60px;font-weight:700;font-size:1.2rem;box-shadow:0 6px 18px -4px rgba(6,182,212,.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
                    <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
                </svg>
                Vehicle Breakdown
            </h4>
        </div>
        <div class="box-body p-0">
            <ul class="list-group list-group-flush">

                {{-- Total --}}
                <li class="list-group-item" style="position:relative;background:linear-gradient(120deg,#fff 0%,#f8f9ff 100%);border-left:4px solid #667eea;cursor:pointer;"
                    data-toggle="collapse" data-target="#collapseTotal">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2" style="background:#667eea20;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#667eea" class="bi bi-car-front" viewBox="0 0 16 16">
                                    <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/><path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                                </svg>
                            </div>
                            <div>
                                <strong>Total Company Vehicles</strong>
                                <small class="text-muted d-block">Complete fleet size</small>
                            </div>
                        </div>
                        <span class="badge" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;font-size:1rem;padding:.4rem 1rem;border-radius:20px;">{{ $totalVehicles }}</span>
                    </div>
                </li>
                <div class="collapse" id="collapseTotal">
                    <ul class="list-group list-group-flush">
                        @foreach($totalFleets as $fleet)
                            <li class="list-group-item list-group-item-light py-2" style="font-size:.9rem;">
                                <strong>{{ $fleet->vehicle_id }}</strong> — {{ $fleet->vehicle_model }}
                                &nbsp;<span class="text-muted">→ {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Active --}}
                <li class="list-group-item" style="position:relative;background:linear-gradient(120deg,#fff 0%,#f0fff4 100%);border-left:4px solid #48bb78;cursor:pointer;"
                    data-toggle="collapse" data-target="#collapseActive">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2" style="background:#48bb7820;">
                                <i class="fa fa-check-circle" style="color:#48bb78;font-size:20px;"></i>
                            </div>
                            <div>
                                <strong>Active Vehicles</strong>
                                <small class="text-muted d-block">Currently operational</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge" style="background:linear-gradient(135deg,#48bb78,#38a169);color:#fff;font-size:1rem;padding:.4rem 1rem;border-radius:20px;">{{ $activeVehicles }}</span>
                            <small class="text-muted d-block">{{ $totalVehicles > 0 ? round(($activeVehicles / $totalVehicles) * 100) : 0 }}% of fleet</small>
                        </div>
                    </div>
                </li>
                <div class="collapse" id="collapseActive">
                    <ul class="list-group list-group-flush">
                        @foreach($activeFleets as $fleet)
                            <li class="list-group-item list-group-item-light py-2" style="font-size:.9rem;">
                                <strong>{{ $fleet->vehicle_id }}</strong> — {{ $fleet->vehicle_model }}
                                &nbsp;<span class="text-muted">→ {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Under Maintenance --}}
                <li class="list-group-item" style="position:relative;background:linear-gradient(120deg,#fff 0%,#fffaf0 100%);border-left:4px solid #ed8936;cursor:pointer;"
                    data-toggle="collapse" data-target="#collapseMaintenance">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2" style="background:#ed893620;">
                                <i class="fa fa-wrench" style="color:#ed8936;font-size:20px;"></i>
                            </div>
                            <div>
                                <strong>Under Maintenance</strong>
                                <small class="text-muted d-block">In workshop / repair</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge" style="background:linear-gradient(135deg,#ed8936,#dd6b20);color:#fff;font-size:1rem;padding:.4rem 1rem;border-radius:20px;">{{ $maintenanceVehicles }}</span>
                            <small class="text-muted d-block">{{ $totalVehicles > 0 ? round(($maintenanceVehicles / $totalVehicles) * 100) : 0 }}% of fleet</small>
                        </div>
                    </div>
                </li>
                <div class="collapse" id="collapseMaintenance">
                    <ul class="list-group list-group-flush">
                        @foreach($maintenanceFleets as $fleet)
                            <li class="list-group-item list-group-item-light py-2" style="font-size:.9rem;">
                                <strong>{{ $fleet->vehicle_id }}</strong> — {{ $fleet->vehicle_model }}
                                &nbsp;<span class="text-muted">→ {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Out of Service --}}
                <li class="list-group-item" style="position:relative;background:linear-gradient(120deg,#fff 0%,#fff5f5 100%);border-left:4px solid #e53e3e;cursor:pointer;"
                    data-toggle="collapse" data-target="#collapseOutOfService">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2" style="background:#e53e3e20;">
                                <i class="fa fa-times-circle" style="color:#e53e3e;font-size:20px;"></i>
                            </div>
                            <div>
                                <strong>Out of Service</strong>
                                <small class="text-muted d-block">Non-operational</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;font-size:1rem;padding:.4rem 1rem;border-radius:20px;">{{ $outOfServiceVehicles }}</span>
                            <small class="text-muted d-block">{{ $totalVehicles > 0 ? round(($outOfServiceVehicles / $totalVehicles) * 100) : 0 }}% of fleet</small>
                        </div>
                    </div>
                </li>
                <div class="collapse" id="collapseOutOfService">
                    <ul class="list-group list-group-flush">
                        @foreach($outOfServiceFleets as $fleet)
                            <li class="list-group-item list-group-item-light py-2" style="font-size:.9rem;">
                                <strong>{{ $fleet->vehicle_id }}</strong> — {{ $fleet->vehicle_model }}
                                &nbsp;<span class="text-muted">→ {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </ul>
        </div>
    </div>

</div>
@endsection

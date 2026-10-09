@extends('layouts.master')
@section('title')
    GOA Manager - Upcoming Maintenance
@endsection

@section('content')
<div class="container-fluid">

    {{-- Page header --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="mb-0" style="font-weight:700; color:#1e293b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#d97706" class="bi bi-wrench-adjustable-circle-fill me-2" viewBox="0 0 16 16">
                            <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
                        </svg>
                        Upcoming Maintenance
                    </h3>
                    <p class="text-muted mb-0 mt-1">Pending maintenance schedules ordered by due date.</p>
                </div>
                {{-- Sub-page nav --}}
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('goa.fleet.vehicle-inventory') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-car me-1"></i> Vehicle Inventory
                    </a>
                    <a href="{{ route('goa.fleet.statistics') }}" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-pie-chart me-1"></i> Fleet Statistics
                    </a>
                    <a href="{{ route('goa.fleet.upcoming-maintenance') }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-wrench me-1"></i> Upcoming Maintenance
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Record Maintenance button --}}
    <div class="mb-3">
        <button type="button" class="btn btn-warning" id="btnRecordMaintenance">
            <i class="fa fa-plus me-1"></i> Record Maintenance
        </button>
    </div>

    {{-- Summary badges --}}
    @php
        $overdueCount = $maintenanceSchedules->filter(fn($s) => $s->due_date && now()->diffInDays($s->due_date, false) < 0)->count();
        $dueSoonCount = $maintenanceSchedules->filter(fn($s) => $s->due_date && now()->diffInDays($s->due_date, false) >= 0 && now()->diffInDays($s->due_date, false) <= 5)->count();
    @endphp
    @if($overdueCount > 0 || $dueSoonCount > 0)
    <div class="d-flex flex-wrap gap-2 mb-3">
        @if($overdueCount > 0)
            <span class="label label-danger" style="font-size:1rem;padding:.4rem .85rem;border-radius:20px;">
                <i class="fa fa-exclamation-triangle me-1"></i> {{ $overdueCount }} Overdue
            </span>
        @endif
        @if($dueSoonCount > 0)
            <span class="label label-warning" style="font-size:1rem;padding:.4rem .85rem;border-radius:20px;">
                <i class="fa fa-clock-o me-1"></i> {{ $dueSoonCount }} Due within 5 days
            </span>
        @endif
    </div>
    @endif

    {{-- Maintenance list --}}
    <div class="box box-warning">
        <div class="box-header with-border">
            <h4 class="box-title" style="display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);color:#1e293b;padding:10px 24px;border-radius:60px;font-weight:700;font-size:1.2rem;box-shadow:0 6px 18px -4px rgba(245,158,11,.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
                    <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
                </svg>
                Upcoming Maintenance Schedules
            </h4>
        </div>
        <div class="box-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($maintenanceSchedules as $schedule)
                    @php
                        $daysUntilDue = $schedule->due_date ? now()->diffInDays($schedule->due_date, false) : null;
                        $rowBg = '';
                        if ($daysUntilDue !== null) {
                            if ($daysUntilDue < 0) {
                                $rowBg = 'background-color:#f8d7da;';
                            } elseif ($daysUntilDue <= 5) {
                                $rowBg = 'background-color:#fff3cd;';
                            }
                        }
                        $isOverdue  = $daysUntilDue !== null && $daysUntilDue < 0;
                        $isDueSoon  = $daysUntilDue !== null && $daysUntilDue >= 0 && $daysUntilDue <= 5;
                    @endphp
                    <li class="list-group-item" style="{{ $rowBg }} {{ $isOverdue ? 'animation:pulse 1s infinite;' : '' }}">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">

                            {{-- Vehicle info --}}
                            <div style="flex:1; min-width:220px;">
                                <p class="mb-1">
                                    <strong style="color:#0d6efd; font-size:1.1rem;">{{ $schedule->fleet->vehicle_id ?? 'N/A' }}</strong>
                                    @if($isOverdue)
                                        <span class="label label-danger ml-2">Overdue</span>
                                    @elseif($isDueSoon)
                                        <span class="label label-warning ml-2">Due Soon</span>
                                    @endif
                                </p>
                                <p class="mb-0 text-muted">{{ $schedule->maintenance_type }}</p>
                                <p class="mb-0 text-muted" style="font-size:.9rem;">
                                    <i class="fa fa-building-o me-1"></i>Office: {{ $schedule->fleet->office->name ?? 'N/A' }}
                                </p>
                                <p class="mb-0 text-muted" style="font-size:.9rem;">
                                    <i class="fa fa-user me-1"></i>Assigned: {{ $schedule->fleet->user->first_name ?? 'N/A' }} {{ $schedule->fleet->user->last_name ?? '' }}
                                </p>
                                @if($schedule->technician)
                                    <p class="mb-0 text-muted" style="font-size:.9rem;">
                                        <i class="fa fa-wrench me-1"></i>Technician: {{ $schedule->technician }}
                                    </p>
                                @endif
                                @if($schedule->notes)
                                    <p class="mb-0 text-muted" style="font-size:.9rem; font-style:italic;">{{ $schedule->notes }}</p>
                                @endif
                            </div>

                            {{-- Due date & countdown --}}
                            <div class="text-center" style="min-width:140px;">
                                <span class="badge" style="font-size:1.1rem; font-weight:700; padding:.5rem 1rem; border-radius:.375rem; {{ $isOverdue ? 'background:#dc3545;color:#fff;' : 'background:#ffc107;color:#000;' }}">
                                    Due: {{ $schedule->due_date ? $schedule->due_date->format('Y-m-d') : 'N/A' }}
                                </span>
                                @if($daysUntilDue !== null)
                                    <p class="mb-0 mt-1 font-weight-bold" style="{{ $isOverdue ? 'color:#dc3545;' : ($isDueSoon ? 'color:#d97706;' : 'color:#16a34a;') }}">
                                        @if($daysUntilDue > 0)
                                            {{ $daysUntilDue }} day(s) to go
                                        @elseif($daysUntilDue < 0)
                                            {{ abs($daysUntilDue) }} day(s) overdue
                                        @else
                                            Due today
                                        @endif
                                    </p>
                                @endif
                            </div>

                            {{-- Mark completed form --}}
                            <form method="POST" action="{{ route('maintenance.complete', $schedule->id) }}" class="d-flex align-items-center gap-2" style="min-width:240px;">
                                @csrf
                                <input type="number" name="amount" step="0.01" placeholder="Amount (K)" class="form-control form-control-sm" style="width:130px;" required>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fa fa-check me-1"></i> Done
                                </button>
                            </form>

                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">
                        <i class="fa fa-check-circle fa-2x text-success mb-2 d-block"></i>
                        No upcoming maintenance scheduled.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- ============================================================
     RECORD MAINTENANCE MODAL
     ============================================================ --}}
<div class="modal fade" id="recordMaintenanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);color:#1e293b;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Record Maintenance</h4>
            </div>
            <form method="POST" action="{{ route('maintenance.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Vehicle <span class="text-danger">*</span></label>
                        <select name="maintenanceVehicleId" class="form-control" required>
                            <option value="">-- Select Vehicle --</option>
                            @foreach($allFleets as $f)
                                <option value="{{ $f->vehicle_id }}">{{ $f->vehicle_id }} — {{ $f->vehicle_model }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Maintenance Type <span class="text-danger">*</span></label>
                        <input type="text" name="maintenanceType" class="form-control" placeholder="Oil Change, Tyre Rotation…" required>
                    </div>
                    <div class="form-group">
                        <label>Technician</label>
                        <input type="text" name="maintenanceTechnician" class="form-control" placeholder="Technician name">
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="maintenanceDueDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="maintenanceNotes" class="form-control" rows="3" placeholder="Additional notes…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fa fa-wrench me-1"></i> Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%   { opacity: 1; }
    50%  { opacity: 0.7; }
    100% { opacity: 1; }
}
</style>

<script>
    document.getElementById('btnRecordMaintenance').addEventListener('click', function () {
        $('#recordMaintenanceModal').modal('show');
    });
</script>
@endsection

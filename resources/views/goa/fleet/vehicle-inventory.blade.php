@extends('layouts.master')
@section('title')
    GOA Manager - Vehicle Inventory
@endsection

@section('content')
<div class="container-fluid">

    {{-- Page header --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="mb-0" style="font-weight:700; color:#1e293b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#2563eb" class="bi bi-car-front me-2" viewBox="0 0 16 16">
                            <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
                            <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                        </svg>
                        Vehicle Inventory
                    </h3>
                    <p class="text-muted mb-0 mt-1">Full list of company-owned fleet vehicles.</p>
                </div>
                {{-- Sub-page nav --}}
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('goa.fleet.vehicle-inventory') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-car me-1"></i> Vehicle Inventory
                    </a>
                    <a href="{{ route('goa.fleet.statistics') }}" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-pie-chart me-1"></i> Fleet Statistics
                    </a>
                    <a href="{{ route('goa.fleet.upcoming-maintenance') }}" class="btn btn-outline-warning btn-sm">
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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Action buttons --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <button type="button" class="btn btn-primary" id="btnAddVehicle">
            <i class="fa fa-plus me-1"></i> Add Vehicle
        </button>
        <button type="button" class="btn btn-secondary" id="btnRecordMaintenance">
            <i class="fa fa-wrench me-1"></i> Record Maintenance
        </button>
    </div>

    {{-- Vehicle table --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h4 class="box-title" style="display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#2563eb 0%,#1e40af 100%);color:#fff;padding:10px 24px;border-radius:60px;font-weight:600;font-size:1.2rem;box-shadow:0 6px 18px -4px rgba(37,99,235,.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-car-front" viewBox="0 0 16 16">
                    <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/><path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                </svg>
                Vehicle Inventory List
            </h4>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-hover table-condensed">
                    <thead class="thead-light">
                        <tr>
                            <th>Vehicle ID</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Office</th>
                            <th>Color</th>
                            <th>Date Purchased</th>
                            <th>Insurance Expire</th>
                            <th>Current Value</th>
                            <th>White Book</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Last Maintenance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fleets as $fleet)
                            <tr>
                                <td><strong>{{ $fleet->vehicle_id }}</strong></td>
                                <td>{{ $fleet->vehicle_type }}</td>
                                <td>{{ $fleet->vehicle_model }}</td>
                                <td>{{ $fleet->office ? $fleet->office->name : '-' }}</td>
                                <td>{{ $fleet->color ?: '-' }}</td>
                                <td>{{ $fleet->date_purchased ? $fleet->date_purchased->format('Y-m-d') : '-' }}</td>
                                <td>{{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('Y-m-d') : '-' }}</td>
                                <td>{{ $fleet->current_value ? 'K' . number_format($fleet->current_value, 2) : '-' }}</td>
                                <td>{{ ucfirst($fleet->white_book) }}</td>
                                <td>
                                    @if($fleet->vehicle_status == 'Active')
                                        <span class="label label-success">Active</span>
                                    @elseif($fleet->vehicle_status == 'Maintenance')
                                        <span class="label label-warning">Maintenance</span>
                                    @else
                                        <span class="label label-danger">{{ $fleet->vehicle_status }}</span>
                                    @endif
                                </td>
                                <td>{{ $fleet->user ? $fleet->user->first_name . ' ' . $fleet->user->last_name : $fleet->assigned_to }}</td>
                                <td>{{ $fleet->last_maintenance ? $fleet->last_maintenance->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <a href="{{ route('fleets.edit', $fleet->id) }}" class="btn btn-xs btn-default" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="{{ route('fleets.show', $fleet->id) }}" class="btn btn-xs btn-info" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form action="{{ route('fleets.destroy', $fleet->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this vehicle?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted">No fleet records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7"></td>
                            <td><strong>Total: K{{ number_format($totalValue, 2) }}</strong></td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {{ $fleets->links() }}
        </div>
    </div>
</div>

{{-- ============================================================
     ADD VEHICLE MODAL
     ============================================================ --}}
<div class="modal fade" id="addVehicleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
                <h4 class="modal-title">Add Vehicle</h4>
            </div>
            <form method="POST" action="{{ route('fleets.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vehicle ID</label>
                                <input type="text" name="vehicle_id" class="form-control" placeholder="Auto Generated" disabled>
                            </div>
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="vehicle_type" class="form-control" required>
                                    <option value="">-- Select Type --</option>
                                    @foreach(['Sedan','Hatchback','Coupe','Convertible','SUV','Crossover','Truck','Pickup Truck','Van','Minivan','Bus','Motorcycle','Electric Vehicle','Hybrid'] as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Model <span class="text-danger">*</span></label>
                                <input type="text" name="vehicle_model" class="form-control" placeholder="Toyota Camry" required>
                            </div>
                            <div class="form-group">
                                <label>Date Purchased</label>
                                <input type="date" name="date_purchased" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Current Value</label>
                                <input type="number" name="current_value" class="form-control" step="0.01" placeholder="12500.00">
                            </div>
                            <div class="form-group">
                                <label>Last Maintenance <span class="text-danger">*</span></label>
                                <input type="date" name="last_maintenance" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned To <span class="text-danger">*</span></label>
                                <select name="assigned_to" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Office <span class="text-danger">*</span></label>
                                <select name="office_id" class="form-control" required>
                                    <option value="">-- Select Office --</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Color <span class="text-danger">*</span></label>
                                <input type="text" name="color" class="form-control" placeholder="Blue" required>
                            </div>
                            <div class="form-group">
                                <label>Insurance Expire Date <span class="text-danger">*</span></label>
                                <input type="date" name="insurance_expire_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>White Book <span class="text-danger">*</span></label>
                                <select name="white_book" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="available">Available</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="vehicle_status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active">Active</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Out of Service">Out of Service</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Vehicle</button>
                </div>
            </form>
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

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#addVehicleModal').modal('show');
    });
</script>
@endif

<script>
    document.getElementById('btnAddVehicle').addEventListener('click', function () {
        $('#addVehicleModal').modal('show');
    });
    document.getElementById('btnRecordMaintenance').addEventListener('click', function () {
        $('#recordMaintenanceModal').modal('show');
    });
</script>
@endsection

@extends('layouts.master')
@section('title')
    GOA Manager - Fleet Management
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p class="lead">Manage and monitor government vehicle fleet.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <strong>There were some issues with your submission.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <style>
        .fleet-nav-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 0;
        }

        .fleet-nav-btn {
            border: none;
            background: white;
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .fleet-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }

        .fleet-action-btn {
            border: none;
            background: #2563eb;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .fleet-action-btn.secondary {
            background: #0d6efd;
        }

        .fleet-action-btn:hover {
            transform: translateY(-2px);
            background: #1d4ed8;
        }

        .fleet-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            z-index: 1040;
        }

        .fleet-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            padding: 1rem;
        }

        .fleet-modal-content {
            width: 100%;
            max-width: 620px;
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.16);
        }

        .fleet-modal-header,
        .fleet-modal-footer {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .fleet-modal-footer {
            border-top: 1px solid #e9ecef;
            border-bottom: none;
        }

        .fleet-modal-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .fleet-modal-close {
            background: transparent;
            border: none;
            font-size: 1.35rem;
            color: #64748b;
            cursor: pointer;
        }

        .fleet-modal-body {
            padding: 1.5rem;
        }

        .fleet-modal-form-group {
            margin-bottom: 1rem;
        }

        .fleet-modal-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #475569;
        }

        .fleet-modal-input,
        .fleet-modal-select,
        .fleet-modal-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #f8fafc;
            color: #0f172a;
            font-size: 0.95rem;
        }

        .fleet-modal-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .fleet-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .fleet-modal-submit,
        .fleet-modal-secondary {
            border: none;
            padding: 0.85rem 1.4rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .fleet-modal-submit {
            background: #2563eb;
            color: white;
            position: relative;
        }

        .fleet-modal-submit.loading {
            pointer-events: none;
            opacity: 0.85;
        }

        .fleet-modal-submit.loading::after {
            content: "";
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.75);
            border-top-color: #ffffff;
            border-radius: 50%;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            animation: fleetButtonSpinner 0.8s linear infinite;
        }

        .fleet-modal-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .fleet-modal-submit:hover {
            background: #1d4ed8;
        }

        .fleet-modal-secondary:hover {
            background: #e2e8f0;
        }

        @keyframes fleetButtonSpinner {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        .fleet-form-section {
            display: none;
        }

        .fleet-form-section.active {
            display: block;
        }

        .fleet-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fleet-modal-header h4 {
            margin-bottom: 0;
        }

        .fleet-modal-body .row {
            gap: 1rem;
        }

        .fleet-modal-body .col {
            flex: 1;
        }

        .fleet-modal-grid {
            display: grid;
            gap: 1rem;
        }

        .fleet-modal-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .fleet-form-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .fleet-form-columns {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        @media (max-width: 576px) {
            .fleet-modal-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .fleet-nav-btn:hover {
            background-color: rgba(0, 123, 255, 0.08);
            color: #495057;
            transform: translateY(-2px);
        }

        .fleet-nav-btn.active {
            background: linear-gradient(135deg, #e7f3ff 0%, #f0f7ff 100%);
            color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            border-bottom: 3px solid #007bff;
        }

        .fleet-content-container {
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .fleet-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .fleet-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>


    <div class="fleet-actions">
        <button type="button" class="fleet-action-btn" data-modal="addVehicleModal">
            <i class="fas fa-plus"></i>Add Vehicle
        </button>
        <button type="button" class="fleet-action-btn secondary" data-modal="recordMaintenanceModal">
            <i class="fas fa-tools"></i>Record Maintenance
        </button>
    </div>

    <div class="fleet-nav-container" role="tablist">
        <button class="fleet-nav-btn active" data-section="inventory" role="tab">
            <i class="fas fa-boxes"></i>Vehicle Inventory
        </button>
        <button class="fleet-nav-btn" data-section="statistics" role="tab">
            <i class="fas fa-chart-bar"></i>Fleet Statistics
        </button>
        <button class="fleet-nav-btn" data-section="maintenance" role="tab">
            <i class="fas fa-wrench"></i>Upcoming Maintenance
        </button>
    </div>

    <div class="fleet-content-container" id="fleetTabsContent">
        <div class="fleet-section active" id="inventory" role="tabpanel">
            <h5 class="mb-4">
                <i class="fas fa-list text-primary"></i> Vehicle Inventory
            </h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Vehicle ID</th>
                                    <th>Type</th>
                                    <th>Model</th>
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
                                    <td>
                                        @if($fleet->vehicle_status == 'Active')
                                            <span class="badge badge-success badge-pill">Active</span>
                                        @elseif($fleet->vehicle_status == 'Maintenance')
                                            <span class="badge badge-warning badge-pill">Maintenance</span>
                                        @else
                                            <span class="badge badge-danger badge-pill">{{ $fleet->vehicle_status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $fleet->user ? $fleet->user->first_name . ' ' . $fleet->user->last_name : $fleet->assigned_to }}</td>
                                    <td>{{ $fleet->last_maintenance ? $fleet->last_maintenance->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No fleet records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $fleets->links() }}
                    </div>
        </div>

        <div class="fleet-section" id="statistics" role="tabpanel">
            <h5 class="mb-4">
                <i class="fas fa-chart-pie text-primary"></i> Fleet Statistics
            </h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fas fa-car text-primary mr-2"></i><strong>Total Vehicles</strong></span>
                            <span class="badge badge-primary badge-pill px-3 py-2">{{ $totalVehicles }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fas fa-check-circle text-success mr-2"></i><strong>Active Vehicles</strong></span>
                            <span class="badge badge-success badge-pill px-3 py-2">{{ $activeVehicles }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fas fa-tools text-warning mr-2"></i><strong>Under Maintenance</strong></span>
                            <span class="badge badge-warning badge-pill px-3 py-2">{{ $maintenanceVehicles }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span><i class="fas fa-ban text-danger mr-2"></i><strong>Out of Service</strong></span>
                            <span class="badge badge-danger badge-pill px-3 py-2">{{ $outOfServiceVehicles }}</span>
                        </li>
                    </ul>
        </div>

        <div class="fleet-section" id="maintenance" role="tabpanel">
            <h5 class="mb-4">
                <i class="fas fa-calendar-check text-primary"></i> Upcoming Maintenance
            </h5>
                    <ul class="list-group list-group-flush">
                        @forelse($maintenanceSchedules as $schedule)
                        <li class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary">{{ $schedule->fleet->vehicle_id ?? 'N/A' }}</strong>
                                    <p class="mb-0 text-muted small">{{ $schedule->maintenance_type }}</p>
                                    @if($schedule->technician)
                                        <p class="mb-0 text-muted small">Technician: {{ $schedule->technician }}</p>
                                    @endif
                                    @if($schedule->notes)
                                        <p class="mb-0 text-muted small">{{ $schedule->notes }}</p>
                                    @endif
                                </div>
                                <span class="badge badge-warning">Due: {{ $schedule->due_date ? $schedule->due_date->format('Y-m-d') : 'N/A' }}</span>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item py-3">
                            <div class="text-center text-muted">No upcoming maintenance scheduled.</div>
                        </li>
                        @endforelse
                    </ul>
        </div>
    </div>

    <div class="fleet-modal-backdrop" id="fleetModalBackdrop"></div>
    <div class="fleet-modal" id="fleetModal">
        <div class="fleet-modal-content">
            <div class="fleet-modal-header">
                <h4 class="fleet-modal-title" id="fleetModalTitle">Add Vehicle</h4>
                <button type="button" class="fleet-modal-close" data-dismiss="modal">×</button>
            </div>
            <div class="fleet-modal-body">
                <form id="fleetModalForm" method="POST" action="{{ route('fleets.store') }}">
                    @csrf
                    <div class="fleet-form-section active" data-type="vehicle">
                        <div class="fleet-form-columns">
                            <!-- Left Column -->
                            <div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="vehicleId">Vehicle ID</label>
                                    <input class="fleet-modal-input" id="vehicleId" name="vehicle_id" type="text" placeholder="GOA-010">
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="vehicleType">Type</label>
                                    <select class="fleet-modal-select" id="vehicleType" name="vehicle_type">
                                        <option>Sedan</option>
                                        <option>SUV</option>
                                        <option>Truck</option>
                                        <option>Van</option>
                                        <option>Bus</option>
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="vehicleModel">Model</label>
                                    <input class="fleet-modal-input" id="vehicleModel" name="vehicle_model" type="text" placeholder="Toyota Camry">
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="datePurchased">Date Purchased</label>
                                    <input class="fleet-modal-input" id="datePurchased" name="date_purchased" type="date">
                                </div>

                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="currentValue">Current Value</label>
                                    <input class="fleet-modal-input" id="currentValue" name="current_value" type="number" step="0.01" placeholder="12500.00">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="assignedTo">Assigned To</label>
                                    <select class="fleet-modal-select fleet-user-select" id="assignedTo" name="assigned_to">
                                        <option value="">-- Select User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="officeId">Office</label>
                                    <select class="fleet-modal-select fleet-office-select" id="officeId" name="office_id">
                                        <option value="">-- Select Office --</option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="color">Color</label>
                                    <input class="fleet-modal-input" id="color" name="color" type="text" placeholder="Blue">
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="insuranceExpireDate">Insurance Expire Date</label>
                                    <input class="fleet-modal-input" id="insuranceExpireDate" name="insurance_expire_date" type="date">
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="whiteBook">White Book</label>
                                    <select class="fleet-modal-select" id="whiteBook" name="white_book">
                                        <option value="available">Available</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="vehicleStatus">Status</label>
                                    <select class="fleet-modal-select" id="vehicleStatus" name="vehicle_status">
                                        <option>Active</option>
                                        <option>Maintenance</option>
                                        <option>Out of Service</option>
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="lastMaintenance">Last Maintenance</label>
                                    <input class="fleet-modal-input" id="lastMaintenance" name="last_maintenance" type="date">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <form id="maintenanceModalForm" method="POST" action="{{ route('maintenance.store') }}">
                    @csrf
                    <div class="fleet-form-section" data-type="maintenance">
                        <div class="fleet-form-columns">
                            <!-- Left Column -->
                            <div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="maintenanceVehicleId">Vehicle ID</label>
                                    <select class="fleet-modal-select" id="maintenanceVehicleId" name="maintenanceVehicleId">
                                        <option value="">-- Select Vehicle --</option>
                                        @foreach($fleets as $fleet)
                                            <option value="{{ $fleet->vehicle_id }}">{{ $fleet->vehicle_id }} ({{ $fleet->office ? $fleet->office->name : 'No Office' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="maintenanceType">Maintenance Type</label>
                                    <select class="fleet-modal-select" id="maintenanceType" name="maintenanceType">
                                        <option>Oil Change</option>
                                        <option>Tire Rotation</option>
                                        <option>Brake Inspection</option>
                                        <option>Engine Check</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="maintenanceTechnician">Technician</label>
                                    <input class="fleet-modal-input" id="maintenanceTechnician" name="maintenanceTechnician" type="text" placeholder="Maintenance Dept">
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="maintenanceDueDate">Due Date</label>
                                    <input class="fleet-modal-input" id="maintenanceDueDate" name="maintenanceDueDate" type="date">
                                </div>
                            </div>
                        </div>
                        <div class="fleet-modal-form-group">
                            <label class="fleet-modal-label" for="maintenanceNotes">Notes</label>
                            <textarea class="fleet-modal-textarea" id="maintenanceNotes" name="maintenanceNotes" placeholder="Describe the work or issue..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="fleet-modal-footer">
                <button type="button" class="fleet-modal-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="fleet-modal-submit" form="fleetModalForm">Save</button>
            </div>
        </div>
    </div>

    <script>
        const fleetModalBackdrop = document.getElementById('fleetModalBackdrop');
        const fleetModal = document.getElementById('fleetModal');
        const fleetModalTitle = document.getElementById('fleetModalTitle');
        const fleetFormSections = document.querySelectorAll('.fleet-form-section');

        function openFleetModal(type) {
            fleetModalTitle.textContent = type === 'vehicle' ? 'Add Vehicle' : 'Record Maintenance';
            const submitButton = document.querySelector('.fleet-modal-submit');
            if (type === 'vehicle') {
                submitButton.setAttribute('form', 'fleetModalForm');
            } else if (type === 'maintenance') {
                submitButton.setAttribute('form', 'maintenanceModalForm');
            }
            fleetFormSections.forEach(section => {
                section.classList.toggle('active', section.dataset.type === type);
            });
            fleetModalBackdrop.style.display = 'block';
            fleetModal.style.display = 'flex';
        }

        function closeFleetModal() {
            fleetModalBackdrop.style.display = 'none';
            fleetModal.style.display = 'none';
        }

        document.querySelectorAll('[data-modal]').forEach(button => {
            button.addEventListener('click', () => {
                openFleetModal(button.dataset.modal === 'addVehicleModal' ? 'vehicle' : 'maintenance');
            });
        });

        document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', closeFleetModal);
        });

        fleetModalBackdrop.addEventListener('click', closeFleetModal);

        const fleetModalSubmit = document.querySelector('.fleet-modal-submit');
        const fleetModalForm = document.getElementById('fleetModalForm');
        const maintenanceModalForm = document.getElementById('maintenanceModalForm');

        if (fleetModalForm && fleetModalSubmit) {
            fleetModalForm.addEventListener('submit', () => {
                fleetModalSubmit.disabled = true;
                fleetModalSubmit.classList.add('loading');
                fleetModalSubmit.textContent = 'Saving...';
            });
        }

        if (maintenanceModalForm && fleetModalSubmit) {
            maintenanceModalForm.addEventListener('submit', () => {
                fleetModalSubmit.disabled = true;
                fleetModalSubmit.classList.add('loading');
                fleetModalSubmit.textContent = 'Saving...';
            });
        }

        @if($errors->any())
            openFleetModal('vehicle');
        @endif

        document.querySelectorAll('.fleet-nav-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-section');
                
                // Remove active class from all buttons and sections
                document.querySelectorAll('.fleet-nav-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.fleet-section').forEach(section => section.classList.remove('active'));
                
                // Add active class to clicked button and corresponding section
                this.classList.add('active');
                document.getElementById(sectionId).classList.add('active');
            });
        });

        // Office dropdown search functionality
        const officeSelect = document.getElementById('officeId');
        if (officeSelect) {
            officeSelect.addEventListener('keydown', function(e) {
                if (e.key.length === 1 && !e.ctrlKey && !e.altKey) {
                    const searchChar = e.key.toLowerCase();
                    const options = this.querySelectorAll('option');
                    for (let option of options) {
                        if (option.textContent.toLowerCase().startsWith(searchChar)) {
                            this.value = option.value;
                            break;
                        }
                    }
                }
            });
        }
    </script>
@endsection
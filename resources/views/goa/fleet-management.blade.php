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
            font-size: 1.25rem;
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
            padding: 1rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.295rem;
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
            font-size: 1.25rem;
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

        .bg-warning-light {
            background-color: #fff3cd !important;
        }

        .bg-danger.pulse {
            background-color: #f8d7da !important;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>


    <div class="fleet-actions">
        <button type="button" class="fleet-action-btn" data-modal="addVehicleModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>Add Vehicle
        </button>
        <button type="button" class="fleet-action-btn secondary" data-modal="recordMaintenanceModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
            <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
            </svg>Record Maintenance
        </button>
    </div>

    <div class="fleet-nav-container" role="tablist">
        <button class="fleet-nav-btn active" data-section="inventory" role="tab">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front" viewBox="0 0 16 16">
            <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
            <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
            </svg>Vehicle Inventory
        </button>
        <button class="fleet-nav-btn" data-section="statistics" role="tab">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
            <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
            </svg>Fleet Statistics
        </button>
        <button class="fleet-nav-btn" data-section="maintenance" role="tab">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
            <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
            </svg>Upcoming Maintenance
        </button>
    </div>

    <div class="fleet-content-container" id="fleetTabsContent">
        <div class="fleet-section active" id="inventory" role="tabpanel">
            <h4 style="display: inline-flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 1.5rem; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: white; padding: 12px 28px; border-radius: 60px; font-weight: 600; font-size: 1.5rem; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.1); letter-spacing: -0.01em; backdrop-filter: blur(2px); border: 1px solid rgba(255, 255, 255, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; width: 100%; margin-left: auto; margin-right: auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front" viewBox="0 0 16 16">
                    <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
                    <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                </svg>
                Vehicle Inventory List
            </h4>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
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
                                    <a href="{{ route('fleets.edit', $fleet->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('fleets.show', $fleet->id) }}" class="btn btn-sm btn-outline-info" title="View"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('fleets.destroy', $fleet->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V6a.5.5 0 0 1-.5-.5z"/>
                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v1a1 1 0 0 1-1 1H.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 3.885 16h8.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a1 1 0 0 1-1-1V2a1 1 0 0 0-1-1h-1a1 1 0 0 1-1 1v1a1 1 0 0 0-1 1zm-3-2a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5zM4 1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 0-1h-3a.5.5 0 0 0-.5.5z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center">No fleet records found.</td>
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
                {{ $fleets->links() }}
            </div>
        </div>

        <div class="fleet-section" id="statistics" role="tabpanel">
            <h4 style="display: inline-flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 1.5rem; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; padding: 12px 28px; border-radius: 60px; font-weight: 700; font-size: 1.5rem; box-shadow: 0 10px 25px -5px rgba(6, 182, 212, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.1); letter-spacing: -0.01em; backdrop-filter: blur(2px); border: 1px solid rgba(255, 255, 255, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; width: 100%; margin-left: auto; margin-right: auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16" style="filter: drop-shadow(0 1px 1px rgba(0,0,0,0.15));">
                    <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
                </svg>
                Fleet Statistics
            </h4>
            <ul class="list-group list-group-flush shadow-sm">
                <!-- Total Vehicles -->
                <li class="list-group-item flex justify-content-between align-items-center py-3" style="background: linear-gradient(120deg, #ffffff 0%, #f8f9ff 100%); border-left: 4px solid #667eea; position: relative; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseTotal" aria-expanded="false" aria-controls="collapseTotal">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-2" style="background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#667eea" class="bi bi-car-front" viewBox="0 0 16 16">
                                <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
                                <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                            </svg>
                        </div>
                        <div>
                            <strong style="color: #2d3748;">Total Company Vehicles</strong>
                            <small class="text-muted d-block">Complete fleet size</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 1rem; border-radius: 20px;">{{ $totalVehicles }}</span>
                        <small class="text-muted d-block mt-1">100%</small>
                    </div>

                    <div style="position: absolute; right: 10px; top: 10px; width: 60px; height: 60px; background-color: #667eea; clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.969rem; font-weight: bold;">
                        {{ $totalVehicles }}
                    </div>
                </li>
                <div class="collapse" id="collapseTotal">
                    <ul class="list-group list-group-flush">
                        @foreach($totalFleets as $fleet)
                            <li class="list-group-item">{{ $fleet->vehicle_id }} - {{ $fleet->vehicle_model }} -> {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Active Vehicles -->
                <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="background: linear-gradient(120deg, #ffffff 0%, #f0fff4 100%); border-left: 4px solid #48bb78; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseActive" aria-expanded="false" aria-controls="collapseActive">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-2" style="background-color: #48bb7820;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#48bb78" class="bi bi-car-front" viewBox="0 0 16 16">
                                <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
                                <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                            </svg>
                        </div>
                        <div>
                            <strong style="color: #2d3748;">Active Vehicles</strong>
                            <small class="text-muted d-block">Currently operational</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; font-size: 1rem; border-radius: 20px;">{{ $activeVehicles }}</span>
                        <small class="text-muted d-block mt-1">{{ $totalVehicles > 0 ? round(($activeVehicles / $totalVehicles) * 100) : 0 }}% of fleet</small>
                    </div>

                    <div style="position: absolute; right: 10px; top: 10px; width: 60px; height: 60px; background-color: #33b222; clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.969rem; font-weight: bold;">
                        {{ $activeVehicles }}
                    </div>
                </li>
                <div class="collapse" id="collapseActive">
                    <ul class="list-group list-group-flush">
                        @foreach($activeFleets as $fleet)
                            <li class="list-group-item">{{ $fleet->vehicle_id }} - {{ $fleet->vehicle_model }} -> {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Under Maintenance -->
                <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="background: linear-gradient(120deg, #ffffff 0%, #fffaf0 100%); border-left: 4px solid #ed8936; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseMaintenance" aria-expanded="false" aria-controls="collapseMaintenance">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-2" style="background-color: #ed893620;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ed8936" class="bi bi-wrench" viewBox="0 0 16 16">
                                <path d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11z"/>
                            </svg>
                        </div>
                        <div>
                            <strong style="color: #2d3748;">Under Maintenance</strong>
                            <small class="text-muted d-block">In workshop/repair</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); color: white; font-size: 1rem; border-radius: 20px;">{{ $maintenanceVehicles }}</span>
                        <small class="text-muted d-block mt-1">{{ $totalVehicles > 0 ? round(($maintenanceVehicles / $totalVehicles) * 100) : 0 }}% of fleet</small>
                    </div>
                    

                    <div style="position: absolute; right: 10px; top: 10px; width: 60px; height: 60px; background-color: #ff7700; clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.969rem; font-weight: bold;">
                        {{ $maintenanceVehicles }}
                    </div>
                </li>
                <div class="collapse" id="collapseMaintenance">
                    <ul class="list-group list-group-flush">
                        @foreach($maintenanceFleets as $fleet)
                            <li class="list-group-item">{{ $fleet->vehicle_id }} - {{ $fleet->vehicle_model }} -> {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Out of Service -->
                <li class="list-group-item d-flex justify-content-between align-items-center py-3" style="background: linear-gradient(120deg, #ffffff 0%, #fff5f5 100%); border-left: 4px solid #e53e3e; cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseOutOfService" aria-expanded="false" aria-controls="collapseOutOfService">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-2" style="background-color: #e53e3e20;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e53e3e" class="bi bi-x-octagon-fill" viewBox="0 0 16 16">
                                <path d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353zm-6.106 4.5L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708"/>
                            </svg>
                        </div>
                        <div>
                            <strong style="color: #2d3748;">Out of Service</strong>
                            <small class="text-muted d-block">Non-operational</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); color: white; font-size: 1rem; border-radius: 20px;">{{ $outOfServiceVehicles }}</span>
                        <small class="text-muted d-block mt-1">{{ $totalVehicles > 0 ? round(($outOfServiceVehicles / $totalVehicles) * 100) : 0 }}% of fleet</small>
                    </div>
                    <div style="position: absolute; right: 10px; top: 10px; width: 60px; height: 60px; background-color: #ee1c09; clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.969rem; font-weight: bold;">
                        {{ $outOfServiceVehicles }}
                    </div>
                </li>
                <div class="collapse" id="collapseOutOfService">
                    <ul class="list-group list-group-flush">
                        @foreach($outOfServiceFleets as $fleet)
                            <li class="list-group-item">{{ $fleet->vehicle_id }} - {{ $fleet->vehicle_model }} -> {{ $fleet->office->name ?? 'N/A' }}, {{ $fleet->user->first_name ?? 'N/A' }} {{ $fleet->user->last_name ?? '' }}</li>
                        @endforeach
                    </ul>
                </div>
            </ul>

            <!-- Optional: Add a subtle footer with total operational rate -->
            <div class="mt-3 p-3 bg-light rounded text-center">
                <small class="text-muted">
                    Fleet Operational Rate: 
                    <strong class="text-success">
                        {{ $totalVehicles > 0 ? round((($activeVehicles) / $totalVehicles) * 100) : 0 }}%
                    </strong>
                    | 
                    Maintenance Rate: 
                    <strong class="text-warning">
                        {{ $totalVehicles > 0 ? round((($maintenanceVehicles + $outOfServiceVehicles) / $totalVehicles) * 100) : 0 }}%
                    </strong>
                </small>
            </div>
        </div>

        <div class="fleet-section" id="maintenance" role="tabpanel">
            <h4 style="display: inline-flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 1.5rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #1e293b; padding: 12px 28px; border-radius: 60px; font-weight: 700; font-size: 1.5rem; box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.1); letter-spacing: -0.01em; backdrop-filter: blur(2px); border: 1px solid rgba(255, 255, 255, 0.3); transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; width: 100%; margin-left: auto; margin-right: auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16" style="filter: drop-shadow(0 1px 1px rgba(0,0,0,0.15));">
                    <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
                </svg>
                Upcoming Maintenance
            </h4>
            <ul class="list-group list-group-flush">
                @forelse($maintenanceSchedules as $schedule)
                @php
                    $daysUntilDue = $schedule->due_date ? now()->diffInDays($schedule->due_date, false) : null;
                    $liStyle = 'padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;';
                    if ($daysUntilDue !== null) {
                        if ($daysUntilDue <= 5 && $daysUntilDue >= 0) {
                            $liStyle .= ' background-color: #ff99009d;';
                        } elseif ($daysUntilDue < 0) {
                            $liStyle .= ' background-color: #f8d7da; animation: pulse 1s infinite;';
                        }
                    }
                @endphp
                <li style="{{ $liStyle }}">
                    <div style="display: flex; align-items: center; flex: 1;">
                        <div>
                            <strong style="color: #0d6efd; font-size: 1.2rem;">{{ $schedule->fleet->vehicle_id ?? 'N/A' }}</strong>
                            <p style="margin-bottom: 0; color: #6c757d; font-size: 1.1rem;">{{ $schedule->maintenance_type }}</p>
                            <p style="margin-bottom: 0; color: #6c757d; font-size: 1rem;">Office: {{ $schedule->fleet->office->name ?? 'N/A' }}</p>
                            <p style="margin-bottom: 0; color: #6c757d; font-size: 1rem;">Assigned to: {{ $schedule->fleet->user->first_name ?? 'N/A' }} {{ $schedule->fleet->user->last_name ?? '' }}</p>
                            @if($schedule->technician)
                                <p style="margin-bottom: 0; color: #6c757d; font-size: 1rem;">Technician: {{ $schedule->technician }}</p>
                            @endif
                            @if($schedule->notes)
                                <p style="margin-bottom: 0; color: #6c757d; font-size: 1rem;">{{ $schedule->notes }}</p>
                            @endif
                            @if($daysUntilDue !== null)
                                <p style="margin-bottom: 0; {{ $daysUntilDue < 0 ? 'color: #dc3545;' : ($daysUntilDue <= 5 ? 'color: #ff9900;' : 'color: #28a745;') }} font-size: 1rem; font-weight: 600;">
                                    {{ $daysUntilDue > 0 ? "Due in {$daysUntilDue} day(s)" : ($daysUntilDue < 0 ? 'Overdue by ' . abs($daysUntilDue) . ' day(s)' : 'Due today') }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <span style="padding: 0.375rem 0.75rem; font-size: 1.375rem; font-weight: 700; border-radius: 0.375rem; margin-left: 1rem; {{ $daysUntilDue < 0 ? 'background-color: #dc3545; color: #fff;' : 'background-color: #ffc107; color: #000;' }}">Due: {{ $schedule->due_date ? $schedule->due_date->format('Y-m-d') : 'N/A' }}</span>
                        
                            @if($daysUntilDue !== null)
                                <p style="margin-bottom: 0; margin-left: 1rem; {{ $daysUntilDue < 0 ? 'color: #ff0000;' : ($daysUntilDue <= 5 ? 'color: #ff9900b6;' : 'color: #05d235;') }} font-size: 1.2rem; font-weight: 600;">
                                    {{ $daysUntilDue > 0 ? "{$daysUntilDue} day(s) days to go" : ($daysUntilDue < 0 ?  abs($daysUntilDue) . ' day(s) days to go' : 'Due today') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('maintenance.complete', $schedule->id) }}" style="display: flex; align-items: center; gap: 0.75rem;">
                        @csrf
                        <input type="number" name="amount" step="0.01" placeholder="Amount" style="width: 120px; padding: 0.5rem 0.75rem; font-size: 1rem; border: 1px solid #ced4da; border-radius: 0.375rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: border-color 0.2s, box-shadow 0.2s;" required>
                        <button type="submit" style="padding: 0.5rem 1rem; font-size: 1rem; font-weight: 600; border-radius: 0.375rem; background: linear-gradient(135deg, #198754 0%, #28a745 100%); color: #fff; border: none; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.2s;">Mark Completed</button>
                    </form>
                </li>
                @empty
                <li style="padding: 0.75rem 1rem; display: flex; justify-content: center;">
                    <div style="text-align: center; color: #6c757d;">No upcoming maintenance scheduled.</div>
                </li>
                @endforelse
            </ul>

            <style>
            @keyframes pulse {
                0% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.7;
                }
                100% {
                    opacity: 1;
                }
            }
            </style>
        </div>
    </div>

    <div class="fleet-modal-backdrop" id="fleetModalBackdrop"></div>
    <div class="fleet-modal" id="fleetModal">
        <div class="fleet-modal-content">
            <div class="fleet-modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
                <h4 class="fleet-modal-title" id="fleetModalTitle" style="color: white;">Add Vehicle</h4>
                <button type="button" class="fleet-modal-close" data-dismiss="modal" style="color: white;">×</button>
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
                                    <input disabled class="fleet-modal-input" id="vehicleId" name="vehicle_id" type="text" placeholder="Auto Generated">
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="vehicleType">Type</label>
                                    <select class="fleet-modal-select" id="vehicleType" name="vehicle_type" required>
                                        <option value="">-- Select Type --</option>
                                        <option>Sedan</option>
                                        <option>Hatchback</option>
                                        <option>Coupe</option>
                                        <option>Convertible</option>
                                        <option>SUV</option>
                                        <option>Crossover</option>
                                        <option>Truck</option>
                                        <option>Pickup Truck</option>
                                        <option>Van</option>
                                        <option>Minivan</option>
                                        <option>Bus</option>
                                        <option>Motorcycle</option>
                                        <option>Electric Vehicle</option>
                                        <option>Hybrid</option>
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="vehicleModel">Model</label>
                                    <input class="fleet-modal-input" id="vehicleModel" name="vehicle_model" type="text" placeholder="Toyota Camry" required>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="datePurchased">Date Purchased (Optional)</label>
                                    <input class="fleet-modal-input" id="datePurchased" name="date_purchased" type="date">
                                </div>

                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="currentValue">Current Value (Optional)</label>
                                    <input class="fleet-modal-input" id="currentValue" name="current_value" type="number" step="0.01" placeholder="12500.00">
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="lastMaintenance">Last Maintenance</label>
                                    <input class="fleet-modal-input" id="lastMaintenance" name="last_maintenance" type="date" required>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="assignedTo">Assigned To</label>
                                    <select class="fleet-modal-select fleet-user-select" id="assignedTo" name="assigned_to" required>
                                        <option value="">-- Select User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="officeId">Office</label>
                                    <select class="fleet-modal-select fleet-office-select" id="officeId" name="office_id" required>
                                        <option value="">-- Select Office --</option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="color">Color</label>
                                    <input class="fleet-modal-input" id="color" name="color" type="text" placeholder="Blue" required>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="insuranceExpireDate">Insurance Expire Date</label>
                                    <input class="fleet-modal-input" id="insuranceExpireDate" name="insurance_expire_date" type="date" required>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="whiteBook">White Book</label>
                                    <select class="fleet-modal-select" id="whiteBook" name="white_book" required>
                                        <option value="">-- Select --</option>
                                        <option value="available">Available</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="vehicleStatus">Status</label>
                                    <select class="fleet-modal-select" id="vehicleStatus" name="vehicle_status" required>
                                        <option value="">-- Select Status --</option>
                                        <option>Active</option>
                                        <option>Maintenance</option>
                                        <option>Out of Service</option>
                                    </select>
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
                                    <select class="fleet-modal-select" id="maintenanceVehicleId" name="maintenanceVehicleId" required>
                                        <option value="">-- Select Vehicle --</option>
                                        @foreach($fleets as $fleet)
                                            <option value="{{ $fleet->vehicle_id }}">{{ $fleet->vehicle_id }} ({{ $fleet->office ? $fleet->office->name : 'No Office' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="maintenanceType">Maintenance Type</label>
                                    <select class="fleet-modal-select" id="maintenanceType" name="maintenanceType" required>
                                        <option value="">-- Select Type --</option>
                                        <option>Oil Change</option>
                                        <option>Tire Rotation</option>
                                        <option>Brake Inspection</option>
                                        <option>Engine Check</option>
                                        <option>Battery Replacement</option>
                                        <option>Air Filter Change</option>
                                        <option>Fuel Filter Replacement</option>
                                        <option>Transmission Service</option>
                                        <option>Coolant Flush</option>
                                        <option>Spark Plugs Replacement</option>
                                        <option>Wheel Alignment</option>
                                        <option>Suspension Check</option>
                                        <option>Exhaust System Inspection</option>
                                        <option>Timing Belt Replacement</option>
                                        <option>General Inspection</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="maintenanceTechnician">Technician</label>
                                    <input class="fleet-modal-input" id="maintenanceTechnician" name="maintenanceTechnician" type="text" placeholder="Maintenance Dept" required>
                                </div>
                                <div class="fleet-modal-form-group">
                                    <label class="fleet-modal-label" for="maintenanceDueDate">Due Date</label>
                                    <input class="fleet-modal-input" id="maintenanceDueDate" name="maintenanceDueDate" type="date" required>
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

        // Collapse functionality for fleet statistics
        document.querySelectorAll('.list-group-item[data-bs-toggle="collapse"]').forEach(item => {
            item.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target');
                const target = document.querySelector(targetId);
                if (target) {
                    target.classList.toggle('show');
                }
            });
        });
    </script>
@endsection
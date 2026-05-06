@extends('layouts.master')
@section('title')
    GOA Manager - Dashboard
@endsection
@section('content')
<!-- Include ApexCharts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
/* Professional Fleet Management Dashboard */
.goa-dashboard {
    background: #f8fafc;
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Header Section */
.dashboard-header {
    background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 32px 0;
    margin-bottom: 32px;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    letter-spacing: -0.025em;
}

.header-stats {
    display: flex;
    gap: 32px;
}

.header-stat {
    text-align: center;
}

.header-stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #2563eb;
    display: block;
    line-height: 1;
}

.header-stat-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Main Content Grid */
.dashboard-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 32px;
}

/* Main Dashboard Area */
.main-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
}

/* Status Cards */
.status-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.status-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.status-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
}

.status-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.fleet-icon {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
}

.staff-icon {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.maintenance-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.status-title {
    font-size: 1.85rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.status-content {
    padding: 24px;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.metric-item {
    text-align: center;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.metric-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    display: block;
    line-height: 1;
    margin-bottom: 4px;
}

.metric-label {
    font-size: 0.95rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Chart Cards */
.chart-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    grid-column: span 2;
}

.chart-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
}

.chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    text-align: center;
}

.chart-content {
    padding: 24px;
}

.chart-container {
    height: 300px;
    border-radius: 8px;
    overflow: hidden;
}

/* Sidebar */
        .dashboard-sidebar {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            position: sticky;
            top: 0;
            height: fit-content;
        }

.sidebar-section {
    border-bottom: 1px solid #f1f5f9;
}

.sidebar-section:last-child {
    border-bottom: none;
}

.sidebar-header {
    padding: 16px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.sidebar-title {
    font-size: 1.250rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.sidebar-content {
    padding: 5px;
}

.quick-stats {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.quick-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.quick-stat:last-child {
    border-bottom: none;
}

.quick-stat-label {
    font-size: 1.250rem;
    color: #64748b;
    font-weight: 500;
}

.quick-stat-value {
    font-size: 1.250rem;
    font-weight: 600;
    color: #1e293b;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-action {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background: #e2e8f0;
    color: #334155;
    transform: translateY(-1px);
}

.btn-action i {
    font-size: 1rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-content {
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .dashboard-sidebar {
        order: -1;
    }
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 24px;
        text-align: center;
    }

    .header-stats {
        justify-content: center;
    }

    .main-dashboard {
        grid-template-columns: 1fr;
    }

    .chart-card {
        grid-column: span 1;
    }

    .dashboard-content {
        padding: 0 16px;
    }

    .dashboard-title {
        font-size: 2rem;
    }

    .header-stat-value {
        font-size: 1.5rem;
    }
}

    @media (max-width: 480px) {
        .goa-dashboard {
            padding: 0;
        }

        .dashboard-header {
            padding: 24px 0;
            margin-bottom: 24px;
        }

        .header-content {
            padding: 0 16px;
        }

        .dashboard-title {
            font-size: 1.75rem;
        }

        .header-stat-value {
            font-size: 1.5rem;
        }
    }

    /* Alerts */
    .alert-notification {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 16px;
        margin-bottom: 10px;
        border-left: 4px solid #ff6b6b;
        transform: translateY(100%);
        opacity: 0;
        transition: all 0.5s ease;
        max-width: 400px;
    }

    .alert-notification.show {
        transform: translateY(0);
        opacity: 1;
    }

    .alert-warning {
        border-left-color: #ffa726;
    }

    .alert-info {
        border-left-color: #42a5f5;
    }

    .alert-danger {
        border-left-color: #ef5350;
    }

    .dashboard-header {
        padding: 24px 0;
        margin-bottom: 24px;
    }

    .header-content {
        padding: 0 16px;
    }

    .dashboard-title {
        font-size: 1.75rem;
    }

    .header-stats {
        flex-wrap: wrap;
        gap: 16px;
    }

    .header-stat {
        flex: 1;
        min-width: 80px;
    }

    .status-header {
        padding: 16px 20px;
    }

    .status-content {
        padding: 20px;
    }

    .chart-content {
        padding: 20px;
    }

    .chart-container {
        height: 250px;
    }
}

/* Alerts */
#sidebar-alerts {
    max-height: 400px;
    overflow-y: auto;
}

.alert-section {
    margin-bottom: 16px;
}

.alert-section:last-child {
    margin-bottom: 0;
}

.alert-section h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 8px 0;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}

.alert-critical {
    background: #fef2f2;
    padding: 12px;
    border-radius: 6px;
}

.alert-critical h5 {
    color: #dc2626;
}

.alert-warning {
    background: #fef3c7;
    padding: 12px;
    border-radius: 6px;
}

.alert-warning h5 {
    color: #d97706;
}

.alert-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.alert-item {
    padding: 4px 0;
    font-size: 12px;
    color: #ff0000;
    border-bottom: 1px solid #f8fafc;
    line-height: 1.3;
}

.alert-item:last-child {
    border-bottom: none;
}

.alert-item strong {
    color: #1e293b;
}

.no-alerts {
    text-align: center;
    color: #64748b;
    font-style: italic;
    padding: 20px 0;
}
</style>

<div class="goa-dashboard">

    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">GOA Fleet Management</h1>
            <div class="header-stats">
                <div class="header-stat">
                    <span class="header-stat-value">{{ $totalVehicles }}</span>
                    <span class="header-stat-label">Total Vehicles</span>
                </div>
                <div class="header-stat">
                    <span class="header-stat-value">{{ $totalPositions }}</span>
                    <span class="header-stat-label">Staff Positions</span>
                </div>
                <div class="header-stat">
                    <span class="header-stat-value">{{ $utilization }}%</span>
                    <span class="header-stat-label">Utilization</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-content">

        <!-- Main Dashboard Area -->
        <div class="main-dashboard">

        <div>

            <!-- Staffing Status Card -->
            <div class="status-card">
                <div class="status-header">
                    <div class="status-icon staff-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                        </svg>
                    </div>
                    <h3 class="status-title" style="color: #002c04;">Staffing Status</h3>
                </div>
                <div class="status-content">
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <span class="metric-value">{{ $filledPositions }}</span>
                            <span class="metric-label">Filled</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $vacantPositions }}</span>
                            <span class="metric-label">Vacant</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $inProcessPositions }}</span>
                            <span class="metric-label">In Process</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $fillRate }}%</span>
                            <span class="metric-label">Fill Rate</span>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        <!-- Fleet Status Card -->
            <div class="status-card">
                <div class="status-header">
                    <div class="status-icon fleet-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                        <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
                        </svg>
                    </div>
                    <h3 class="status-title" style="color: #0004ff;">Fleet Status</h3>
                </div>
                <div class="status-content">
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <span class="metric-value">{{ $activeVehicles }}</span>
                            <span class="metric-label">Active</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $maintenanceVehicles }}</span>
                            <span class="metric-label">Maintenance</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $outOfServiceVehicles }}</span>
                            <span class="metric-label">Out of Service</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $insuranceUpToDate }}</span>
                            <span class="metric-label">Insurance Up to Date</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $insuranceExpired }}</span>
                            <span class="metric-label">Insurance Expired</span>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <!-- Maintenance Overview Card -->
            <div class="status-card">
                <div class="status-header">
                    <div class="status-icon maintenance-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wrench-adjustable" viewBox="0 0 16 16">
                        <path d="M16 4.5a4.5 4.5 0 0 1-1.703 3.526L13 5l2.959-1.11q.04.3.041.61"/>
                        <path d="M11.5 9c.653 0 1.273-.139 1.833-.39L12 5.5 11 3l3.826-1.53A4.5 4.5 0 0 0 7.29 6.092l-6.116 5.096a2.583 2.583 0 1 0 3.638 3.638L9.908 8.71A4.5 4.5 0 0 0 11.5 9m-1.292-4.361-.596.893.809-.27a.25.25 0 0 1 .287.377l-.596.893.809-.27.158.475-1.5.5a.25.25 0 0 1-.287-.376l.596-.893-.809.27a.25.25 0 0 1-.287-.377l.596-.893-.809.27-.158-.475 1.5-.5a.25.25 0 0 1 .287.376M3 14a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
                        </svg>
                    </div>
                    <h3 class="status-title" style="color: #ff7b00;">Maintenance Overview</h3>
                </div>
                <div class="status-content">
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <span class="metric-value">{{ $scheduledMaintenance }}</span>
                            <span class="metric-label">Scheduled</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $overdueMaintenance }}</span>
                            <span class="metric-label">Overdue</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">{{ $thisMonthMaintenance }}</span>
                            <span class="metric-label">This Month</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">ZMW {{ number_format($monthlyMaintenanceCost) }}</span>
                            <span class="metric-label">Monthly Cost</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fleet Distribution Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Fleet Distribution</h3>
                </div>
                <div class="chart-content">
                    <div id="fleet-chart" class="chart-container"></div>
                </div>
            </div>

            <!-- Staffing Overview Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Staffing Overview</h3>
                </div>
                <div class="chart-content">
                    <div id="staffing-chart" class="chart-container"></div>
                </div>
            </div>
        </div>
        </div>    

        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <!-- Notifications/ Alerts -->
            <div class="sidebar-section">
                <div class="sidebar-header">
                    <h4 class="sidebar-title">Alerts</h4>
                </div>
                <div class="sidebar-content">
                    <div id="sidebar-alerts">
                        @if($insurancePastDue->count() > 0)
                            <div class="alert-section alert-critical">
                                <h3>Insurance Past Due ({{ $insurancePastDue->count() }})</h3>
                                <ul class="alert-list">
                                    @foreach($insurancePastDue as $fleet)
                                        <li class="alert-item">
                                            <strong>{{ $fleet->vehicle_id }}</strong> - Expired {{ $fleet->insurance_expire_date->diffForHumans() }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($insuranceExpiredRecent->count() > 0)
                            <div class="alert-section alert-critical">
                                <h3>Insurance Expired ({{ $insuranceExpiredRecent->count() }})</h3>
                                <ul class="alert-list">
                                    @foreach($insuranceExpiredRecent as $fleet)
                                        <li class="alert-item">
                                            <strong>{{ $fleet->vehicle_id }}</strong> - Expired {{ $fleet->insurance_expire_date->diffForHumans() }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($maintenancePastDue->count() > 0)
                            <div class="alert-section alert-critical">
                                <h3>Maintenance Past Due ({{ $maintenancePastDue->count() }})</h3>
                                <ul class="alert-list">
                                    @foreach($maintenancePastDue as $schedule)
                                        <li class="alert-item">
                                            <strong>{{ $schedule->fleet->vehicle_id }}</strong> - {{ $schedule->maintenance_type }} due {{ $schedule->due_date->diffForHumans() }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($insuranceExpiringSoon->count() > 0)
                            <div class="alert-section alert-warning">
                                <h3>Insurance Expiring Soon ({{ $insuranceExpiringSoon->count() }})</h3>
                                <ul class="alert-list">
                                    @foreach($insuranceExpiringSoon as $fleet)
                                        <li class="alert-item">
                                            <strong>{{ $fleet->vehicle_id }}</strong> - Expires {{ $fleet->insurance_expire_date->format('M d, Y') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($maintenanceSoon->count() > 0)
                            <div class="alert-section alert-warning">
                                <h3>Maintenance Due Soon ({{ $maintenanceSoon->count() }})</h3>
                                <ul class="alert-list">
                                    @foreach($maintenanceSoon as $schedule)
                                        <li class="alert-item">
                                            <strong>{{ $schedule->fleet->vehicle_id }}</strong> - {{ $schedule->maintenance_type }} due {{ $schedule->due_date->format('M d, Y') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($insuranceExpiringSoon->count() == 0 && $maintenanceSoon->count() == 0 && $insurancePastDue->count() == 0 && $maintenancePastDue->count() == 0 && $insuranceExpiredRecent->count() == 0)
                            <p class="no-alerts">No alerts at this time.</p>
                        @endif
                    </div>
                </div>
            </div>
        
            <!-- Quick Stats -->
            <div class="sidebar-section">
                <div class="sidebar-header">
                    <h4 class="sidebar-title">Quick Stats</h4>
                </div>
                <div class="sidebar-content">
                    <div class="quick-stats" style="padding: 0px 10px;">
                        <div class="quick-stat">
                            <span class="quick-stat-label">Avg. Vehicle Age</span>
                            <span class="quick-stat-value">{{ $avgVehicleAge }} years</span>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <!-- Quick Actions -->
            <div class="sidebar-section">
                <div class="sidebar-header">
                    <h4 class="sidebar-title">Quick Actions</h4>
                </div>
                <div class="sidebar-content">
                    <div class="quick-actions" style="display: flex; flex-wrap: wrap; gap: 16px; justify-content: center; align-items: center; margin: 24px 0 32px 0;">
                        <a href="{{ route('goa.fleet-management') }}" style="width:100%; display: inline-flex; align-items: center; gap: 8px; color: #2563eb; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.2s ease; padding: 8px 16px; border-radius: 2px; background: rgba(37, 99, 235, 0.1);" onmouseover="this.style.color='#1d4ed8'; this.style.background='rgba(37, 99, 235, 0.2)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.color='#2563eb'; this.style.background='rgba(37, 99, 235, 0.1)'; this.style.transform='translateY(0)';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                            <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                            <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                            </svg>
                            Add Vehicle
                        </a>

                        <a href="{{ route('goa.fleet-management') }}" style="width:100%; display: inline-flex; align-items: center; gap: 8px; color: #d97706; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.2s ease; padding: 8px 16px; border-radius: 2px; background: rgba(245, 158, 11, 0.1);" onmouseover="this.style.color='#b45309'; this.style.background='rgba(245, 158, 11, 0.2)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.color='#d97706'; this.style.background='rgba(245, 158, 11, 0.1)'; this.style.transform='translateY(0)';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                            <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                            <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                            </svg>
                            Schedule Maintenance
                        </a>

                        <a href="{{ route('goa.vacancies-and-staffing') }}" style="width:100%; display: inline-flex; align-items: center; gap: 8px; color: #059669; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.2s ease; padding: 8px 16px; border-radius: 2px; background: rgba(16, 185, 129, 0.1);" onmouseover="this.style.color='#047857'; this.style.background='rgba(16, 185, 129, 0.2)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.color='#059669'; this.style.background='rgba(16, 185, 129, 0.1)'; this.style.transform='translateY(0)';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                            <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                            <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                            </svg>
                            Post Job Opening
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Alerts Container -->
    <div id="alerts-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050; max-width: 400px;"></div>

</div>

<script>
// Fleet Distribution Pie Chart
var fleetOptions = {
    series: [{{ $activeVehicles }}, {{ $maintenanceVehicles }}, {{ $outOfServiceVehicles }}],
    chart: {
        type: 'pie',
        height: 300,
        toolbar: { show: false },
        fontFamily: 'Segoe UI, sans-serif',
        background: 'transparent'
    },
    labels: ['Active', 'Maintenance', 'Out of Service'],
    colors: ['#2563eb', '#f59e0b', '#ef4444'],
    legend: {
        position: 'bottom',
        fontSize: '12px',
        fontWeight: 500,
        labels: {
            colors: '#64748b'
        },
        markers: {
            width: 8,
            height: 8,
            radius: 2
        }
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: '12px',
            fontWeight: 600,
            colors: ['#ffffff', '#ffffff', '#ffffff']
        },
        dropShadow: {
            enabled: false
        }
    },
    tooltip: {
        y: {
            formatter: function(value) {
                return value + ' vehicles';
            }
        },
        style: {
            fontSize: '12px'
        }
    },
    stroke: {
        width: 0
    },
    plotOptions: {
        pie: {
            donut: {
                size: '0%'
            }
        }
    }
};
var fleetChart = new ApexCharts(document.querySelector("#fleet-chart"), fleetOptions);
fleetChart.render();

// Staffing Overview Pie Chart
var staffingOptions = {
    series: [{{ $filledPositions }}, {{ $vacantPositions }}],
    chart: {
        type: 'pie',
        height: 300,
        toolbar: { show: false },
        fontFamily: 'Segoe UI, sans-serif',
        background: 'transparent'
    },
    labels: ['Filled', 'Vacant'],
    colors: ['#059669', '#ef4444'],
    legend: {
        position: 'bottom',
        fontSize: '12px',
        fontWeight: 500,
        labels: {
            colors: '#64748b'
        },
        markers: {
            width: 8,
            height: 8,
            radius: 2
        }
    },
    dataLabels: {
        enabled: true,
        style: {
            fontSize: '12px',
            fontWeight: 600,
            colors: ['#ffffff', '#ffffff']
        },
        dropShadow: {
            enabled: false
        }
    },
    tooltip: {
        y: {
            formatter: function(value) {
                return value + ' positions';
            }
        },
        style: {
            fontSize: '12px'
        }
    },
    stroke: {
        width: 0
    },
    plotOptions: {
        pie: {
            donut: {
                size: '0%'
            }
        }
    }
};
var staffingChart = new ApexCharts(document.querySelector("#staffing-chart"), staffingOptions);
staffingChart.render();

// Alerts
function showAlert(message, type = 'warning') {
    // Fixed bottom container
    const container = document.getElementById('alerts-container');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-notification alert-${type}`;
    alertDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <span>${message}</span>
            <button type="button" class="close" onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 20px; color: #999;">&times;</button>
        </div>
    `;
    container.appendChild(alertDiv);
    setTimeout(() => alertDiv.classList.add('show'), 100);
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 500);
    }, 10000); // Auto remove after 10s

    // Sidebar container
    const sidebarContainer = document.getElementById('sidebar-alerts');
    const sidebarAlert = document.createElement('div');
    sidebarAlert.className = `alert alert-${type} alert-dismissible fade show mb-2`;
    sidebarAlert.innerHTML = `
        <span>${message}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    sidebarContainer.appendChild(sidebarAlert);
}

// Show alerts
@if($insuranceExpiringSoon->count() > 0)
    showAlert('{{ $insuranceExpiringSoon->count() }} vehicle(s) have insurance expiring within 1 week.', 'warning');
@endif

@if($maintenanceSoon->count() > 0)
    showAlert('{{ $maintenanceSoon->count() }} maintenance schedule(s) due within 5 days.', 'info');
@endif

@if($insurancePastDue->count() > 0)
    showAlert('{{ $insurancePastDue->count() }} vehicle(s) have insurance expired more than 1 week ago.', 'danger');
@endif

@if($maintenancePastDue->count() > 0)
    showAlert('{{ $maintenancePastDue->count() }} maintenance schedule(s) are past due.', 'danger');
@endif
</script>
@endsection
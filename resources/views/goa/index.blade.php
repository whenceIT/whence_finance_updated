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
    font-size: 1.25rem;
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
    font-size: 0.75rem;
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
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.sidebar-content {
    padding: 20px;
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
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.quick-stat-value {
    font-size: 0.875rem;
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
</style>

<div class="goa-dashboard">

    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">GOA Fleet Management</h1>
            <div class="header-stats">
                <div class="header-stat">
                    <span class="header-stat-value">150</span>
                    <span class="header-stat-label">Total Vehicles</span>
                </div>
                <div class="header-stat">
                    <span class="header-stat-value">500</span>
                    <span class="header-stat-label">Staff Positions</span>
                </div>
                <div class="header-stat">
                    <span class="header-stat-value">93%</span>
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
        <!-- Fleet Status Card -->
            <div class="status-card">
                <div class="status-header">
                    <div class="status-icon fleet-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3 class="status-title">Fleet Status</h3>
                </div>
                <div class="status-content">
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <span class="metric-value">140</span>
                            <span class="metric-label">Active</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">8</span>
                            <span class="metric-label">Maintenance</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">2</span>
                            <span class="metric-label">Out of Service</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">95%</span>
                            <span class="metric-label">Availability</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staffing Status Card -->
            <div class="status-card">
                <div class="status-header">
                    <div class="status-icon staff-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="status-title">Staffing Status</h3>
                </div>
                <div class="status-content">
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <span class="metric-value">450</span>
                            <span class="metric-label">Filled</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">50</span>
                            <span class="metric-label">Vacant</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">12</span>
                            <span class="metric-label">In Process</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">90%</span>
                            <span class="metric-label">Fill Rate</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Overview Card -->
            <div class="status-card">
                <div class="status-header">
                    <div class="status-icon maintenance-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="status-title">Maintenance Overview</h3>
                </div>
                <div class="status-content">
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <span class="metric-value">5</span>
                            <span class="metric-label">Scheduled</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">3</span>
                            <span class="metric-label">Overdue</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">12</span>
                            <span class="metric-label">This Month</span>
                        </div>
                        <div class="metric-item">
                            <span class="metric-value">$45K</span>
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

            <!-- Quick Stats -->
            <div class="sidebar-section">
                <div class="sidebar-header">
                    <h4 class="sidebar-title">Quick Stats</h4>
                </div>
                <div class="sidebar-content">
                    <div class="quick-stats">
                        <div class="quick-stat">
                            <span class="quick-stat-label">Avg. Vehicle Age</span>
                            <span class="quick-stat-value">4.2 years</span>
                        </div>
                        <div class="quick-stat">
                            <span class="quick-stat-label">Fuel Efficiency</span>
                            <span class="quick-stat-value">24.5 mpg</span>
                        </div>
                        <div class="quick-stat">
                            <span class="quick-stat-label">Monthly Mileage</span>
                            <span class="quick-stat-value">45,230 mi</span>
                        </div>
                        <div class="quick-stat">
                            <span class="quick-stat-label">Safety Rating</span>
                            <span class="quick-stat-value">4.8/5.0</span>
                        </div>
                        <div class="quick-stat">
                            <span class="quick-stat-label">Staff Turnover</span>
                            <span class="quick-stat-value">8.5%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="sidebar-section">
                <div class="sidebar-header">
                    <h4 class="sidebar-title">Quick Actions</h4>
                </div>
                <div class="sidebar-content">
                    <div class="quick-actions">
                        <a href="#" class="btn-action">
                            <i class="fas fa-plus"></i>
                            Add Vehicle
                        </a>
                        <a href="#" class="btn-action">
                            <i class="fas fa-calendar-plus"></i>
                            Schedule Maintenance
                        </a>
                        <a href="#" class="btn-action">
                            <i class="fas fa-user-plus"></i>
                            Post Job Opening
                        </a>
                        <a href="#" class="btn-action">
                            <i class="fas fa-chart-bar"></i>
                            View Reports
                        </a>
                        <a href="#" class="btn-action">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
// Fleet Distribution Pie Chart
var fleetOptions = {
    series: [140, 8, 2],
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
    series: [450, 50],
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
</script>
@endsection
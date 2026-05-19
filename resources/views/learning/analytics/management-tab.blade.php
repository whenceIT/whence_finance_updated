<!-- Executive Summary Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-title">High Risk Users</div>
        <div class="kpi-value">{{ $managementData['risk_indicators']->count() }}</div>
        <div class="kpi-change neutral">
            <i class="fa fa-exclamation-triangle"></i>
            Low engagement + poor performance
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-title">Engaged Offices</div>
        <div class="kpi-value">{{ $managementData['office_metrics']->count() }}</div>
        <div class="kpi-change positive">
            <i class="fa fa-building"></i>
            Offices with active learning
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-title">Performance Correlation</div>
        <div class="kpi-value">{{ number_format($managementData['performance_correlation']->avg_views_default ?? 0) }}</div>
        <div class="kpi-change negative">
            <i class="fa fa-chart-line"></i>
            Avg views for default users
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-title">Role Diversity</div>
        <div class="kpi-value">{{ $managementData['role_metrics']->count() }}</div>
        <div class="kpi-change neutral">
            <i class="fa fa-users"></i>
            Active roles in learning
        </div>
    </div>
</div>

<!-- Charts Container -->
<div class="charts-container">
    <div class="chart-card">
        <h3 class="chart-title">
            <i class="fa fa-pie-chart"></i>
            Engagement by Role
        </h3>
        <div id="role-engagement-chart" class="chart-container"></div>
    </div>

    <div class="chart-card">
        <h3 class="chart-title">
            <i class="fa fa-bar-chart"></i>
            Office Performance
        </h3>
        <div id="office-performance-chart" class="chart-container"></div>
    </div>
</div>

<!-- Risk Indicators Table -->
<div class="breakdown-container">
    <div class="breakdown-card">
        <h3 class="chart-title">
            <i class="fa fa-warning"></i>
            Risk Indicators: Low Engagement + Poor Performance
        </h3>
        <div class="engagement-table-container">
            <table class="engagement-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Total Views</th>
                        <th>Default Loans</th>
                        <th>Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($managementData['risk_indicators'] as $user)
                    <tr>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->total_views }}</td>
                        <td>{{ $user->default_loans }}</td>
                        <td>
                            <span class="completion-status {{ $user->total_views < 3 && $user->default_loans > 0 ? 'completed' : 'in-progress' }}">
                                {{ $user->total_views < 3 && $user->default_loans > 0 ? 'High Risk' : 'Medium Risk' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="no-data">No risk indicators found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Performance vs Learning Correlation -->
<div class="breakdown-container">
    <div class="breakdown-card">
        <h3 class="chart-title">
            <i class="fa fa-link"></i>
            Performance vs Learning Correlation
        </h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Users with Default Loans</div>
                <div class="summary-value">{{ $managementData['performance_correlation']->default_users ?? 0 }}</div>
                <div class="summary-change negative">
                    <i class="fa fa-times-circle"></i>
                    Poor performers
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-label">Avg Learning Views (Default)</div>
                <div class="summary-value">{{ number_format($managementData['performance_correlation']->avg_views_default ?? 0) }}</div>
                <div class="summary-change neutral">
                    <i class="fa fa-clock"></i>
                    Engagement duration
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-label">Users with Good Performance</div>
                <div class="summary-value">{{ $managementData['performance_correlation']->good_users ?? 0 }}</div>
                <div class="summary-change positive">
                    <i class="fa fa-check-circle"></i>
                    Strong performers
                </div>
            </div>

            <div class="summary-item">
                <div class="summary-label">Avg Learning Views (Good)</div>
                <div class="summary-value">{{ number_format($managementData['performance_correlation']->avg_views_good ?? 0) }}</div>
                <div class="summary-change positive">
                    <i class="fa fa-trophy"></i>
                    Higher engagement
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize management charts when the tab becomes active
    function initManagementCharts() {
        // Role Engagement Pie Chart
        var roleData = @json($managementData['role_metrics']);
        var roleOptions = {
            series: roleData.map(item => item.user_count),
            chart: {
                type: 'pie',
                height: 350
            },
            labels: roleData.map(item => 'Role ' + item.role_id),
            colors: ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'],
            legend: {
                position: 'bottom'
            }
        };
        var roleChart = new ApexCharts(document.querySelector("#role-engagement-chart"), roleOptions);
        roleChart.render();

        // Office Performance Bar Chart
        var officeData = @json($managementData['office_metrics']);
        var officeOptions = {
            series: [{
                name: 'Active Users',
                data: officeData.map(item => item.user_count)
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            xaxis: {
                categories: officeData.map(item => 'Office ' + item.office_id)
            },
            colors: ['#3498db'],
            fill: {
                opacity: 0.8
            }
        };
        var officeChart = new ApexCharts(document.querySelector("#office-performance-chart"), officeOptions);
        officeChart.render();
    }

    // Check if management tab is active on load
    if (document.getElementById('management-tab').classList.contains('active')) {
        initManagementCharts();
    }

    // Listen for tab changes
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            var tabId = this.getAttribute('aria-controls');
            if (tabId === 'management-tab') {
                setTimeout(initManagementCharts, 100); // Small delay to ensure DOM is ready
            }
        });
    });
});
</script>
    </div>
</div>
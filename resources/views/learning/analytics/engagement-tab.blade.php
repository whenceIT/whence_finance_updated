<!-- Engagement Tracking -->
<div class="engagement-container">
    <h2 class="analytics-title" style="margin-bottom: 20px;">
        <i class="fa fa-chart-line"></i>
        Engagement Tracking
    </h2>

    <!-- Individual Engagement Cards -->
    <div class="kpi-grid">
        <div class="kpi-card clickable-views" onclick="showOpenedMaterials()">
            <div class="kpi-title">Material Opened</div>
            <div class="kpi-value">{{ number_format($engagementStats['opened_count'] ?? 0) }}</div>
            <div class="kpi-change positive">
                <i class="fa fa-eye"></i>
                {{ \App\Helpers\GeneralHelper::calculate_view_percentage($engagementStats['opened_count'] ?? 0) }}% of total users
            </div>
        </div>

        <div class="kpi-card clickable-views" onclick="showAverageDurationMaterials()">
            <div class="kpi-title">Average Engagement Duration</div>
            <div class="kpi-value">{{ $engagementStats['avg_duration'] ?? '0:00' }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-clock"></i>
                {{ $engagementStats['avg_duration_raw'] ?? 0 }} seconds average
            </div>
        </div>

        <div class="kpi-card clickable-views" onclick="showCompletedMaterials()">
            <div class="kpi-title">Completion Rate</div>
            <div class="kpi-value">{{ number_format($engagementStats['completion_rate'] ?? 0) }}%</div>
            <div class="kpi-change positive">
                <i class="fa fa-check-circle"></i>
                Materials completed
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Active Learners</div>
            <div class="kpi-value">{{ number_format($engagementStats['active_learners'] ?? 0) }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-users"></i>
                Engaged this period
            </div>
        </div>
    </div>

    <!-- Individual Engagement Table -->
    <div class="breakdown-container">
        <div class="breakdown-card">
            <h3 class="chart-title">
                <i class="fa fa-user-clock"></i>
                Individual Engagement Details
            </h3>
            <div class="engagement-table-container">
                <table class="engagement-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Material</th>
                            <th>Opened</th>
                            <th>Duration</th>
                            <th>Completion Status</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($individualEngagement ?? [] as $engagement)
                        <tr>
                            <td class="user-cell">
                                <div class="user-info">
                                    <div class="user-avatar">{{ substr($engagement['user_name'] ?? 'U', 0, 1) }}</div>
                                    <div>
                                        <div class="user-name">{{ $engagement['user_name'] ?? 'Unknown User' }}</div>
                                        <div class="user-email">{{ $engagement['user_email'] ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $engagement['material_title'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-indicator {{ $engagement['opened'] ? 'opened' : 'not-opened' }}">
                                    <i class="fa {{ $engagement['opened'] ? 'fa-check' : 'fa-times' }}"></i>
                                    {{ $engagement['opened'] ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>{{ $engagement['duration'] ?? '0:00' }}</td>
                            <td>
                                <span class="completion-status {{ strtolower($engagement['completion_status'] ?? 'not-started') }}">
                                    {{ $engagement['completion_status'] ?? 'Partially viewed and left' }}
                                </span>
                            </td>
                            <td>{{ $engagement['last_activity'] ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="no-data">No engagement data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
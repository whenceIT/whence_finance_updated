@extends('layouts.learning')

@section('title', 'Learning Analytics - Whence Learn')

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
/* Analytics Dashboard Styles */
.analytics-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Clickable View Count Styles */
.clickable-views {
    cursor: pointer;
    transition: all 0.2s ease;
}

.clickable-views:hover {
    color: var(--secondary-color) !important;
    text-decoration: underline;
}

/* Modal Styles */
.viewers-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 100000;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.viewers-modal-overlay.active {
    display: flex;
}

.viewers-modal {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 600px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.viewers-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
}

.viewers-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.viewers-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.viewers-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.viewers-modal-body {
    padding: 0;
    max-height: calc(80vh - 140px);
    overflow-y: auto;
}

.viewers-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.viewers-list-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    transition: background 0.2s;
}

.viewers-list-item:hover {
    background: var(--light-bg);
}

.viewers-list-item:last-child {
    border-bottom: none;
}

.viewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.viewer-info {
    flex: 1;
    min-width: 0;
}

.viewer-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 14px;
}

.viewer-email {
    font-size: 12px;
    color: var(--text-secondary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.viewers-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--light-bg);
}

.viewers-count {
    font-weight: 600;
    color: var(--text-primary);
}

.viewers-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    color: var(--text-secondary);
}

.viewers-loading i {
    margin-right: 8px;
    animation: spin 1s linear infinite;
}

.viewers-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-secondary);
}

.viewers-empty i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.analytics-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.date-filter {
    display: flex;
    align-items: center;
    gap: 15px;
}

.date-filter select {
    padding: 8px 16px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: white;
    font-weight: 500;
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.date-filter select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

    .analytics-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 24px 0 30px;
    }

    .tab-button {
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 12px 18px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .tab-button.active,
    .tab-button:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 8px 20px rgba(52, 152, 219, 0.12);
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
    }

/* KPI Cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: var(--primary-color);
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.kpi-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.kpi-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
    line-height: 1;
}

.kpi-change {
    font-size: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.kpi-change.positive {
    color: #27ae60;
}

.kpi-change.negative {
    color: #e74c3c;
}

.kpi-change.neutral {
    color: var(--text-secondary);
}

/* Charts Container */
.charts-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.chart-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow);
    height: fit-content;
}

.chart-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-title i {
    color: var(--primary-color);
}

.chart-container {
    height: 350px;
}

/* Top Performers */
.performers-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.performer-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow);
}

.performer-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.performer-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.performer-item:last-child {
    border-bottom: none;
}

.performer-name {
    font-weight: 500;
    color: var(--text-primary);
    flex: 1;
}

.performer-metric {
    font-weight: 600;
    color: var(--primary-color);
    background: rgba(52, 152, 219, 0.1);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 14px;
}

/* Content Breakdown */
.breakdown-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.breakdown-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow);
}

.breakdown-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
}

.breakdown-item:last-child {
    border-bottom: none;
}

.breakdown-name {
    font-weight: 500;
    color: var(--text-primary);
}

.breakdown-stats {
    display: flex;
    gap: 15px;
    font-size: 14px;
}

.breakdown-count {
    color: var(--text-secondary);
}

.breakdown-views {
    font-weight: 600;
    color: var(--primary-color);
}

/* Loading States */
.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: var(--text-secondary);
}

.loading i {
    margin-right: 8px;
    animation: spin 1s linear infinite;
}

/* Engagement Tracking Styles */
.engagement-container {
    margin-bottom: 30px;
}

.engagement-table-container {
    overflow-x: auto;
    max-height: 500px;
    overflow-y: auto;
}

.engagement-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.engagement-table thead {
    background: var(--light-bg);
    position: sticky;
    top: 0;
    z-index: 1;
}

.engagement-table th,
.engagement-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.engagement-table th {
    font-weight: 600;
    color: var(--text-primary);
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.engagement-table tbody tr:hover {
    background: var(--light-bg);
}

.user-cell {
    min-width: 200px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
    flex-shrink: 0;
}

.user-name {
    font-weight: 600;
    color: var(--text-primary);
}

.user-email {
    font-size: 12px;
    color: var(--text-secondary);
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}

.status-indicator.opened {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.status-indicator.not-opened {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.completion-status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-transform: capitalize;
}

.completion-status.completed {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.completion-status.in-progress {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.completion-status.not-started {
    background: rgba(149, 165, 166, 0.1);
    color: #95a5a6;
}

.no-data {
    text-align: center;
    color: var(--text-secondary);
    font-style: italic;
    padding: 40px;
}

/* Executive Summary Styles */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.summary-item {
    background: var(--light-bg);
    border-radius: 8px;
    padding: 16px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.summary-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.summary-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.summary-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.summary-change {
    font-size: 11px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.summary-change.positive {
    color: #27ae60;
}

.summary-change.negative {
    color: #e74c3c;
}

.summary-change.neutral {
    color: var(--text-secondary);
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .analytics-container {
        padding: 15px;
    }

    .analytics-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }

    .charts-container {
        grid-template-columns: 1fr;
    }

    .performers-container {
        grid-template-columns: 1fr;
    }

    .kpi-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    .kpi-value {
        font-size: 24px;
    }

    .engagement-table-container {
        max-height: 300px;
    }

    .engagement-table th,
    .engagement-table td {
        padding: 8px 12px;
        font-size: 12px;
    }

    .user-cell {
        min-width: 150px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .summary-value {
        font-size: 20px;
    }
}
</style>

@section('content')
<div class="analytics-container">
    <!-- Header -->
    <div class="analytics-header">
        <h1 class="analytics-title">
            <i class="fa fa-chart-bar"></i>
            Learning Analytics
        </h1>
        <div class="date-filter">
            <label for="period-select" style="font-weight: 500; color: var(--text-primary);">Period:</label>
            <select id="period-select" onchange="changePeriod(this.value)">
                <option value="7" {{ $period == '7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $period == '30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $period == '90' ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $period == '365' ? 'selected' : '' }}>Last year</option>
            </select>
        </div>
    </div>

        <div class="analytics-tabs" role="tablist">
            <button type="button" class="tab-button active" onclick="setActiveTab('analytics-tab')" role="tab" aria-selected="true" aria-controls="analytics-tab">Analytics</button>
            <button type="button" class="tab-button" onclick="setActiveTab('engagement-tab')" role="tab" aria-selected="false" aria-controls="engagement-tab">Engagement Tracking</button>
            <button type="button" class="tab-button" onclick="setActiveTab('feedback-tab')" role="tab" aria-selected="false" aria-controls="feedback-tab">Feedback Loop to Users</button>
        </div>

    <div id="analytics-tab" class="tab-panel active">

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">Total Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_views']) }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-eye"></i>
                {{\App\Helpers\GeneralHelper::calculate_view_percentage($overallStats['total_views'])}}% of total users
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Course Topics Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_course_views']) }}</div>
            <div class="kpi-change positive">
                <i class="fa fa-graduation-cap"></i>
                {{\App\Helpers\GeneralHelper::calculate_view_percentage($overallStats['total_course_views'])}}% of total users
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Course Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_course_views']) }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-file-text"></i>
                {{\App\Helpers\GeneralHelper::calculate_view_percentage($overallStats['total_course_views'])}}% of total users
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Upload Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_upload_views']) }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-cloud-upload"></i>
                {{\App\Helpers\GeneralHelper::calculate_view_percentage($overallStats['total_upload_views'])}}% of total users
            </div>
        </div>
    </div>

    

    <!-- Top Performers -->
    <div class="performers-container">
        <!-- Top Courses -->
        <div class="performer-card">
            <h3 class="chart-title">
                <i class="fa fa-trophy"></i>
                Top Performing Courses
            </h3>
            <ul class="performer-list">
                @foreach($topCourses as $index => $course)
                <li class="performer-item">
                    <span class="performer-name">
                        <span style="color: var(--primary-color); margin-right: 8px;">#{{ $index + 1 }}</span>
                        {{ $course['title'] }}
                    </span>
                    <span class="performer-metric clickable-views" onclick="showViewers('course', '{{ $course['id'] ?? $index }}', '{{ addslashes($course['title']) }}')">{{ number_format($course['views']) }} views ({{ \App\Helpers\GeneralHelper::calculate_view_percentage($course['views']) }}%)</span>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Top Uploads -->
        <div class="performer-card">
            <h3 class="chart-title">
                <i class="fa fa-star"></i>
                Top Performing Uploads
            </h3>
            <ul class="performer-list">
                @foreach($topUploads as $index => $upload)
                    @if(!empty($upload['topic']))
                    <li class="performer-item">
                        <span class="performer-name">
                            <span style="color: var(--primary-color); margin-right: 8px;">#{{ $index + 1 }}</span>
                            {{ $upload['topic'] }}
                        </span>
                        <span class="performer-metric clickable-views" onclick="showViewers('upload', '{{ $upload['id'] ?? $index }}', '{{ addslashes($upload['topic']) }}')">{{ number_format($upload['views']) }} views ({{ \App\Helpers\GeneralHelper::calculate_view_percentage($upload['views']) }}%)</span>
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Content Breakdown -->
    <div class="breakdown-container">
        <!-- Course Categories -->
        <div class="breakdown-card">
            <h3 class="chart-title">
                <i class="fa fa-tags"></i>
                Course Categories
            </h3>
            <ul class="breakdown-list">
                @foreach($chartData['course_categories'] as $index => $category)
                <li class="breakdown-item">
                    <span class="breakdown-name">{{ $category['category'] }}</span>
                    <div class="breakdown-stats">
                        <span class="breakdown-count">{{ $category['count'] }} courses</span>
                        <span class="breakdown-views clickable-views" onclick="showViewers('category', '{{ $index }}', '{{ addslashes($category['category']) }}')">{{ number_format($category['views']) }} views ({{ \App\Helpers\GeneralHelper::calculate_view_percentage($category['views']) }}%)</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Content Types Breakdown -->
        <div class="breakdown-card">
            <h3 class="chart-title">
                <i class="fa fa-file"></i>
                Upload Types
            </h3>
            <ul class="breakdown-list">
                @foreach($chartData['content_types'] as $index => $type)
                <li class="breakdown-item">
                    <span class="breakdown-name">{{ $type['type'] }}</span>
                    <div class="breakdown-stats">
                        <span class="breakdown-count">{{ $type['count'] }} files</span>
                        <span class="breakdown-views clickable-views" onclick="showViewers('content_type', '{{ $index }}', '{{ addslashes($type['type']) }}')">{{ number_format($type['views']) }} views ({{ \App\Helpers\GeneralHelper::calculate_view_percentage($type['views']) }}%)</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Office Analytics -->
    <!-- <div class="charts-container">
        <div class="chart-card">
            <h3 class="chart-title">
                <i class="fa fa-building"></i>
                Office Performance
            </h3>
            <div id="office-performance-chart" class="chart-container"></div>
        </div>
    </div> -->
    <br>
    <!-- Detailed Analytics Tables -->
    <div class="breakdown-container">
        <!-- Office Analytics Table -->
        <div class="breakdown-card">
            <h3 class="chart-title">
                <i class="fa fa-building"></i>
                Course Views by Office
            </h3>
            <ul class="breakdown-list">
                @foreach($chartData['office_analytics']['course_views'] as $index => $office)
                <li class="breakdown-item">
                    <span class="breakdown-name">{{ $office['office'] }}</span>
                    <div class="breakdown-stats">
                        <span class="breakdown-count">{{ $office['courses_count'] }} courses</span>
                        <span class="breakdown-views clickable-views" onclick="showViewers('office', '{{ $index }}', '{{ addslashes($office['office']) }}')">{{ number_format($office['views']) }} views ({{ \App\Helpers\GeneralHelper::calculate_view_percentage($office['views']) }}%)</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

</div>

    <div id="engagement-tab" class="tab-panel">

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

        <!-- Aggregated Summaries for Management -->
        <div class="breakdown-container">
            <div class="breakdown-card">
                <h3 class="chart-title">
                    <i class="fa fa-chart-bar"></i>
                    Executive Summary
                </h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Total Engaged Users</div>
                        <div class="summary-value">{{ number_format($executiveSummary['total_engaged'] ?? 0) }}</div>
                        <div class="summary-change positive">
                            <i class="fa fa-arrow-up"></i>
                            {{ $executiveSummary['engaged_growth'] ?? 0 }}% from last period
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Average Completion Time</div>
                        <div class="summary-value">{{ $executiveSummary['avg_completion_time'] ?? '0:00' }}</div>
                        <div class="summary-change neutral">
                            <i class="fa fa-clock"></i>
                            Across all materials
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Top Performing Department</div>
                        <div class="summary-value">{{ $executiveSummary['top_department'] ?? 'N/A' }}</div>
                        <div class="summary-change positive">
                            <i class="fa fa-trophy"></i>
                            {{ $executiveSummary['dept_completion_rate'] ?? 0 }}% completion
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Learning Retention Rate</div>
                        <div class="summary-value">{{ number_format($executiveSummary['retention_rate'] ?? 0) }}%</div>
                        <div class="summary-change positive">
                            <i class="fa fa-brain"></i>
                            Based on re-engagement
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div id="feedback-tab" class="tab-panel">
        <div class="breakdown-container">
            <div class="breakdown-card">
                <h3 class="chart-title">
                    <i class="fa fa-comments"></i>
                    Feedback Loop to Users
                </h3>
                <ul class="breakdown-list">
                    <li class="breakdown-item">
                        <span class="breakdown-name">Weekly User Summary Generator</span>
                        <div class="breakdown-stats">
                            <span class="breakdown-count">Automated summaries</span>
                            <span class="breakdown-views">Build weekly digest reports for users</span>
                        </div>
                    </li>
                    <li class="breakdown-item">
                        <span class="breakdown-name">Consumed vs Ignored</span>
                        <div class="breakdown-stats">
                            <span class="breakdown-count">Engagement logic</span>
                            <span class="breakdown-views">Track content consumption and ignore patterns</span>
                        </div>
                    </li>
                    <li class="breakdown-item">
                        <span class="breakdown-name">Notification Delivery</span>
                        <div class="breakdown-stats">
                            <span class="breakdown-count">In-system alerts</span>
                            <span class="breakdown-views">Send dashboard notifications to users</span>
                        </div>
                    </li>
                    <li class="breakdown-item">
                        <span class="breakdown-name">User-facing Dashboard Widget</span>
                        <div class="breakdown-stats">
                            <span class="breakdown-count">Activity overview</span>
                            <span class="breakdown-views">Show user activity and summary metrics</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Viewers Modal -->
<div id="viewers-modal" class="viewers-modal-overlay">
    <div class="viewers-modal">
        <div class="viewers-modal-header">
            <h3 id="viewers-modal-title">
                <i class="fa fa-users"></i> Viewers
            </h3>
            <button class="viewers-modal-close" onclick="closeViewersModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="viewers-modal-body" id="viewers-modal-body">
            <!-- Content loaded dynamically -->
        </div>
        <div class="viewers-modal-footer">
            <span class="viewers-count" id="viewers-count">0 viewers</span>
            <button class="btn btn-default" onclick="closeViewersModal()">Close</button>
        </div>
    </div>
</div>

<!-- Opened Materials Modal -->
<div id="materials-modal" class="viewers-modal-overlay">
    <div class="viewers-modal">
        <div class="viewers-modal-header">
            <h3 id="materials-modal-title">
                <i class="fa fa-file"></i> Opened Materials
            </h3>
            <button class="viewers-modal-close" onclick="closeMaterialsModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="viewers-modal-body" id="materials-modal-body">
            <!-- Content loaded dynamically -->
        </div>
        <div class="viewers-modal-footer">
            <span class="viewers-count" id="materials-count">0 materials</span>
            <button class="btn btn-default" onclick="closeMaterialsModal()">Close</button>
        </div>
    </div>
</div>

<!-- Average Duration Materials Modal -->
<div id="duration-materials-modal" class="viewers-modal-overlay">
    <div class="viewers-modal">
        <div class="viewers-modal-header">
            <h3 id="duration-materials-modal-title">
                <i class="fa fa-clock"></i> Materials by Engagement Duration
            </h3>
            <button class="viewers-modal-close" onclick="closeDurationMaterialsModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="viewers-modal-body" id="duration-materials-modal-body">
            <!-- Content loaded dynamically -->
        </div>
        <div class="viewers-modal-footer">
            <span class="viewers-count" id="duration-materials-count">0 materials</span>
            <button class="btn btn-default" onclick="closeDurationMaterialsModal()">Close</button>
        </div>
    </div>
</div>

<!-- Completed Materials Modal -->
<div id="completed-materials-modal" class="viewers-modal-overlay">
    <div class="viewers-modal">
        <div class="viewers-modal-header">
            <h3 id="completed-materials-modal-title">
                <i class="fa fa-check-circle"></i> Completed Materials
            </h3>
            <button class="viewers-modal-close" onclick="closeCompletedMaterialsModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="viewers-modal-body" id="completed-materials-modal-body">
            <!-- Content loaded dynamically -->
        </div>
        <div class="viewers-modal-footer">
            <span class="viewers-count" id="completed-materials-count">0 materials</span>
            <button class="btn btn-default" onclick="closeCompletedMaterialsModal()">Close</button>
        </div>
    </div>
</div>

<script>
// Open modal and fetch viewers
function showViewers(type, itemId, itemTitle) {
    const modal = document.getElementById('viewers-modal');
    const modalBody = document.getElementById('viewers-modal-body');
    const modalTitle = document.getElementById('viewers-modal-title');
    const viewersCount = document.getElementById('viewers-count');
    
    // Show modal with loading state
    modal.classList.add('active');
    modalTitle.innerHTML = '<i class="fa fa-users"></i> Viewers - ' + itemTitle;
    modalBody.innerHTML = '<div class="viewers-loading"><i class="fa fa-spinner fa-spin"></i> Loading viewers...</div>';
    viewersCount.textContent = 'Loading...';
    
    // Fetch viewers from server
    fetch('{{ url("learning/analytics/viewers") }}?type=' + type + '&item_id=' + itemId + '&item_title=' + encodeURIComponent(itemTitle))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-circle"></i><p>' + data.error + '</p></div>';
                viewersCount.textContent = '0 viewers';
                return;
            }
            
            // Update count
            viewersCount.textContent = data.total + ' viewer' + (data.total !== 1 ? 's' : '');
            
            if (data.viewers && data.viewers.length > 0) {
                // Build viewers list
                let html = '<ul class="viewers-list">';
                data.viewers.forEach(function(viewer) {
                    const initials = viewer.first_name ? viewer.first_name.charAt(0) + (viewer.last_name ? viewer.last_name.charAt(0) : '') : '?';
                    const fullName = (viewer.first_name || '') + ' ' + (viewer.last_name || '');
                    html += '<li class="viewers-list-item">' +
                        '<div class="viewer-avatar">' + initials + '</div>' +
                        '<div class="viewer-info">' +
                        '<div class="viewer-name">' + fullName.trim() + '</div>' +
                        '<div class="viewer-email">' + (viewer.email || 'No email') + '</div>' +
                        '</div>' +
                        '</li>';
                });
                html += '</ul>';
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-user-slash"></i><p>No viewers found for this item</p></div>';
            }
        })
        .catch(error => {
            console.error('Error fetching viewers:', error);
            modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-triangle"></i><p>Error loading viewers</p></div>';
            viewersCount.textContent = 'Error';
        });
}

function closeViewersModal() {
    document.getElementById('viewers-modal').classList.remove('active');
}

// Open modal and fetch opened materials
function showOpenedMaterials() {
    const modal = document.getElementById('materials-modal');
    const modalBody = document.getElementById('materials-modal-body');
    const materialsCount = document.getElementById('materials-count');

    // Show modal with loading state
    modal.classList.add('active');
    modalBody.innerHTML = '<div class="viewers-loading"><i class="fa fa-spinner fa-spin"></i> Loading materials...</div>';
    materialsCount.textContent = 'Loading...';

    // Fetch materials from server
    fetch('{{ url("learning/analytics/opened-materials") }}')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-circle"></i><p>' + data.error + '</p></div>';
                materialsCount.textContent = '0 materials';
                return;
            }

            // Update count
            materialsCount.textContent = data.total + ' material' + (data.total !== 1 ? 's' : '');

            if (data.materials && data.materials.length > 0) {
                // Build materials list
                let html = '<ul class="viewers-list">';
                data.materials.forEach(function(material) {
                    html += '<li class="viewers-list-item">' +
                        '<div class="viewer-avatar"><i class="fa fa-file"></i></div>' +
                        '<div class="viewer-info">' +
                        '<div class="viewer-name">' + material.name + '</div>' +
                        '<div class="viewer-email">Type: ' + material.type + ' | Topic: ' + material.topic + ' | Last viewed: ' + material.last_viewed + '</div>' +
                        '</div>' +
                        '</li>';
                });
                html += '</ul>';
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-file-o"></i><p>No opened materials found</p></div>';
            }
        })
        .catch(error => {
            console.error('Error fetching materials:', error);
            modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-triangle"></i><p>Error loading materials</p></div>';
            materialsCount.textContent = 'Error';
        });
}

function closeMaterialsModal() {
    document.getElementById('materials-modal').classList.remove('active');
}

// Close materials modal on overlay click
document.getElementById('materials-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMaterialsModal();
    }
});

// Open modal and fetch average duration materials
function showAverageDurationMaterials() {
    const modal = document.getElementById('duration-materials-modal');
    const modalBody = document.getElementById('duration-materials-modal-body');
    const materialsCount = document.getElementById('duration-materials-count');

    // Show modal with loading state
    modal.classList.add('active');
    modalBody.innerHTML = '<div class="viewers-loading"><i class="fa fa-spinner fa-spin"></i> Loading materials...</div>';
    materialsCount.textContent = 'Loading...';

    // Fetch materials from server
    fetch('{{ url("learning/analytics/average-duration-materials") }}')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-circle"></i><p>' + data.error + '</p></div>';
                materialsCount.textContent = '0 materials';
                return;
            }

            // Update count
            materialsCount.textContent = data.total + ' material' + (data.total !== 1 ? 's' : '');

            if (data.materials && data.materials.length > 0) {
                // Build materials list
                let html = '<ul class="viewers-list">';
                data.materials.forEach(function(material) {
                    html += '<li class="viewers-list-item">' +
                        '<div class="viewer-avatar"><i class="fa fa-clock"></i></div>' +
                        '<div class="viewer-info">' +
                        '<div class="viewer-name">' + material.name + ' (' + material.avg_duration_formatted + ')</div>' +
                        '<div class="viewer-email">Type: ' + material.type + ' | Topic: ' + material.topic + ' | Avg: ' + material.avg_duration + ' sec | Last viewed: ' + material.last_viewed + '</div>' +
                        '</div>' +
                        '</li>';
                });
                html += '</ul>';
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-clock-o"></i><p>No materials with engagement duration found</p></div>';
            }
        })
        .catch(error => {
            console.error('Error fetching materials:', error);
            modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-triangle"></i><p>Error loading materials</p></div>';
            materialsCount.textContent = 'Error';
        });
}

function closeDurationMaterialsModal() {
    document.getElementById('duration-materials-modal').classList.remove('active');
}

// Close duration materials modal on overlay click
document.getElementById('duration-materials-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDurationMaterialsModal();
    }
});

// Open modal and fetch completed materials
function showCompletedMaterials() {
    const modal = document.getElementById('completed-materials-modal');
    const modalBody = document.getElementById('completed-materials-modal-body');
    const materialsCount = document.getElementById('completed-materials-count');

    // Show modal with loading state
    modal.classList.add('active');
    modalBody.innerHTML = '<div class="viewers-loading"><i class="fa fa-spinner fa-spin"></i> Loading materials...</div>';
    materialsCount.textContent = 'Loading...';

    // Fetch materials from server
    fetch('{{ url("learning/analytics/completed-materials") }}')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-circle"></i><p>' + data.error + '</p></div>';
                materialsCount.textContent = '0 materials';
                return;
            }

            // Update count
            materialsCount.textContent = data.total + ' material' + (data.total !== 1 ? 's' : '');

            if (data.materials && data.materials.length > 0) {
                // Build materials list
                let html = '<ul class="viewers-list">';
                data.materials.forEach(function(material) {
                    html += '<li class="viewers-list-item">' +
                        '<div class="viewer-avatar"><i class="fa fa-check-circle"></i></div>' +
                        '<div class="viewer-info">' +
                        '<div class="viewer-name">' + material.name + '</div>' +
                        '<div class="viewer-email">Type: ' + material.type + ' | Topic: ' + material.topic + ' | Last viewed: ' + material.last_viewed + '</div>' +
                        '</div>' +
                        '</li>';
                });
                html += '</ul>';
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-check-circle-o"></i><p>No completed materials found</p></div>';
            }
        })
        .catch(error => {
            console.error('Error fetching materials:', error);
            modalBody.innerHTML = '<div class="viewers-empty"><i class="fa fa-exclamation-triangle"></i><p>Error loading materials</p></div>';
            materialsCount.textContent = 'Error';
        });
}

function closeCompletedMaterialsModal() {
    document.getElementById('completed-materials-modal').classList.remove('active');
}

// Close completed materials modal on overlay click
document.getElementById('completed-materials-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCompletedMaterialsModal();
    }
});

// Close modal on overlay click
document.getElementById('viewers-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeViewersModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeViewersModal();
        closeMaterialsModal();
        closeDurationMaterialsModal();
        closeCompletedMaterialsModal();
    }
});

function setActiveTab(tabId) {
    var buttons = document.querySelectorAll('.tab-button');
    var panels = document.querySelectorAll('.tab-panel');

    buttons.forEach(function(button) {
        var controls = button.getAttribute('aria-controls');
        var isActive = controls === tabId;
        button.classList.toggle('active', isActive);
        button.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    panels.forEach(function(panel) {
        panel.classList.toggle('active', panel.id === tabId);
    });
}

function changePeriod(value) {
    window.location.href = '{{ url("learning/analytics") }}?period=' + value;
}
</script>
@endsection

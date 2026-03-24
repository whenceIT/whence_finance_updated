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

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-title">Total Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_views']) }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-eye"></i>
                All time
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Course Topics Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_course_views']) }}</div>
            <div class="kpi-change positive">
                <i class="fa fa-graduation-cap"></i>
                Course materials
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Course Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_course_views']) }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-file-text"></i>
                Learning materials
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-title">Upload Views</div>
            <div class="kpi-value">{{ number_format($overallStats['total_upload_views']) }}</div>
            <div class="kpi-change neutral">
                <i class="fa fa-cloud-upload"></i>
                Additional resources
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
                    <span class="performer-metric">{{ number_format($course['views']) }} views</span>
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
                        <span class="performer-metric">{{ number_format($upload['views']) }} views</span>
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
                @foreach($chartData['course_categories'] as $category)
                <li class="breakdown-item">
                    <span class="breakdown-name">{{ $category['category'] }}</span>
                    <div class="breakdown-stats">
                        <span class="breakdown-count">{{ $category['count'] }} courses</span>
                        <span class="breakdown-views">{{ number_format($category['views']) }} views</span>
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
                @foreach($chartData['content_types'] as $type)
                <li class="breakdown-item">
                    <span class="breakdown-name">{{ $type['type'] }}</span>
                    <div class="breakdown-stats">
                        <span class="breakdown-count">{{ $type['count'] }} files</span>
                        <span class="breakdown-views">{{ number_format($type['views']) }} views</span>
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
                @foreach($chartData['office_analytics']['course_views'] as $office)
                <li class="breakdown-item">
                    <span class="breakdown-name">{{ $office['office'] }}</span>
                    <div class="breakdown-stats">
                        <span class="breakdown-count">{{ $office['courses_count'] }} courses</span>
                        <span class="breakdown-views">{{ number_format($office['views']) }} views</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>


@endsection

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
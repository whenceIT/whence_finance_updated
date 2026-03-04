@extends('layouts.learning')

@section('title', 'My Courses - Whence Learn')

@section('content')
<div class="page-header">
    @if(isset($selectedCategory) && $selectedCategory)
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
        <a href="{{ url('course-categories') }}" style="color: var(--primary-color); text-decoration: none;">
            <i class="fa fa-arrow-left"></i> Back to Categories
        </a>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
        @if(isset($category) && $category && $category->icon)
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; background: {{ $category->color ? $category->color . '20' : '#4a90e220' }};">
            <i class="fa {{ $category->icon }}" style="font-size: 18px; color: {{ $category->color ?: 'var(--primary-color)' }};"></i>
        </span>
        @endif
        <div>
            <h1>{{ $selectedCategory }}</h1>
            <p>Browse courses in this category</p>
        </div>
    </div>
    @else
    <h1>My Courses</h1>
    <p>Explore and manage your enrolled courses</p>
    @endif
</div>

<!-- Filter Tabs -->
<div style="margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap;">
    <button style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
        All Courses
    </button>
    <button style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500;">
        Documents
    </button>
    <button style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500;">
        Videos
    </button>
    <button style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500;">
        Audio
    </button>
</div>

<!-- Courses Grid -->
<div class="courses-grid" id="courses-grid">
    @foreach($courses as $course)
    <div class="course-card" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'">
        <div class="course-image">
            <i class="fa {{ $course['icon'] }}"></i>
            @if($course['is_featured'])
            <span class="featured-badge">Featured</span>
            @endif
        </div>
        <div class="course-body">
            <span class="course-category">{{ $course['category'] }}</span>
            <h3 class="course-title">{{ $course['title'] }}</h3>
            <p class="course-description">{{ $course['description'] }}</p>
            
            @if($course['enrolled'] && $course['progress'] > 0)
            <div class="course-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
                </div>
                <div class="progress-text">{{ $course['progress'] }}% Complete</div>
            </div>
            @endif
            
            <div class="course-meta">
                <div class="course-stats">
                    <span><i class="fa fa-file-o"></i> {{ ucfirst($course['material_type']) }}</span>
                    @if($course['duration'] != 'N/A')
                    <span><i class="fa fa-clock-o"></i> {{ $course['duration'] }}</span>
                    @endif
                    @if($course['file_size'])
                    <span><i class="fa fa-database"></i> {{ $course['file_size'] }}</span>
                    @endif
                </div>
                <div class="course-stats" style="margin-top: 8px;">
                    <span><i class="fa fa-eye"></i> {{ $course['view_count'] ?? 0 }} views</span>
                    <span><i class="fa fa-download"></i> {{ $course['download_count'] ?? 0 }} downloads</span>
                </div>
                @if($course['department'])
                <div class="course-stats" style="margin-top: 8px;">
                    <span><i class="fa fa-building-o"></i> {{ $course['department'] }}</span>
                </div>
                @endif
                @if($course['enrolled_at'])
                <div class="course-stats" style="margin-top: 8px;">
                    <span><i class="fa fa-calendar-check-o"></i> Enrolled: {{ $course['enrolled_at']->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Empty State -->
@if(count($courses) == 0)
<div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
    <i class="fa fa-book" style="font-size: 64px; color: var(--text-secondary); margin-bottom: 20px;"></i>
    @if(isset($selectedCategory) && $selectedCategory)
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
        No Courses in {{ $selectedCategory }}
    </h2>
    <p style="color: var(--text-secondary); font-size: 16px;">
        There are no courses available in this category yet.
    </p>
    @else
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
        No Enrolled Courses
    </h2>
    <p style="color: var(--text-secondary); font-size: 16px;">
        You haven't enrolled in any courses yet. Visit the <a href="{{ url('/learning') }}" style="color: var(--primary-color);">Home</a> page to browse and enroll in available courses.
    </p>
    @endif
</div>
@endif
@endsection

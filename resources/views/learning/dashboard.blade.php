@extends('layouts.learning')

@section('title', 'Dashboard - Whence Learn')

@section('content')
<div class="page-header">
    <h1>Welcome to Whence Learn</h1>
    <p>Continue your learning journey and achieve your professional goals</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fa fa-book"></i>
        </div>
        <div class="stat-value">{{ $stats['total_courses'] }}</div>
        <div class="stat-label">Total Courses</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa fa-check-circle"></i>
        </div>
        <div class="stat-value">{{ $stats['enrolled_courses'] }}</div>
        <div class="stat-label">Enrolled Courses</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fa fa-trophy"></i>
        </div>
        <div class="stat-value">{{ $stats['completed_courses'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fa fa-clock-o"></i>
        </div>
        <div class="stat-value">{{ $stats['total_hours'] }}h</div>
        <div class="stat-label">Learning Hours</div>
    </div>
</div>

<!-- Continue Learning Section -->
@if($stats['in_progress'] > 0)
<div style="margin-bottom: 40px;">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-play-circle" style="color: var(--primary-color); margin-right: 10px;"></i>
        Continue Learning
    </h2>
    <div class="courses-grid">
        @foreach($courses as $course)
            @if($course['enrolled'] && $course['progress'] > 0 && $course['progress'] < 100)
            <div class="course-card" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'">
                <div class="course-image">
                    <i class="fa {{ $course['icon'] }}"></i>
                </div>
                <div class="course-body">
                    <span class="course-category">{{ $course['category'] }}</span>
                    <h3 class="course-title">{{ $course['title'] }}</h3>
                    <p class="course-description">{{ $course['description'] }}</p>
                    <div class="course-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
                        </div>
                        <div class="progress-text">{{ $course['progress'] }}% Complete</div>
                    </div>
                    <div class="course-meta">
                        <div class="course-stats">
                            <span><i class="fa fa-list"></i> {{ $course['lessons'] }} Lessons</span>
                            <span><i class="fa fa-clock-o"></i> {{ $course['duration'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif

<!-- All Courses Section -->
<div>
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-graduation-cap" style="color: var(--primary-color); margin-right: 10px;"></i>
        All Courses
    </h2>
    <div class="courses-grid">
        @foreach($courses as $course)
        <div class="course-card" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'">
            <div class="course-image">
                <i class="fa {{ $course['icon'] }}"></i>
            </div>
            <div class="course-body">
                <span class="course-category">{{ $course['category'] }}</span>
                <h3 class="course-title">{{ $course['title'] }}</h3>
                <p class="course-description">{{ $course['description'] }}</p>
                @if($course['enrolled'])
                <div class="course-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
                    </div>
                    <div class="progress-text">{{ $course['progress'] }}% Complete</div>
                </div>
                @endif
                    <div class="course-meta">
                        <div class="course-stats">
                            <span><i class="fa fa-list"></i> {{ $course['lessons'] }} Lessons</span>
                            <span><i class="fa fa-clock-o"></i> {{ $course['duration'] }}</span>
                        </div>
                        @if(!$course['enrolled'])
                        <button onclick="event.stopPropagation(); openEnrollModal({{ $course['id'] }}, '{{ $course['title'] }}')" style="background: var(--primary-color); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
                            Enroll Now
                        </button>
                        @endif
                    </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Welcome Message for New Users -->
@if($stats['enrolled_courses'] == 0)
<div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; margin-top: 40px;">
    <i class="fa fa-rocket" style="font-size: 64px; color: var(--primary-color); margin-bottom: 20px;"></i>
    <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
        Welcome to Whence Learn!
    </h2>
    <p style="color: var(--text-secondary); font-size: 16px; max-width: 600px; margin: 0 auto 30px;">
        Start your learning journey by enrolling in one of our courses. Develop new skills and advance your career with our comprehensive training programs.
    </p>
    <a href="{{ url('learning/courses') }}" style="display: inline-block; background: var(--primary-color); color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        Browse All Courses
    </a>
</div>
@endif
@endsection

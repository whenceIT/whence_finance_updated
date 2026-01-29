@extends('layouts.learning')

@section('title', 'My Courses - Whence Learn')

@section('content')
<div class="page-header">
    <h1>My Courses</h1>
    <p>Explore and manage your enrolled courses</p>
</div>

<!-- Filter Tabs -->
<div style="margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap;">
    <button style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
        All Courses
    </button>
    <button style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500;">
        In Progress
    </button>
    <button style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500;">
        Completed
    </button>
    <button style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500;">
        Not Started
    </button>
</div>

<!-- Courses Grid -->
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
                <button style="background: var(--primary-color); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
                    Enroll Now
                </button>
                @else
                <button style="background: var(--secondary-color); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
                    {{ $course['progress'] > 0 ? 'Continue' : 'Start' }}
                </button>
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
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
        No Courses Available
    </h2>
    <p style="color: var(--text-secondary); font-size: 16px;">
        Check back later for new courses or contact your administrator.
    </p>
</div>
@endif
@endsection

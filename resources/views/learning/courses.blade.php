@extends('layouts.learning')

@section('title', 'My Courses - Whence Learn')

@section('content')
<!-- Professional Header with Gradient -->
<div style="background: linear-gradient(135deg, var(--secondary-color) 0%, #3da862 100%); border-radius: 16px; padding: 32px; margin-bottom: 30px; color: white;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            @if(isset($selectedCategory) && $selectedCategory)
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <a href="{{ url('course-categories') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.2); color: white; text-decoration: none; transition: background 0.2s;">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <span style="font-size: 13px; opacity: 0.9;">Back to Categories</span>
            </div>
            @endif
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 8px; color: white;">
                <i class="fa fa-book"></i> {{ isset($selectedCategory) && $selectedCategory ? $selectedCategory : 'My Courses' }}
            </h1>
            <p style="font-size: 14px; opacity: 0.9; margin: 0;">{{ isset($selectedCategory) && $selectedCategory ? 'Browse courses in this category' : 'Explore and manage your enrolled courses' }}</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 30px; box-shadow: var(--shadow);">
    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary); font-weight: 500;">
            <i class="fa fa-filter"></i> Filter by:
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button class="filter-btn active" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--primary-color); color: white; cursor: pointer; font-size: 13px; font-weight: 500;">
                <i class="fa fa-th"></i> All
            </button>
            <button class="filter-btn" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); color: var(--text-secondary); cursor: pointer; font-size: 13px; font-weight: 500;">
                <i class="fa fa-file"></i> Documents
            </button>
            <button class="filter-btn" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); color: var(--text-secondary); cursor: pointer; font-size: 13px; font-weight: 500;">
                <i class="fa fa-video-camera"></i> Videos
            </button>
            <button class="filter-btn" style="padding: 8px 16px; border: 1px solid var(--border-color); border-radius: 20px; background: var(--light-bg); color: var(--text-secondary); cursor: pointer; font-size: 13px; font-weight: 500;">
                <i class="fa fa-music"></i> Audio
            </button>
        </div>
    </div>
</div>

<style>
.filter-btn {
    transition: all 0.2s;
}
.filter-btn:hover {
    background: var(--primary-color) !important;
    color: white !important;
    border-color: var(--primary-color) !important;
}
</style>

<!-- Courses Grid -->
<div class="courses-grid" id="courses-grid">
    @foreach($courses as $course)
    <div class="course-card" onclick="window.location.href='{{ url('learning/course/' . $course['id']) }}'" style="cursor: pointer;">
        <div class="course-image" style="background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); {{ isset($course['poster']) && $course['poster'] ? 'background: none;' : '' }}">
            @if(isset($course['poster']) && $course['poster'])
                <img src="{{ $course['poster'] }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $course['title'] }}">
            @else
                <i class="fa {{ $course['icon'] }}" style="font-size: 48px;"></i>
            @endif
            @if($course['is_featured'])
            <span class="featured-badge">Featured</span>
            @endif
        </div>
        <div class="course-body" style="padding-bottom: 16px;">
            <span class="course-category">{{ $course['category'] }}</span>
            <h3 class="course-title">{{ strtoupper($course['title']) }}</h3>
            <p class="course-description">{{ $course['description'] }}</p>
            
            
            
            <a href="{{ url('learning/course/' . $course['id']) }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 16px; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; width: 100%; margin-top: 12px;">
                <i class="fa fa-eye"></i> View Course
            </a>
        </div>
    </div>
    @endforeach
</div>

<!-- Empty State -->
@if(count($courses) == 0)
<div style="text-align: center; padding: 80px 20px; background: white; border-radius: 16px; box-shadow: var(--shadow);">
    <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--secondary-color) 0%, #3da862 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <i class="fa fa-book" style="font-size: 48px; color: white;"></i>
    </div>
    @if(isset($selectedCategory) && $selectedCategory)
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
        No Courses in {{ $selectedCategory }}
    </h2>
    <p style="color: var(--text-secondary); font-size: 15px; max-width: 500px; margin: 0 auto;">
        There are no courses available in this category yet.
    </p>
    @else
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
        No Enrolled Courses
    </h2>
    <p style="color: var(--text-secondary); font-size: 15px; max-width: 500px; margin: 0 auto 24px; line-height: 1.6;">
        You haven't enrolled in any courses yet. Visit the Home page to browse and enroll in available courses.
    </p>
    <a href="{{ url('/learning') }}" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, var(--secondary-color) 0%, #3da862 100%); color: white; padding: 14px 32px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(80, 200, 120, 0.4);">
        <i class="fa fa-search"></i> Browse Courses
    </a>
    @endif
</div>
@endif
@endsection

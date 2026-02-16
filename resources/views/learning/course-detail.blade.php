@extends('layouts.learning')

@section('title', $course['title'] . ' - Whence Learn')

@section('content')
<!-- Course Header -->
<div style="background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); padding: 40px; border-radius: 12px; color: white; margin-bottom: 30px;">
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <div style="width: 80px; height: 80px; background: rgba(255,255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
            <i class="fa {{ $course['icon'] }}" style="font-size: 40px;"></i>
        </div>
        <div>
            <span style="background: rgba(255,255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                {{ $course['category'] }}
            </span>
            <h1 style="font-size: 32px; font-weight: 700; margin: 10px 0;">{{ $course['title'] }}</h1>
        </div>
    </div>
    <p style="font-size: 16px; opacity: 0.9; max-width: 800px;">{{ $course['description'] }}</p>
    <div style="display: flex; gap: 30px; margin-top: 20px;">
        <span><i class="fa fa-list"></i> {{ $course['lessons'] }} Lessons</span>
        <span><i class="fa fa-clock-o"></i> {{ $course['duration'] }}</span>
        @if($course['enrolled'])
        <span><i class="fa fa-check-circle"></i> {{ $course['progress'] }}% Complete</span>
        @endif
    </div>
</div>

<!-- Course Progress -->
@if($course['enrolled'])
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin: 0;">
            Your Progress
        </h2>
        <span style="font-size: 24px; font-weight: 700; color: var(--primary-color);">
            {{ $course['progress'] }}%
        </span>
    </div>
    <div class="progress-bar" style="height: 12px; margin-bottom: 20px;">
        <div class="progress-fill" style="width: {{ $course['progress'] }}%;"></div>
    </div>
    <div style="display: flex; gap: 15px;">
        <button style="flex: 1; padding: 15px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; transition: background 0.3s;">
            <i class="fa fa-play"></i> {{ $course['progress'] > 0 ? 'Continue Learning' : 'Start Course' }}
        </button>
        <button style="padding: 15px 25px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; font-weight: 600; transition: background 0.3s;">
            <i class="fa fa-bookmark"></i> Save
        </button>
    </div>
</div>
@else
<!-- Enroll Button -->
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); margin-bottom: 30px; text-align: center;">
    <i class="fa fa-graduation-cap" style="font-size: 64px; color: var(--primary-color); margin-bottom: 20px;"></i>
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
        Ready to Start Learning?
    </h2>
    <p style="color: var(--text-secondary); font-size: 16px; max-width: 600px; margin: 0 auto 25px;">
        Enroll in this course to access all lessons, quizzes, and earn your certificate upon completion.
    </p>
    <button style="padding: 15px 40px; background: var(--primary-color); color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: 600; font-size: 16px; transition: background 0.3s;">
        <i class="fa fa-user-plus"></i> Enroll Now
    </button>
</div>
@endif

<!-- Course Modules -->
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow);">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 25px; color: var(--text-primary);">
        <i class="fa fa-folder-open" style="color: var(--primary-color); margin-right: 10px;"></i>
        Course Content
    </h2>
    
    @foreach($course['modules'] as $module)
    <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; margin-bottom: 20px; {{ $module['completed'] ? 'background: rgba(80, 200, 120, 0.05);' : '' }}">
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
            <div style="width: 40px; height: 40px; background: {{ $module['completed'] ? 'var(--secondary-color)' : 'var(--light-bg)' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <i class="fa {{ $module['completed'] ? 'fa-check' : 'fa-lock' }}" style="font-size: 18px; color: {{ $module['completed'] ? 'white' : 'var(--text-secondary)' }};"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 18px; font-weight: 600; margin: 0; color: var(--text-primary);">
                    Module {{ $module['id'] }}: {{ $module['title'] }}
                </h3>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 5px 0 0;">
                    {{ $module['lessons'] }} Lessons
                </p>
            </div>
            @if($module['completed'])
            <span style="background: var(--secondary-color); color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                Completed
            </span>
            @elseif($course['enrolled'])
            <button style="padding: 8px 20px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
                Start
            </button>
            @else
            <span style="color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-lock"></i> Locked
            </span>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Course Info -->
<div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: var(--shadow);">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
            <i class="fa fa-info-circle" style="color: var(--primary-color); margin-right: 10px;"></i>
            What You'll Learn
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="padding: 10px 0; border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-check" style="color: var(--secondary-color); margin-right: 10px;"></i>
                Core concepts and fundamentals
            </li>
            <li style="padding: 10px 0; border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-check" style="color: var(--secondary-color); margin-right: 10px;"></i>
                Practical applications and examples
            </li>
            <li style="padding: 10px 0; border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-check" style="color: var(--secondary-color); margin-right: 10px;"></i>
                Best practices and industry standards
            </li>
            <li style="padding: 10px 0; color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-check" style="color: var(--secondary-color); margin-right: 10px;"></i>
                Assessment and certification
            </li>
        </ul>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: var(--shadow);">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
            <i class="fa fa-users" style="color: var(--primary-color); margin-right: 10px;"></i>
            Requirements
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="padding: 10px 0; border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-circle" style="color: var(--primary-color); margin-right: 10px; font-size: 8px;"></i>
                Basic computer skills
            </li>
            <li style="padding: 10px 0; border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-circle" style="color: var(--primary-color); margin-right: 10px; font-size: 8px;"></i>
                Internet connection
            </li>
            <li style="padding: 10px 0; color: var(--text-secondary); font-size: 14px;">
                <i class="fa fa-circle" style="color: var(--primary-color); margin-right: 10px; font-size: 8px;"></i>
                Commitment to complete the course
            </li>
        </ul>
    </div>
</div>
@endsection

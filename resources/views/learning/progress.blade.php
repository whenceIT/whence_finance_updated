@extends('layouts.learning')

@section('title', 'My Progress - Whence Learn')

@section('content')
<div class="page-header">
    <h1>My Progress</h1>
    <p>Track your learning achievements and milestones</p>
</div>

<!-- Progress Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fa fa-check-circle"></i>
        </div>
        <div class="stat-value">{{ $progressData['courses_completed'] }}</div>
        <div class="stat-label">Courses Completed</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa fa-spinner"></i>
        </div>
        <div class="stat-value">{{ $progressData['courses_in_progress'] }}</div>
        <div class="stat-label">In Progress</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fa fa-certificate"></i>
        </div>
        <div class="stat-value">{{ $progressData['certificates_earned'] }}</div>
        <div class="stat-label">Certificates Earned</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fa fa-fire"></i>
        </div>
        <div class="stat-value">{{ $progressData['streak_days'] }}</div>
        <div class="stat-label">Day Streak</div>
    </div>
</div>

<!-- Learning Activity -->
<div style="margin-bottom: 40px;">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-bar-chart" style="color: var(--primary-color); margin-right: 10px;"></i>
        Learning Activity
    </h2>
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div style="text-align: center; flex: 1;">
                <div style="font-size: 32px; font-weight: 700; color: var(--primary-color); margin-bottom: 5px;">
                    {{ $progressData['total_lessons_completed'] }}
                </div>
                <div style="color: var(--text-secondary); font-size: 14px;">Lessons Completed</div>
            </div>
            <div style="text-align: center; flex: 1; border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color);">
                <div style="font-size: 32px; font-weight: 700; color: var(--secondary-color); margin-bottom: 5px;">
                    {{ $progressData['total_lessons'] }}
                </div>
                <div style="color: var(--text-secondary); font-size: 14px;">Total Lessons</div>
            </div>
            <div style="text-align: center; flex: 1;">
                <div style="font-size: 32px; font-weight: 700; color: var(--accent-color); margin-bottom: 5px;">
                    {{ $progressData['learning_hours'] }}h
                </div>
                <div style="color: var(--text-secondary); font-size: 14px;">Learning Hours</div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div style="margin-top: 30px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="font-weight: 600; color: var(--text-primary);">Overall Progress</span>
                <span style="font-weight: 600; color: var(--primary-color);">
                    {{ $progressData['total_lessons'] > 0 ? round(($progressData['total_lessons_completed'] / $progressData['total_lessons']) * 100) : 0 }}%
                </span>
            </div>
            <div class="progress-bar" style="height: 12px;">
                <div class="progress-fill" style="width: {{ $progressData['total_lessons'] > 0 ? round(($progressData['total_lessons_completed'] / $progressData['total_lessons']) * 100) : 0 }}%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Achievements -->
<div style="margin-bottom: 40px;">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-trophy" style="color: var(--accent-color); margin-right: 10px;"></i>
        Recent Achievements
    </h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: var(--shadow); text-align: center;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <i class="fa fa-star" style="font-size: 36px; color: white;"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">First Course Completed</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Completed your first course</p>
        </div>
        
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: var(--shadow); text-align: center;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <i class="fa fa-fire" style="font-size: 36px; color: white;"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">7-Day Streak</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Learned for 7 consecutive days</p>
        </div>
        
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: var(--shadow); text-align: center;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #50c878 0%, #3cb371 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <i class="fa fa-book" style="font-size: 36px; color: white;"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">Knowledge Seeker</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Completed 10 lessons</p>
        </div>
    </div>
</div>

<!-- Learning Goals -->
<div>
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-bullseye" style="color: var(--primary-color); margin-right: 10px;"></i>
        Learning Goals
    </h2>
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; padding: 20px; border-bottom: 1px solid var(--border-color);">
            <div style="width: 50px; height: 50px; background: rgba(74, 144, 226, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                <i class="fa fa-check-circle" style="font-size: 24px; color: var(--primary-color);"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">Complete 5 Courses</h3>
                <div class="progress-bar" style="height: 8px; margin-bottom: 5px;">
                    <div class="progress-fill" style="width: 40%;"></div>
                </div>
                <span style="color: var(--text-secondary); font-size: 14px;">2 of 5 completed</span>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; padding: 20px; border-bottom: 1px solid var(--border-color);">
            <div style="width: 50px; height: 50px; background: rgba(80, 200, 120, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                <i class="fa fa-clock-o" style="font-size: 24px; color: var(--secondary-color);"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">Learn for 50 Hours</h3>
                <div class="progress-bar" style="height: 8px; margin-bottom: 5px;">
                    <div class="progress-fill" style="width: 48%;"></div>
                </div>
                <span style="color: var(--text-secondary); font-size: 14px;">24 of 50 hours completed</span>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; padding: 20px;">
            <div style="width: 50px; height: 50px; background: rgba(255, 107, 107, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                <i class="fa fa-certificate" style="font-size: 24px; color: var(--accent-color);"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">Earn 5 Certificates</h3>
                <div class="progress-bar" style="height: 8px; margin-bottom: 5px;">
                    <div class="progress-fill" style="width: 40%;"></div>
                </div>
                <span style="color: var(--text-secondary); font-size: 14px;">2 of 5 earned</span>
            </div>
        </div>
    </div>
</div>
@endsection

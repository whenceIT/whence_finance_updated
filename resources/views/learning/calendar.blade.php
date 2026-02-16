@extends('layouts.learning')

@section('title', 'Calendar - Whence Learn')

@section('content')
<div class="page-header">
    <h1>Learning Calendar</h1>
    <p>View your upcoming lessons, deadlines, and learning schedule</p>
</div>

<!-- Calendar Container -->
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="font-size: 24px; font-weight: 600; color: var(--text-primary);">
            {{ date('F Y') }}
        </h2>
        <div style="display: flex; gap: 10px;">
            <button style="padding: 8px 16px; background: var(--light-bg); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;">
                <i class="fa fa-chevron-left"></i>
            </button>
            <button style="padding: 8px 16px; background: var(--light-bg); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;">
                <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background: var(--border-color); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
        <!-- Day Headers -->
        <div style="background: var(--light-bg); padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary);">Sun</div>
        <div style="background: var(--light-bg); padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary);">Mon</div>
        <div style="background: var(--light-bg); padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary);">Tue</div>
        <div style="background: var(--light-bg); padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary);">Wed</div>
        <div style="background: var(--light-bg); padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary);">Thu</div>
        <div style="background: var(--light-bg); padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary);">Fri</div>
        <div style="background: var(--light-bg); padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary);">Sat</div>

        <!-- Calendar Days (Sample) -->
        @for($i = 1; $i <= 35; $i++)
        @php
            $day = $i - 3; // Adjust for month start
            $isToday = $day == date('j');
            $hasEvent = in_array($day, [5, 12, 18, 25]);
        @endphp
        @if($day > 0 && $day <= 31)
        <div style="background: white; padding: 15px; min-height: 100px; position: relative; cursor: pointer; transition: background 0.2s; {{ $isToday ? 'background: rgba(74, 144, 226, 0.1);' : '' }}">
            <span style="font-weight: 600; color: {{ $isToday ? 'var(--primary-color)' : 'var(--text-primary)' }}; {{ $hasEvent ? 'color: var(--secondary-color);' : '' }}">
                {{ $day }}
            </span>
            @if($hasEvent)
            <div style="margin-top: 8px;">
                <div style="background: var(--primary-color); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; margin-bottom: 4px;">
                    <i class="fa fa-book"></i> Lesson
                </div>
            </div>
            @endif
        </div>
        @else
        <div style="background: var(--light-bg); padding: 15px; min-height: 100px;"></div>
        @endif
        @endfor
    </div>
</div>

<!-- Upcoming Events -->
<div style="margin-top: 30px;">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-calendar-check-o" style="color: var(--primary-color); margin-right: 10px;"></i>
        Upcoming Events
    </h2>
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: var(--shadow);">
        <div style="display: flex; align-items: center; padding: 15px; border-bottom: 1px solid var(--border-color);">
            <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; margin-right: 20px;">
                <span style="font-size: 24px; font-weight: 700;">05</span>
                <span style="font-size: 12px; text-transform: uppercase;">Jan</span>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">
                    Financial Management Fundamentals - Module 3
                </h3>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">
                    <i class="fa fa-clock-o"></i> 10:00 AM - 11:30 AM
                </p>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; padding: 15px; border-bottom: 1px solid var(--border-color);">
            <div style="width: 60px; height: 60px; background: var(--secondary-color); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; margin-right: 20px;">
                <span style="font-size: 24px; font-weight: 700;">12</span>
                <span style="font-size: 12px; text-transform: uppercase;">Jan</span>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">
                    Leadership and Team Management - Quiz
                </h3>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">
                    <i class="fa fa-clock-o"></i> 2:00 PM - 3:00 PM
                </p>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; padding: 15px;">
            <div style="width: 60px; height: 60px; background: var(--accent-color); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; margin-right: 20px;">
                <span style="font-size: 24px; font-weight: 700;">18</span>
                <span style="font-size: 12px; text-transform: uppercase;">Jan</span>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: var(--text-primary);">
                    Customer Service Excellence - Live Session
                </h3>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">
                    <i class="fa fa-clock-o"></i> 9:00 AM - 10:30 AM
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.learning')

@section('title', 'Settings - Whence Learn')

@section('content')
<div class="page-header">
    <h1>Settings</h1>
    <p>Manage your learning platform settings and configurations</p>
</div>

@php
$user = Sentinel::getUser();
$role = $user ? $user->roles->first() : null;
$isAdmin = $role && $role->id == 1;
@endphp

<!-- Stats Cards -->
<section aria-labelledby="stats-heading">
    <h2 id="stats-heading" class="visually-hidden">Platform Statistics</h2>
    <div class="stats-grid" role="region" aria-label="Platform statistics overview">

        <div class="stat-card" role="article" aria-label="Total Settings: {{ $totalSettings ?? 0 }}">
            <div class="stat-icon blue" aria-hidden="true">
                <i class="fa fa-cog"></i>
            </div>
            <div class="stat-value" aria-label="Total Settings: {{ $totalSettings ?? 0 }}">{{ $totalSettings ?? 0 }}</div>
            <div class="stat-label" aria-label="Total Settings">Total Settings</div>
        </div>
        <div class="stat-card" role="article" aria-label="Course Categories: {{ $totalCategories ?? 0 }}">
            <div class="stat-icon green" aria-hidden="true">
                <i class="fa fa-folder"></i>
            </div>
            <div class="stat-value" aria-label="Course Categories: {{ $totalCategories ?? 0 }}">{{ $totalCategories ?? 0 }}</div>
            <div class="stat-label" aria-label="Course Categories">Course Categories</div>
        </div>
        <div class="stat-card" role="article" aria-label="Students: {{ $totalStudents ?? 0 }}">
            <div class="stat-icon orange" aria-hidden="true">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-value" aria-label="Students: {{ $totalStudents ?? 0 }}">{{ $totalStudents ?? 0 }}</div>
            <div class="stat-label" aria-label="Students">Students</div>
        </div>
        <div class="stat-card" role="article" aria-label="Teachers: {{ $totalTeachers ?? 0 }}">
            <div class="stat-icon purple" aria-hidden="true">
                <i class="fa fa-user-tie"></i>
            </div>
            <div class="stat-value" aria-label="Teachers: {{ $totalTeachers ?? 0 }}">{{ $totalTeachers ?? 0 }}</div>
            <div class="stat-label" aria-label="Teachers">Teachers</div>
        </div>
    </div>
</section>

<!-- Settings Grid -->
<section aria-labelledby="settings-heading">
    <h2 id="settings-heading" class="visually-hidden">Settings Management</h2>
    <div class="settings-grid" role="grid" aria-label="Settings management options">
        
        <!-- Categories Settings (Admin Only) -->
        @if($isAdmin)
        <div class="setting-card" id="setting-categories" role="gridcell" aria-label="Course Categories Management">
            <div class="setting-card-header" role="heading" aria-level="3">
                <i class="fa fa-folder setting-card-icon" aria-hidden="true"></i>
                <div class="setting-card-title" role="heading" aria-level="4">Course Categories</div>
                <div class="setting-card-description" role="definition">Manage course categories and their configurations</div>
            </div>
            <div class="setting-card-body" role="group">
                <p role="doc-abstract">Create, edit, and organize course categories for better content management.</p>
                <p role="doc-abstract">Set up category icons, descriptions, and hierarchy.</p>
                <a href="{{ url('course-categories') }}" class="btn" role="button" aria-label="Manage Categories" aria-describedby="categories-description">Manage Categories</a>
                <div id="categories-description" class="visually-hidden">Create, edit, and organize course categories for better content management.</div>
            </div>
        </div>
        @endif

        <!-- Students Settings (Visible to Teachers) -->
        @if($isAdmin || $user->istrainer == 1) 
        <div class="setting-card" role="gridcell" aria-label="Students Management">
            <div class="setting-card-header" role="heading" aria-level="3">
                <i class="fa fa-users setting-card-icon" aria-hidden="true"></i>
                <div class="setting-card-title" role="heading" aria-level="4">Students Management</div>
                <p class="setting-card-description" role="definition">Manage student profiles and learning progress</p>
            </div>
            <div class="setting-card-body" role="group">
                <p role="doc-abstract">View and manage student profiles, enrollment status, and progress.</p>
                <p role="doc-abstract">Monitor learning activities and generate reports.</p>
                <a href="{{ url('learning/settings/students') }}" class="btn" role="button" aria-label="Manage Students" aria-describedby="students-description">Manage Students</a>
                <div id="students-description" class="visually-hidden">View and manage student profiles, enrollment status, and progress.</div>
            </div>
        </div>
        @endif    

        <!-- Teachers Settings (Admin Only) -->
        @if($isAdmin)
        <div class="setting-card" role="gridcell" aria-label="Teachers Management">
            <div class="setting-card-header" role="heading" aria-level="3">
                <i class="fa fa-user-tie setting-card-icon" aria-hidden="true"></i>
                <div class="setting-card-title" role="heading" aria-level="4">Teachers Management</div>
                <p class="setting-card-description" role="definition">Manage teacher profiles and course assignments</p>
            </div>
            <div class="setting-card-body" role="group">
                <p role="doc-abstract">Create and manage teacher profiles with their expertise areas.</p>
                <p role="doc-abstract">Assign courses to teachers and track their performance.</p>
                <a href="{{ url('learning/settings/teachers') }}" class="btn" role="button" aria-label="Manage Teachers" aria-describedby="teachers-description">Manage Teachers</a>
                <div id="teachers-description" class="visually-hidden">Create and manage teacher profiles with their expertise areas.</div>
            </div>
        </div>
        @endif

        <!-- Additional Settings Card (Admin Only) -->
        @if($isAdmin)
        <div class="setting-card" role="gridcell" aria-label="Platform Settings">
            <div class="setting-card-header" role="heading" aria-level="3">
                <i class="fa fa-sliders setting-card-icon" aria-hidden="true"></i>
                <div class="setting-card-title" role="heading" aria-level="4">Platform Settings</div>
                <p class="setting-card-description" role="definition">Configure platform-wide settings and preferences</p>
            </div>
            <div class="setting-card-body" role="group">
                <p role="doc-abstract">Customize platform appearance, notifications, and user preferences.</p>
                <p role="doc-abstract">Set up system configurations and integrations.</p>
                <a href="{{ url('learning/settings/platform') }}" class="btn" role="button" aria-label="Platform Settings" aria-describedby="platform-description">Platform Settings</a>
                <div id="platform-description" class="visually-hidden">Customize platform appearance, notifications, and user preferences.</div>
            </div>
        </div>
        @endif

        <!-- Non-Admin Message -->
        @if(!$isAdmin)
        <div class="setting-card" style="grid-column: 1 / -1;" role="gridcell" aria-label="Access Notice">
            <div class="setting-card-header" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);" role="heading" aria-level="3">
                <i class="fa fa-info-circle setting-card-icon" aria-hidden="true"></i>
                <div class="setting-card-title" role="heading" aria-level="4">Limited Access</div>
                <div class="setting-card-description" role="definition">Contact administrator for full access</div>
            </div>
            <div class="setting-card-body" role="group">
                <p role="doc-abstract">You have access to Students Management only.</p>
                <p role="doc-abstract">For additional settings and configurations, please contact your system administrator.</p>
            </div>
        </div>
        @endif
    </div>
</section>

<style>
/* Settings Card Styles - extends layout styles */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.setting-card {
    background: var(--card-bg);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.25s ease;
    cursor: pointer;
}

.setting-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}

.setting-card-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
    color: white;
    padding: 20px;
    text-align: center;
}

.setting-card-icon {
    font-size: 48px;
    margin-bottom: 12px;
}

.setting-card-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
}

.setting-card-description {
    font-size: 14px;
    opacity: 0.9;
}

.setting-card-body {
    padding: 20px;
}

.setting-card-body p {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 16px;
}

.setting-card-body .btn {
    width: 100%;
    padding: 12px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}

.setting-card-body .btn:hover {
    background: #357abd;
    transform: translateY(-2px);
}

/* Visually Hidden Class */
.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Focus States for Accessibility */
.setting-card:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

.setting-card-body .btn:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('footer-scripts')
<script>
// Handle active state for settings page
$(document).ready(function() {
    var currentPath = window.location.pathname;
    var currentUrl = window.location.href;
    
    // Handle sidebar menu active state
    $('.sidebar-menu a').each(function() {
        var $link = $(this);
        var linkHref = $link.attr('href');
        
        if (currentPath === linkHref || currentUrl.includes(linkHref)) {
            $link.addClass('active');
        }
    });

    // Handle nav links active state
    $('.learning-nav a').each(function() {
        var $link = $(this);
        var linkHref = $link.attr('href');
        
        if (currentPath === linkHref || currentUrl.includes(linkHref)) {
            $link.addClass('active');
        }
    });

    // Handle user dropdown active state
    if (currentPath.includes('/settings')) {
        $('.user-dropdown-item[href*="settings"]').addClass('active');
    }
});
</script>
@endsection

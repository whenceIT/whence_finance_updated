<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Whence Learn')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #50c878;
            --accent-color: #ff6b6b;
            --dark-bg: #1a1a2e;
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --border-color: #e0e0e0;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Header Styles */
        .learning-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
            color: white;
            padding: 10px 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .learning-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .learning-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
        }

        .learning-logo i {
            font-size: 24px;
            margin-right: 10px;
        }

        .learning-logo span {
            font-size: 18px;
            font-weight: 600;
        }

        .learning-nav {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .learning-nav a {
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .learning-nav a:hover {
            background: rgba(255,255,255,0.15);
        }

        .learning-nav a.active {
            background: rgba(255,255,255,0.25);
        }

        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-profile:hover {
            background: rgba(255,255,255,0.2);
        }

        .user-profile-avatar {
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .user-profile-info {
            text-align: left;
        }

        .user-profile-name {
            font-size: 13px;
            font-weight: 600;
            color: white;
            margin: 0;
            line-height: 1.2;
        }

        .user-profile-role {
            font-size: 10px;
            color: rgba(255,255,255,0.75);
            margin: 0;
            line-height: 1.2;
        }

        .user-menu-btn {
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-size: 13px;
        }

        .user-menu-btn:hover {
            background: rgba(255,255,255,0.25);
        }

        /* User dropdown menu */
        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: white;
            border-radius: 6px;
            box-shadow: var(--shadow-hover);
            min-width: 180px;
            z-index: 1001;
            overflow: hidden;
        }

        .user-dropdown.show {
            display: block;
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            color: var(--text-primary);
            text-decoration: none;
            transition: background 0.15s;
            font-size: 13px;
        }

        .user-dropdown-item:hover {
            background: var(--light-bg);
        }

        .user-dropdown-item i {
            width: 20px;
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 14px;
        }

        .user-dropdown-divider {
            height: 1px;
            background: var(--border-color);
            margin: 4px 0;
        }

        @media (max-width: 992px) {
            .user-profile-info {
                display: none;
            }

            .user-profile {
                padding: 6px 10px;
            }

            .learning-nav a {
                padding: 5px 10px;
                font-size: 13px;
            }
        }

        @media (max-width: 768px) {
            .user-profile {
                display: none;
            }

            .learning-nav {
                display: none;
            }

            .user-dropdown {
                min-width: 160px;
            }

            .user-dropdown-item {
                padding: 8px 14px;
                font-size: 12px;
            }
        }

        /* Main Layout */
        .learning-container {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        /* Sidebar */
        .learning-sidebar {
            width: 220px;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            padding: 16px;
            position: sticky;
            top: 60px;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }

        .sidebar-section {
            margin-bottom: 24px;
        }

        .sidebar-title {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 4px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 13px;
        }

        .sidebar-menu a:hover {
            background: var(--light-bg);
            transform: translateX(3px);
        }

        .sidebar-menu a.active {
            background: var(--primary-color);
            color: white;
        }

        .sidebar-menu a i {
            width: 20px;
            margin-right: 10px;
            font-size: 14px;
        }

        /* Main Content */
        .learning-content {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Course Cards */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .course-card {
            background: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .course-image {
            height: 140px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            position: relative;
        }

        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--accent-color);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .course-body {
            padding: 16px;
        }

        .course-category {
            display: inline-block;
            padding: 3px 10px;
            background: var(--light-bg);
            color: var(--text-secondary);
            border-radius: 10px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .course-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .course-description {
            color: var(--text-secondary);
            font-size: 13px;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
        }

        .course-progress {
            flex: 1;
            margin-right: 12px;
        }

        .progress-bar {
            height: 5px;
            background: var(--light-bg);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--secondary-color);
            border-radius: 3px;
            transition: width 0.25s ease;
        }

        .progress-text {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .course-stats {
            display: flex;
            gap: 12px;
            font-size: 11px;
            color: var(--text-secondary);
        }

        .course-stats span {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 22px;
        }

        .stat-icon.blue {
            background: rgba(74, 144, 226, 0.1);
            color: var(--primary-color);
        }

        .stat-icon.green {
            background: rgba(80, 200, 120, 0.1);
            color: var(--secondary-color);
        }

        .stat-icon.orange {
            background: rgba(255, 107, 107, 0.1);
            color: var(--accent-color);
        }

        .stat-icon.purple {
            background: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 13px;
        }

        /* Footer */
        .learning-footer {
            background: var(--dark-bg);
            color: white;
            padding: 20px 0;
            margin-top: 40px;
        }

        .learning-footer .container {
            text-align: center;
        }

        .learning-footer p {
            margin: 0;
            opacity: 0.8;
            font-size: 13px;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            margin-right: 12px;
        }

        .mobile-menu-btn:hover {
            background: rgba(255,255,255,0.25);
        }

        /* Mobile Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Mobile User Panel */
        .mobile-user-panel {
            display: none;
            background: rgba(255,255,255,0.08);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .mobile-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mobile-user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .mobile-user-details h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .mobile-user-details p {
            margin: 4px 0 0;
            font-size: 11px;
            opacity: 0.8;
        }

        /* Close Button for Mobile Sidebar */
        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--light-bg);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-primary);
            z-index: 1001;
        }

        .sidebar-close-btn:hover {
            background: var(--border-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .learning-sidebar {
                position: fixed;
                top: 0;
                left: -240px;
                width: 240px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.25s ease;
                box-shadow: var(--shadow-hover);
            }

            .learning-sidebar.active {
                left: 0;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .learning-nav {
                display: none;
            }

            .courses-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .course-card {
                border-radius: 8px;
            }

            .course-image {
                height: 120px;
                font-size: 36px;
            }

            .course-body {
                padding: 14px;
            }

            .course-title {
                font-size: 15px;
            }

            .course-description {
                font-size: 12px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
                margin: 0 auto 10px;
            }

            .stat-value {
                font-size: 24px;
            }

            .stat-label {
                font-size: 11px;
            }

            .sidebar-close-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar-close-btn {
                width: 28px;
                height: 28px;
                font-size: 14px;
                top: 10px;
                right: 10px;
            }

            .mobile-user-panel {
                display: block;
            }

            .mobile-user-panel {
                padding: 10px;
                margin-bottom: 14px;
            }

            .mobile-user-avatar {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .mobile-user-details h4 {
                font-size: 13px;
            }

            .mobile-user-details p {
                font-size: 10px;
            }

            .sidebar-section {
                margin-bottom: 20px;
            }

            .sidebar-title {
                margin-bottom: 10px;
            }
        }

            .sidebar-menu a {
                padding: 10px 12px;
                font-size: 14px;
            }

            .sidebar-menu a i {
                width: 22px;
                margin-right: 12px;
                font-size: 15px;
            }

            .learning-header .container {
                padding: 0 12px;
            }

            .learning-header {
                padding: 8px 0;
            }

            .mobile-menu-btn {
                padding: 6px 10px;
                font-size: 16px;
                margin-right: 10px;
            }

            .learning-logo span {
                font-size: 16px;
            }

            .learning-content {
                padding: 16px;
            }

            .page-header h1 {
                font-size: 24px;
            }

            .page-header {
                margin-bottom: 20px;
            }

            .page-header p {
                font-size: 13px;
            }

            .learning-footer {
                padding: 16px 0;
                margin-top: 32px;
            }

            .learning-footer p {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .learning-sidebar {
                width: 260px;
                left: -260px;
            }

            .learning-sidebar.active {
                left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
                margin: 0 auto 10px;
            }

            .stat-value {
                font-size: 24px;
            }

            .stat-label {
                font-size: 11px;
            }

            .learning-logo i {
                font-size: 20px;
            }

            .learning-logo span {
                font-size: 14px;
            }

            .learning-header {
                padding: 6px 0;
            }

            .mobile-menu-btn {
                padding: 5px 8px;
                font-size: 14px;
                margin-right: 8px;
            }

            .course-card {
                border-radius: 8px;
            }

            .course-image {
                height: 100px;
                font-size: 32px;
            }

            .course-body {
                padding: 12px;
            }

            .course-title {
                font-size: 14px;
            }

            .course-description {
                font-size: 11px;
            }

            .page-header h1 {
                font-size: 20px;
            }

            .page-header p {
                font-size: 12px;
            }

            .sidebar-menu a {
                padding: 12px 14px;
                font-size: 15px;
            }

            .sidebar-menu a i {
                width: 24px;
                margin-right: 14px;
                font-size: 16px;
            }

            .learning-footer {
                padding: 14px 0;
                margin-top: 28px;
            }

            .learning-footer p {
                font-size: 11px;
            }

            .user-dropdown {
                min-width: 150px;
            }

            .user-dropdown-item {
                padding: 7px 12px;
                font-size: 11px;
            }

            .sidebar-close-btn {
                width: 26px;
                height: 26px;
                font-size: 13px;
                top: 8px;
                right: 8px;
            }

            .mobile-user-panel {
                padding: 8px;
                margin-bottom: 12px;
            }

            .mobile-user-avatar {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .mobile-user-details h4 {
                font-size: 12px;
            }

            .mobile-user-details p {
                font-size: 9px;
            }
        }

        /* Loader */
        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            transition: opacity 0.75s, visibility 0.75s;
            z-index: 99999;
        }

        #loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid var(--primary-color);
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    @yield('page-header-scripts')
</head>
<body>
    <!-- Header -->
    <header class="learning-header">
        <div class="container">
            <div style="display: flex; align-items: center;">
                <button class="mobile-menu-btn" id="mobile-menu-toggle">
                    <i class="fa fa-bars"></i>
                </button>
                <a href="{{ url('/learning') }}" class="learning-logo">
                    <i class="fa fa-graduation-cap"></i>
                    <span>Whence Learn</span>
                </a>
            </div>
            
            <nav class="learning-nav">
                <a href="{{ url('/learning') }}" class="active">Dashboard</a>
                <a href="{{ url('/learning/courses') }}">My Courses</a>
                <a href="{{ url('/learning/calendar') }}">Calendar</a>
                <a href="{{ url('/learning/progress') }}">Progress</a>
            </nav>
            
            <div class="user-menu">
                @php
                $user = Sentinel::getUser();
                $role = $user ? $user->roles->first() : null;
                @endphp
                
                @if($user)
                <div class="user-profile" id="user-profile-toggle">
                    <div class="user-profile-avatar">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="user-profile-info">
                        <p class="user-profile-name">{{ $user->first_name }} {{ $user->last_name }}</p>
                        <p class="user-profile-role">{{ $role ? $role->name : 'Staff' }}</p>
                    </div>
                    <i class="fa fa-chevron-down" style="color: rgba(255,255,255,0.7); font-size: 12px;"></i>
                </div>
                
                <!-- User Dropdown Menu -->
                <div class="user-dropdown" id="user-dropdown">
                    <a href="{{ url('user/edit_profile') }}" class="user-dropdown-item">
                        <i class="fa fa-user"></i>
                        My Profile
                    </a>
                    <a href="{{ url('learning/progress') }}" class="user-dropdown-item">
                        <i class="fa fa-chart-line"></i>
                        My Progress
                    </a>
                    <a href="{{ url('learning/certificates') }}" class="user-dropdown-item">
                        <i class="fa fa-certificate"></i>
                        Certificates
                    </a>
                    <a class="user-menu-btn" onclick="window.location.href='{{ url('/') }}'">
                        <i class="fa fa-arrow-left"></i>
                        <span>Back to Main</span>
                    </a>
                    <div class="user-dropdown-divider"></div>
                    <a href="{{ url('logout') }}" class="user-dropdown-item" style="color: var(--accent-color);">
                        <i class="fa fa-sign-out"></i>
                        Logout
                    </a>
                </div>
                @endif
                
            </div>
        </div>
    </header>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Container -->
    <div class="learning-container">
        <!-- Sidebar -->
        <aside class="learning-sidebar">
            <button class="sidebar-close-btn" id="sidebar-close">
                <i class="fa fa-times"></i>
            </button>
            
            @php
            $user = Sentinel::getUser();
            $role = $user ? $user->roles->first() : null;
            @endphp
            
            <!-- User Details Panel -->
            <div class="mobile-user-panel">
                <div class="mobile-user-info">
                    <div class="mobile-user-avatar">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="mobile-user-details">
                        <h4>{{ $user ? $user->first_name . ' ' . $user->last_name : 'Guest' }}</h4>
                        <p>{{ $role ? $role->name : 'No Role' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-title">Quick Links</div>
                <ul class="sidebar-menu">
                    <li><a href="{{ url('/learning') }}" class="active"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('/learning/courses') }}"><i class="fa fa-book"></i> My Courses</a></li>
                    <li><a href="{{ url('/learning/calendar') }}"><i class="fa fa-calendar"></i> Calendar</a></li>
                    <li><a href="{{ url('/learning/progress') }}"><i class="fa fa-chart-line"></i> My Progress</a></li>
                    <li><a href="{{ url('/learning/certificates') }}"><i class="fa fa-certificate"></i> Certificates</a></li>
                    <li><a href="{{ url('/learning/training-materials') }}"><i class="fa fa-folder-open"></i> Training Materials</a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-title">Categories</div>
                <ul class="sidebar-menu">
                    <li><a href="#"><i class="fa fa-briefcase"></i> Business</a></li>
                    <li><a href="#"><i class="fa fa-calculator"></i> Finance</a></li>
                    <li><a href="#"><i class="fa fa-users"></i> Leadership</a></li>
                    <li><a href="#"><i class="fa fa-laptop"></i> Technology</a></li>
                    <li><a href="#"><i class="fa fa-comments"></i> Communication</a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-title">Support</div>
                <ul class="sidebar-menu">
                    <li><a href="#"><i class="fa fa-question-circle"></i> Help Center</a></li>
                    <li><a href="#"><i class="fa fa-envelope"></i> Contact Support</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="learning-content">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="learning-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Whence Financial Services. All rights reserved.</p>
        </div>
    </footer>

    <!-- Loader -->
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.js') }}"></script>
    
    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Handle session flash messages for toastr
        @if(Session::has('toastr_type'))
            toastr.{{ Session::get('toastr_type') }}('{{ Session::get('toastr_message') }}', '{{ Session::get('toastr_title', 'Notification') }}');
        @endif

        // Hide loader on page load
        $(window).on('load', function () {
            $('#loader-wrapper').fadeOut(2000);
        });

        // Mobile Menu Toggle
        $(document).ready(function() {
            var $sidebar = $('.learning-sidebar');
            var $overlay = $('#sidebar-overlay');
            var $toggleBtn = $('#mobile-menu-toggle');
            var $closeBtn = $('#sidebar-close');
            var $userProfile = $('#user-profile-toggle');
            var $userDropdown = $('#user-dropdown');

            function openSidebar() {
                $sidebar.addClass('active');
                $overlay.addClass('active');
                $('body').css('overflow', 'hidden');
            }

            function closeSidebar() {
                $sidebar.removeClass('active');
                $overlay.removeClass('active');
                $('body').css('overflow', '');
            }

            function toggleUserDropdown() {
                $userDropdown.toggleClass('show');
            }

            function closeUserDropdown() {
                $userDropdown.removeClass('show');
            }

            // Toggle sidebar on button click
            $toggleBtn.on('click', function(e) {
                e.preventDefault();
                openSidebar();
            });

            // Close sidebar on close button click
            $closeBtn.on('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });

            // Close sidebar on overlay click
            $overlay.on('click', function() {
                closeSidebar();
            });

            // Toggle user dropdown on profile click
            $userProfile.on('click', function(e) {
                e.stopPropagation();
                toggleUserDropdown();
            });

            // Close user dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#user-profile-toggle').length) {
                    closeUserDropdown();
                }
            });

            // Close sidebar on Escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                    closeUserDropdown();
                }
            });

            // Add active class to current menu item
            var currentPath = window.location.pathname;
            $('.sidebar-menu a').each(function() {
                var $link = $(this);
                if (currentPath === $link.attr('href')) {
                    $link.addClass('active');
                }
            });

            // Add active class to nav links
            $('.learning-nav a').each(function() {
                var $link = $(this);
                if (currentPath === $link.attr('href')) {
                    $link.addClass('active');
                }
            });
        });
    </script>
    
    @yield('footer-scripts')
</body>
</html>

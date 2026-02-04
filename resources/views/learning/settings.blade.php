<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Settings - Whence Learn</title>
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

        /* Settings Grid */
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
        }

        .setting-card-body .btn:hover {
            background: #357abd;
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
                    <a href="{{ url('learning/progress') }}" class="user-dropdown-item">
                        <i class="fa fa-chart-line"></i>
                        My Progress
                    </a>
                    <a href="{{ url('learning/settings') }}" class="user-dropdown-item">
                        <i class="fa fa-cog"></i>
                        Settings
                    </a>
                    <div class="user-dropdown-divider"></div>
                    <a href="{{ url('/') }}" class="user-dropdown-item">
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
                    <li><a href="{{ url('/learning') }}" class="active"><i class="fa fa-home"></i> Home</a></li>
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
                    @if(isset($categories) && count($categories) > 0)
                        @foreach($categories as $category)
                        <li><a href="{{ url('/learning?category=' . urlencode($category->name)) }}"><i class="fa {{ $category->icon }}"></i> {{ $category->name }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#"><i class="fa fa-folder"></i> No categories available</a></li>
                    @endif
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
            <div class="page-header">
                <h1>Settings</h1>
                <p>Manage your learning platform settings and configurations</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa fa-cog"></i>
                    </div>
                    <div class="stat-value">{{ $totalSettings ?? 0 }}</div>
                    <div class="stat-label">Total Settings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fa fa-folder"></i>
                    </div>
                    <div class="stat-value">{{ $totalCategories ?? 0 }}</div>
                    <div class="stat-label">Course Categories</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fa fa-user-tie"></i>
                    </div>
                    <div class="stat-value">{{ $totalTeachers ?? 0 }}</div>
                    <div class="stat-label">Teachers</div>
                </div>
            </div>

            <!-- Settings Grid -->
            <div class="settings-grid">
                <!-- Categories Settings -->
                <div class="setting-card">
                    <div class="setting-card-header">
                        <i class="fa fa-folder setting-card-icon"></i>
                        <div class="setting-card-title">Course Categories</div>
                        <div class="setting-card-description">Manage course categories and their configurations</div>
                    </div>
                    <div class="setting-card-body">
                        <p>Create, edit, and organize course categories for better content management.</p>
                        <p>Set up category icons, descriptions, and hierarchy.</p>
                        <a href="{{ url('learning/settings/categories') }}" class="btn">Manage Categories</a>
                    </div>
                </div>

                <!-- Students Settings -->
                <div class="setting-card">
                    <div class="setting-card-header">
                        <i class="fa fa-users setting-card-icon"></i>
                        <div class="setting-card-title">Students Management</div>
                        <div class="setting-card-description">Manage student profiles and learning progress</div>
                    </div>
                    <div class="setting-card-body">
                        <p>View and manage student profiles, enrollment status, and progress.</p>
                        <p>Monitor learning activities and generate reports.</p>
                        <a href="{{ url('learning/settings/students') }}" class="btn">Manage Students</a>
                    </div>
                </div>

                <!-- Teachers Settings -->
                <div class="setting-card">
                    <div class="setting-card-header">
                        <i class="fa fa-user-tie setting-card-icon"></i>
                        <div class="setting-card-title">Teachers Management</div>
                        <p class="setting-card-description">Manage teacher profiles and course assignments</p>
                    </div>
                    <div class="setting-card-body">
                        <p>Create and manage teacher profiles with their expertise areas.</p>
                        <p>Assign courses to teachers and track their performance.</p>
                        <a href="{{ url('learning/settings/teachers') }}" class="btn">Manage Teachers</a>
                    </div>
                </div>

                <!-- Additional Settings Card (Placeholder for future settings) -->
                <div class="setting-card">
                    <div class="setting-card-header">
                        <i class="fa fa-sliders setting-card-icon"></i>
                        <div class="setting-card-title">Platform Settings</div>
                        <p class="setting-card-description">Configure platform-wide settings and preferences</p>
                    </div>
                    <div class="setting-card-body">
                        <p>Customize platform appearance, notifications, and user preferences.</p>
                        <p>Set up system configurations and integrations.</p>
                        <a href="{{ url('learning/settings/platform') }}" class="btn">Platform Settings</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="learning-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Whence Financial Services. All rights reserved.</p>
        </div>
    </footer>

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
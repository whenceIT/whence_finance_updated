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
            padding: 15px 0;
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
            font-size: 32px;
            margin-right: 12px;
        }

        .learning-logo span {
            font-size: 24px;
            font-weight: 700;
        }

        .learning-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .learning-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .learning-nav a:hover {
            background: rgba(255,255,255,0.2);
        }

        .learning-nav a.active {
            background: rgba(255,255,255,0.3);
        }

        .user-menu {
            position: relative;
        }

        .user-menu-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .user-menu-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Main Layout */
        .learning-container {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        /* Sidebar */
        .learning-sidebar {
            width: 280px;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            padding: 20px;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }

        .sidebar-section {
            margin-bottom: 30px;
        }

        .sidebar-title {
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover {
            background: var(--light-bg);
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background: var(--primary-color);
            color: white;
        }

        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 16px;
        }

        /* Main Content */
        .learning-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .page-header p {
            color: var(--text-secondary);
            font-size: 16px;
        }

        /* Course Cards */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .course-card {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .course-image {
            height: 160px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }

        .course-body {
            padding: 20px;
        }

        .course-category {
            display: inline-block;
            padding: 4px 12px;
            background: var(--light-bg);
            color: var(--text-secondary);
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .course-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .course-description {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .course-progress {
            flex: 1;
            margin-right: 15px;
        }

        .progress-bar {
            height: 6px;
            background: var(--light-bg);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--secondary-color);
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }

        .course-stats {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .course-stats span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
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
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Footer */
        .learning-footer {
            background: var(--dark-bg);
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }

        .learning-footer .container {
            text-align: center;
        }

        .learning-footer p {
            margin: 0;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .learning-sidebar {
                display: none;
            }

            .learning-nav {
                display: none;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
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
            <a href="{{ url('/learning') }}" class="learning-logo">
                <i class="fa fa-graduation-cap"></i>
                <span>Whence Learn</span>
            </a>
            
            <nav class="learning-nav">
                <a href="{{ url('/learning') }}" class="active">Dashboard</a>
                <a href="{{ url('/learning/courses') }}">My Courses</a>
                <a href="{{ url('/learning/calendar') }}">Calendar</a>
                <a href="{{ url('/learning/progress') }}">Progress</a>
            </nav>
            
            <div class="user-menu">
                <button class="user-menu-btn" onclick="window.location.href='{{ url('/') }}'">
                    <i class="fa fa-arrow-left"></i>
                    <span>Back to Main</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="learning-container">
        <!-- Sidebar -->
        <aside class="learning-sidebar">
            <div class="sidebar-section">
                <div class="sidebar-title">Quick Links</div>
                <ul class="sidebar-menu">
                    <li><a href="{{ url('/learning') }}" class="active"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('/learning/courses') }}"><i class="fa fa-book"></i> My Courses</a></li>
                    <li><a href="{{ url('/learning/calendar') }}"><i class="fa fa-calendar"></i> Calendar</a></li>
                    <li><a href="{{ url('/learning/progress') }}"><i class="fa fa-chart-line"></i> My Progress</a></li>
                    <li><a href="{{ url('/learning/certificates') }}"><i class="fa fa-certificate"></i> Certificates</a></li>
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

        // Hide loader on page load
        $(window).on('load', function () {
            $('#loader-wrapper').fadeOut(2000);
        });
    </script>
    
    @yield('footer-scripts')
</body>
</html>

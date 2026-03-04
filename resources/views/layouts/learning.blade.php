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
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- IntroJS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    
    <style>
        :root {
            --primary-color: #4ae2bc;
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
            /* Brand Colors */
            --brand-navy: #1a365d;
            --brand-green: #22c55e;
            --brand-yellow: #eab308;
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

        .learning-logo-img {
            height: 38px;
            width: auto;
            margin-right: 10px;
            border-radius: 10px;
        }

        .learning-logo:hover .learning-logo-img {
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        .learning-logo span {
            font-size: 18px;
            font-weight: 600;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.9) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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

        .sidebar-menu .badge {
            margin-left: auto;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .sidebar-menu .badge-primary {
            background: var(--primary-color);
            color: white;
        }

        .sidebar-menu .badge-secondary {
            background: var(--brand-navy);
            color: white;
        }

        .sidebar-menu .badge-warning {
            background: var(--brand-yellow);
            color: var(--brand-navy);
        }

        .sidebar-menu .badge-success {
            background: var(--brand-green);
            color: white;
        }

        .sidebar-menu a:hover .badge {
            opacity: 0.8;
        }

        /* Breadcrumb Styles */
        .breadcrumb {
            background: var(--light-bg);
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            list-style: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            color: var(--text-secondary);
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #357abd;
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: var(--text-secondary);
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
            display: block;
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

            .sidebar-menu .badge {
                padding: 1px 6px;
                font-size: 10px;
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

            .sidebar-menu .badge {
                padding: 1px 5px;
                font-size: 9px;
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
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        #loader.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .loader-content {
            text-align: center;
        }
        
        .loader-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .loader-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .loader-icon::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 4px solid transparent;
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .loader-logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .loader-logo img {
            max-width: 100%;
            height: auto;
        }

        .loader-text {
            color: white;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .loader-subtext {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
        
        .loader-progress {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin-top: 30px;
            overflow: hidden;
        }
        
        .loader-progress-bar {
            height: 100%;
            background: white;
            border-radius: 2px;
            animation: progress 1.5s ease-in-out infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes progress {
            0% { width: 0%; margin-left: 0; margin-right: 100%; }
            50% { width: 100%; margin-left: 0; margin-right: 0; }
            100% { width: 0%; margin-left: 100%; margin-right: 0; }
        }
        
        /* Flash Message Styles */
        .flash-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 99999;
            max-width: 400px;
            width: 100%;
        }
        
        .flash-message {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 14px 18px;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            border-left: 4px solid;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .flash-message.flash-success {
            border-left-color: #28a745;
        }
        
        .flash-message.flash-error {
            border-left-color: #dc3545;
        }
        
        .flash-message.flash-warning {
            border-left-color: #ffc107;
        }
        
        .flash-message.flash-info {
            border-left-color: #17a2b8;
        }
        
        .flash-message .flash-icon {
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .flash-message.flash-success .flash-icon {
            color: #28a745;
        }
        
        .flash-message.flash-error .flash-icon {
            color: #dc3545;
        }
        
        .flash-message.flash-warning .flash-icon {
            color: #ffc107;
        }
        
        .flash-message.flash-info .flash-icon {
            color: #17a2b8;
        }
        
        .flash-message .flash-content {
            flex: 1;
        }
        
        .flash-message .flash-title {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 2px;
        }
        
        .flash-message .flash-body {
            font-size: 13px;
            color: #555;
            line-height: 1.4;
        }
        
        .flash-message .flash-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #999;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            flex-shrink: 0;
        }
        
        .flash-message .flash-close:hover {
            color: #333;
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
                <a href="{{ url('/learning') }}" class="learning-logo" id="learning-logo">
                    <img src="/images/w/logo.jpg" alt="Whence Learn" class="learning-logo-img">
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
                    <a href="javascript:void(0)" onclick="restartTour()" class="user-dropdown-item">
                        <i class="fa fa-question-circle"></i>
                        Restart Tour
                    </a>
                    <a href="{{ url('learning/progress') }}" class="user-dropdown-item">
                        <i class="fa fa-tasks"></i>
                        My Progres
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
        <aside class="learning-sidebar" id="learning-sidebar">
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
            
            <!-- Quick Links Section -->
            <div class="sidebar-section">
                <div class="sidebar-title">Quick Links</div>
                <ul class="sidebar-menu">
                    <li><a href="{{ url('/learning') }}" class="active"><i class="fa fa-home"></i> Home</a></li>
                    <li>
                        <a href="{{ url('/learning/courses') }}">
                            <i class="fa fa-book"></i> My Courses
                            @php
                            $enrolledCount = 0;
                            if($user) {
                                $enrolledCount = \App\Models\Enrollment::where('user_id', $user->id)->count();
                            }
                            @endphp
                            @if($enrolledCount > 0)
                                <span class="badge badge-primary">{{ $enrolledCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/learning/calendar') }}">
                            <i class="fa fa-calendar"></i> Calendar
                            @php $upcomingCount = isset($upcomingLessons) ? count($upcomingLessons) : 0; @endphp
                            @if($upcomingCount > 0)
                                <span class="badge badge-warning">{{ $upcomingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/learning/progress') }}">
                            <i class="fa fa-tasks"></i> My Progress
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/learning/certificates') }}">
                            <i class="fa fa-certificate"></i> Certificates
                            @php $certCount = isset($certificates) ? count($certificates) : 0; @endphp
                            @if($certCount > 0)
                                <span class="badge badge-success">{{ $certCount }}</span>
                            @endif
                        </a>
                    </li>
                    @if($user && $user->istrainer == 1)
                    <li>
                        <a href="{{ url('/learning/training-materials') }}">
                            <i class="fa fa-folder-open"></i> Training Materials
                            @php $materialsCount = isset($trainingMaterials) ? count($trainingMaterials) : 0; @endphp
                            @if($materialsCount > 0)
                                <span class="badge badge-secondary">{{ $materialsCount }}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ url('/course-categories') }}">
                            <i class="fa fa-folder"></i> Categories
                            @php $categoryCount = isset($categories) ? count($categories) : (isset($sidebarCategories) ? count($sidebarCategories) : 0); @endphp
                            @if($categoryCount > 0)
                                <span class="badge badge-secondary">{{ $categoryCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Categories Section -->
            <div class="sidebar-section">
                <div class="sidebar-title">Categories</div>
                <ul class="sidebar-menu">
                    @php
                        $sidebarCategories = isset($categories) && count($categories) > 0 ? $categories : \App\Models\CourseCategory::active()->ordered()->get();
                    @endphp
                    @if(count($sidebarCategories) > 0)
                        @foreach($sidebarCategories as $category)
                        <li>
                            <a href="{{ url('/learning?category=' . urlencode($category->name)) }}">
                                <i class="fa fa-book"></i> {{ $category->name }}
                                @php $courseCount = isset($category->courses) ? $category->courses->count() : 0; @endphp
                                @if($courseCount > 0)
                                    <span class="badge badge-primary">{{ $courseCount }}</span>
                                @endif
                            </a>
                        </li>
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
        <main class="learning-content" id="learning-content">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <!-- <footer class="learning-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Whence Financial Services. All rights reserved.</p>
        </div>
    </footer> -->

    <!-- Enrollment Modal -->
    <div class="modal fade" id="enrollModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Enroll in Course</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to enroll in this course?</p>
                    <p id="enrollCourseTitle" style="font-weight: 600; color: var(--primary-color);"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmEnrollBtn">Enroll</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader">
        <div class="loader-content">
             <div class="loader-logo">
                 <img src="{{ asset('images/w/logo.jpg') }}" alt="Whence Learn" style="width: 60px; height: auto;">
             </div>
            <div class="loader-text">Whence Learn</div>
            <div class="loader-subtext">Loading your learning experience...</div>
            <div class="loader-progress">
                <div class="loader-progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    
    <!-- IntroJS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Flash Message Handler
        @if(Session::has('toastr_type'))
            @php
            $message = Session::get('toastr_message');
            $title = Session::get('toastr_title', 'Notification');
            $type = Session::get('toastr_type');
            $iconMap = [
                'success' => 'fa-check-circle',
                'error' => 'fa-times-circle',
                'warning' => 'fa-exclamation-circle',
                'info' => 'fa-info-circle'
            ];
            $icon = $iconMap[$type] ?? 'fa-bell';
            @endphp
            
            document.addEventListener('DOMContentLoaded', function() {
                showFlashMessage('{{ $type }}', '{{ $title }}', '{{ addslashes($message) }}', '{{ $icon }}');
            });
        @endif
        
        function showFlashMessage(type, title, message, icon) {
            var container = document.querySelector('.flash-container') || createFlashContainer();
            
            var flashDiv = document.createElement('div');
            flashDiv.className = 'flash-message flash-' + type;
            flashDiv.innerHTML = '<div class="flash-icon"><i class="fa ' + icon + '"></i></div>' +
                '<div class="flash-content">' +
                    '<div class="flash-title">' + title + '</div>' +
                    '<div class="flash-body">' + message + '</div>' +
                '</div>' +
                '<button class="flash-close" onclick="this.parentElement.remove()"><i class="fa fa-times"></i></button>';
            
            container.appendChild(flashDiv);
            
            // Auto remove after 6 seconds
            setTimeout(function() {
                flashDiv.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(function() {
                    flashDiv.remove();
                }, 300);
            }, 6000);
        }
        
        function createFlashContainer() {
            var div = document.createElement('div');
            div.className = 'flash-container';
            document.body.appendChild(div);
            return div;
        }

        // Hide loader on page load
        $(window).on('load', function () {
            $('#loader').fadeOut(1000);
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

            // Enrollment Modal Handler
            var currentCourseId = null;
            var currentCourseTitle = '';

            window.openEnrollModal = function(courseId, courseTitle) {
                currentCourseId = courseId;
                currentCourseTitle = courseTitle;
                $('#enrollCourseTitle').text(courseTitle);
                $('#enrollModal').modal('show');
            };

            $('#confirmEnrollBtn').on('click', function() {
                if (!currentCourseId) return;

                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enrolling...');

                $.ajax({
                    url: '{{ url('learning/enroll') }}/' + currentCourseId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showFlashMessage('success', 'Success', response.message, 'fa-check-circle');
                            $('#enrollModal').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showFlashMessage('error', 'Error', response.message, 'fa-times-circle');
                        }
                    },
                    error: function(xhr) {
                        var message = 'An error occurred while enrolling';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showFlashMessage('error', 'Error', message, 'fa-times-circle');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Enroll');
                    }
                });
            });
        });
    </script>
    
    <!-- IntroJS Initialization -->
    <script>
        // IntroJS Tour Configuration
        const TOUR_KEY = 'whenceLearnTourCompleted';
        
        // Define tour steps for different pages
        const tours = {
            // Dashboard tour
            dashboard: [
                {
                    element: '#learning-logo',
                    title: 'Welcome to Whence Learn!',
                    intro: 'Your institutional learning platform for professional development and training.'
                },
                {
                    element: '#stats-grid',
                    title: 'Your Statistics',
                    intro: 'Track your enrolled courses, completed courses, learning hours, and achievements here.'
                },
                {
                    element: '.course-card:first-child',
                    title: 'Continue Learning',
                    intro: 'Click on any course card to continue where you left off or start a new course.'
                },
                {
                    element: '#learning-sidebar',
                    title: 'Navigation Sidebar',
                    intro: 'Use the sidebar to quickly access different sections of the learning platform.'
                }
            ],
            // Courses page tour
            courses: [
                {
                    element: '.page-header',
                    title: 'My Courses',
                    intro: 'Browse and manage all your enrolled courses here.'
                },
                {
                    element: '#courses-grid',
                    title: 'Course Cards',
                    intro: 'Each card shows course details including title, description, progress, and duration.'
                }
            ],
            // Training Materials tour
            trainingMaterials: [
                {
                    element: '.page-header',
                    title: 'Training Materials',
                    intro: 'Access and manage institutional learning resources including documents, videos, and audio materials.'
                },
                {
                    element: '#materials-grid',
                    title: 'Material Library',
                    intro: 'Browse available training materials organized by type and department.'
                }
            ],
            // Training Materials Create tour
            trainingMaterialsCreate: [
                {
                    element: '#step-1',
                    title: 'Course Information',
                    intro: 'Fill in the basic course details including title, description, material type, and target audience.'
                },
                {
                    element: '#step-2',
                    title: 'Add Topics',
                    intro: 'Create multiple topics for your course with different content types like video, PDF, PPT, or documents.'
                }
            ],
            // Settings tour
            settings: [
                {
                    element: '.page-header',
                    title: 'Platform Settings',
                    intro: 'Manage learning platform configurations and user settings.'
                },
                {
                    element: '#setting-categories',
                    title: 'Course Categories',
                    intro: 'Organize courses into categories for better navigation and management.'
                }
            ],
            // Classroom tour
            classroom: [
                {
                    element: '#classroom-header',
                    title: 'Classroom Header',
                    intro: 'View course title and access navigation controls.'
                },
                {
                    element: '#classroom-sidebar',
                    title: 'Course Content',
                    intro: 'Navigate through course topics and track your progress.'
                },
                {
                    element: '#content-area',
                    title: 'Course Material',
                    intro: 'View and interact with your course content here.'
                }
            ],
            // Calendar tour
            calendar: [
                {
                    element: '.page-header',
                    title: 'Learning Calendar',
                    intro: 'View your upcoming lessons, deadlines, and learning schedule.'
                }
            ],
            // Progress tour
            progress: [
                {
                    element: '.page-header',
                    title: 'My Progress',
                    intro: 'Track your learning achievements, milestones, and overall progress.'
                },
                {
                    element: '.stats-grid',
                    title: 'Progress Overview',
                    intro: 'See your completed courses, certificates earned, and learning streaks.'
                }
            ],
            // Certificates tour
            certificates: [
                {
                    element: '.page-header',
                    title: 'My Certificates',
                    intro: 'View and download your earned certificates of completion.'
                }
            ]
        };

        // Get current page and start appropriate tour
        function initIntroTour() {
            const currentPath = window.location.pathname;
            const isLearningPath = currentPath.includes('/learning');
            
            // Check if user has already completed the tour
            const tourCompleted = localStorage.getItem(TOUR_KEY);
            
            if (!tourCompleted && isLearningPath) {
                // Determine which tour to show based on current page
                let tourSteps = tours.dashboard; // Default tour
                
                if (currentPath.includes('/training-materials/create')) {
                    tourSteps = tours.trainingMaterialsCreate;
                } else if (currentPath.includes('/training-materials')) {
                    tourSteps = tours.trainingMaterials;
                } else if (currentPath.includes('/settings')) {
                    tourSteps = tours.settings;
                } else if (currentPath.includes('/classroom')) {
                    tourSteps = tours.classroom;
                } else if (currentPath.includes('/calendar')) {
                    tourSteps = tours.calendar;
                } else if (currentPath.includes('/progress')) {
                    tourSteps = tours.progress;
                } else if (currentPath.includes('/certificates')) {
                    tourSteps = tours.certificates;
                } else if (currentPath.includes('/courses')) {
                    tourSteps = tours.courses;
                }
                
                // Start the tour with IntroJS
                introJs()
                    .setOptions({
                        steps: tourSteps,
                        showBullets: true,
                        showProgress: false,
                        scrollToElement: true,
                        scrollTo: 'element',
                        tooltipClass: 'introjs-tooltip',
                        highlightClass: 'introjs-highlight',
                        buttonClass: 'introjs-button',
                        prevLabel: '<i class="fa fa-arrow-left"></i> Previous',
                        nextLabel: 'Next <i class="fa fa-arrow-right"></i>',
                        skipLabel: 'Skip Tour',
                        doneLabel: 'Complete'
                    })
                    .oncomplete(function() {
                        localStorage.setItem(TOUR_KEY, 'true');
                    })
                    .onskip(function() {
                        localStorage.setItem(TOUR_KEY, 'true');
                    })
                    .start();
            }
        }

        // Function to restart tour manually
        window.restartTour = function() {
            localStorage.removeItem(TOUR_KEY);
            location.reload();
        };

        // Initialize tour on page load
        $(document).ready(function() {
            // Wait for all elements to be rendered
            setTimeout(initIntroTour, 500);
        });
    </script>
    
    @yield('footer-scripts')
</body>
</html>

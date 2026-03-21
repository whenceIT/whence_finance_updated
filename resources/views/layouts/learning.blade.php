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
    
    <style>
        /* Nunito Sans Variable Font */
        @font-face {
            font-family: 'NunitoSans';
            src: url('{{ asset('fonts/Nunito_Sans/NunitoSans-VariableFont_YTLC,opsz,wdth,wght.ttf') }}') format('truetype');
            font-weight: 300 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'NunitoSans';
            src: url('{{ asset('fonts/Nunito_Sans/NunitoSans-Italic-VariableFont_YTLC,opsz,wdth,wght.ttf') }}') format('truetype');
            font-weight: 300 900;
            font-style: italic;
            font-display: swap;
        }

        /* Iosevka Charon Mono Font (for code/monospace) */
        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-Italic.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: italic;
            font-display: swap;
        }

        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-Light.ttf') }}') format('truetype');
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-LightItalic.ttf') }}') format('truetype');
            font-weight: 300;
            font-style: italic;
            font-display: swap;
        }

        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-Medium.ttf') }}') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-MediumItalic.ttf') }}') format('truetype');
            font-weight: 500;
            font-style: italic;
            font-display: swap;
        }

        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'IosevkaCharonMono';
            src: url('{{ asset('fonts/Iosevka_Charon_Mono/IosevkaCharonMono-BoldItalic.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: italic;
            font-display: swap;
        }

        :root {
            --primary-color: #4ae2bc;
            --secondary-color: #50c878;
            --success-color: #28a745;
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
            font-family: 'NunitoSans', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Header Styles */
        .learning-header {
            background: linear-gradient(135deg, #00b5a3 0%, #63bea3 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-radius: 0 0 16px 16px;
        }

        .learning-header .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .learning-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            gap: 12px;
        }

        .learning-logo-img {
            height: 45px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .learning-logo:hover .learning-logo-img {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-main {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo-sub {
            font-size: 11px;
            opacity: 0.8;
            font-weight: 400;
            letter-spacing: 0.3px;
        }

        .learning-nav {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .learning-nav a {
            color: white;
            text-decoration: none !important;
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

        /* Ensure all links in header have no text decoration */
        .learning-header a {
            text-decoration: none !important;
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
            /* border-radius: 50px;
            background: rgba(255, 255, 255, 0.2); */
        }

        .loader-logo img {
            max-width: 100%;
            height: 90%;
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

        /* Cookie Consent Modal Styles */
        .cookie-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: flex-end;
            z-index: 100000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .cookie-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .cookie-modal {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #ffffff;
            border-radius: 12px 12px 0 0;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
            overflow: hidden;
            margin-bottom: 0;
        }

        .cookie-modal-overlay.show .cookie-modal {
            transform: translateX(-50%) translateY(0);
        }

        .cookie-modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
            color: white;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cookie-modal-header i {
            font-size: 28px;
        }

        .cookie-modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .cookie-modal-body {
            padding: 24px;
        }

        .cookie-modal-body p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .cookie-modal-body .cookie-features {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .cookie-modal-body .cookie-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: var(--text-primary);
        }

        .cookie-modal-body .cookie-features li:last-child {
            margin-bottom: 0;
        }

        .cookie-modal-body .cookie-features li i {
            color: var(--secondary-color);
            font-size: 16px;
        }

        .cookie-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .cookie-modal-footer .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .cookie-modal-footer .btn-accept {
            background: var(--secondary-color);
            color: white;
        }

        .cookie-modal-footer .btn-accept:hover {
            background: #45b369;
            transform: translateY(-1px);
        }

        .cookie-modal-footer .btn-decline {
            background: var(--light-bg);
            color: var(--text-secondary);
        }

        .cookie-modal-footer .btn-decline:hover {
            background: var(--border-color);
        }
    </style>
    
    @yield('page-header-scripts')
</head>
<body>
    <!-- Header -->
    @php
        $user = Sentinel::getUser();
        $role = $user ? $user->roles->first() : null;
    @endphp
    <header class="learning-header">
        <div class="container">
            <div class="header-content">
                <!-- Mobile Menu and Logo -->
                <div class="header-left">
                    <button class="mobile-menu-btn" id="mobile-menu-toggle">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a href="{{ url('/learning') }}" class="learning-logo" id="learning-logo">
                        <img src="/images/wlh.jpg" alt="Whence Training Hub" class="learning-logo-img">
                        <div class="logo-text">
                            <span class="logo-main">Whence<br>Training Hub</span>
                        </div>
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="learning-nav">
                    <a href="{{ url('/learning') }}" class="nav-link active">
                        <i class="fa fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                    @if(($user && $user->istrainer == 1) || ($role && in_array($role->id, ['1'])))
                        <a href="{{ url('learning/settings/courses') }}" class="nav-link">
                            <i class="fa fa-book"></i>
                            <span>Manage Courses</span>
                        </a>
                        <a href="{{ url('learning/settings/students') }}" class="nav-link">
                            <i class="fa fa-users"></i>
                            <span>Manage Students</span>
                        </a>
                        <a href="{{ url('learning/settings/teachers') }}" class="nav-link">
                            <i class="fa fa-graduation-cap"></i>
                            <span>Manage Trainers</span>
                        </a>
                        <a href="{{ url('learning/settings/general-topics') }}" class="nav-link">
                            <i class="fa fa-folder-open"></i>
                            <span>Manage Topics</span>
                        </a>
                    @endif
                </nav>
                
                <!-- User Menu -->
                <div class="user-menu">
                    @if($user)
                        <div class="user-profile" id="user-profile-toggle">
                            <div class="user-profile-avatar">
                                <i class="fa fa-user-circle"></i>
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
                    <li><a href="{{ url('/learning') }}" class="{{ request()->is('learning') ? 'active' : '' }}"><i class="fa fa-home"></i> Home</a></li>
                    <li>
                        <a href="{{ url('/learning/courses') }}" class="{{ request()->is('learning/courses*') ? 'active' : '' }}">
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
                        <a href="{{ url('/learning/calendar') }}" class="{{ request()->is('learning/calendar*') ? 'active' : '' }}">
                            <i class="fa fa-calendar"></i> Calendar
                            @php $upcomingCount = isset($upcomingLessons) ? count($upcomingLessons) : 0; @endphp
                            @if($upcomingCount > 0)
                                <span class="badge badge-warning">{{ $upcomingCount }}</span>
                            @endif
                        </a>
                    </li>
                    @if($role && $role->id != 1 && $user->istrainer != 1)
                    <li>
                        <a href="{{ url('/learning/progress') }}" class="{{ request()->is('learning/progress*') ? 'active' : '' }}">
                            <i class="fa fa-tasks"></i> My Progress
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ url('/learning/certificates') }}" class="{{ request()->is('learning/certificates*') ? 'active' : '' }}">
                            <i class="fa fa-certificate"></i> Certificates
                            @php $certCount = isset($certificates) ? count($certificates) : 0; @endphp
                            @if($certCount > 0)
                                <span class="badge badge-success">{{ $certCount }}</span>
                            @endif
                        </a>
                    </li>
                    @if($user && $user->istrainer == 1)
                    <li>
                        <a href="{{ url('/learning/training-materials') }}" class="{{ request()->is('learning/training-materials*') ? 'active' : '' }}">
                            <i class="fa fa-folder-open"></i> Training Materials
                            @php $materialsCount = isset($trainingMaterials) ? count($trainingMaterials) : 0; @endphp
                            @if($materialsCount > 0)
                                <span class="badge badge-secondary">{{ $materialsCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/learning/general-uploads') }}" class="{{ request()->is('learning/general-uploads*') ? 'active' : '' }}">
                            <i class="fa fa-cloud-upload"></i> General Uploads
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ url('/course-categories') }}" class="{{ request()->is('course-categories*') ? 'active' : '' }}">
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
                <ul class="sidebar-menu" id="categories-menu">
                    @php
                        $sidebarCategories = isset($categories) && count($categories) > 0 ? $categories : \App\Models\CourseCategory::active()->ordered()->get();
                        $visibleCategories = $sidebarCategories->take(5);
                        $hiddenCategories = $sidebarCategories->slice(5);
                        $totalCount = count($sidebarCategories);
                    @endphp
                    @if($totalCount > 0)
                        @foreach($visibleCategories as $category)
                        <li class="category-item">
                            <a href="{{ url('/learning?category=' . urlencode($category->name)) }}">
                                <i class="fa fa-book"></i> {{ $category->name }}
                                @php $courseCount = isset($category->courses) ? $category->courses->count() : 0; @endphp
                                @if($courseCount > 0)
                                    <span class="badge badge-primary">{{ $courseCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endforeach
                        @if($hiddenCategories->count() > 0)
                        <li class="category-item hidden-categories" style="display: none;">
                            @foreach($hiddenCategories as $category)
                            <a href="{{ url('/learning?category=' . urlencode($category->name)) }}">
                                <i class="fa fa-book"></i> {{ $category->name }}
                                @php $courseCount = isset($category->courses) ? $category->courses->count() : 0; @endphp
                                @if($courseCount > 0)
                                    <span class="badge badge-primary">{{ $courseCount }}</span>
                                @endif
                            </a>
                            @endforeach
                        </li>
                        <li>
                            <a href="javascript:void(0)" id="see-more-categories" onclick="toggleCategories()">
                                <i class="fa fa-chevron-down"></i> See More ({{ $hiddenCategories->count() }} more)
                            </a>
                            <a href="javascript:void(0)" id="see-less-categories" onclick="toggleCategories()" style="display: none;">
                                <i class="fa fa-chevron-up"></i> See Less
                            </a>
                        </li>
                        @endif
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
    <footer class="learning-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Whence Financial Services. All rights reserved.</p>
        </div>
    </footer>

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

    <!-- Cookie Consent Modal -->
    <div class="cookie-modal-overlay" id="cookieConsentModal">
        <div class="cookie-modal">
            <div class="cookie-modal-header">
                <i class="fa fa-cookie-bite"></i>
                <h3>Cookie & Storage Consent</h3>
            </div>
            <div class="cookie-modal-body">
                <p>We use cookies and local storage to provide you with a better learning experience. Please accept to enable the following features:</p>
                
                <p style="font-size: 12px; color: #888; margin-bottom: 0;">
                    <i class="fa fa-info-circle"></i> Your data is stored locally on your device and is not shared with third parties.
                </p>
            </div>
            <div class="cookie-modal-footer">
                <button type="button" class="btn btn-decline" id="declineCookies">Not Now</button>
                <button type="button" class="btn btn-accept" id="acceptCookies">Accept & Continue</button>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader">
        <div class="loader-content">
             <div class="loader-logo">
                 <img style="border-radius: 40px; background: rgba(255, 255, 255, 0.2);" src="{{ asset('images/wlh.jpg') }}" alt="Whence Learn" style="width: 100%; height: auto;">
             </div>
            <div class="loader-text">Whence Training Hub</div>
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
    
    <!-- Playerjs Script -->
    <script src="{{ asset('layouts/player.js') }}" type="text/javascript"></script>
    
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

            // Cookie Consent Handler
            const COOKIE_CONSENT_KEY = 'whenceLearnCookieConsent';
            const USER_DATA_KEY = 'whenceLearnUserData';
            
            function initCookieConsent() {
                // Check if user has accepted cookies
                // Modal always shows until user accepts (not when declined)
                const consentAccepted = localStorage.getItem(COOKIE_CONSENT_KEY) === 'accepted';
                
                if (!consentAccepted) {
                    // Show cookie consent modal after a short delay
                    setTimeout(function() {
                        $('#cookieConsentModal').addClass('show');
                    }, 1000);
                } else {
                    // If consent was previously given, restore user data
                    restoreUserData();
                }
            }
            
            function restoreUserData() {
                const userData = localStorage.getItem(USER_DATA_KEY);
                if (userData) {
                    try {
                        const parsed = JSON.parse(userData);
                        console.log('User data restored from localStorage:', parsed);
                        // You can use this data for personalization
                        window.learningUserData = parsed;
                    } catch (e) {
                        console.error('Error parsing user data:', e);
                    }
                }
            }
            
            function acceptCookies() {
                // Store consent in localStorage
                localStorage.setItem(COOKIE_CONSENT_KEY, 'accepted');
                localStorage.setItem(COOKIE_CONSENT_KEY + '_date', new Date().toISOString());
                
                // Store user details for learning experience and streaming
                @if($user)
                var userData = {
                    id: {{ $user->id }},
                    first_name: '{{ $user->first_name }}',
                    last_name: '{{ $user->last_name }}',
                    email: '{{ $user->email }}',
                    role: '{{ $role ? $role->name : 'Staff' }}',
                    is_trainer: {{ $user->istrainer ?? 0 }},
                    accepted_at: new Date().toISOString(),
                    purpose: ['learning_progress', 'streaming', 'personalization']
                };
                localStorage.setItem(USER_DATA_KEY, JSON.stringify(window.learningUserData = userData));
                console.log('User data stored in localStorage for learning and streaming');
                @endif
                
                // Hide modal with animation
                $('#cookieConsentModal').removeClass('show');
                
                // Show success message
                showFlashMessage('success', 'Cookie Consent', 'Your preferences have been saved. Enjoy your learning experience!', 'fa-check-circle');
            }
            
            function declineCookies() {
                // Just hide the modal - it will show again on next visit until accepted
                // This ensures the modal always comes up until user accepts
                $('#cookieConsentModal').removeClass('show');
                
                console.log('Cookie consent deferred - will show again on next visit');
            }
            
            // Bind cookie consent buttons
            $('#acceptCookies').on('click', acceptCookies);
            $('#declineCookies').on('click', declineCookies);
            
            // Close modal on overlay click
            $('#cookieConsentModal').on('click', function(e) {
                if (e.target === this) {
                    declineCookies();
                }
            });
            
            // Toggle categories see more/see less
            window.toggleCategories = function() {
                var hiddenCategories = document.querySelector('.hidden-categories');
                var seeMoreLink = document.getElementById('see-more-categories');
                var seeLessLink = document.getElementById('see-less-categories');
                
                if (hiddenCategories.style.display === 'none') {
                    hiddenCategories.style.display = 'block';
                    seeMoreLink.style.display = 'none';
                    seeLessLink.style.display = 'inline-flex';
                } else {
                    hiddenCategories.style.display = 'none';
                    seeMoreLink.style.display = 'inline-flex';
                    seeLessLink.style.display = 'none';
                }
            };
            
            // Initialize cookie consent on page load
            initCookieConsent();
        });
    </script>
    
    <!-- IntroJS Initialization -->
    <style>
        /* Custom IntroJS Tour Styling */
         .introjs-tooltip {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            font-family: 'NunitoSans', sans-serif;
            max-width: 420px;
            border: none;
            overflow: hidden;
        }

        .introjs-tooltip-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ffffff 100%);
            color: blue;
            padding: 20px 24px;
            border-radius: 0;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .introjs-tooltip-header i {
            font-size: 28px;
            background: rgb(255, 255, 255);
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .introjs-tooltip-header-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            line-height: 1.3;
        }

        .introjs-tooltip-body {
            padding: 24px;
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.7;
        }

        .introjs-tooltip-body p {
            margin-bottom: 12px;
            color: #4a5568;
        }

        .introjs-tooltip-body p:last-child {
            margin-bottom: 0;
        }

        .introjs-tooltip-footer {
            padding: 16px 24px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .introjs-button {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2d6a9f 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: 'Roboto', sans-serif;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.35);
        }

        .introjs-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.45);
        }

        .introjs-button:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.25);
        }

        .introjs-button.introjs-prevbutton {
            background: white;
            color: var(--text-secondary);
            border: 2px solid #dee2e6;
            box-shadow: none;
        }

        .introjs-button.introjs-prevbutton:hover {
            background: #f8f9fa;
            border-color: #ced4da;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .introjs-button.introjs-skipbutton {
            background: transparent;
            color: #a0aec0;
            font-size: 13px;
            box-shadow: none;
            padding: 10px 14px;
        }

        .introjs-button.introjs-skipbutton:hover {
            color: var(--accent-color);
            background: rgba(255, 107, 107, 0.1);
            transform: none;
            box-shadow: none;
        }

        .introjs-progressbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2d6a9f 100%);
            height: 5px;
            border-radius: 3px;
        }

        .introjs-progress {
            background: #e9ecef;
            border-radius: 3px;
            height: 5px;
        }

        .introjs-highlight {
            background: rgb(237, 244, 244);
            border-radius: 12px;
            box-shadow: 0 0 0 5px rgba(74, 144, 226, 0.2);
        }

        .introjs-bullets ul li a {
            background: #cbd5e0;
            width: 12px;
            height: 12px;
            transition: all 0.2s ease;
        }

        .introjs-bullets ul li a.active {
            background: var(--primary-color);
            width: 24px;
            border-radius: 6px;
        }

        /* Tour notification toast */
        .tour-notification {
            position: fixed;
            bottom: 100px;
            right: 24px;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
            padding: 20px 28px;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(26, 54, 93, 0.4);
            z-index: 99999;
            display: flex;
            align-items: center;
            gap: 16px;
            animation: slideInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 380px;
        }

        .tour-notification i {
            font-size: 32px;
            color: var(--primary-color);
        }

        .tour-notification-content {
            flex: 1;
        }

        .tour-notification-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .tour-notification-text {
            font-size: 13px;
            opacity: 0.85;
            line-height: 1.5;
        }

        .tour-notification-close {
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .tour-notification-close:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.1);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Tour step counter */
        .tour-step-counter {
            font-size: 12px;
            color: #a0aec0;
            font-weight: 500;
        }
    </style>
    <script>
        // IntroJS Tour Configuration
        const TOUR_KEY = 'whenceLearnTourCompleted';
        
        // Define enhanced tour steps for different pages with better content
        const tours = {
            // Dashboard tour - enhanced with more steps and better content
            dashboard: [
                {
                    element: '#learning-logo',
                    title: '🎓 Welcome to Whence Learn!',
                    intro: 'Your centralized learning management system. This platform helps you watch learning materials, access courses, track progress, and earn certificates - all in one place!'
                },
                {
                    element: '.learning-nav',
                    title: '📋 Navigation Menu',
                    intro: 'Quick access to Dashboard, Manage Courses, Students, and Trainers. Use these links to navigate between different sections of the platform.'
                },
                {
                    element: '#stats-grid',
                    title: '📊 Your Learning Dashboard',
                    intro: 'Get an instant overview of your learning journey: courses enrolled, courses completed, total learning hours, and certificates earned.'
                },
                {
                    element: '.course-card:first-child',
                    title: '📚 Course Cards',
                    intro: 'Each card represents a course. Click on any course to continue learning, view details, or start a new course. Progress bars show completion status.'
                },
                {
                    element: '#learning-sidebar',
                    title: '🧭 Navigation Sidebar',
                    intro: 'Your main navigation hub! Access My Courses, Calendar, Progress, Certificates, Training Materials, and Categories from here.'
                },
                {
                    element: '.sidebar-section:first-child',
                    title: '⚡ Quick Links',
                    intro: 'Jump to frequently visited sections. Track upcoming lessons, view your certificates, and access training materials quickly.'
                }
            ],
            // Courses page tour
            courses: [
                {
                    element: '.page-header',
                    title: '📖 My Learning Courses',
                    intro: 'Welcome to your personal course library! Browse all your enrolled courses, see progress, and continue where you left off.'
                },
                {
                    element: '#courses-grid',
                    title: '🎯 Course Cards Overview',
                    intro: 'Each card displays: course title, description, category, progress percentage, and duration. Look for the progress bar to see completion status.'
                },
                {
                    element: '.course-card:first-child',
                    title: '▶️ Start Learning',
                    intro: 'Click any course card to enter the classroom. Your progress is automatically saved, so you can always continue from where you stopped.'
                },
                {
                    element: '.sidebar-section:first-child',
                    title: '🔄 Filter & Sort',
                    intro: 'Use sidebar options to filter courses by category or search for specific courses. Your enrolled courses are marked with your progress.'
                }
            ],
            // Course details/management tour
            manageCourses: [
                {
                    element: '.page-header',
                    title: '⚙️ Course Management',
                    intro: 'Manage all courses in the system. View course details, track enrollments, and monitor topic completion rates.'
                },
                {
                    element: '#coursesTable',
                    title: '📋 Course Listing',
                    intro: 'This table shows all courses with key information: title, type, category, creator, enrolled users count, topics, and status.'
                },
                {
                    element: 'tbody tr:first-child',
                    title: '👁️ View Course Details',
                    intro: 'Click the "View Details" button to see complete course information including topics, resources, and enrolled students.'
                }
            ],
            // Training Materials tour
            trainingMaterials: [
                {
                    element: '.page-header',
                    title: '📁 Training Materials Library',
                    intro: 'Access and manage institutional learning resources. Upload and organize documents, videos, audio files, and presentations.'
                },
                {
                    element: '#materials-grid',
                    title: '📚 Material Categories',
                    intro: 'Training materials are organized by type and department. Find PDFs, videos, audio content, and presentations easily.'
                },
                {
                    element: '.sidebar-section:first-child',
                    title: '🔍 Quick Access',
                    intro: 'Use the sidebar to filter materials by category, department, or type. Search for specific content quickly.'
                }
            ],
            // Training Materials Create tour
            trainingMaterialsCreate: [
                {
                    element: '.page-header',
                    title: '➕ Create New Course',
                    intro: 'Start creating a new course by filling in the basic information. Add a compelling title and description to attract learners.'
                },
                {
                    element: '#step-1',
                    title: '📝 Course Information',
                    intro: 'Fill in: course title, detailed description, material type, and target audience. Choose appropriate categories for discoverability.'
                },
                {
                    element: '#step-2',
                    title: '📚 Add Topics',
                    intro: 'Create multiple topics within your course. Each topic can include: video, audio, PDF, PPT, or document resources. Add quizzes for assessment.'
                },
                {
                    element: '#step-3',
                    title: '✅ Review & Publish',
                    intro: 'Review your course details, set it as active or draft, and publish when ready. Students can then enroll and start learning.'
                }
            ],
            // Settings tour
            settings: [
                {
                    element: '.page-header',
                    title: '⚙️ Platform Settings',
                    intro: 'Configure and manage your learning platform. Set up categories, manage users, and customize the learning experience.'
                },
                {
                    element: '#setting-categories',
                    title: '📂 Course Categories',
                    intro: 'Organize courses into categories for better navigation. Create, edit, or reorder categories to suit your organizational needs.'
                }
            ],
            // Classroom tour
            classroom: [
                {
                    element: '#classroom-header',
                    title: '🏫 Course Classroom',
                    intro: 'Welcome to the learning classroom! View the course title, track your overall progress, and access navigation controls.'
                },
                {
                    element: '#classroom-sidebar',
                    title: '📑 Course Topics List',
                    intro: 'All course topics are listed here. Click any topic to view its content. Completed topics show a checkmark, current topic is highlighted.'
                },
                {
                    element: '#content-area',
                    title: '📺 Learning Content',
                    intro: 'This is where the magic happens! Watch videos, read PDFs, listen to audio, or view presentations. Your progress is tracked automatically.'
                }
            ],
            // Calendar tour
            calendar: [
                {
                    element: '.page-header',
                    title: '📅 Learning Calendar',
                    intro: 'Plan your learning schedule! View upcoming lessons, deadlines, and important dates all in one place.'
                },
                {
                    element: '.calendar-grid',
                    title: '📆 Calendar View',
                    intro: 'Navigate through months to see scheduled lessons. Click on dates to see what courses have activities or deadlines.'
                }
            ],
            // Progress tour
            progress: [
                {
                    element: '.page-header',
                    title: '📈 Learning Progress',
                    intro: 'Track your learning journey! View detailed statistics about your completed courses, time spent learning, and achievements.'
                },
                {
                    element: '.stats-grid',
                    title: '📊 Progress Statistics',
                    intro: 'Key metrics at a glance: courses completed, certificates earned, learning hours, and current learning streak.'
                },
                {
                    element: '.progress-chart',
                    title: '📉 Progress Charts',
                    intro: 'Visual representations of your learning progress over time. See how your learning habits have evolved.'
                }
            ],
            // Certificates tour
            certificates: [
                {
                    element: '.page-header',
                    title: '🏆 My Certificates',
                    intro: 'Congratulations on your achievements! View and download your earned certificates of completion.'
                },
                {
                    element: '.certificate-grid',
                    title: '📜 Certificate Gallery',
                    intro: 'Each certificate shows: course name, completion date, and credential ID. Download or share your achievements.'
                }
            ],
            // Students management tour
            manageStudents: [
                {
                    element: '.page-header',
                    title: '👥 Student Management',
                    intro: 'Manage all enrolled students in the system. View their progress, track completion rates, and monitor engagement.'
                },
                {
                    element: '#studentsTable',
                    title: '📋 Student Listing',
                    intro: 'View all students with details: name, email, enrolled courses, progress, and enrollment date.'
                }
            ],
            // Trainers management tour
            manageTrainers: [
                {
                    element: '.page-header',
                    title: '👨‍🏫 Trainer Management',
                    intro: 'Manage trainers and instructors in the system. View their courses, student count, and performance metrics.'
                },
                {
                    element: '#trainersTable',
                    title: '📋 Trainer Listing',
                    intro: 'View all trainers with their details: name, email, assigned courses, and total students enrolled.'
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
                let tourIcon = 'fa-graduation-cap';
                
                if (currentPath.includes('/training-materials/create')) {
                    tourSteps = tours.trainingMaterialsCreate;
                    tourIcon = 'fa-plus-circle';
                } else if (currentPath.includes('/training-materials')) {
                    tourSteps = tours.trainingMaterials;
                    tourIcon = 'fa-folder-open';
                } else if (currentPath.includes('/settings/students')) {
                    tourSteps = tours.manageStudents;
                    tourIcon = 'fa-users';
                } else if (currentPath.includes('/settings/teachers') || currentPath.includes('/settings/trainers')) {
                    tourSteps = tours.manageTrainers;
                    tourIcon = 'fa-chalkboard-teacher';
                } else if (currentPath.includes('/settings/courses')) {
                    tourSteps = tours.manageCourses;
                    tourIcon = 'fa-cogs';
                } else if (currentPath.includes('/settings')) {
                    tourSteps = tours.settings;
                    tourIcon = 'fa-sliders-h';
                } else if (currentPath.includes('/classroom')) {
                    tourSteps = tours.classroom;
                    tourIcon = 'fa-school';
                } else if (currentPath.includes('/calendar')) {
                    tourSteps = tours.calendar;
                    tourIcon = 'fa-calendar-alt';
                } else if (currentPath.includes('/progress')) {
                    tourSteps = tours.progress;
                    tourIcon = 'fa-chart-line';
                } else if (currentPath.includes('/certificates')) {
                    tourSteps = tours.certificates;
                    tourIcon = 'fa-award';
                } else if (currentPath.includes('/courses')) {
                    tourSteps = tours.courses;
                    tourIcon = 'fa-book-open';
                }
                
                // Show tour notification first
                showTourNotification(tourIcon, tourSteps.length);
                
                // Start the tour with IntroJS after a short delay
                setTimeout(() => {
                    introJs()
                        .setOptions({
                            steps: tourSteps,
                            showBullets: true,
                            showProgress: true,
                            scrollToElement: true,
                            scrollTo: 'element',
                            tooltipClass: 'introjs-tooltip',
                            highlightClass: 'introjs-highlight',
                            buttonClass: 'introjs-button',
                            prevLabel: 'Previous',
                            nextLabel: 'Next',
                            skipLabel: 'x',
                            doneLabel: 'Get Started'
                        })
                        .oncomplete(function() {
                            localStorage.setItem(TOUR_KEY, 'true');
                            showFlashMessage('success', '🎉 Tour Complete!', 'You\'re all set! Enjoy your learning journey on Whence Learn.', 'fa-star');
                        })
                        .onskip(function() {
                            localStorage.setItem(TOUR_KEY, 'true');
                        })
                        .start();
                }, 1500);
            }
        }

        // Show tour notification
        function showTourNotification(icon, stepCount) {
            // Remove existing notification if any
            const existing = document.querySelector('.tour-notification');
            if (existing) existing.remove();
            
            const iconMap = {
                'fa-graduation-cap': '🎓',
                'fa-book-open': '📖',
                'fa-folder-open': '📁',
                'fa-plus-circle': '➕',
                'fa-cogs': '⚙️',
                'fa-sliders-h': '⚙️',
                'fa-school': '🏫',
                'fa-calendar-alt': '📅',
                'fa-chart-line': '📈',
                'fa-award': '🏆',
                'fa-users': '👥',
                'fa-chalkboard-teacher': '👨‍🏫'
            };
            
            const notification = document.createElement('div');
            notification.className = 'tour-notification';
            notification.innerHTML = `
                <i class="fa ${icon}">${iconMap[icon] || '🎯'}</i>
                <div class="tour-notification-content">
                    <div class="tour-notification-title">Ready for a Quick Tour?</div>
                    <div class="tour-notification-text">We'll show you around in ${stepCount} easy steps</div>
                </div>
                <button class="tour-notification-close" onclick="this.parentElement.remove()">
                    <i class="fa fa-times"></i>
                </button>
            `;
            document.body.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.animation = 'slideInUp 0.3s ease reverse';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }

        // Function to restart tour manually
        window.restartTour = function() {
            localStorage.removeItem(TOUR_KEY);
            location.reload();
        };

        // Initialize tour on page load
        $(document).ready(function() {
            // Wait for all elements to be rendered
            setTimeout(initIntroTour, 800);
        });
    </script>
    
    @yield('footer-scripts')
</body>
</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> @yield('title')</title>
    @laravelPWA
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src='https://cdn.plot.ly/plotly-2.24.1.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    </link>


    <style>

       
    .ledger-toggle {
        margin: 20px 0 30px;
    }

    .toggle-wrapper {
        position: relative;
        display: inline-flex;
        background: #f4f6f9;
        border-radius: 30px;
        padding: 4px;
        box-shadow: inset 0 0 0 1px #ddd;
    }

    .toggle-btn {
        position: relative;
        z-index: 2;
        background: none;
        border: none;
        padding: 8px 20px;
        cursor: pointer;
        font-weight: 600;
        color: #555;
        outline: none;
    }

    .toggle-btn.active {
        color: #fff;
    }

    .toggle-slider {
        position: absolute;
        top: 4px;
        left: 4px;
        width: 33.333%;
        height: calc(100% - 8px);
        background: #00a65a;
        border-radius: 25px;
        transition: transform 0.3s ease;
        z-index: 1;
    }

    .toggle-wrapper[data-active="disbursements"] .toggle-slider {
        transform: translateX(100%);
    }

    .toggle-wrapper[data-active="adjustments"] .toggle-slider {
        transform: translateX(200%);
    }

    .ledger-section {
        padding: 30px 0;
    }


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
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes modalFadeIn {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Bottom Sheet Modal Styles */
        .bottom-sheet-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99998;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .bottom-sheet-overlay.active {
            display: flex;
            opacity: 1;
        }

        .bottom-sheet {
            position: fixed;
            bottom: -100%;
            left: 0;
            width: 100%;
            background: white;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -5px 30px rgba(0, 0, 0, 0.3);
            z-index: 99999;
            transition: bottom 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            max-height: 80vh;
            overflow-y: auto;
        }

        .bottom-sheet.active {
            bottom: 0;
        }

        .bottom-sheet-handle {
            width: 50px;
            height: 5px;
            background: #ddd;
            border-radius: 3px;
            margin: 15px auto;
        }

        .bottom-sheet-content {
            padding: 20px 30px 30px 30px;
        }

        .bottom-sheet-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .bottom-sheet-description {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .bottom-sheet-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #00a04a;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .bottom-sheet-btn:hover {
            background: #008a3f;
        }

        .bottom-sheet-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: #999;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .bottom-sheet-close:hover {
            color: #333;
        }

        .bg-white {
            background-color: #ffffff !important;
        }

        .bg-success {
            background-color: #5cb85c !important;
        }

        .bg-danger {
            background-color: #d9534f !important;
        }

        .bg-warning {
            background-color: #f0ad4e !important;
        }
    </style>
    <!-- Theme style -->

    <link href="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/amcharts/plugins/export/export.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/datatables.net/extensions/Buttons/css/buttons.dataTables.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables.net/extensions/Buttons/css/buttons.bootstrap.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/fancybox/jquery.fancybox.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datepicker/bootstrap-datepicker3.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/icheck/square/blue.css') }}" rel="stylesheet" type="text/css" />
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="{{ asset('assets/themes/adminlte/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('assets/themes/adminlte/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/themes/adminlte/css/custom.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery 2.2.3 -->

    
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jqueryui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    {{--Start Page header level scripts--}}
    @yield('page-header-scripts')
    {{--End Page level scripts--}}
</head>
<?php
    $userInfo = \App\Helpers\GeneralHelper::get_user_info();
    $user = $userInfo->user;
    $role = $userInfo->role;
    $office = $userInfo->office;
?>
<div class="modal fade" id="announcementModal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-header bg-primary">
        <h4 class="modal-title text-white" id="announcementTitle"></h4>
      </div>

      <div class="modal-body">
        <p id="announcementMessage"></p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="closeAnnouncement">
          Close
        </button>
      </div>
        </div>
    </div>
</div>

<style>
  #announcementModal {
    z-index: 9999;
  }
</style>
<!-- Bottom Sheet Modal -->
<div class="bottom-sheet-overlay" id="surveyBottomSheetOverlay">

    <div class="bottom-sheet" id="surveyBottomSheet">
        <button class="bottom-sheet-close" id="closeSurveyBottomSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content">
            <h3 class="bottom-sheet-title">We Value Your Feedback!</h3>
            <p class="bottom-sheet-description">
                Help us improve our services by taking a quick survey. Your feedback is important to us and will help us serve you better.
            </p>
            <a href="#" class="bottom-sheet-btn" id="surveyLink">Take Survey</a>
        </div>
    </div>
</div>

<!-- Tools Menu Bottom Sheet -->
<div class="bottom-sheet-overlay" id="toolsBottomSheetOverlay">
    <div class="bottom-sheet" id="toolsBottomSheet">
        <button class="bottom-sheet-close" id="closeToolsBottomSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content">
            <h3 class="bottom-sheet-title">Tools</h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                @if($role && in_array($role, ['1','4','6','9','10']))
                <!-- <a href="https://erp.whencefinancesystem.com/app" title="comming soon" target="_blank" style="text-decoration: none; color: #333; text-align: center; padding: 10px; border-radius: 8px; background: #f8f9fa; transition: background 0.3s;">
                    <i class="fa fa-cogs" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                    <span style="font-size: 12px;">ERPNext</span>
                </a> -->
                @endif

                <a href="{{ url('learning') }}" title="Whence Learn" style="text-decoration: none; color: #333; text-align: center; padding: 8px; border-radius: 8px; background: transparent; display: inline-block; transition: all 0.3s;">
                    <img src="{{ asset('images/education.gif') }}" alt="Education" style="width: 36px; height: 36px; display: block; margin: 0 auto 4px;">
                    <span style="font-size: 11px; font-weight: 500;">Whence Learn</span>
                </a>

                <a href="https://meet.google.com" target="_blank" style="text-decoration: none; color: #333; text-align: center; padding: 10px; border-radius: 8px; background: #f8f9fa; transition: background 0.3s;">
                    <img src="{{ asset('anim/conference.gif') }}" alt="Education" style="width: 36px; height: 36px; display: block; margin: 0 auto 4px;">
                    <span style="font-size: 12px;">Google Meet</span>
                </a>

                <a href="https://mail.google.com" target="_blank" style="text-decoration: none; color: #333; text-align: center; padding: 10px; border-radius: 8px; background: #f8f9fa; transition: background 0.3s;">
                    <img src="{{ asset('anim/gmail.gif') }}" alt="Education" style="width: 36px; height: 36px; display: block; margin: 0 auto 4px;">
                    <span style="font-size: 12px;">Gmail</span>
                </a>

                <a href="{{ url('logout') }}" style="text-decoration: none; color: #fff; text-align: center; padding: 10px; border-radius: 8px; background: #dc3545; transition: background 0.3s;">
                    <i class="fa fa-sign-out" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                    <span style="font-size: 12px;">Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>


<body class="hold-transition sidebar-mini" style="background-color:#000041; color: #000000;">
    <div class="wrapper">

        <header class="main-header">
            <a id="hide-in-mobile-view" href="{{url('/')}}" class="logo"
                style="display: flex; align-items: center; height: 50px; padding: 0 10px;">
                <img src="{{ asset('images/w/logo.jpg') }}" alt="Whence Finance Logo"
                    style="width: 40px; height: 40px; border-radius: 30%; object-fit: cover; margin-right: 10px;">
                <span style="color: #ffffff; font-weight: bold; font-size: 12px; white-space: nowrap;">Whence
                    Finance
                </span>
            </a>
            <!-- Mobile Header (visible on small screens ≤767px) -->
            <div class="mobile-header" style="display: none; justify-content: center; align-items: center; height: 50px; width: 100%; position: relative;">
                <a href="{{url('/')}}" class="logo"
                    style="display: flex; align-items: center; height: 50px; padding: 0 10px;">
                    <img src="{{ asset('images/w/logo.jpg') }}" alt="Whence Finance Logo"
                        style="width: 40px; height: 40px; border-radius: 30%; object-fit: cover; margin-right: 10px;">
                    <span style="color: #ffffff; font-weight: bold; font-size: 12px; white-space: nowrap;">Whence
                        Finance
                    </span>
                </a>
                <!-- Add a Notification  -->
                <a href="#" onclick="toggleNotificationDropdown(event); return false;" style="color: #ffffff; position: absolute; right: 70px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; background: rgba(255,255,255,0.1); text-decoration: none; border: none; cursor: pointer;">
                    <i class="fa fa-bell" style="font-size: 18px;"></i>
                    <span id="notificationBadge" style="position: absolute; top: -5px; right: -5px; background: #ff4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; display: none;">0</span>
                </a>
                <!-- Tools Menu (visible on mobile) -->
                <a href="#" onclick="toggleUserDropdown(event); return false;" style="color: #ffffff; position: absolute; right: 20px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; background: rgba(255,255,255,0.1); text-decoration: none; border: none; cursor: pointer;">
                    <i class="fa fa-cog" style="font-size: 18px;"></i>
                </a>
            </div>
            <style>
                /* Desktop header: visible on screens >= 768px */
                @media (min-width: 768px) {
                    .desktop-header {
                        display: block !important;
                    }
                    .mobile-header {
                        display: none !important;
                    }
                }
                
                /* Mobile header: visible on screens <= 767px */
                @media (max-width: 767px) {
                    .desktop-header {
                        display: none !important;
                    }
                    .mobile-header {
                        display: flex !important;
                    }
                    #hide-in-mobile-view {
                        display: none !important;
                    }
                }
                
                /* Hide desktop navbar elements on mobile */
                @media (max-width: 767px) {
                    .main-header .navbar > .navbar-custom-menu,
                    .main-header .navbar > .navbar-search {
                        display: none !important;
                    }
                    .main-header .navbar {
                        display: flex !important;
                        justify-content: flex-start !important;
                    }
                }
            </style>
            
            <style>
                /* Hide mobile wrench button on desktop (>= 768px) */
                @media (min-width: 768px) {
                    .mobile-wrench-btn {
                        display: none !important;
                    }
                }
                
                /* Show mobile wrench button only on mobile (<= 767px) */
                @media (max-width: 767px) {
                    .mobile-wrench-btn {
                        display: flex !important;
                    }
                }
            </style>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" style="background-color:#00a04a; display: flex; justify-content: space-between; align-items: center;">
            <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="color: #ffffff;">
                    <span class="sr-only">
                        Toggle navigation
                    </span>
                </a>

                @if($role && in_array($role, ['1', '6', '4', '9', '10']))
                <!-- Search Bar -->
                <div class="navbar-search" style="flex: 1; display: flex; justify-content: center; position: relative;">
                    <div class="input-group" style="width: 350px;">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-type-btn" style="border-radius: 25px 0 0 25px; border-right: none; background: #f8f9fa; color: #333; font-weight: bold; margin-top: 0.55%;">
                                Staff <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" id="search-type-menu">
                                <li><a href="#" data-value="staff">Staff</a></li>
                                <li><a href="#" data-value="client">Client</a></li>
                            </ul>
                        </div>
                        <input type="text" id="user-search" placeholder="Search for staff..." class="form-control" style="border-radius: 0 25px 25px 0; border-left: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <span class="input-group-addon" style="border-radius: 0 25px 25px 0; border-left: none; background: transparent; border: none;">
                            <i class="fa fa-search" style="color: #666;"></i>
                        </span>
                    </div>
                    <div id="search-results" style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); background: white; border: 1px solid #ddd; width: 350px; max-height: 250px; overflow-y: auto; display: none; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; margin-top: 5px;"></div>
                </div>
                @else
                <div class="col-md-10"></div>
                @endif

                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <!-- Add a Notification  -->
                    <a href="#" onclick="toggleNotificationDropdown(event); return false;" style="margin-top:2px; margin-right: 90px; color: #ffffff; position: absolute; right: 70px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; background: rgba(255,255,255,0.1); text-decoration: none; border: none; cursor: pointer;">
                        <i class="fa fa-bell" style="font-size: 18px;"></i>
                        <span id="notificationBadgeDesk" style="position: absolute; top: -5px; right: -5px; background: #ff4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; display: none;">0</span>
                    </a>
                    <ul class="nav navbar-nav">
                        @if($user)
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-user"></i>
                                    <span class="hidden-xs" style="color: #ffffff;">{{ $user->first_name }}
                                        {{ $user->last_name }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header" style="height: auto; padding-bottom: 20px;">
                                        <i class="fa fa-user" style="font-size: 60px"></i>
                                        <p style="color: #000000; margin-bottom: 0;">
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </p>
                                        <p style="color: #444; font-size: 13px; margin-top: 2px; margin-bottom: 5px;">
                                            <i class="fa fa-briefcase"></i> {{ $user->roles->first() ? $user->roles->first()->name : 'Staff' }}
                                        </p>
                                        <p style="color: #000000; font-weight: bold; margin-bottom: 5px;">
                                            {{  isset($user->office) ? $user->office->name : ''}}
                                            <small style="color: #00b30fff;">{{ $user->province ? ' ' . $user->province->name : '' }} PROVINCE</small>
                                        </p>
                                        <small style="color: #666;">Member since {{ $user->created_at->format('M. Y') }}</small>
                                    </li>

                                    <!-- External System Links -->
                                    <li class="user-body" style="padding: 15px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                                            @if($role && in_array($role, ['1','4','6','9','10']))
                                            <!-- <a href="https://erp.whencefinancesystem.com/app" title="comming soon" target="_blank" style="text-decoration: none; color: #333; text-align: center; padding: 10px; border-radius: 8px; background: #f8f9fa; transition: background 0.3s;">
                                                <i class="fa fa-cogs" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                                                <span style="font-size: 12px;">ERPNext</span>
                                            </a> -->
                                            @endif

                                            <a href="{{ url('learning') }}" title="Whence Learn" style="text-decoration: none; color: #333; text-align: center; padding: 8px; border-radius: 8px; background: transparent; display: inline-block; transition: all 0.3s;">
                                                <img src="{{ asset('images/education.gif') }}" alt="Education" style="width: 36px; height: 36px; display: block; margin: 0 auto 4px;">
                                                <span style="font-size: 11px; font-weight: 500;">Whence Learn</span>
                                            </a>

                                            <a href="https://meet.google.com" target="_blank" style="text-decoration: none; color: #333; text-align: center; padding: 10px; border-radius: 8px; background: #f8f9fa; transition: background 0.3s;">
                                                <i class="fa fa-video-camera" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                                                <span style="font-size: 12px;">Google Meet</span>
                                            </a>

                                            <a href="https://mail.google.com" target="_blank" style="text-decoration: none; color: #333; text-align: center; padding: 10px; border-radius: 8px; background: #f8f9fa; transition: background 0.3s;">
                                                <i class="fa fa-envelope" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                                                <span style="font-size: 12px;">Gmail</span>
                                            </a>
                            

                                        </div>
                                    </li>

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="{{ url('user/edit_profile') }}"
                                                class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="{{ url('logout') }}" class="btn btn-default btn-flat">logout</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>

            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        @if($user && Sentinel::inRole('client'))
            @include('menu.client')
        @elseif($user && Sentinel::inRole('referral'))
            @include('menu.intern')
        @else
            @include('menu.admin')
        @endif
        <!-- end Left side column. contains the logo and sidebar -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header" style="min-height: 30px">
                <h1>
                    @yield('title')
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{ url('dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">@yield('title')</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                @if(Session::has('flash_notification'))
                    @foreach(session('flash_notification') as $notification)
                        <script>toastr.{{$notification->level }}('{{ $notification->message }}', 'Response Status')</script>
                    @endforeach
                @endif
                @if (isset($msg))
                    <script>
                        toastr.success('{{ $msg }}', 'Success');
                    </script>
                @endif
                @if (isset($error))
                    <script>
                        toastr.error('{{ $error }}', 'Error');
                    </script>
                @endif
                @if (count($errors) > 0)
                    <script>
                        @foreach ($errors->all() as $error)
                            toastr.error('{{ $error }}', 'Validation Error');
                        @endforeach
                    </script>
                @endif
                @php
                    $user = Sentinel::getUser();
                    $showInductionModal = false;
                    $showPolicyModal = false;

                    if ($user && request()->route() && request()->route()->getName() !== 'policies.view_policies') {
                        // Induction Check
                        if (!$user->has_seen_induction) {
                            $showInductionModal = true;
                        } else {
                            // Policy Check
                            $showPolicyModal = !\App\Models\InductionChecklist::hasCompletedPolicies($user->id);
                        }
                    }
                @endphp
                @if($showInductionModal && $role !== 11)
                    @include('partials.induction_modal')
                @elseif($showPolicyModal)
                    <!-- Policy Response Required Modal -->
                    <div id="policyModal"
                        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; display: flex; align-items: center; justify-content: center; animation: modalFadeIn 0.4s ease-out;">
                        <div
                            style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 500px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                            <h3 style="margin-bottom: 20px; color: #333;">Policy Acknowledgment Required</h3>
                            <p style="margin-bottom: 30px; color: #666;">You have unread company policies that require your
                                acknowledgment. Please review and respond to them.</p>
                            <a href="{{ route('policies.view_policies') }}" class="btn btn-primary btn-lg"
                                style="padding: 10px 30px; font-size: 16px;">Review Policies</a>
                        </div>
                    </div>
                    <script>
                        // Prevent closing the modal
                        document.getElementById('policyModal').addEventListener('click', function (event) {
                            event.stopPropagation();
                        });
                        document.addEventListener('keydown', function (event) {
                            if (event.key === 'Escape') {
                                event.preventDefault();
                            }
                        });
                    </script>
                @endif
                @yield('content')
                <!-- @include('partials.induction_checklist_popup') -->
            </section>
            <!-- /.content -->
            <div id="loader-wrapper">
                <div id="loader"></div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer hidden-print">
            <strong>Copyright &copy; {{ date("Y") }}<a href="https://whencegroup.com/" target="_blank">Whence Financial
                    Services</a>.</strong>
            All rights
            reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- FastClick -->
    <script src="{{ asset('assets/plugins/fastclick/lib/fastclick.js') }}"></script>
    <script src="{{ asset('assets/plugins/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/moment/js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-bs/js/dataTables.bootstrap.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/themes/adminlte/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vue.js') }}"></script>

    <!-- SlimScroll 1.3.0 -->

    <script>
        jQuery.validator.setDefaults({
            // Different components require proper error label placement
            ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

        // Configure toastr options
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

        var state = 0;


        $(window).on('load', function () {
            $('#loader-wrapper').fadeOut(2000);
        });

    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const url = "https://lms2backend.whencefinancesystem.com/announcement";

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data || !data.title || !data.message || !data.end_date) return;

                    // Convert ISO date to YYYY-MM-DD
                    const endDate = new Date(data.end_date).toISOString().split('T')[0];
                    const today = new Date().toISOString().split('T')[0];

                    // Stop if campaign expired
                    if (today > endDate) return;



                    // Fill modal
                    document.getElementById("announcementTitle").textContent = data.title;
                    document.getElementById("announcementMessage").textContent = data.message;

                    // Show modal
                    $('#announcementModal').modal('show');

                    // Close button
                    document.getElementById("closeAnnouncement").addEventListener("click", function () {
                        $('#announcementModal').modal('hide');
                    });

                })
                .catch(err => console.error("Announcement fetch failed:", err));

        });
    </script>


    @if ($role && in_array($role, ['1', '6', '4']))
        <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
        <!-- Pusher JS for Laravel WebSockets (local) -->
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script src="{{ asset('js/websocket-client.js') }}"></script>
        <script>

            var office = <?php    echo json_encode($office); ?>;
            const socket = io('https://notifications.whencefinancesystem.com');

            socket.on('connect', () => {
                console.log('Connected to Socket.io server', socket.id);
            });

            function showNotification(data) {
                // Play notification sound
                const audio = new Audio('https://www.myinstants.com/media/sounds/undertakers-bell_2UwFCIe.mp3'); // You can replace this URL with your own sound
                audio.play();

                // Create the container div
                const div = document.createElement('div');
                div.style.cssText = `
                    position: fixed;
                    top: 25px;
                    right: 25px;
                    width: 320px;
                    background: #ffffff;
                    border-radius: 12px;
                    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
                    overflow: hidden;
                    z-index: 9999;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    color: #333;
                    transform: translateX(150%);
                    opacity: 0;
                    transition: all 0.5s ease;
                `;

                // Inner content
                div.innerHTML = `
                    <div style="padding: 15px 20px; border-left: 6px solid #007bff;">
                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #007bff;">
                            TRANSACTION ALERT 🔔
                        </h4>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Type:</strong> ${data.type || 'N/A'}</p>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Amount:</strong> ${data.amount || 'N/A'}</p>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Client:</strong> ${data.client || 'N/A'}</p>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Added by:</strong> ${data.created_by || 'N/A'}</p>
                        <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
                            <button style="
                                background: #007bff;
                                color: #fff;
                                border: none;
                                border-radius: 6px;
                                padding: 6px 14px;
                                font-size: 13px;
                                cursor: pointer;
                                transition: background 0.3s ease;
                            ">Close</button>
                        </div>
                    </div>
                `;

                // Add to document
                document.body.appendChild(div);

                // Slide in animation
                requestAnimationFrame(() => {
                    div.style.transform = "translateX(0)";
                    div.style.opacity = "1";
                });

                // Close button
                div.querySelector('button').addEventListener('click', () => {
                    div.style.transform = "translateX(150%)";
                    div.style.opacity = "0";
                    setTimeout(() => div.remove(), 400);
                });
            }


                       function showTicketNotification(data) {
                // Play notification sound
                const audio = new Audio('https://www.myinstants.com/media/sounds/undertakers-bell_2UwFCIe.mp3'); // You can replace this URL with your own sound
                audio.play();

                // Create the container div
                const div = document.createElement('div');
                div.style.cssText = `
                    position: fixed;
                    top: 25px;
                    right: 25px;
                    width: 320px;
                    background: #ffffff;
                    border-radius: 12px;
                    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
                    overflow: hidden;
                    z-index: 9999;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    color: #333;
                    transform: translateX(150%);
                    opacity: 0;
                    transition: all 0.5s ease;
                `;

                // Inner content
                div.innerHTML = `
                    <div style="padding: 15px 20px; border-left: 6px solid #007bff;">
                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #007bff;">
                            NEW TICKET ALERT 🔔
                        </h4>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Name:</strong> ${data.name || 'N/A'}</p>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Type:</strong> ${data.type || 'N/A'}</p>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Created by:</strong> ${data.user || 'N/A'}</p>
                        <p style="margin: 3px 0; font-size: 14px;"><strong>Branch:</strong> ${data.office || 'N/A'}</p>
                        <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
                            <button style="
                                background: #007bff;
                                color: #fff;
                                border: none;
                                border-radius: 6px;
                                padding: 6px 14px;
                                font-size: 13px;
                                cursor: pointer;
                                transition: background 0.3s ease;
                            ">Close</button>
                        </div>
                    </div>
                `;

                // Add to document
                document.body.appendChild(div);

                // Slide in animation
                requestAnimationFrame(() => {
                    div.style.transform = "translateX(0)";
                    div.style.opacity = "1";
                });

                // Close button
                div.querySelector('button').addEventListener('click', () => {
                    div.style.transform = "translateX(150%)";
                    div.style.opacity = "0";
                    setTimeout(() => div.remove(), 400);
                });
            }




            function showNotificationTest(data) {
                // Play notification sound
                const audio = new Audio('https://www.myinstants.com/media/sounds/mario-jump.mp3');
                audio.play();

                // Create the container div
                const div = document.createElement('div');
                div.style.cssText = `
                    position: fixed;
                    top: 25px;
                    right: 25px;
                    width: 320px;
                    background: #ffffff;
                    border-radius: 12px;
                    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
                    overflow: hidden;
                    z-index: 9999;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    color: #333;
                    transform: translateX(150%);
                    opacity: 0;
                    transition: all 0.5s ease;
                `;

                // Inner content
                div.innerHTML = `
                    <div style="padding: 15px 20px; border-left: 6px solid #007bff;">
                        <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700; color: #007bff;">
                            NOTIFICATION TEST 🔔
                        </h4>
                        <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
                            <button style="
                                background: #007bff;
                                color: #fff;
                                border: none;
                                border-radius: 6px;
                                padding: 6px 14px;
                                font-size: 13px;
                                cursor: pointer;
                                transition: background 0.3s ease;
                            ">Close</button>
                        </div>
                    </div>
                `;

                // Add to document
                document.body.appendChild(div);

                // Slide in animation
                requestAnimationFrame(() => {
                    div.style.transform = "translateX(0)";
                    div.style.opacity = "1";
                });

                // Close button click handler
                const closeNotification = () => {
                    div.style.transform = "translateX(150%)";
                    div.style.opacity = "0";
                    setTimeout(() => div.remove(), 400);
                };

                div.querySelector('button').addEventListener('click', closeNotification);

                // Auto-close after 4 seconds
                setTimeout(closeNotification, 10000);
            }



            socket.on('loan.created', (data) => {
                if (data.office_id == office) {
                    showNotification(data)
                }

            });

              socket.on('ticket.created', (data) => {
                  if ("{{ $role }}" === "1") {
                showTicketNotification(data)
                  }
            });
           

            socket.on('notification.created', (data) => {
                showNotificationTest()
            });

        </script>
    @endif
    @yield('footer-scripts')
    <!-- ChartJS 1.0.1 -->
    <script src="{{ asset('assets/themes/adminlte/js/custom.js') }}">
    </script>

    @if($role !== 11)
        @include('partials.profile_completion_wizard')
    @endif

    
    <!-- Floating SMS Button -->
    @if($role == 1)
    <div id="sms-floating-btn" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background: #00a65a; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 1000; transition: all 0.3s;">
        <i class="fa fa-envelope" style="color: white; font-size: 24px;"></i>
    </div>
    @endif

    <!-- SMS Modal -->
    <div id="sms-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1001;">
        <div style="background: white; padding: 20px; border-radius: 10px; width: 90%; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <h4 style="margin: 0 0 15px 0; color: #333;">Send SMS </h4>
            <i>Pending to be Whitelisted</i>
            <form id="sms-form">
                <div style="margin-bottom: 15px;">
                    <label for="sms-phone" style="display: block; margin-bottom: 5px; font-weight: bold;">Phone Number:</label>
                    <input disabled type="text" id="sms-phone" name="phone" placeholder="Enter phone number" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sms-message" style="display: block; margin-bottom: 5px; font-weight: bold;">Message:</label>
                    <textarea disabled id="sms-message" name="message" placeholder="Enter message" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;" required></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" id="sms-cancel" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 10px 20px; background: #00a65a; color: white; border: none; border-radius: 5px; cursor: pointer;">Send</button>
                </div>
            </form>
            <div id="sms-response" style="margin-top: 15px; display: none;"></div>
        </div>
    </div>

    <script>
        // SMS Modal Script
        $(document).ready(function() {
            $('#sms-floating-btn').on('click', function() {
                $('#sms-modal').css('display', 'flex');
            });

            $('#sms-cancel').on('click', function() {
                $('#sms-modal').hide();
                $('#sms-form')[0].reset();
                $('#sms-response').hide();
            });

            $('#sms-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                    $('#sms-form')[0].reset();
                    $('#sms-response').hide();
                }
            });

            $('#sms-form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $('#sms-response').html('<div style="color: #007bff;">Sending...</div>').show();

                $.ajax({
                    url: '/api/send-sms',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#sms-response').html('<div style="color: #28a745;">SMS sent successfully!</div>');
                            $('#sms-form')[0].reset();
                        } else {
                            $('#sms-response').html('<div style="color: #dc3545;">Error: ' + (response.error || 'Unknown error') + '</div>');
                        }
                    },
                    error: function(xhr) {
                        $('#sms-response').html('<div style="color: #dc3545;">Error: ' + xhr.responseJSON?.message || 'Failed to send SMS' + '</div>');
                    }
                });
            });
        });

        $(document).ready(function() {
            var currentSearchType = 'staff';

            function updateSearchType(type) {
                currentSearchType = type;
                var label = type === 'staff' ? 'Staff' : 'Client';
                $('#search-type-btn').html(label + ' <span class="caret"></span>');
                var placeholder = 'Search for ' + (type === 'staff' ? 'staff' : 'clients') + '...';
                $('#user-search').attr('placeholder', placeholder);
            }

            $('#search-type-menu a').on('click', function(e) {
                e.preventDefault();
                var type = $(this).data('value');
                updateSearchType(type);
            });

            updateSearchType('staff'); // Initial

            $('#user-search').on('input', function() {
                var query = $(this).val();
                var url = currentSearchType === 'staff' ? '/user/search' : '/client/search';
                if (query.length > 2) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        data: { q: query },
                        success: function(data) {
                            var results = $('#search-results');
                            results.empty();
                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    var itemDiv = $('<div class="search-item" style="padding: 12px 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.2s; color: #333; font-size: 14px;"></div>');
                                    if (currentSearchType === 'staff') {
                                        itemDiv.html('<strong>' + item.first_name + ' ' + item.last_name + '</strong><br><small style="color: #666;">' + item.email + ' | ' + (item.office ? item.office.name : 'No Office') + '</small>');
                                        itemDiv.on('click', function() {
                                            $('#fullscreen-loader').fadeIn(200);
                                            setTimeout(function() {
                                                window.location.href = '/user/' + item.id + '/staff_info';
                                            }, 100);
                                        });
                                    } else {
                                        itemDiv.html('<strong>' + item.first_name + ' ' + item.last_name + '</strong><br><small style="color: #666;">' + item.mobile + ' | ' + (item.office ? item.office.name : 'No Office') + '</small>');
                                        itemDiv.on('click', function() {
                                            $('#fullscreen-loader').fadeIn(200);
                                            setTimeout(function() {
                                                window.location.href = '/client/' + item.id + '/show';
                                            }, 100);
                                        });
                                    }
                                    itemDiv.hover(function() { $(this).css('background', '#f8f9fa'); }, function() { $(this).css('background', 'transparent'); });
                                    results.append(itemDiv);
                                });
                                results.show();
                            } else {
                                results.hide();
                            }
                        }
                    });
                } else {
                    $('#search-results').hide();
                }
            });

            // Hide results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.navbar-search').length) {
                    $('#search-results').hide();
                }
            });
        });
    </script>

    <!-- Survey Bottom Sheet Modal Script -->
    <script>
        $(document).ready(function() {
            // Get current route name
            var currentRoute = "{{ request()->route()->getName() }}";

            // Check if user has seen survey from server-side
            var hasSeenSurvey = {{ $user && $user->has_seen_survey ? 'true' : 'false' }};
            var role = {{ $role }};

            // Close bottom sheet when clicking close button
            $('#closeSurveyBottomSheet').on('click', function() {
                $('#surveyBottomSheetOverlay').removeClass('active');
                $('#surveyBottomSheet').removeClass('active');
            });

            // Close bottom sheet when clicking overlay
            $('#surveyBottomSheetOverlay').on('click', function(e) {
                if (e.target === this) {
                    $('#surveyBottomSheetOverlay').removeClass('active');
                    $('#surveyBottomSheet').removeClass('active');
                }
            });

            // Update survey link
            $('#surveyLink').attr('href', '{{ route('survey.show') }}');

            // Show survey 2 seconds after page load if user hasn't seen it
            if (!hasSeenSurvey && role !== 11) {
                setTimeout(function() {
                    $('#surveyBottomSheetOverlay').addClass('active');
                    $('#surveyBottomSheet').addClass('active');
                }, 2000);
            }
        });
    </script>

    <!-- Toggle User Dropdown Script -->
    <script>
        function toggleUserDropdown(event) {
            event.preventDefault();
            $('#toolsBottomSheetOverlay').toggleClass('active');
            $('#toolsBottomSheet').toggleClass('active');
        }

        // Close tools bottom sheet when clicking close button
        $('#closeToolsBottomSheet').on('click', function() {
            $('#toolsBottomSheetOverlay').removeClass('active');
            $('#toolsBottomSheet').removeClass('active');
        });

        // Close tools bottom sheet when clicking overlay
        $('#toolsBottomSheetOverlay').on('click', function(e) {
            if (e.target === this) {
                $('#toolsBottomSheetOverlay').removeClass('active');
                $('#toolsBottomSheet').removeClass('active');
            }
        });


    </script>

    <!-- Notification Count Polling Script -->
    <script>
        $(document).ready(function() {
            // Function to update notification count
            function updateNotificationCount() {
                $.ajax({
                    url: '/notification-count',
                    method: 'GET',
                    success: function(response) {
                        var count = response.count || 0;
                        var mobileBadge = $('#notificationBadge');
                        var desktopBadge = $('#notificationBadgeDesk');

                        if (count > 0) {
                            var displayCount = count > 99 ? '99+' : count;
                            mobileBadge.text(displayCount).show();
                            desktopBadge.text(displayCount).show();
                        } else {
                            mobileBadge.hide();
                            desktopBadge.hide();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching notification count:', xhr);
                    }
                });
            }

            // Initial load
            updateNotificationCount();

            // Poll every 30 seconds
            setInterval(updateNotificationCount, 30000);
        });
    </script>

    @include('components.notification')

</body>
</html>

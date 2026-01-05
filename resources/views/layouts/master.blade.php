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
    <link rel="stylesheet" href="dist/css/adminlte.min.css"></link>


<style>

#loader-wrapper{
    position:fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: white;
    transition: opacity 0.75s, visibility 0.75s;
    z-index:99999;
   
}

#loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes modalFadeIn {
  0% { opacity: 0; transform: scale(0.9); }
  100% { opacity: 1; transform: scale(1); }
}
</style>
    <!-- Theme style -->

    <link href="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/amcharts/plugins/export/export.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables.net/extensions/Buttons/css/buttons.dataTables.css') }}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables.net/extensions/Buttons/css/buttons.bootstrap.css') }}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/fancybox/jquery.fancybox.css') }}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
          rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/datepicker/bootstrap-datepicker3.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/icheck/square/blue.css') }}" rel="stylesheet" type="text/css"/>
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
    <script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    {{--Start Page header level scripts--}}
    @yield('page-header-scripts')
    {{--End Page level scripts--}}
</head>          
<?php
$user = Sentinel::getUser();
if ($user) {
    $role = $user->role->role_id;
    $office = $user->office->id;
} else {
    $role = null;
    $office = null;
}
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





<body class="hold-transition sidebar-mini" style="background-color:#000041; color: #000000;">
<div class="wrapper">

    <header class="main-header">

        <!-- Logo -->
        <a href="{{url('/')}}" class="logo" style="display: flex; align-items: center; height: 50px; padding: 0 10px;">
         <img src="{{ asset('images/w/logo.jpg') }}" alt="Whence Finance Logo" style="width: 40px; height: 40px; border-radius: 30%; object-fit: cover; margin-right: 10px;">
         <span style="color: #ffffff; font-weight: bold; font-size: 12px; white-space: nowrap;">Whence Finance</span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" style="background-color:#00a04a;">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="color: #ffffff">
                <span class="sr-only">
                    Toggle navigation
                </span>
            </a>
           
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    @if($user)
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                            <span class="hidden-xs" style="color: #ffffff;">{{ $user->first_name }} {{ $user->last_name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <i class="fa fa-user" style="font-size: 60px"></i>

                                <p style="color: #000000;">
                                    {{  isset($user->office) ? $user->office->name : ''}}
                                </p>

                                <p style="color: #000000;">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                    <small>Member since {{ $user->created_at }}</small>
                                </p>
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
    @elseif($user && Sentinel::inRole('intern'))
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
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ $msg }}
                </div>
            @endif
            @if (isset($error))
                <div class="alert alert-error">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ $error }}
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @php
            $user = Sentinel::getUser();
            if ($user && request()->route() && request()->route()->getName() !== 'policies.view_policies') {
                $totalPolicies = \App\Models\Policy::count();
                $userResponses = \App\Models\UserPolicyResponse::where('user_id', $user->id)->count();
                $showPolicyModal = $userResponses < $totalPolicies;
            } else {
                $showPolicyModal = false;
            }
            @endphp
            @if($showPolicyModal)
            <!-- Policy Response Required Modal -->
            <div id="policyModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; display: flex; align-items: center; justify-content: center; animation: modalFadeIn 0.4s ease-out;">
                <div style="background: white; padding: 30px; border-radius: 10px; text-align: center; max-width: 500px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <h3 style="margin-bottom: 20px; color: #333;">Policy Acknowledgment Required</h3>
                    <p style="margin-bottom: 30px; color: #666;">You have unread company policies that require your acknowledgment. Please review and respond to them.</p>
                    <a href="{{ route('policies.view_policies') }}" class="btn btn-primary btn-lg" style="padding: 10px 30px; font-size: 16px;">Review Policies</a>
                </div>
            </div>
            <script>
                // Prevent closing the modal
                document.getElementById('policyModal').addEventListener('click', function(event) {
                    event.stopPropagation();
                });
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                    }
                });
            </script>
            @endif
            @yield('content')
        </section>
        <!-- /.content -->
        <div id="loader-wrapper">
            <div id="loader"></div>
    </div>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer hidden-print">

        <strong>Copyright &copy; {{ date("Y") }}<a
                    href="https://whencegroup.com/" target="_blank" >Whence Financial Services</a>.</strong>
        All rights
        reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- FastClick -->
<script src="{{ asset('assets/plugins/fastclick/lib/fastclick.js') }}"></script>
<script src="{{ asset('assets/plugins/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/moment/js/moment.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.js') }}"
        type="text/javascript"></script>
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
<script>
    
    var office = <?php echo json_encode($office); ?>;
const socket = io('https://notifications.whencefinancesystem.com');

socket.on('connect', () => {
    console.log('Connected to Socket.io server', socket.id);
});

function showNotification(data) {
    // Play notification sound
    const audio = new Audio('https://www.myinstants.com/media/sounds/mario-jump.mp3'); // You can replace this URL with your own sound
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
if(data.office_id == office){
     showNotification(data)
}

});

socket.on('notification.created',(data)=>{
    showNotificationTest()
});

</script>
@endif
@yield('footer-scripts')
<!-- ChartJS 1.0.1 -->
<script src="{{ asset('assets/themes/adminlte/js/custom.js') }}">
</script>
</body>
</html>

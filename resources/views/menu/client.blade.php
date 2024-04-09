<aside class="main-sidebar" style="color: #ffffff">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="color: #ffffff">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <i class="fa fa-user" style="font-size: 60px"></i>
            </div>
            <div class="pull-left info">
                <p>{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">

            <li class="@if(Request::is('dashboard')) active @endif">
                <a href="{{ url('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>{{trans_choice('general.dashboard',1)}}</span>
                </a>
            </li>
            <li class="@if(Request::is('portal/loan/*')) active @endif">
                <a href="{{ url('portal/loan/data') }}">
                    <i class="fa fa-money"></i>
                    <span>{{trans_choice('general.my',2)}} {{trans_choice('general.loan',2)}}</span>
                </a>
            </li>
            <li class="@if(Request::is('portal/savings*')) active @endif">
                <a href="{{ url('portal/savings/data') }}">
                    <i class="fa fa-bank"></i>
                    <span>{{trans_choice('general.my',2)}} {{trans_choice('general.savings',2)}}</span>
                </a>
            </li>
            @if(\App\Models\Setting::where('setting_key','allow_client_apply')->first()->setting_value==1)
                <li class="treeview @if(Request::is('portal/loan_application*')) active @endif">
                    <a href="#">
                        <i class="fa fa-briefcase"></i> <span>{{trans_choice('general.application',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ url('portal/loan_application/data') }}"><i
                                        class="fa fa-circle-o"></i> {{trans_choice('general.my',1)}} {{trans_choice('general.application',2)}}
                            </a></li>
                        <li><a href="{{ url('portal/loan_application/create') }}"><i
                                        class="fa fa-circle-o"></i> {{trans_choice('general.new',1)}} {{trans_choice('general.application',1)}}
                            </a></li>
                    </ul>
                </li>
            @endif


            <li class="">
                    <!-- <a href="{{ url('user/edit_my_details') }}"> -->
                    <a href="{{ url('user/my_details') }}">
                        <i class="fa fa-user"></i> <span>My information</span>
                    </a>
                </li>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

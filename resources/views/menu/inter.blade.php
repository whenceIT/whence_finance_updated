<?php
                        use App\Models\AppraisalForm;
                       
                      
                        ?>
<aside class="main-sidebar" style="color: #ffffff">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="color:#ffffff;">
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

            @if(Sentinel::hasAccess('clients'))
                <li class="treeview @if(Request::is('client/*')) active @endif
                @if(Request::is('group/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-users"></i> <span>{{trans_choice('general.client',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
               
                        @if(Sentinel::hasAccess('clients.my_clients'))
                            <li><a href="{{ url('client/my_clients') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.my',1)}} {{trans_choice('general.client',2)}}
                                    <span class="pull-right-container">
                                    <?php
                                            $staff_id = Sentinel::getUser()->id;
                                            ?>
                                        <span class="label label-danger pull-right">{{\App\Models\Client::where('staff_id',$staff_id)->where('status','active')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif
                 
                        @if(Sentinel::hasAccess('clients.create'))
                            <li><a href="{{ url('client/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.client',1)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
       
                <li class="treeview @if(Request::is('loan/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-money"></i> <span>{{trans_choice('general.loan',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">

                        @if(Sentinel::hasAccess('loans.my_loans'))
                            <li><a href="{{ url('loan/my_loans') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.my',1)}} {{trans_choice('general.active',2)}} {{trans_choice('general.loan',2)}}
                                    <span class="pull-right-container">
                                    <?php
                                            $loan_officer_id = Sentinel::getUser()->loan_officer_id;
                                            ?>
                                        <span class="label label-info pull-right">{{\App\Models\Loan::where('loan_officer_id',$loan_officer_id)->where('status','disbursed')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif

                        @if(Sentinel::hasAccess('loans.create'))
                            <li><a href="{{ url('loan/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.loan',1)}}
                                </a></li>
                        @endif
                  
                   
                    </ul>
                </li>
    
      
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

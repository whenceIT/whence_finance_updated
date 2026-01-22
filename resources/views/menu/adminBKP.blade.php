<?php
    use App\Models\AppraisalForm;               

    $userInfo = \App\Helpers\GeneralHelper::get_user_info();
    $user = $userInfo->user;
    $role = $userInfo->role;
    $office = $userInfo->office;
?>
<aside class="main-sidebar" style="color: #ffffff">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" style="color:#ffffff;">
        <!-- Sidebar user panel -->
        <div class="user-panel" style="background: linear-gradient(135deg, #667eea 0%, #100E3D 100%); padding: 10px; border-radius: 12px; margin: 15px 0px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <div class="pull-left image">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #667eea; font-size: 18px; border: 2px solid #fff;">
                    {{ substr(Sentinel::getUser()->first_name, 0, 1) . substr(Sentinel::getUser()->last_name, 0, 1) }}
                </div>
            </div>
            <div class="pull-left info" style="margin-left: 15px;">
                <p style="margin: 0; color: #fff; font-weight: bold; font-size: 14px;">{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</p>

                @if(Sentinel::getUser()->hasAccess('offices'))
                    <p style="margin: 0; color: #fff; font-size: 12px; opacity: 0.9;">{{ substr(Sentinel::getUser()->office->name ?? 'Office', 0, 19) }}</p>
                @else
                    @php
                        $province = \App\Models\Province::where('id', Sentinel::getUser()->province_id)->first();
                    @endphp
                    <p style="margin: 0; color: #fff; font-size: 12px; opacity: 0.9;">{{ $province ? $province->name : 'Province' }} PROVINCIAL <br/> MANAGER</p>
                @endif
                <a href="#" style="color: #fff; font-size: 12px;"><i class="fa fa-circle text-success"></i> Online</a>
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

            <li class="@if(Request::is('ticket*')) active @endif">
                <a href="{{ url('ticket') }}">
                    <i class="fa fa-ticket"></i> <span>Tickets</span>
                </a>
            </li>
            
            <li class="@if(Request::is('dashboard')) active @endif">
                <a href="{{ url('user/cycle') }}">
                    <i class="fa fa-dashboard"></i> <span>My Cycle</span>
                </a>
            </li>


            @if(Sentinel::hasAccess('reports.client_reports'))
                <li class="treeview @if(Request::is('ledger/*')) active menu-open @endif">
                    <a href="#">
                        <i class="fa fa-dashboard"></i> <span>Ledger</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>    
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('groups.create'))
                            <li>
                                <a href="{{ route('ledger.summary') }}"><i class="fa fa-circle-o"></i> General Ledger</a>
                            </li> 
                        @endif
                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('ledger.transactions') }}"><i class="fa fa-circle-o"></i>Branch Ledgers</a>
                        </li>
                        @endif
                    </ul>
                </li>
            @endif


 @if(Sentinel::hasAccess('settings'))
                                   <li>
                                <a href="{{ url('user/branch_deposits') }}"><i
                                            class="fa fa-circle-o"></i>Branch Deposits
                                    <span class="pull-right-container">
                                    </span>
                                </a>
                            </li>
                       @endif      


            <?php
                $office_id = Sentinel::getUser()->office_id;
            ?>

            @if(Sentinel::hasAccess('expenses'))
                <li class="treeview @if(Request::is('expense/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-thumbs-up"></i> <span>Approvals</span>
                        <?php
                                            $office_id = Sentinel::getUser()->office_id;
                                            ?>
                          @if(Sentinel::hasAccess('settings'))
                        <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionsPending::count() + \App\Models\Loan::where('status','pending')->count() + \App\Models\Client::where('status','pending')->count() + \App\Models\LoanTransactionUnapproved::count() + \App\Models\Advance::where('status','pending')->count() + \App\Models\TopUp::where('status','pending')->count() + \App\Models\Leave::where('status', 'pending')->count() + \App\Models\WaiverTransactionUnapproved::where('status', 'pending')->count() +  \App\Models\ChargeTransactionUnapproved::where('status', 'pending')->count()  }}</span>

                      @else
                        <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionsPending::where('office_id',$office_id)->count() }}</span>
                        @endif
                    </a>
                    <ul class="treeview-menu">

    @if(Sentinel::hasAccess('expenses'))
                            <li>
                                <a href="{{ url('loan/managers_pending_approval') }}"><i
                                            class="fa fa-circle-o"></i> Loans Pending
                                    <span class="pull-right-container">
                                    <?php
                                            $office_id = Sentinel::getUser()->office_id;
?>
@if(Sentinel::hasAccess('settings'))
                                        <span class="label label-warning pull-right">{{\App\Models\Loan::whereIn('status', ['pending', 'approved'])->count() }}</span>
@else
                                        <span class="label label-warning pull-right">{{\App\Models\Loan::whereIn('status', ['pending', 'approved'])->where('office_id',$office_id)->count() }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
			    @endif

                @if(Sentinel::hasAccess('settings'))
                                              <!-- <li>
                                <a href="{{ url('user/performance_information') }}"><i
                                            class="fa fa-circle-o"></i>Performance Information
                                    <span class="pull-right-container">
                                    </span>
                                </a>
                            </li> -->
@endif

			          @if(Sentinel::hasAccess('expenses'))
                            <li>
                                <a href="{{ url('loan/top_up_approvals') }}"><i
                                            class="fa fa-circle-o"></i> Top Ups Pending Approval
                                    <span class="pull-right-container">
                                    <?php
                                            $office_id = Sentinel::getUser()->office_id;
?>
@if(Sentinel::hasAccess('settings'))
                                        <span class="label label-warning pull-right">{{\App\Models\LoanTopUp::whereIn('status', ['pending'])->count() }}</span>
@else
                                        <span class="label label-warning pull-right">{{\App\Models\LoanTopUp::whereIn('status', ['pending'])->where('office_id',$office_id)->count() }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            @endif


                    @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('loan/transaction_approvals') }}"><i
                                            class="fa fa-circle-o"></i> Transaction Approvals
                                            <span class="pull-right-container">
                                            <?php
                                            $office_id = Sentinel::getUser()->office_id;
                                            ?>
@if(Sentinel::hasAccess('settings'))
                                <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionUnapproved::count()}}</span>
                                @else
                                <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionUnapproved::where('office_id',$office_id)->count() }}</span>
                                @endif
                                    </span>
                                </a>

                            </li>
                        @endif





                        @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('loan/reloan_approvals') }}"><i
                                            class="fa fa-circle-o"></i> Reloan Approvals
                                            <span class="pull-right-container">
                                            <?php
                                            $office_id = Sentinel::getUser()->office_id;
                                            ?>
@if(Sentinel::hasAccess('settings'))
                                <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionsPending::count() }}</span>
                                @else
                                <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionsPending::where('office_id',$office_id)->count() }}</span>
                                @endif
                                    </span>
                                </a>

                            </li>
			    @endif
			    <!--------------waiver approvals---------------->
                        @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ route('loan.waiver_approvals') }}"><i class="fa fa-circle-o"></i> Waiver Approvals
                                            <span class="pull-right-container">
                                            <?php
                                            $office_id = Sentinel::getUser()->office_id;
                                            ?>
@if(Sentinel::hasAccess('settings'))
                                <span class="label label-info pull-right-container" >{{\App\Models\WaiverTransactionUnapproved::where('status','pending')->count()}}</span>
                                @else
                                <span class="label label-info pull-right-container" >{{\App\Models\WaiverTransactionUnapproved::where('status','pending')->where('office_id',$office_id)->count() }}</span>
                                @endif
                                    </span>
                                </a>

                            </li>
                        @endif


                        @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ route('loan.charge_approvals') }}"><i
                                            class="fa fa-circle-o"> </i> Charge Approvals
                                            <span class="pull-right-container">
                                            <?php
                                            $office_id = Sentinel::getUser()->office_id;
                                            ?>
@if(Sentinel::hasAccess('settings'))
                                <span class="label label-info pull-right-container" >{{\App\Models\ChargeTransactionUnapproved::where('status','pending')->count()}}</span>
                                @else
                                <span class="label label-info pull-right-container" >{{\App\Models\ChargeTransactionUnapproved::where('status','pending')->where('office_id',$office_id)->count() }}</span>
                                @endif
                                </span>
                                </a>

                            </li>
                        @endif

                        



                   
        

                        @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('client/managers_pending_approval') }}"><i
                                            class="fa fa-circle-o"></i>Clients Pending Approval
                                            <span class="pull-right-container">
                                            <?php
                                            $office_id = Sentinel::getUser()->office_id;
?>
@if(Sentinel::hasAccess('settings'))
					    <span class="label label-info pull-right">{{\App\Models\Client::where('status','pending')->count() }}</span>
@else
                                            <span class="label label-info pull-right">{{\App\Models\Client::where('status','pending')->where('office_id',$office_id)->count() }}</span>
                                            @endif
                                    </span>
                                </a></li>
				@endif
				@if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('advances.pending_approvals') }}">
                                <i class="fa fa-circle-o"></i> Advances Pending Approvals
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    $pendingApprovalsCount = 0;
                                    if ($user->hasAccess('groups.create')) {
                                        $pendingApprovalsCount = \App\Models\Advance::where('status', 'pending')->count();
                                    } elseif ($user->hasAccess('offices')) {
                                        $office_id = $user->office_id;
                                        $pendingApprovalsCount = \App\Models\Advance::where('status', 'pending')
                                            ->where('office_id', $office_id)
                                            ->count();
                                    } else {
                                        $provinceId = $user->province_id;
                                        $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                        $pendingApprovalsCount = \App\Models\Advance::whereIn('office_id', $provinceOffices)
                                            ->where('status', 'pending')
                                            ->count();
                                    }
                                    ?>
                                  <span class="label label-warning pull-right">{{ $pendingApprovalsCount }}</span>   
                                </span>
                            </a>
                        </li>
			@endif
			@if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('advances.topups_pending_approval') }}">
                                <i class="fa fa-circle-o"></i> Advance-TopUps Approvals
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    $pendingApprovalsCount = 0;
                                    if ($user->hasAccess('groups.create')) {
                                        $pendingApprovalsCount = \App\Models\TopUp::where('status', 'pending')->count();
                                    } elseif ($user->hasAccess('offices')) {
                                        $office_id = $user->office_id;
                                        $pendingApprovalsCount = \App\Models\TopUp::where('status', 'pending')
                                            ->where('office_id', $office_id)
                                            ->count();
                                    } else {
                                        $provinceId = $user->province_id;
                                        $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                        $pendingApprovalsCount = \App\Models\TopUp::whereIn('office_id', $provinceOffices)
                                            ->where('status', 'pending')
                                            ->count();
                                    }
                                    ?>
                                  <span class="label label-warning pull-right">{{ $pendingApprovalsCount }}</span>
                                </span>
                            </a>
                        </li>
                        @endif
		
		
                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('leave.pending_leave_approvals') }}">
                                <i class="fa fa-circle-o"></i> Pending Leave Approvals
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    $pendingApprovalsCount = 0;
                                    if ($user->hasAccess('groups.create')) {
                                        $pendingApprovalsCount = \App\Models\Leave::where('status', 'pending')->count();
                                    } elseif ($user->hasAccess('offices')) {
                                        $office_id = $user->office_id;
                                        $pendingApprovalCount = \App\Models\Leave::where('status', 'pending')
                                            ->where('office_id', $office_id)
                                            ->count();
                                    } else {
                                        $provinceId = $user->province_id;
                                        $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                        $pendingApprovalsCount = \App\Models\Leave::whereIn('office_id', $provinceOffices)
                                            ->where('status', 'pending')
                                            ->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $pendingApprovalsCount }}</span> 
                                </span>
                            </a>
                        </li>
                        @endif

                    </ul>
                </li>
            @endif


      @if(Sentinel::hasAccess('settings'))
                <li class="treeview @if(Request::is('setting/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-money"></i> <span>Payroll Loan Applications</span>
                        <span class="pull-right-container">
                    <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::count()}}</span>
                        </span>
                    </a>
              
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('settings.general.view'))
                            <li><a href="{{ url('loan/payroll_loan/pending_list') }}"><i
                                            class="fa fa-circle-o"></i> Pending approval
                                            <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::where('status','pending')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('settings.organisation.view'))
                            <li><a href="{{ url('loan/payroll_loan/approved_list') }}"><i
                                            class="fa fa-circle-o"></i> Approved
                                            <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::where('status','approved')->count() }}</span>
                                    </span>
                        @endif

                        @if(Sentinel::hasAccess('settings.organisation.view'))
                            <li><a href="{{ url('loan/payroll_loan/declined_list') }}"><i
                                            class="fa fa-circle-o"></i> Declined
                                            <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::where('status','declined')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif






           <li class="treeview @if(Request::is('setting/*')) active @endif">
                    <a href="#">
                    <i class="fa fa-clock-o"></i><span>Collections</span>
                    </a>
              
                    <ul class="treeview-menu">
                    @if(Sentinel::hasAccess('expenses'))
                <li class="">
                    <a href="{{ url('loan/new_collections') }}">
                      <span>Collections</span>
                    </a>
                </li>
            @endif

            @if(Sentinel::hasAccess('expenses'))
                <li class="">
                    <a href="{{ url('loan/expected_collections') }}">
                       <span>Expected collections</span>
                    </a>
                </li>
            @endif

                    <li class="">
                    <a href="{{ url('loan/my_collections') }}">
                        <span>My Collections</span>
                    </a>
                </li>


                     
                <li class="">
                    <a href="{{ url('loan/my_expected_collections') }}">
                         <span>My Expected Collections</span>
                    </a>
                </li>
           

                    </ul>
                </li>



   <li class="treeview @if(Request::is('setting/*')) active @endif">
                    <a href="#">
                    <i class="fa fa-hand-rock-o"></i> <span>Recoveries</span>
                    </a>

                    <ul class="treeview-menu">
            @if(Sentinel::hasAccess('expenses'))
                <li class="">
                    <a href="{{ url('loan/branch_uncollected') }}">
                       <span>Branch uncollected</span>
                    </a>
                </li>
            @endif

                    </ul>
                </li>


            @if(Sentinel::hasAccess('clients'))
                <li class="">
                    <a href="{{ url('user/leaderboard') }}">
                        <i class="fa fa-trophy"></i> <span>Leaderboard</span>
                    </a>
                </li>
		@endif



		        @if(Sentinel::hasAccess('clients'))

            <li class="treeview @if(Request::is('setting/*')) active @endif">
                <a href="#">
                    <i class="fa fa-star"></i> <span>Appraisal</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>


                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('settings.general.view'))
                            <li><a href="{{ url('user/appraisal_forms') }}"><i
                                            class="fa fa-circle-o"></i> Forms
                                            <span class="pull-right-container">
                                    </span>
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('clients'))
                            <li>          
                              
                                  <a href="{{ url('user/my_appraisal_forms') }}">
                                 
                                    <i
                                            class="fa fa-circle-o"></i> My Appraisal
                                            <span class="pull-right-container">
                                        
                                            </a></span></li>
					    @endif


                        @if(Sentinel::hasAccess('clients'))
                      
            @endif
                    </ul>
                </li>
            @endif
       

            @if(Sentinel::hasAccess('loans.create'))
                            <li>
                                <a href="{{ url('loan/my_applications/data') }}"><i
                                            class="fa fa-pencil"></i> My Loan Applications
                                    <span class="pull-right-container">
                                    <?php
                                            $loan_officer_id = Sentinel::getUser()->id;
                                            ?>
                                        <span class="label label-warning pull-right">{{\App\Models\LoanApplication::where('staff_id',$loan_officer_id)->where('status','pending')->count()}}</span>
                                    </span>
                                </a>
                            </li>
                        @endif


            @if(Sentinel::hasAccess('offices'))
                <li class="treeview @if(Request::is('office/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-briefcase"></i> <spanxs>{{trans_choice('general.branch',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('offices.view'))
                            <li><a href="{{ url('office/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.branch',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('offices.create'))
                            <li><a href="{{ url('office/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.branch',1)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
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
                        @if(Sentinel::hasAccess('clients.view'))
                            <li><a href="{{ url('client/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.client',2)}}
                                   <!-- <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\Client::where('status','active')->count() }}</span>
                                    </span>-->
                                </a></li>
                        @endif
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
                        @if(Sentinel::hasAccess('clients.branch_clients'))
                            <li><a href="{{ url('client/branch_clients') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.branch',1)}} {{trans_choice('general.client',2)}}
                                    <span class="pull-right-container">
                                    <?php
                                            $office_id = Sentinel::getUser()->office_id;
                                            ?>
                                        <span class="label label-success pull-right">{{\App\Models\Client::where('office_id',$office_id)->where('status','active')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif

                        @if(Sentinel::hasAccess('clients.pending_approval'))
                            <li><a href="{{ url('client/pending_approval') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',2)}}
                                            <span class="pull-right-container">
                                            <span class="label label-info pull-right">{{\App\Models\Client::where('status','pending')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('clients.closed'))
                            <li><a href="{{ url('client/closed') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.closed',1)}}
                                            <span class="pull-right-container">
                                            <span class="label label-info pull-right">{{\App\Models\Client::where('status','closed')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('clients.closed'))
                            <li><a href="{{ url('client/clients_inactive') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.inactive',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('clients.closed'))
                            <li><a href="{{ url('client/clients_blacklisted') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.blacklisted',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('clients.view'))
                            <li><a href="{{ url('client/declined') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.declined',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('clients.create'))
                            <li><a href="{{ url('client/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.client',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('groups'))
                            <li><a href="{{ url('group/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}}  {{trans_choice('general.group',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('groups.pending_approval'))
                            <li><a href="{{ url('group/pending_approval') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.group',2)}} {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('groups.view'))
                            <li><a href="{{ url('group/groups_declined') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.group',2)}} {{trans_choice('general.declined',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('groups.view'))
                            <li><a href="{{ url('group/groups_closed') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.group',2)}} {{trans_choice('general.closed',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('groups.create'))
                            <li><a href="{{ url('group/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}}  {{trans_choice('general.group',1)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(Sentinel::hasAccess('loans'))
                <li class="treeview @if(Request::is('loan/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-money"></i> <span>{{trans_choice('general.loan',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('loans.view'))
                            <li><a href="{{ url('loan/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.active',2)}} {{trans_choice('general.loan',2)}}
                                   <!-- <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\Loan::where('status','disbursed')->count() }}</span>
                                    </span>-->
                                </a></li>
                        @endif

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

                        @if(Sentinel::hasAccess('loans.branch_loans'))
                            <li><a href="{{ url('loan/branch_loans') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.branch',1)}} {{trans_choice('general.active',2)}} {{trans_choice('general.loan',2)}}
                                    <span class="pull-right-container">
                                    <?php
                                            $office_id = Sentinel::getUser()->office_id;
                                            ?>
                                        <span class="label label-info pull-right">{{\App\Models\Loan::where('office_id',$office_id)->where('status','disbursed')->count() }}</span>
                                    </span>
                                </a></li>
                        @endif

                        @if(Sentinel::hasAccess('loans.pending_approval'))
                            <li>
                                <a href="{{ url('loan/pending_approval') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{\App\Models\Loan::where('status','pending')->count() }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if(Sentinel::hasAccess('loans.awaiting_disbursement'))
                            <li>
                                <a href="{{ url('loan/awaiting_disbursement') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\Loan::where('status','approved')->count() }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if(Sentinel::hasAccess('loans.declined'))
                            <li>
                                <a href="{{ url('loan/loans_declined') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.declined',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Loan::where('status','declined')->count() }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if(Sentinel::hasAccess('loans.written_off'))
                            <li>
                                <a href="{{ url('loan/loans_written_off') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.written_off',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Loan::where('status','written_off')->count() }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if(Sentinel::hasAccess('loans.closed'))
                            <li>
                                <a href="{{ url('loan/loans_closed') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.closed',1)}}
                                   <!-- <span class="pull-right-container">
                                        <span class="label label-success pull-right">{{\App\Models\Loan::where('status','closed')->count() }}</span>
                                    </span>-->
                                </a>
                            </li>
                        @endif
                        @if(Sentinel::hasAccess('loans.rescheduled'))
                            <li>
                                <a href="{{ url('loan/loans_rescheduled') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.rescheduled',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-success pull-right">{{\App\Models\Loan::where('status','rescheduled')->count() }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif



                        @if(Sentinel::hasAccess('payroll'))
                            <li>
                                <a href="{{ url('loan/application/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.application',2)}}
                                    <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{\App\Models\LoanApplication::where('status','pending')->count() }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif



    

                        @if(Sentinel::hasAccess('loans.create'))
                            <li><a href="{{ url('loan/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.loan',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('products.loan_products.view'))
                            <li><a href="{{ url('loan/product/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.product',2)}}
                                </a></li>
                        @endif

                        @if(Sentinel::hasAccess('loans.create'))
                            <li><a href="{{ url('loan/calculator/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.loan',1)}} {{trans_choice('general.calculator',1)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
		@endif

		<!---Policies----->
            @if(Sentinel::hasAccess('reports'))
                <li class="treeview @if(Request::is('policies/*')) active menu-open @endif">
                    <a href="#">
                        <i class="fa fa-user"></i> <span>Company Policies</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('reports.loan_reports'))
                            <li>
                                <a href="{{ route('policies.view_policies') }}"><i class="fa fa-circle-o"></i> View Policies</a>
                            </li>
                        @endif

                        @if(Sentinel::hasAccess('reports.client_reports'))
                            <li>
                                <a href="{{ route('policies.user_responses') }}"><i class="fa fa-circle-o"></i> User Responses</a>
                            </li>
                        @endif

                        @if(Sentinel::hasAccess('reports.client_reports'))
                            <li>
                                <a href="{{ route('policies.add_policies') }}">
                                    <i class="fa fa-circle-o"></i> Add Policies</a>
                                    <span class="pull-right-container">
                                        <?php

                                        ?>
                                    </span>
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>
		@endif

            @if(Sentinel::hasAccess('accounting'))
                <li class="treeview @if(Request::is('accounting/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-money"></i> <span>{{trans_choice('general.accounting',1)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('accounting.gl_accounts.view'))
                            <li><a href="{{ url('accounting/gl_account/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.chart_of_account',2)}}
                                </a></li>
                        @endif

<!-- 
                        @if(Sentinel::hasAccess('accounting.journals.view'))
                        <li><a href="{{ url('accounting/journal/delete_jv') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.delete',1)}} {{trans_choice('general.journal',2)}}
                                </a></li>
                        @endif -->

                        @if(Sentinel::hasAccess('accounting.journals.view'))
                            <li><a href="{{ url('accounting/journal/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.journal',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('accounting.journals.create'))
                            <li><a href="{{ url('accounting/journal/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.journal',1)}} {{trans_choice('general.entry',1)}}
                                </a></li>
                        @endif


                        @if(Sentinel::hasAccess('accounting.journals.reconciliation.view'))
                            <li><a href="{{ url('accounting/reconciliation/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.reconciliation',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('accounting.period.view'))
                            <li><a href="{{ url('accounting/period/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.close',1)}} {{trans_choice('general.period',2)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
		@endif




            @if(Sentinel::hasAccess('reports'))
                <li class="treeview @if(Request::is('report/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-bar-chart"></i> <span>{{trans_choice('general.report',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ url('report/client_report') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.client',1)}} {{trans_choice('general.report',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('reports.loan_reports'))
                            <li><a href="{{ url('report/loan_report') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.loan',1)}} {{trans_choice('general.report',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('reports.financial_reports'))
                            <li><a href="{{ url('report/financial_report') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.financial',1)}} {{trans_choice('general.report',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('reports.company_reports'))
                            <li class="hidden"><a href="{{ url('report/company_report') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.organisation',1)}} {{trans_choice('general.report',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('reports.savings_reports'))
                            <li><a href="{{ url('report/savings_report') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.savings',2)}} {{trans_choice('general.report',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('reports.reports_scheduler.view'))
                            <li class="hidden"><a href="{{ url('report/report_scheduler/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.report',1)}} {{trans_choice('general.scheduler',1)}}
                                </a></li>
                        @endif

                    </ul>
                </li>
            @endif

 @if(Sentinel::hasAccess('reports'))
                <li class="treeview @if(Request::is('advances/*')) active menu-open @endif">
                    <a href="#">
                        <i class="fa fa-money"></i> <span>Advances</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>    
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('reports.loan_reports'))
                            <li>
                                <a href="{{ route('advances.apply') }}"><i class="fa fa-circle-o"></i> Apply for Advance</a>
                            </li> 
                        @endif
                        @if(Sentinel::hasAccess('reports.loan_reports'))
                        <li>
                            <a href="{{ route('advances.my_advances') }}"><i class="fa fa-circle-o"></i> My Advances</a>
                        </li>
                        @endif
                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('advances.active_advances') }}">
                                <i class="fa fa-circle-o"></i> Active Advances
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    if ($user->hasAccess('groups.create')) {
                                        $activeAdvancesCount = \App\Models\Advance::where('status', 'approved')->count();
                                    } elseif ($user->hasAccess('offices')) {
                                        $office_id = $user->office_id;
                                        $activeAdvancesCount = \App\Models\Advance::where('status', 'approved')
                                            ->where('office_id', $office_id)
                                            ->count();
                                    } else {
                                        $provinceId = $user->province_id;
                                        $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                        $activeAdvancesCount = \App\Models\Advance::whereIn('office_id', $provinceOffices)
                                            ->where('status', 'approved')
                                            ->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $activeAdvancesCount }}</span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('advances.pending_approvals') }}">
                                <i class="fa fa-circle-o"></i> Pending Approvals
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    $pendingApprovalsCount = 0;
                                    if ($user->hasAccess('groups.create')) {
                                        $pendingApprovalsCount = \App\Models\Advance::where('status', 'pending')->count();
                                    } elseif ($user->hasAccess('offices')) {
                                        $office_id = $user->office_id;
                                        $pendingApprovalsCount = \App\Models\Advance::where('status', 'pending')
                                            ->where('office_id', $office_id)
                                            ->count();
                                    } else {
                                        $provinceId = $user->province_id;
                                        $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                        $pendingApprovalsCount = \App\Models\Advance::whereIn('office_id', $provinceOffices)
                                            ->where('status', 'pending')
                                            ->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $pendingApprovalsCount }}</span> 
                                </span>
                            </a>
                        </li>
			@endif
			@if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('advances.topups_pending_approval') }}">
                                <i class="fa fa-circle-o"></i> TopUps Pending Approval
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    $pendingApprovalsCount = 0;
                                    if ($user->hasAccess('groups.create')) {
                                        $pendingApprovalsCount = \App\Models\TopUp::where('status', 'pending')->count();
                                    } elseif ($user->hasAccess('offices')) {
                                        $office_id = $user->office_id;
                                        $pendingApprovalsCount = \App\Models\TopUp::where('status', 'pending')
                                            ->where('office_id', $office_id)
                                            ->count();
                                    } else {
                                        $provinceId = $user->province_id;
                                        $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                        $pendingApprovalsCount = \App\Models\TopUp::whereIn('office_id', $provinceOffices)
                                            ->where('status', 'pending')
                                            ->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $pendingApprovalsCount }}</span>
                                </span>
                            </a>
                        </li>
                        @endif
		
                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('advances.declined_advances') }}">
                                <i class="fa fa-circle-o"></i> Declined Advances
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    if ($user->hasAccess('groups.create')) {
                                        $declinedAdvancesCount = \App\Models\Advance::where('status', 'declined')->count();
                                    } else {
                                        $office_id = $user->office_id;
                                        $declinedAdvancesCount = \App\Models\Advance::where('status', 'declined')->where('office_id', $office_id)->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $declinedAdvancesCount }}</span>
                                </span>   
                            </a>
                        </li>
                        @endif
                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('advances.closed_advances') }}">
                                <i class="fa fa-circle-o"></i> Closed Advances
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    if ($user->hasAccess('groups.create')) {
                                        $closedAdvancesCount = \App\Models\Advance::where('status', 'closed')->count();
                                    } else {
                                        $office_id = $user->office_id;
                                        $closedAdvancesCount = \App\Models\Advance::where('status', 'closed')->where('office_id', $office_id)->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $closedAdvancesCount }}</span>
                                </span>   
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
		@endif

		            @if(Sentinel::hasAccess('reports'))
                <li class="treeview @if(Request::is('leave/*')) active menu-open @endif">
                    <a href="#">
                        <i class="fa fa-user"></i> <span>Annual Leave</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('reports.loan_reports'))
                            <li>
                                <a href="{{ route('leave.my_leave_days') }}"><i class="fa fa-circle-o"></i> My Leave Days</a>
                            </li>
                        @endif

                        @if(Sentinel::hasAccess('reports.client_reports'))
                            <li>
                                <a href="{{ route('leave.active_leave') }}">
                                    <i class="fa fa-circle-o"></i> Active Leave
                                    <span class="pull-right-container">
                                        <?php
                                        $user = Sentinel::getUser();
                                        $currentDate = \Carbon\Carbon::now()->toDateString();
                                        $activeLeaveCount = 0;

                                        if ($user->hasAccess('groups.create')) {
                                            $activeLeaveCount = \App\Models\Leave::where('status', 'approved')
                                                ->where('commencement_date', '<=', $currentDate)
                                                ->where('return_date', '>=', $currentDate)
                                                ->count();
                                        } elseif ($user->hasAccess('offices')) {
                                            $office_id = $user->office_id;
                                            $activeLeaveCount = \App\Models\Leave::where('status', 'approved')
                                                ->where('office_id', $office_id)
                                                ->where('commencement_date', '<=', $currentDate)
                                                ->where('return_date', '>=', $currentDate)
                                                ->count();
                                        } else {
                                            $provinceId = $user->province_id;
                                            $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                            $activeLeaveCount = \App\Models\Leave::whereIn('office_id', $provinceOffices)
                                                ->where('status', 'approved')
                                                ->where('commencement_date', '<=', $currentDate)
                                                ->where('return_date', '>=', $currentDate)
                                                ->count();
                                        }
                                        ?>
                                        <span class="label label-warning pull-right">{{ $activeLeaveCount }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif


                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('leave.pending_leave_approvals') }}">
                                <i class="fa fa-circle-o"></i> Pending Leave Approvals
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    $pendingApprovalsCount = 0;
                                    if ($user->hasAccess('groups.create')) {
                                        $pendingApprovalsCount = \App\Models\Leave::where('status', 'pending')->count();
                                    } elseif ($user->hasAccess('offices')) {
                                        $office_id = $user->office_id;
                                        $pendingApprovalCount = \App\Models\Leave::where('status', 'pending')
                                            ->where('office_id', $office_id)
                                            ->count();
                                    } else {
                                        $provinceId = $user->province_id;
                                        $provinceOffices = \App\Models\Office::where('province_id', $provinceId)->pluck('id');
                                        $pendingApprovalsCount = \App\Models\Leave::whereIn('office_id', $provinceOffices)
                                            ->where('status', 'pending')
                                            ->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $pendingApprovalsCount }}</span>
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Sentinel::hasAccess('reports.client_reports'))
                        <li>
                            <a href="{{ route('leave.declined_leave') }}">
                                <i class="fa fa-circle-o"></i> Declined Leave
                                <span class="pull-right-container">
                                    <?php
                                    $user = Sentinel::getUser();
                                    if ($user->hasAccess('groups.create')) {
                                        $declinedLeaveCount = \App\Models\Leave::where('status', 'declined')->count();
                                    } else {
                                        $office_id = $user->office_id;
                                        $declinedLeaveCount = \App\Models\Leave::where('status', 'declined')->where('office_id', $office_id)->count();
                                    }
                                    ?>
                                    <span class="label label-warning pull-right">{{ $declinedLeaveCount }}</span>
                                </span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
            @endif


            @if(Sentinel::hasAccess('communication'))
                <li class="treeview @if(Request::is('communication/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-envelope"></i> <span>{{trans_choice('general.communication',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('communication.view'))
                            <li><a href="{{ url('communication/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.campaign',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('communication.create'))
                            <li><a href="{{ url('communication/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.create',1)}} {{trans_choice('general.campaign',1)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(Sentinel::hasAccess('assets'))
                <li class="treeview @if(Request::is('asset/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-building"></i> <span>{{trans_choice('general.asset',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('assets.view'))
                            <li><a href="{{ url('asset/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.asset',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('assets.create'))
                            <li><a href="{{ url('asset/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.asset',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('assets.types.view'))
                            <li><a href="{{ url('asset/type/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.asset',1)}} {{trans_choice('general.type',2)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(Sentinel::hasAccess('expenses'))
                <li class="treeview @if(Request::is('expense/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-share"></i> <span>{{trans_choice('general.expense',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('expenses.view'))
                            <li><a href="{{ url('expense/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.expense',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('expenses.create'))
                            <li><a href="{{ url('expense/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.expense',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('expenses.types.view'))
                            <li><a href="{{ url('expense/type/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.expense',1)}} {{trans_choice('general.type',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('expenses.budget.view'))
                            <li><a href="{{ url('expense/budget/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.budget',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('expenses.budget.view'))
                            <li><a href="{{ url('expense/budget/report') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.budget',1)}} {{trans_choice('general.report',2)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(Sentinel::hasAccess('other_income'))
                <li class="treeview @if(Request::is('other_income/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-plus"></i> <span>{{trans_choice('general.other_income',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('other_income.view'))
                            <li><a href="{{ url('other_income/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.other_income',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('other_income.create'))
                            <li><a href="{{ url('other_income/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.other_income',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('other_income.create'))
                            <li><a href="{{ url('other_income/type/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.other_income',1)}} {{trans_choice('general.type',2)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
              @if(Sentinel::hasAccess('expenses'))
                <li class="treeview @if(Request::is('payroll/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-paypal"></i> <span>{{trans_choice('general.payroll',1)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <!-- @if(Sentinel::hasAccess('payroll.view'))
                            <li><a href="{{ url('payroll/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.payroll',1)}}
                                </a></li>
                        @endif -->

                        @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('payroll/create_wage_bill') }}"><i
                                            class="fa fa-circle-o"></i> Add payroll
                                </a></li>
                        @endif

                        @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('payroll/payroll_list') }}"><i
                                            class="fa fa-circle-o"></i> Payroll List
                                </a></li>
                        @endif

                        <?php
                       
                           $payroll_list = \App\Models\Payroll::where('status','pending')->get();
                           $unapproved_count = count($payroll_list);
                        ?>


                        @if(Sentinel::hasAccess('payroll.update'))

                            <li><a href="{{ url('payroll/payroll_pending_approval') }}"><i
                                            class="fa fa-circle-o"></i> Payroll Approvals
                                            @if($unapproved_count > 0)
                                            <span class="label label-info pull-right-container" >
                                            <i class="fa fa-bell" aria-hidden="true"></i>
                                            </span>
                                            @endif
                                </a></li>
                        @endif


                        @if(Sentinel::hasAccess('payroll.update'))
                            <li><a href="{{ url('payroll/template') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.payroll',1)}} {{trans_choice('general.template',2)}}
                                </a></li>
                        @endif
                    

                        <!-- @if(Sentinel::hasAccess('payroll.update'))
                            <li><a href="{{ url('payroll/lc_information') }}"><i
                                            class="fa fa-circle-o"></i> Loan Consultant information
                                </a></li>
                        @endif -->
                    </ul>
                </li>
		@endif

		<!--@if(Sentinel::hasAccess('expenses'))
                <li class="">
                    <a href="{{ route('performance_metrics.index') }}">
                        <i class="fa fa-trophy"></i> <span>Performance Metrics</span>
                    </a>
                </li>
            @endif-->


                            @if(Sentinel::hasAccess('settings'))
                                              <li>
                                <a href="{{ url('user/performance_information') }}"><i
                                            class="fa fa-circle-o"></i>Performance Information
                                    <span class="pull-right-container">
                                    </span>
                                </a>
                            </li>
@endif
    
            @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('payroll/lc_information') }}"><i
                                            class="fa fa-circle-o"></i> Loan Consultant information
                                </a></li>
            @endif



            @if(Sentinel::hasAccess('custom_fields'))
                <li class="treeview @if(Request::is('custom_field/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-list"></i> <span>{{trans_choice('general.custom_field',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('custom_fields.view'))
                            <li><a href="{{ url('custom_field/data') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.custom_field',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('custom_fields.create'))
                            <li><a href="{{ url('custom_field/create') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.custom_field',1)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif
            @if(Sentinel::hasAccess('expenses'))
                <li class="treeview @if(Request::is('user/*')) active @endif">
                    <a href="{{ url('user/data') }}">
                        <i class="fa fa-users"></i> <span>{{trans_choice('general.user',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('user/data') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>{{trans_choice('general.view',2)}} {{trans_choice('general.user',2)}}</span>
                                </a></li>
				@endif
				@if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ route('user.inactive') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>View Inactive Users </span>
                                </a></li>
                        @endif

                        @if(Sentinel::hasAccess('users.create'))
                            <li><a href="{{ url('user/client_users/data') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>{{trans_choice('general.view',2)}} {{trans_choice('general.client_users',2)}}</span>
                                </a></li>
                        @endif

		
                        @if(Sentinel::hasAccess('users.roles.view'))
                            <li><a href="{{ url('user/role/data') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.manage',2)}} {{trans_choice('general.role',2)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('users.create'))
                            <li><a href="{{ url('user/create') }}"><i
                                            class="fa fa-circle-o"></i>{{trans_choice('general.add',2)}} {{trans_choice('general.user',1)}}
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif

          <!--  @if(Sentinel::hasAccess('loans'))
                <li class="">
                    <a href="{{ url('payroll/mypayslips') }}">
                        <i class="fa fa-money"></i> <span>My Payslips</span>
                    </a>
                </li>
            @endif

              
            @if(Sentinel::hasAccess('loans'))
                <li class="">
                    <a href="{{ url('payroll/mypayslips_old') }}">
                        <i class="fa fa-money"></i> <span>My Payslips 2023 - Jan 2024</span>
                    </a>
                </li>
            @endif -->

      @if(Sentinel::hasAccess('loans'))
                <li class="">
                    <a href="{{ url('payroll/mypayslips') }}">
                        <i class="fa fa-money"></i> <span>My Payslips</span>
                    </a>
                </li>
            @endif

            @if(Sentinel::hasAccess('audit_trail'))
                <li class="@if(Request::is('audit_trail/*')) active @endif">
                    <a href="{{ url('audit_trail/data') }}">
                        <i class="fa fa-area-chart"></i> <span>{{trans_choice('general.audit_trail',2)}}</span>
                    </a>
                </li>
            @endif
            @if(Sentinel::hasAccess('settings'))
                <li class="treeview @if(Request::is('setting/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-cog"></i> <span>{{trans_choice('general.setting',2)}}</span>
                        <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Sentinel::hasAccess('settings.general.view'))
                            <li><a href="{{ url('setting/general') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.general',1)}}
                                </a></li>
                        @endif
                        @if(Sentinel::hasAccess('settings.organisation.view'))
                            <li><a href="{{ url('setting/organisation') }}"><i
                                            class="fa fa-circle-o"></i> {{trans_choice('general.organisation',1)}}
                                </a></li>
				@endif


				 @if(Sentinel::hasAccess('settings.organisation.view'))
                            <li><a href="{{ url('setting/fail_safe') }}"><i
                                            class="fa fa-circle-o"></i> System fail safes
                                </a></li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- @include('menu.partials.resignation-links') -->
            
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

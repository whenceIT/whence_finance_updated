<?php
    use App\Models\AppraisalForm;
    use App\Models\Ticket;

    $userInfo = \App\Helpers\GeneralHelper::get_user_info();
    $user = $userInfo->user;
    $role = $userInfo->role;
    $office = $userInfo->office;
    $assignedTickets = \App\Models\Ticket::with(['openedBy', 'assignedTo', 'closedBy', 'issueCategory'])
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderBy('created_at', 'desc')
            ->get();
    $office_id = Sentinel::getUser()->office_id;

    // Collateral count based on role
    $userId = $user->id;
    $roleId = $role;
    $query = \App\Models\Collateral::query();
    if ($roleId == 1) {
        // Admin — sees ALL collateral
    } elseif ($roleId == 4) {
        // Loan Officer / Branch Manager — own office only
        $officeId = $user->office_id;
        $query->where('office_id', $officeId);
    } elseif ($roleId == 12) {
        // DM Manager — own district
        $userOffice = $user->office;
        $districtId = $userOffice ? $userOffice->district_id : null;
        $query->where('district_id', $districtId);
    } elseif ($roleId == 6) {
        // Provincial Manager — own province
        $provinceId = $user->office->province_id;
        $query->where('province_id', $provinceId);
    } else {
        // Default: scope to collateral created by the user (Loan Consultants)
        $query->where('created_by_id', $userId);
    }
    $collateralCount = $query->count();
?>
<style>
@keyframes pulse-red {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
    50% { transform: scale(1.05); box-shadow: 0 0 0 6px rgba(231, 76, 60, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
}
</style>
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

            <!-- Check the config/role.php with predefined users accounts ids -->
            @hasRole('role.exec', 'role.poa' )
            <li class="@if(Request::is('dashboard')) active @endif">
                <a href="{{ url('user/poadashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>POA Dashboard</span>
                </a>
	        </li>
             @endif

         @if($role == 4 || $role == 6)
            <li class="@if(Request::is('dashboard')) active @endif">
                <a href="{{ url('user/verify_wallet') }}">
                    <i class="fa fa-check-circle"></i> <span>Withinhere Wallet</span>
                </a>
	        </li>

               @endif

             
            @hasRole('role.exec', 'role.goa')
            <!-- ============================================
                 GOA MANAGER SECTION
            ============================================ -->
            <li class="treeview @if(Request::is('goa_dashboard*')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-building"></i> <span>GOA Manager</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('goa.index') }}"><i class="fa fa-circle-o"></i> Dashboard</a></li>
                    <li><a href="{{ route('goa.fleet-management') }}"><i class="fa fa-circle-o"></i> Fleet Management</a></li>
                    <li><a href="{{ route('goa.vacancies-and-staffing') }}"><i class="fa fa-circle-o"></i> Vacancies & Staffing</a></li>
                </ul>
            </li>
            @endif

            @if($role == 4 || $role == 6)
            <li class="@if(Request::is('dashboard')) active @endif">
                <a href="{{ url('user/manager_performance') }}">
                    <i class="fa fa-line-chart"></i> <span>Manager Performance</span>
                </a>
	        </li>
            @endif


            <!-- Audit Trail / Risk Management -->
            @hasRole('role.exec', 'role.risk')
            <li class="treeview @if(Request::is('risk*') || Request::is('audits*')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-history"></i> <span>Risk Management</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li @if(Request::is('audits*')) class="active" @endif><a href="{{ url('risk/overview') }}"><i class="fa fa-circle-o"></i> Overview</a></li>
                    <li @if(Request::is('audits*')) class="active" @endif><a href="{{ route('audits.index') }}"><i class="fa fa-circle-o"></i> Audit Trail</a></li>
                    <li @if(Request::is('risk/branch-deposit-audit*')) class="active" @endif><a href="{{ url('risk/branch-deposit-audit?period=overall',) }}"><i class="fa fa-circle-o"></i> Branch Deposit Audit</a></li>
                    <li @if(Request::is('risk/branch-deposit-transactions*')) class="active" @endif><a href="{{ route('branch-deposit-transactions') }}"><i class="fa fa-circle-o"></i> Branch Deposit Transactions</a></li>
                    <li @if(Request::is('risk/exemption-list*')) class="active" @endif><a href="{{ route('risk.exemption-list') }}"><i class="fa fa-circle-o"></i> Exemption List</a></li>
                    <li @if(Request::is('risk/blocked-list*')) class="active" @endif><a href="{{ route('risk.blocked-list') }}"><i class="fa fa-circle-o"></i> Blocked List</a></li>
                    <li @if(Request::is('risk/setup-debt-management*')) class="active" @endif><a href="{{ route('risk.setup-debt-management') }}"><i class="fa fa-circle-o"></i> Setup Debt Costs</a></li>
                    <li @if(Request::is('risk/heat-map*')) class="active" @endif><a href="{{ url('risk/heat-map') }}"><i class="fa fa-circle-o"></i> Risk Heat Map</a></li>
                    <li @if(Request::is('risk/branch-ranking*')) class="active" @endif><a href="{{ url('risk/branch-ranking') }}"><i class="fa fa-circle-o"></i> Branch Risk Ranking</a></li>
                    <li @if(Request::is('risk/fraud-feed*')) class="active" @endif><a href="{{ url('risk/fraud-feed') }}"><i class="fa fa-circle-o"></i> Real-Time Risk</a></li>
                    
                    <!-- <li @if(Request::is('risk/recovery-efficiency*')) class="active" @endif><a href="{{ url('risk/recovery-efficiency') }}"><i class="fa fa-circle-o"></i> Recovery Tracker</a></li>
                    <li @if(Request::is('risk/policy-breach*')) class="active" @endif><a href="{{ url('risk/policy-breach') }}"><i class="fa fa-circle-o"></i> Policy Breach Tracker</a></li>
                    <li @if(Request::is('risk/cost-value*')) class="active" @endif><a href="{{ url('risk/cost-value') }}"><i class="fa fa-circle-o"></i> Risk Cost vs Value<br>Preservation Analytics</a></li>
                    <li @if(Request::is('risk/geographic-intelligence*')) class="active" @endif><a href="{{ url('risk/geographic-intelligence') }}"><i class="fa fa-circle-o"></i> Geographic Risk<br>Intelligence</a></li>
                    <li @if(Request::is('risk/escalation-tracking*')) class="active" @endif><a href="{{ url('risk/escalation-tracking') }}"><i class="fa fa-circle-o"></i> Executive Escalation<br>Tracking</a></li>
                    <li @if(Request::is('risk/staff-profiles*')) class="active" @endif><a href="{{ url('risk/staff-profiles') }}"><i class="fa fa-circle-o"></i> Staff Risk Profiling</a></li> -->
                </ul>
            </li>
            @endif

            <li class="@if(Request::is('ticket*')) active @endif">
                <a href="{{ url('ticket') }}">
                    <i class="fa fa-ticket"></i> <span>Tickets</span>
                    <span class="pull-right-container">
                        <span class="label label-info pull-right">{{ $assignedTickets->count() }}</span>
                    </span>
                </a>
            </li>

            <li><a href="{{ url('loan/pending_client_app_applications') }}"><i class="fa fa-mobile"></i>Client App Loan Applications<span class="label label-warning pull-right">
{{ \App\Models\Client::where('status', 'active')
    ->where('staff_id', Sentinel::getUser()->id)
    ->whereIn('id', \App\Models\ClientAppLoanApplications::where('status', 'pending')->pluck('client_id'))
    ->count() }}
            </span></a></li>
            
            <li class="@if(Request::is('dashboard')) active @endif">
                <a href="{{ url('user/cycle') }}">
                    <i class="fa fa-dashboard"></i> <span>My Cycle</span>
                </a>
            </li>

            <li class="@if(Request::is('dashboard')) active @endif">
                <a href="{{ url('client/verify_client_number') }}">
                    <i class="fa fa-check-circle"></i> <span>Verify Client Number</span>
                </a>
            </li>

            <!-- ============================================
                 OPERATIONS SECTION
            ============================================ -->
            <li class="treeview @if(Request::is('loan/*') || Request::is('client/*') || Request::is('collateral/*') || Request::is('group/*') || Request::is('loan/*')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-cogs"></i> <span>Operations</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <!-- Loans -->
                    @if(Sentinel::hasAccess('loans'))
                    <li class="treeview @if(Request::is('loan/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>{{trans_choice('general.loan',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('loans.view'))
                                <li><a href="{{ url('loan/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.active',2)}} {{trans_choice('general.loan',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('loans.my_loans'))
                                <li><a href="{{ url('loan/my_loans') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.my',1)}} {{trans_choice('general.active',2)}} {{trans_choice('general.loan',2)}}
                                <span class="pull-right-container">
                                <?php
                                        $loan_officer_id = Sentinel::getUser()->loan_officer_id;
                                        ?>
                                    <span class="label label-info pull-right">{{\App\Models\Loan::where('loan_officer_id',$loan_officer_id)->where('status','disbursed')->count() }}</span>
                                </span>
                                </a></li>
                            @endif
                            @if(Sentinel::hasAccess('loans.branch_loans'))
                                <li><a href="{{ url('loan/branch_loans') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.branch',1)}} {{trans_choice('general.active',2)}} {{trans_choice('general.loan',2)}}
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
                                    <a href="{{ url('loan/pending_approval') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{\App\Models\Loan::where('status','pending')->count() }}</span>
                                    </span>
                                    </a>
                                </li>
                            @endif
                            @if(Sentinel::hasAccess('loans.awaiting_disbursement'))
                                <li>
                                    <a href="{{ url('loan/awaiting_disbursement') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\Loan::where('status','approved')->count() }}</span>
                                    </span>
                                    </a>
                                </li>
                            @endif
                            @if(Sentinel::hasAccess('loans.declined'))
                                <li>
                                    <a href="{{ url('loan/loans_declined') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.declined',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Loan::where('status','declined')->count() }}</span>
                                    </span>
                                    </a>
                                </li>
                            @endif
                            @if(Sentinel::hasAccess('loans.written_off'))
                                <li>
                                    <a href="{{ url('loan/loans_written_off') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.written_off',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\Loan::where('status','written_off')->count() }}</span>
                                    </span>
                                    </a>
                                </li>
                            @endif
                            @if(Sentinel::hasAccess('loans.closed'))
                                <li>
                                    <a href="{{ url('loan/loans_closed') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.closed',1)}}
                                    </a>
                                </li>
                            @endif
                            @if(Sentinel::hasAccess('loans.rescheduled'))
                                <li>
                                    <a href="{{ url('loan/loans_rescheduled') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.rescheduled',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-success pull-right">{{\App\Models\Loan::where('status','rescheduled')->count() }}</span>
                                    </span>
                                    </a>
                                </li>
                            @endif
                            @if(Sentinel::hasAccess('payroll'))
                                <li>
                                    <a href="{{ url('loan/application/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.loan',2)}} {{trans_choice('general.application',2)}}
                                    <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{\App\Models\LoanApplication::where('status','pending')->count() }}</span>
                                    </span>
                                    </a>
                                </li>
                            @endif
                            @if(Sentinel::hasAccess('loans.create'))
                                <li><a href="{{ url('loan/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.loan',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('products.loan_products.view'))
                                <li><a href="{{ url('loan/product/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.product',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('loans.create'))
                                <li><a href="{{ url('loan/calculator/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.loan',1)}} {{trans_choice('general.calculator',1)}}</a></li>
                            @endif

                        </ul>
                        
                    </li>
                    @endif

                        <li class="treeview @if(Request::routeIs('collateral.*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-shield"></i> <span>Collateral</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{ route('collateral.index') }}"><i class="fa fa-circle-o"></i> View Collateral <span class="label label-success pull-right">{{ $collateralCount }}</span></a>
                            </li>
                            
                            @if($role == 3)
                            <li>
                                <a href="{{ route('collateral.my') }}"><i class="fa fa-circle-o"></i> My Analytics</a>
                            </li>
                            @endif
                            <li><a href="{{ route('collateral.create') }}"><i class="fa fa-circle-o"></i> Add Collateral</a>

                            </li>
                            @if($role == 1)
                                <li><a href="{{ route('collateral.analytics.executive') }}"><i class="fa fa-circle-o"></i> Executive Analytics</a>
                                     </li>
                            @endif
                            @if($role == 6)
                                <li><a href="{{ route('collateral.analytics.provincial') }}"><i class="fa fa-circle-o"></i> Provincial Analytics</a>
                                     </li>
                            @endif
                            @if($role == 12)
                                <li><a href="{{ route('collateral.analytics.district') }}"><i class="fa fa-circle-o"></i> District Analytics</a>
                                    </li>
                            @endif
                            @if($role == 4)
                                <li><a href="{{ route('collateral.analytics.branch') }}"><i class="fa fa-circle-o"></i> Branch Analytics</a>
                                     </li>
                            @endif
                            <li>
                                <a href="{{ route('collateral.approvals.queue') }}"><i class="fa fa-circle-o"></i> Approval Queue</a>
                            </li>
                        </ul>
                    </li> 

                    <!-- Loan Applications -->
                    @if(Sentinel::hasAccess('loans.create'))
                        <li style="padding-left: 10px;">
                            <a href="{{ url('loan/my_applications/data') }}"><i class="fa fa-pencil"></i> My Loan Applications
                            <span class="pull-right-container">
                            <?php
                                    $loan_officer_id = Sentinel::getUser()->id;
                                    ?>
                                <span class="label label-warning pull-right">{{\App\Models\LoanApplication::where('staff_id',$loan_officer_id)->where('status','pending')->count()}}</span>
                            </span>
                            </a>
                        </li>
                    @endif

                    <!-- Clients -->
                    @if(Sentinel::hasAccess('clients'))
                    <li class="treeview @if(Request::is('client/*') || Request::is('group/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-users"></i> <span>{{trans_choice('general.client',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('clients.view'))
                                <li><a href="{{ url('client/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.client',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.my_clients'))
                                <li><a href="{{ url('client/my_clients') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.my',1)}} {{trans_choice('general.client',2)}}
                                <span class="pull-right-container">
                                <?php
                                        $staff_id = Sentinel::getUser()->id;
                                        ?>
                                    <span class="label label-danger pull-right">{{\App\Models\Client::where('staff_id',$staff_id)->where('status','active')->count() }}</span>
                                </span>
                                </a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.branch_clients'))
                                <li><a href="{{ url('client/branch_clients') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.branch',1)}} {{trans_choice('general.client',2)}}
                                <span class="pull-right-container">
                                <?php
                                        $office_id = Sentinel::getUser()->office_id;
                                        ?>
                                    <span class="label label-success pull-right">{{\App\Models\Client::where('office_id',$office_id)->where('status','active')->count() }}</span>
                                </span>
                                </a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.pending_approval'))
                                <li><a href="{{ url('client/pending_approval') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',2)}}
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\Client::where('status','pending')->count() }}</span>
                                    </span>
                                </a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.closed'))
                                <li><a href="{{ url('client/closed') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.closed',1)}}
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\Client::where('status','closed')->count() }}</span>
                                    </span>
                                </a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.closed'))
                                <li><a href="{{ url('client/clients_inactive') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.inactive',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.closed'))
                                <li><a href="{{ url('client/clients_blacklisted') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.blacklisted',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.view'))
                                <li><a href="{{ url('client/declined') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.client',2)}} {{trans_choice('general.declined',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('clients.create'))
                                <li><a href="{{ url('client/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.client',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('groups'))
                                <li><a href="{{ url('group/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}}  {{trans_choice('general.group',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('groups.pending_approval'))
                                <li><a href="{{ url('group/pending_approval') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.group',2)}} {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('groups.view'))
                                <li><a href="{{ url('group/groups_declined') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.group',2)}} {{trans_choice('general.declined',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('groups.view'))
                                <li><a href="{{ url('group/groups_closed') }}"><i class="fa fa-circle-o"></i>{{trans_choice('general.group',2)}} {{trans_choice('general.closed',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('groups.create'))
                                <li><a href="{{ url('group/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}}  {{trans_choice('general.group',1)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif


                       @if(Sentinel::hasAccess('expenses'))
                            <li style="padding-left: 5%;">
                                <a href="{{ url('user/transfers') }}">
                                    <i class="fa fa-exchange"></i>Transfers</a>
                                </li>
                        @endif

                    <!-- Collections -->
                    <li class="treeview @if(Request::is('loan/new_collections') || Request::is('loan/expected_collections') || Request::is('loan/my_collections') || Request::is('loan/my_expected_collections')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-clock-o"></i><span>Collections</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('expenses'))
                            <li><a href="{{ url('loan/new_collections') }}"><i class="fa fa-circle-o"></i> Collections</a></li>
                            @endif
                            @if(Sentinel::hasAccess('expenses'))
                                <li><a href="{{ url('loan/expected_collections') }}"><i class="fa fa-circle-o"></i> Expected collections</a></li>
                            @endif
                            <li><a href="{{ url('loan/my_collections') }}"><i class="fa fa-circle-o"></i> My Collections</a></li>
                            <li><a href="{{ url('loan/my_expected_collections') }}"><i class="fa fa-circle-o"></i> My Expected Collections</a></li>
                        </ul>
                    </li>

                    <!-- Advances -->
                    @if(Sentinel::hasAccess('reports'))
                    <li class="treeview @if(Request::is('advances/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>Advances</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('advances.apply') }}"><i class="fa fa-circle-o"></i> Apply for Advance</a></li>
                            <li><a href="{{ route('advances.my_advances') }}"><i class="fa fa-circle-o"></i> My Advances</a></li>
                            
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('advances.active_advances') }}">
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
                            </a></li>
                            @endif

                            
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('advances.pending_approvals') }}">
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
                            </a></li>
                            @endif

                            
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('advances.topups_pending_approval') }}">
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
                            <li><a href="{{ route('advances.declined_advances') }}">
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
                            </a></li>
                            @endif

                            
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('advances.closed_advances') }}">
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
                            </a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- My Payslips -->
                    @if(Sentinel::hasAccess('loans'))
                        <li style="padding-left: 5%;">
                            <a href="{{ url('payroll/mypayslips') }}">
                                <i class="fa fa-money"></i> <span>My Payslips</span>
                            </a>
                        </li>
                    @endif
                    <!-- @if(Sentinel::hasAccess('loans'))
                        <li class="">
                            <a href="{{ url('payroll/mypayslips_old') }}">
                                <i class="fa fa-money"></i> <span>My Payslips 2023 - Jan 2024</span>
                            </a>
                        </li>
                    @endif  -->
                    <!-- Annual Leave -->
                    @if(Sentinel::hasAccess('reports'))
                    <li class="treeview @if(Request::is('leave/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-user"></i> <span>Annual Leave</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('leave.my_leave_days') }}"><i class="fa fa-circle-o"></i> My Leave Days</a></li>
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('leave.active_leave') }}">
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
                            </a></li>
                            @endif

                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('leave.pending_leave_approvals') }}">
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
                            </a></li>
                            @endif

                            
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('leave.declined_leave') }}">
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
                            </a></li>
                            @endif

                        </ul>
                    </li>
                    @endif

                    <!-- Payroll Loan Applications -->
                    @if(Sentinel::hasAccess('settings'))
                    <li class="treeview @if(Request::is('loan/payroll_loan/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>Payroll Loan Applications</span>
                            <span class="pull-right-container">
                        <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::count()}}</span>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('settings.general.view'))
                                <li><a href="{{ url('loan/payroll_loan/pending_list') }}"><i class="fa fa-circle-o"></i> Pending approval
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::where('status','pending')->count() }}</span>
                                    </span>
                                </a></li>
                            @endif
                            @if(Sentinel::hasAccess('settings.organisation.view'))
                                <li><a href="{{ url('loan/payroll_loan/approved_list') }}"><i class="fa fa-circle-o"></i> Approved
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::where('status','approved')->count() }}</span>
                                    </span>
                                @endif
                            @if(Sentinel::hasAccess('settings.organisation.view'))
                                <li><a href="{{ url('loan/payroll_loan/declined_list') }}"><i class="fa fa-circle-o"></i> Declined
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\PayrollApplicant::where('status','declined')->count() }}</span>
                                    </span>
                                </a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <li><a href="{{ route('policies.view_policies') }}"><i class="fa fa-circle-o"></i> View Policies</a></li>
                    
                </ul>
            </li>
            <!-- Approvals -->
            @if(Sentinel::hasAccess('expenses'))
            <li class="treeview @if(Request::is('user/carry_over_approvals') || Request::is('loan/managers_pending_approval') || Request::is('advance/top_up_approvals') || Request::is('loan/transaction_approvals') || Request::is('loan/reloan_approvals') || Request::is('loan/waiver_approvals') || Request::is('loan/charge_approvals') || Request::is('client/managers_pending_approval')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-thumbs-up"></i> <span>Approvals</span>
                    @if(Sentinel::hasAccess('settings'))
                    <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionsPending::count() + \App\Models\Loan::where('status','pending')->count() + \App\Models\Client::where('status','pending')->count() + \App\Models\LoanTransactionUnapproved::count() + \App\Models\Advance::where('status','pending')->count() + \App\Models\TopUp::where('status','pending')->count() + \App\Models\Leave::where('status', 'pending')->count() + \App\Models\WaiverTransactionUnapproved::where('status', 'pending')->count() +  \App\Models\ChargeTransactionUnapproved::where('status', 'pending')->count()  }}</span>
                    @else
                    <span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionsPending::where('office_id',$office_id)->count() }}</span>
                    @endif
                </a>
                <ul class="treeview-menu">
                    @if(Sentinel::hasAccess('expenses'))
                    <li><a href="{{ url('user/carry_over_approvals') }}"><i class="fa fa-circle-o"></i> Pending Carry Overs</a></li>
                    @endif
                    @if(Sentinel::hasAccess('expenses'))
                    <li><a href="{{ url('loan/managers_pending_approval') }}"><i class="fa fa-circle-o"></i> Loans Pending @if(Sentinel::hasAccess('settings'))<span class="label label-warning pull-right">{{\App\Models\Loan::whereIn('status', ['pending', 'approved'])->count() }}</span>@else<span class="label label-warning pull-right">{{\App\Models\Loan::whereIn('status', ['pending', 'approved'])->where('office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif

                      @if(Sentinel::hasAccess('expenses'))
                    <li><a href="{{ url('loan/pending_client_app_applications') }}"><i class="fa fa-circle-o"></i>Client App Loan Applications @if(Sentinel::hasAccess('settings'))<span class="label label-warning pull-right">{{\App\Models\ClientAppLoanApplications::whereIn('status', ['pending'])->count() }}</span>@else<span class="label label-warning pull-right">{{\App\Models\ClientAppLoanApplications::whereIn('status', ['pending'])->where('branch',$office_id)->count() }}</span>@endif</a></li>
                    @endif


                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ url('advance/top_up_approvals') }}"><i class="fa fa-circle-o"></i> Top Ups Pending Approval @if(Sentinel::hasAccess('settings'))<span class="label label-warning pull-right">{{\App\Models\LoanTopUp::whereIn('status', ['pending'])->count() }}</span>@else<span class="label label-warning pull-right">{{\App\Models\LoanTopUp::whereIn('status', ['pending'])->where('office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif
                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ url('loan/transaction_approvals') }}"><i class="fa fa-circle-o"></i> Transaction Approvals @if(Sentinel::hasAccess('settings'))<span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionUnapproved::count()}}</span>@else<span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionUnapproved::where('office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif
                     @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ url('client/transfer_approvals') }}"><i class="fa fa-circle-o"></i> Client Transfer Approvals @if(Sentinel::hasAccess('settings'))<span class="label label-info pull-right-container" >{{\App\Models\ClientTransferRequest::count()}}</span>@else<span class="label label-info pull-right-container" >{{\App\Models\ClientTransferRequest::where('new_office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif

                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ url('loan/reloan_approvals') }}"><i class="fa fa-circle-o"></i> Reloan Approvals @if(Sentinel::hasAccess('settings'))<span class="label label-info pull-right-container" >{{ 0 }}</span>@else<span class="label label-info pull-right-container" >{{\App\Models\LoanTransactionsPending::where('office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif
                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ route('loan.waiver_approvals') }}"><i class="fa fa-circle-o"></i> Waiver Approvals @if(Sentinel::hasAccess('settings'))<span class="label label-info pull-right-container" >{{\App\Models\WaiverTransactionUnapproved::where('status','pending')->count()}}</span>@else<span class="label label-info pull-right-container" >{{\App\Models\WaiverTransactionUnapproved::where('status','pending')->where('office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif
                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ route('loan.charge_approvals') }}"><i class="fa fa-circle-o"> </i> Charge Approvals @if(Sentinel::hasAccess('settings'))<span class="label label-info pull-right-container" >{{\App\Models\ChargeTransactionUnapproved::where('status','pending')->count()}}</span>@else<span class="label label-info pull-right-container" >{{\App\Models\ChargeTransactionUnapproved::where('status','pending')->where('office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif
                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ url('client/managers_pending_approval') }}"><i class="fa fa-circle-o"></i>Clients Pending Approval @if(Sentinel::hasAccess('settings'))<span class="label label-info pull-right">{{\App\Models\Client::where('status','pending')->count() }}</span>@else<span class="label label-info pull-right">{{\App\Models\Client::where('status','pending')->where('office_id',$office_id)->count() }}</span>@endif</a></li>
                    @endif
                    @hasRole('role.exec', 'role.risk')
                        <li @if(Request::is('approvals/deposit-approvals*')) class="active" @endif><a href="{{ url('approvals/deposit-approvals') }}"><i class="fa fa-circle-o"></i> Deposit Approvals</a></li>
                    @endif
                      @hasRole('role.exec', 'role.risk')
                        <li @if(Request::is('expense/approvals/expense-approvals*')) class="active" @endif><a href="{{ url('expense/approvals/expense-approvals') }}"><i class="fa fa-circle-o"></i> Expense Approvals</a></li>
                    @endif
                </ul>
            </li>
            @endif

            <!-- ============================================
                 ADMINISTRATION SECTION
            ============================================ -->
            @if($role !== 3)
            <li class="treeview @if(Request::is('office/*') || Request::is('setting/*') || Request::is('user/*') || Request::is('asset/*') || Request::is('survey/*') || Request::is('policies/*') || Request::is('audit_trail/*')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-cog"></i> <span>Administration</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <!-- Links for the Administration section -->

                    <!-- Advance Approvals -->
                    @if(Sentinel::hasAccess('reports.client_reports'))
                    <li style="padding-left: 10px;"><a href="{{ route('advances.pending_approvals') }}">
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
                    </a></li>
                    @endif


                    @if(Sentinel::hasAccess('reports.client_reports'))
                    <li style="padding-left: 10px;"><a href="{{ route('advances.topups_pending_approval') }}">
                        <i class="fa fa-money"></i> Advance-TopUps Approvals
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
                    </a></li>
                    @endif
                    
                    <!-- Staff Survey -->
                    @if(Sentinel::hasAccess('reports'))
                    <li class="treeview @if(Request::is('survey/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-thumbs-o-up"></i> <span>Staff Survey</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('reports.client_reports'))
                                <li><a href="{{ route('survey.responses') }}"><i class="fa fa-circle-o"></i> Survey Responses</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    

                    <!-- Company Policies -->
                   @hasRole('role.exec', 'role.policy_manager', 'role.risk')
                    <li class="treeview @if(Request::is('policies/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-book"></i> <span>Company Policies</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('policies.dashboard') }}"><i class="fa fa-circle-o"></i> Policy Dashboard</a></li>
                
                            <li><a href="{{ route('policies.view_policies') }}"><i class="fa fa-circle-o"></i> View Policies</a></li>
                
                            <li><a href="{{ route('policies.user_responses') }}"><i class="fa fa-circle-o"></i> User Responses</a></li>
                    
                            <li><a href="{{ route('policies.add_policies') }}"><i class="fa fa-circle-o"></i> Add Policies</a></li>
                        
                            <li><a href="{{ route('policies.engagements') }}"><i class="fa fa-circle-o"></i> Policy Engagements</a></li>
                        
                            <li><a href="{{ route('policy.quizzes.index') }}"><i class="fa fa-circle-o"></i> Take Quiz</a></li>
                        
                            <li><a href="{{ route('admin.policy-quizzes.index') }}"><i class="fa fa-cog"></i> Manage Quizzes</a></li>
                        </ul>
                    </li>
                    @endif

                    <!-- Administration Expenses -->
                    @if($role==1)
                    <li class="treeview @if(Request::is('administration-expenses*') || Request::is('bank-account-expenses*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>Administration Expenses</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route('administration_expenses.index') }}"><i class="fa fa-circle-o"></i> Administration Fund Expenses</a></li>
                            <li><a href="{{ route('administration_expenses.dashboard') }}"><i class="fa fa-circle-o"></i> Administration Dashboard</a></li>
                            <li><a href="{{ route('bank_account_expenses.index') }}"><i class="fa fa-circle-o"></i> Bank Account Expenses</a></li>
                            <li><a href="{{ route('bank_account_expenses.dashboard') }}"><i class="fa fa-circle-o"></i> Bank Account Dashboard</a></li>
                        </ul>
                    </li>
                    @endif

                    <!-- District Management -->
                    @if($role==1)
                    <li class="treeview @if(Request::is('districts/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-map-marker"></i> <span>Districts & Regions</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('settings'))
                                <li><a href="{{ url('districts') }}"><i class="fa fa-circle-o"></i> Districts</a></li>
                            @endif
                            @if(Sentinel::hasAccess('settings'))
                                <li><a href="{{ url('district-regionals') }}"><i class="fa fa-circle-o"></i> District Regionals</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- Branches -->
                    @if(Sentinel::hasAccess('offices'))
                    <li class="treeview @if(Request::is('office/*')) active menu-open @endif" style="padding-left: 9px;">
                        <a href="#">
                            <i class="fa fa-briefcase"></i> <span>{{trans_choice('general.branch',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('offices.view'))
                                <li><a href="{{ url('office/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.branch',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('offices.create'))
                                <li><a href="{{ url('office/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.branch',1)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Users -->
                    @if(Sentinel::hasAccess('expenses'))
                    <li class="treeview @if(Request::is('user/*') && !Request::is('user/performance_information') && !Request::is('user/leaderboard') && !Request::is('user/appraisal_forms') && !Request::is('user/my_appraisal_forms')) active menu-open @endif">
                        <a href="#" style="padding-left: 22px;">
                            <i class="fa fa-users"></i> <span>{{trans_choice('general.user',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('expenses'))
                                <li><a href="{{ url('user/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.user',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('expenses'))
                                <li><a href="{{ route('user.inactive') }}"><i class="fa fa-circle-o"></i> View Inactive Users</a></li>
                            @endif
                            @if(Sentinel::hasAccess('users.create'))
                                <li><a href="{{ url('user/client_users/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.client_users',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('users.roles.view'))
                                <li><a href="{{ url('user/role/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.role',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('users.create'))
                                <li><a href="{{ url('user/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.user',1)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Assets -->
                    @if(Sentinel::hasAccess('assets'))
                    <li class="treeview @if(Request::is('asset/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-building"></i> <span>{{trans_choice('general.asset',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('assets.view'))
                                <li><a href="{{ url('asset/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.asset',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('assets.create'))
                                <li><a href="{{ url('asset/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.asset',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('assets.types.view'))
                                <li><a href="{{ url('asset/type/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.asset',1)}} {{trans_choice('general.type',2)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Settings -->
                    @if(Sentinel::hasAccess('settings'))
                    <li class="treeview @if(Request::is('setting/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-cog"></i> <span>{{trans_choice('general.setting',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('settings.general.view'))
                                <li><a href="{{ url('setting/general') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.general',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('settings.organisation.view'))
                                <li><a href="{{ url('setting/organisation') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.organisation',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('settings.organisation.view'))
                                <li><a href="{{ url('setting/fail_safe') }}"><i class="fa fa-circle-o"></i> System fail safes</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                </ul>
            </li>
            @endif

  
            @if($role == 1 )
            <li class="treeview @if(Request::is('hr/*')) active @endif">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Human Resources</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    
                    <li class="@if(Request::is('hr/employees')) active @endif">
                        <a href="{{ url('hr/employees') }}">
                            <i class="fa fa-circle-o"></i> Employee Records
                        </a>
                    </li>
                    <li class="@if(Request::is('hr/workforce_analytics')) active @endif">
                        <a href="{{ url('hr/workforce_analytics') }}">
                            <i class="fa fa-circle-o"></i> Workforce Analytics
                        </a>
                    </li>

                    <li class="@if(Request::is('hr/administrative-records*')) active @endif">
                        <a href="{{ url('hr/administrative-records') }}">
                            <i class="fa fa-circle-o"></i> Disciplinary
                        </a>
                    </li>
                    <li class="@if(Request::is('hr/exports*')) active @endif">
                        <a href="{{ url('hr/employee-exports') }}">
                            <i class="fa fa-circle-o"></i> Exports
                        </a>
                    </li>
                </ul>
            </li>
            @endif


            <!-- ============================================
                 PERFORMANCE SECTION
            ============================================ -->
            <li class="treeview @if(Request::is('user/leaderboard') || Request::is('user/appraisal_forms') || Request::is('user/my_appraisal_forms') || Request::is('user/performance_information') || Request::is('payroll/lc_information') || Request::is('payroll/mypayslits')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Performance</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <!-- Leaderboard -->
                    @if(Sentinel::hasAccess('clients'))
                    <li><a href="{{ url('user/leaderboard') }}"><i class="fa fa-trophy"></i> Leaderboard</a></li>
                    @endif
		            <!-- @if(Sentinel::hasAccess('expenses'))
                        <li class="">
                            <a href="{{ route('performance_metrics.index') }}">
                                <i class="fa fa-trophy"></i> <span>Performance Metrics</span>
                            </a>
                        </li>
                    @endif -->
                    @if(Sentinel::hasAccess('expenses'))
                        <li>
                            <a href="{{ url('user/performance_information') }}"><i
                                        class="fa fa-circle-o"></i>Performance Information
                                <span class="pull-right-container">
                                </span>
                            </a>
                        </li>
                    @endif

                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ url('user/branch_performance') }}">
                            <i class="fa fa-circle-o"></i> Branch Performance information
                            </a>
                        </li>
                    @endif

                    @if(Sentinel::hasAccess('expenses'))
                        <li><a href="{{ url('payroll/lc_information') }}">
                            <i class="fa fa-circle-o"></i> Loan Consultant information
                            </a>
                        </li>
                    @endif


                    <!-- Appraisal -->
                    @if(Sentinel::hasAccess('clients'))
                    <li class="treeview @if(Request::is('user/appraisal_forms') || Request::is('user/my_appraisal_forms')) active menu-open @endif">
                        <a href="#">
                            <i class="fa fa-bar-chart"></i> <span>{{trans_choice('general.report',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('reports.client_reports'))
                                <li><a href="{{ url('report/client_report') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.client',1)}} {{trans_choice('general.report',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('reports.loan_reports'))
                                <li><a href="{{ url('report/loan_report') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.loan',1)}} {{trans_choice('general.report',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('reports.financial_reports'))
                                <li><a href="{{ url('report/financial_report') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.financial',1)}} {{trans_choice('general.report',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('reports.company_reports'))
                                <li><a href="{{ url('report/company_report') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.organisation',1)}} {{trans_choice('general.report',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('reports.savings_reports'))
                                <li><a href="{{ url('report/savings_report') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.savings',2)}} {{trans_choice('general.report',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('reports.reports_scheduler.view'))
                                <li><a href="{{ url('report/report_scheduler/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.report',1)}} {{trans_choice('general.scheduler',1)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>

            <!-- ============================================
                 COMMUNICATION & REPORTS SECTION
            ============================================ -->
            <li class="treeview @if(Request::is('communication/*') || Request::is('report/*')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-envelope"></i> <span>Communication</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    <!-- Communication -->
                    @if(Sentinel::hasAccess('communication'))
                    <li class="treeview @if(Request::is('communication/*')) active menu-open @endif" style="padding-left: 10px;">
                        <a href="#">
                            <i class="fa fa-envelope"></i> <span>{{trans_choice('general.communication',1)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('communication.view'))
                                <li><a href="{{ url('communication/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.campaign',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('communication.create'))
                                <li><a href="{{ url('communication/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.create',1)}} {{trans_choice('general.campaign',1)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>


            @if($role !== 3)
                        <li class="treeview active menu-open">
                              <a href="#">
                    <i class="fa fa-money"></i> <span>Money Movements</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                    <ul class="treeview-menu">

               
                    <li style="padding-left: 10px;" class="@if(Request::is('hr/employees')) active @endif">
                        <a href="{{ url('accounting/money_movements') }}">
                            <i class="fa fa-money"></i> Money Movements
                        </a>
                    </li>
                    </ul>
</li>   
            @endif

@if($role == 1)
<li class="treeview @if(Request::is('user/branch_deposits') || Request::is('user/deposit_logs')) active menu-open @endif">
    <a href="#">
        <i class="fa fa-money"></i>
        <span>Branch Deposits</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">
        @if(Sentinel::hasAccess('reports.client_reports'))
            <li class="@if(Request::is('user/branch_deposits')) active @endif">
                <a href="{{ url('user/branch_deposits') }}">
                    <i class="fa fa-circle-o"></i> Branch Deposits
                </a>
            </li>
        @endif

        @if(Sentinel::hasAccess('settings'))
            <li class="@if(Request::is('user/deposit_logs')) active @endif">
                <a href="{{ url('user/deposit_logs') }}">
                    <i class="fa fa-circle-o"></i> Deposit Logs
                </a>
            </li>
        @endif
    </ul>
</li>
@endif
            <!-- ============================================
                 ACCOUNTS SECTION
            ============================================ -->
            @if($role !== 3)
            <li class="treeview @if(Request::is('ledger/*') || Request::is('accounting/*') || Request::is('report/*') || Request::is('expense/*') || Request::is('other_income/*') || Request::is('payroll/*') || Request::is('user/branch_deposits')) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-calculator"></i> <span>Accounts</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
          

                       <!-- Deposits -->
                  


                         <!-- Deposits -->
                    @if(Sentinel::hasAccess('reports.client_reports'))
                    <li style="padding-left: 10px;" class="treeview @if(Request::is('ledger/*')) active menu-open @endif">
                        <a href="#">
                            <i class="fa fa-exchange"></i> <span>Fund Transfers and Payments</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ url('accounting/add_fund_transfers_and_payments') }}"><i class="fa fa-circle-o"></i>Add Funds Transfer</a></li>
                            @endif
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ url('accounting/pending_fund_movements') }}"><i class="fa fa-circle-o"></i>Pending Fund Transfers</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Ledger -->
                    @if(Sentinel::hasAccess('reports.client_reports'))
                    <li style="padding-left: 10px;" class="treeview @if(Request::is('ledger/*')) active menu-open @endif">
                        <a href="#">
                            <i class="fa fa-dashboard"></i> <span>Ledger</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('groups.create'))
                            <li><a href="{{ route('ledger.summary') }}"><i class="fa fa-circle-o"></i> General Ledger</a></li>
                               <li><a href="{{ route('ledger.executive') }}"><i class="fa fa-circle-o"></i>Executive Ledger</a></li>
                            @endif
                            @if(Sentinel::hasAccess('reports.client_reports'))
                            <li><a href="{{ route('ledger.transactions') }}"><i class="fa fa-circle-o"></i> Branch Ledgers</a></li>
                            @endif
                        </ul>
                        

                        <!-- Accounts Section -->
                        @if($role==6 || $role == 1 || $role == 12 || $role == 4)
                        <li class="treeview @if(Request::is('provincial-ledger*')) active menu-open @endif" style="padding-left: 10px;">
                            <a href="#">
                                <i class="fa fa-book"></i> <span>Provincial</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li @if(Request::is('provincial-ledger')) class="active" @endif><a href="{{ url('provincial-ledger') }}"><i class="fa fa-circle-o"></i> Provincial Ledger</a></li>
                                <li @if(Request::is('provincial-ledger/income*')) class="active" @endif><a href="{{ url('provincial-ledger/income') }}"><i class="fa fa-circle-o"></i> Provincial Income</a></li>
                                <li @if(Request::is('provincial-ledger/expenses*')) class="active" @endif><a href="{{ url('provincial-ledger/expenses') }}"><i class="fa fa-circle-o"></i> Provincial Expenses</a></li>
                                @if($role==6 || $role == 1)
                                    <li @if(Request::is('provincial-ledger/balance*')) class="active" @endif><a href="{{ url('provincial-ledger/balance') }}"><i class="fa fa-circle-o"></i> Provincial Cash Balance</a></li>
                                    <li @if(Request::is('provincial-transactions/pending*')) class="active" @endif><a href="{{ url('provincial-transactions/pending') }}"><i class="fa fa-clock-o"></i> Pending Transactions</a></li>
                                    <li @if(Request::is('provincial-transactions/approved*')) class="active" @endif><a href="{{ url('provincial-transactions/approved') }}"><i class="fa fa-check-circle"></i> Approved Transactions</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                    </li>
                    @endif

                    <!-- Accounting -->
                    @if(Sentinel::hasAccess('accounting'))
                    <li style="padding-left: 10px;" class="treeview @if(Request::is('accounting/*')) active menu-open @endif">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>{{trans_choice('general.accounting',1)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('accounting.gl_accounts.view'))
                                <li><a href="{{ url('accounting/gl_account/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.chart_of_account',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('accounting.journals.view'))
                                <li><a href="{{ url('accounting/journal/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.journal',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('accounting.journals.create'))
                                <li><a href="{{ url('accounting/journal/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.journal',1)}} {{trans_choice('general.entry',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('accounting.journals.reconciliation.view'))
                                <li><a href="{{ url('accounting/reconciliation/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.reconciliation',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('accounting.period.view'))
                                <li><a href="{{ url('accounting/period/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.close',1)}} {{trans_choice('general.period',2)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Expenses -->
                    @if(Sentinel::hasAccess('settings'))
                    <li style="padding-left: 10px;" class="treeview @if(Request::is('expense/*')) active menu-open @endif">
                        <a href="#">
                            <i class="fa fa-share"></i> <span>{{trans_choice('general.expense',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">

                            @if(Sentinel::hasAccess('settings'))
                    <li><a href="{{ url('expense/dashboard') }}"><i class="fa fa-circle-o"></i>Expenses Dashboard</a></li>
                    @endif
                            @if(Sentinel::hasAccess('expenses.view'))
                                <li><a href="{{ url('expense/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',1)}} {{trans_choice('general.expense',2)}}</a></li>
                            @endif
                            <!-- @if(Sentinel::hasAccess('expenses.create'))
                                <li><a href="{{ url('expense/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.expense',1)}}</a></li>
                            @endif -->
                            @if(Sentinel::hasAccess('expenses.types.view'))
                                <li><a href="{{ url('expense/type/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.expense',1)}} {{trans_choice('general.type',2)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('expenses.budget.view'))
                                <li><a href="{{ url('expense/budget/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.budget',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('expenses.budget.view'))
                                <li><a href="{{ url('expense/budget/report') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.budget',1)}} {{trans_choice('general.report',2)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Other Income -->
                    @if(Sentinel::hasAccess('other_income'))
                    <li style="padding-left: 10px;" class="treeview @if(Request::is('other_income/*')) active menu-open @endif">
                        <a href="#">
                            <i class="fa fa-plus"></i> <span>{{trans_choice('general.other_income',2)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(Sentinel::hasAccess('other_income.view'))
                                <li><a href="{{ url('other_income/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.view',2)}} {{trans_choice('general.other_income',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('other_income.create'))
                                <li><a href="{{ url('other_income/create') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.add',2)}} {{trans_choice('general.other_income',1)}}</a></li>
                            @endif
                            @if(Sentinel::hasAccess('other_income.create'))
                                <li><a href="{{ url('other_income/type/data') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.manage',2)}} {{trans_choice('general.other_income',1)}} {{trans_choice('general.type',2)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Payroll -->
                    @if(Sentinel::hasAccess('expenses'))
                    <li style="padding-left: 10px;" class="treeview @if(Request::is('payroll/*')) active menu-open @endif">
                        <a href="#">
                            <i class="fa fa-paypal"></i> <span>{{trans_choice('general.payroll',1)}}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                         @if(Sentinel::hasAccess('settings'))
                                <li><a href="{{ url('payroll/company_payroll') }}"><i class="fa fa-circle-o"></i>Company Payroll</a></li>
                            @endif

                               @if(Sentinel::hasAccess('settings'))
                                <li><a href="{{ url('payroll/company_nhima') }}"><i class="fa fa-circle-o"></i>Company NHIMA</a></li>
                            @endif
                            
                               @if(Sentinel::hasAccess('settings'))
                                <li><a href="{{ url('payroll/company_paye') }}"><i class="fa fa-circle-o"></i>Company PAYE</a></li>
                            @endif

                                @if(Sentinel::hasAccess('settings'))
                                <li><a href="{{ url('payroll/company_napsa') }}"><i class="fa fa-circle-o"></i>Company NAPSA</a></li>
                            @endif
                            @if(Sentinel::hasAccess('expenses'))
                                <li><a href="{{ url('payroll/create_wage_bill') }}"><i class="fa fa-circle-o"></i> Add payroll</a></li>
                            @endif
                            @if(Sentinel::hasAccess('expenses'))
                                <li><a href="{{ url('payroll/payroll_list') }}"><i class="fa fa-circle-o"></i> Payroll List</a></li>
                            @endif

                            @if(Sentinel::hasAccess('payroll.update'))
                                <li><a href="{{ url('payroll/template') }}"><i class="fa fa-circle-o"></i> {{trans_choice('general.manage',1)}} {{trans_choice('general.payroll',1)}} {{trans_choice('general.template',2)}}</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                </ul>
            </li>
            @endif

            {{-- ====================================================== --}}
            {{-- HUMAN RESOURCES MODULE                                 --}}
            {{-- ====================================================== --}}

          


                                 <!-- ============================================
                 MOTOR VECHICLE SECTION
            ============================================ -->
            <li class="treeview @if(Request::is('loan/branch_uncollected') || Request::is('loan/managers_pending_approval') || Request::is('advance/top_up_approvals') || Request::is('loan/transaction_approvals') || Request::is('loan/reloan_approvals') || Request::is('loan/waiver_approvals') || Request::is('loan/charge_approvals') || Request::is('client/managers_pending_approval') || Request::is('loan/waiver_approvals') || Request::is('user/carry_over_approvals') || Request::is('advances/*') || Request::is('advance/top_up_approvals') || Request::is('loan/transaction_approvals') || Request::is('loan/reloan_approvals') || Request::is('loan/waiver_approvals') || Request::is('loan/charge_approvals') || Request::is('client/managers_pending_approval') || Request::is('loan/waiver_approvals') || Request::is('user/carry_over_approvals') || Request::is('advances/*') || Request::is('loan/dormant_loans') ) active menu-open @endif">
                <a href="#">
                    <i class="fa fa-car"></i> <span>Motor Vehicle Loans</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <!-- Branch Uncollected -->
                    @if(Sentinel::hasAccess('expenses'))
                    <li><a href="{{ url('vehicles/dashboard') }}"><i class="fa fa-circle-o"></i>Vehicles Dashboard</a></li>
                    @endif
                  
                    <!-- Branch Uncollected -->
                    @if(Sentinel::hasAccess('expenses'))
                    <li><a href="{{ url('vehicles') }}"><i class="fa fa-circle-o"></i>Vehicle Loans</a></li>
                    @endif
                </ul>
            </li>



            {{-- ====================================================== --}}
            {{-- RECOVERIES MODULE                                        --}}
            {{-- ====================================================== --}}
            @if($role != 3 || $role != 2 || $role != 11 || \App\Helpers\GeneralHelper::isSpecialist())
                <li class="treeview @if(Request::is('recovery/*')) active @endif">
                    <a href="#">
                        <i class="fa fa-refresh"></i> <span>Recoveries</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                            <li class="@if(Request::is('recovery/overview')) active @endif">
                                <a href="{{ url('recovery/overview') }}"><i class="fa fa-circle-o"></i> Recovery Dashboard
                                    <span class="pull-right-container">
                                        <span class="label label-info pull-right">{{\App\Models\RecoveryCase::active()->count()}}</span>
                                    </span>
                                </a>
                            </li>
                            <li class="@if(Request::is('recovery/case/data')) active @endif">
                                <a href="{{ url('recovery/case/data') }}"><i class="fa fa-circle-o"></i> All Cases</a>
                            </li>
                            <li class="@if(Request::is('recovery/case/cross_branch')) active @endif">
                                <a href="{{ url('recovery/case/cross_branch') }}"><i class="fa fa-circle-o"></i> Cross-Branch
                                    <span class="pull-right-container">
                                        <span class="label label-primary pull-right">{{\App\Models\RecoveryCase::whereNotNull('approved_date')->where('category', 'cross_branch')->count()}}</span>
                                    </span>
                                </a>
                            </li>
                            <li class="@if(Request::is('recovery/case/escalated')) active @endif">
                                <a href="{{ url('recovery/case/escalated') }}"><i class="fa fa-circle-o"></i> Escalated Accounts
                                    <span class="pull-right-container">
                                        <span class="label label-warning pull-right">{{\App\Models\RecoveryCase::whereNotNull('approved_date')->where('category', 'escalated')->count()}}</span>
                                    </span>
                                </a>
                            </li>
                            <li class="@if(Request::is('recovery/case/dormant')) active @endif">
                                <a href="{{ url('recovery/case/dormant') }}"><i class="fa fa-circle-o"></i> Dormant Revival
                                    <span class="pull-right-container">
                                        <span class="label label-default pull-right">{{\App\Models\RecoveryCase::whereNotNull('approved_date')->where('category', 'dormant')->count()}}</span>
                                    </span>
                                </a>
                            </li>
                            <li class="@if(Request::is('recovery/case/legal')) active @endif">
                                <a href="{{ url('recovery/case/legal') }}"><i class="fa fa-circle-o"></i> Legal Recovery
                                    <span class="pull-right-container">
                                        <span class="label label-danger pull-right">{{\App\Models\RecoveryCase::whereNotNull('approved_date')->where('category', 'legal')->count()}}</span>
                                    </span>
                                </a>
                            </li>
                            <li class="@if(Request::is('recovery/case/skip_trace')) active @endif">
                                <a href="{{ url('recovery/case/skip_trace') }}"><i class="fa fa-circle-o"></i> Skip Tracing
                                    <span class="pull-right-container">
                                        <span class="label label-success pull-right">{{\App\Models\RecoveryCase::whereNotNull('approved_date')->where('category', 'skip_trace')->count()}}</span>
                                    </span>
                                </a>
                            </li>
                            <li class="@if(Request::is('recovery/case/create')) active @endif">
                                <a href="{{ url('recovery/case/create') }}"><i class="fa fa-circle-o"></i> Open New Case</a>
                            </li>
                            <li class="@if(Request::is('recovery/specialist/*')) active @endif">
                                <a href="{{ url('recovery/specialist/data') }}"><i class="fa fa-circle-o"></i> Specialists</a>
                            </li>

                            <li class="@if(Request::is('clients-in-dormant/*')) active @endif">
                                <a href="{{ url('recovery/clients-in-dormant') }}"><i class="fa fa-bell"></i> Client Recovery Hub </a>
                            </li>
                            <li class="@if(Request::is('dept-shares/*')) active @endif">
                                <a href="{{ url('recovery/dept-shares') }}"><i class="fa fa-share"></i> Department Shares </a>
                            </li>
                            <li class="@if(Request::is('recovery/report/*')) active @endif">
                                <!-- <a href="{{ url('recovery/report/overview') }}"><i class="fa fa-circle-o"></i> Recovery Reports</a> -->
                            </li>
                        
                            <!-- Branch Uncollected -->
                            @if(Sentinel::hasAccess('expenses'))
                            <!-- <li><a href="{{ url('loan/branch_uncollected') }}"><i class="fa fa-circle-o"></i> Branch uncollected</a></li> -->
                            @endif
                        
                            <!-- Branch Uncollected -->
                            @if(Sentinel::hasAccess('expenses'))
                            <!-- <li><a href="{{ url('loan/dormant_loans') }}"><i class="fa fa-frown-o"></i>Dormant Loans</a></li> -->
                            @endif
                            @if($role != 3 || $role != 2 || $role != 11)
                                @if(Sentinel::hasAccess('expenses'))
                                    <li>
                                        <a href="{{ url('loan/recovery_case_approvals') }}">
                                            <i class="fa fa-circle-o"></i> 
                                            Cases of Recoveries 
                                            <span class="label label-danger pull-right-container" >
                                                {{\App\Models\RecoveryCase::whereNull('approved_date')->count()}}
                                            </span>
                                        </a>
                                    </li>
                                @endif
                                @if(Sentinel::hasAccess('expenses'))
                                    <li><a href="{{ url('loan/recoveries_approvals') }}"><i class="fa fa-circle-o"></i> Transactions Approvals <span class="label label-danger pull-right-container" >{{\App\Helpers\GeneralHelper::pending_recoveries_approvals_count()}}</span> </a></li>
                                @endif
                                @if(Sentinel::hasAccess('expenses'))
                                    <li><a href="{{ url('loan/approved_recoveries') }}"><i class="fa fa-circle-o"></i> Approved Transactions <span class="label label-danger pull-right-container" >{{\App\Helpers\GeneralHelper::pending_recoveries_approvals_count()}}</span> </a></li>
                                @endif
                            @endif
                    </ul>
                </li>
            </ul>
            @endif
            
            <!-- Sticky Logout Button -->
            <!-- <div class="sidebar-footer" style="position: fixed; bottom: 0; left: 0; background: linear-gradient(135deg, #667eea 0%, #100E3D 100%); padding: 15px; width: 230px; border-radius: 0 0 0 8px; z-index: 1000;">
                <a href="{{ url('logout') }}" class="btn btn-danger btn-block" style="color: #fff; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); font-weight: bold;">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </div> -->

        </section>
        <!-- /.sidebar -->
    </aside>

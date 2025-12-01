<li class="treeview @if(Request::is('resignation/*')) active menu-open @endif">
                    <a href="#">
                        <i class="fa fa-user-times"></i> <span>Resignations </span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                            <li>
                                <a href="{{ route('resignation.create') }}"><i class="fa fa-circle-o"></i> Submit Resignation</a>
                            </li>
                            <li>
                                <a href="{{ route('resignation.my') }}"><i class="fa fa-circle-o"></i> My Resignations</a>
                            </li>
                        @if($role == 4)
                            <li>
                                <a href="{{ route('resignation.manager.pending') }}">
                                    <i class="fa fa-circle-o"></i> Pending Review Approvals
                                    <span class="pull-right-container">
                                        <?php
                                        $user = Sentinel::getUser();
                                        $pendingCount = 0;
                                        if ($user->role->role_id == 4) {
                                            $pendingCount = \App\Models\ResignationLetter::where('status', 'pending')
                                                ->whereHas('user', function($q) use ($user) {
                                                    $q->where('office_id', $user->office_id);
                                                })
                                                ->count();
                                        }
                                        ?>
                                        <span class="label label-warning pull-right">{{ $pendingCount }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if($role == 1)
                            <li>
                                <a href="{{ route('resignation.admin.pending') }}">
                                    <i class="fa fa-circle-o"></i> Pending Final Approvals
                                    <span class="pull-right-container">
                                        <?php
                                        $user = Sentinel::getUser();
                                        $pendingCount = 0;
                                        if ($user->role->role_id == 1) {
                                            $pendingCount = \App\Models\ResignationLetter::where('status', 'manager_approved')->count();
                                        }
                                        ?>
                                        <span class="label label-warning pull-right">{{ $pendingCount }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if($role == 1 || $role == 4)
                            <li>
                                <a href="{{ route('resignation.approved') }}">
                                    <i class="fa fa-circle-o"></i> Approved Resignations
                                    <span class="pull-right-container">
                                        <?php
                                        $user = Sentinel::getUser();
                                        $approvedCount = 0;
                                        if ($user->role->role_id == 1) {
                                            $approvedCount = \App\Models\ResignationLetter::where('status', 'admin_approved')->count();
                                        } elseif ($user->role->role_id == 4) {
                                            $approvedCount = \App\Models\ResignationLetter::where('status', 'admin_approved')
                                                ->whereHas('user', function($q) use ($user) {
                                                    $q->where('office_id', $user->office_id);
                                                })
                                                ->count();
                                        }
                                        ?>
                                        <span class="label label-success pull-right">{{ $approvedCount }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if($role == 1 || $role == 4)
                            <li>
                                <a href="{{ route('resignation.declined') }}">
                                    <i class="fa fa-circle-o"></i> Declined Resignations
                                    <span class="pull-right-container">
                                        <?php
                                        $user = Sentinel::getUser();
                                        $declinedCount = 0;
                                        if ($user->role->role_id == 1) {
                                            $declinedCount = \App\Models\ResignationLetter::where('status', 'declined')->count();
                                        } elseif ($user->role->role_id == 4) {
                                            $declinedCount = \App\Models\ResignationLetter::where('status', 'declined')
                                                ->whereHas('user', function($q) use ($user) {
                                                    $q->where('office_id', $user->office_id);
                                                })
                                                ->count();
                                        }
                                        ?>
                                        <span class="label label-danger pull-right">{{ $declinedCount }}</span>
                                    </span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
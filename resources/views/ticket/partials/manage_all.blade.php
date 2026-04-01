                <div class="tab-pane active" id="manage_all">
                    <h4>All Tickets</h4>
                    <small>{{$totalTickets}} Total Tickets</small>
                    <div class="view-toggle mb-3">
                        <button id="table-view-btn-manage" class="btn btn-secondary">Table View</button>
                        <button id="grid-view-btn-manage" class="btn btn-primary">Grid View</button>
                    </div>
                    <div id="table-view-manage" style="display:none;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Priority</th>
                                <th>Stage</th>
                                <th>Assigned To</th>
                                <th>Created By</th>
                                <th>Issue Category</th>
                                <th>Opened At</th>
                                <th>Closed At</th>
                                <th>Time to Close</th>
                                <th>SLA (Days)</th>
                                <th>Due Date</th>
                                <th>SLA Met</th>
                                <th>Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ $ticket->name }}</td>
                                <td>{{ ucfirst($ticket->priority) }}</td>
                                <td>{{ $ticket->stage ?? '—' }}</td>
                                <td class="text-info"><b>{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}</b></td>
                                <td>{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}</td>
                                <td>{{ optional($ticket->issueCategory)->name ?? '—' }}</td>
                                <td>{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : '—' }}</td>
                                <td>{{ $ticket->date_closed ? date('d M Y H:i', strtotime($ticket->date_closed)) : ($ticket->datetime_close ? date('d M Y H:i', strtotime($ticket->datetime_close)) : '—') }}</td>
                                <td>{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffForHumans(\Carbon\Carbon::parse($ticket->date_closed), true) : '—' }}</td>
                                <td>{{ $ticket->sla_days ?? '—' }}</td>
                                <td>{{ $ticket->due_date ? date('d M Y', strtotime($ticket->due_date)) : '—' }}</td>
                                <td>{!! is_null($ticket->sla_met) ? '&#8212;' : ($ticket->sla_met ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>') !!}</td>
                                <td><button type="button" class="btn btn-xs btn-info view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" data-resolution-comment="{{ e($ticket->resolution_comment) }}" data-status="{{ $ticket->status }}" title="View details"><i class="fa fa-info-circle"></i></button></td>
                                <td class="flex center justrify-between">
                                    @if($isAdmin == 1)
                                        @if($ticket->stage != 'Started')
                                        <button type="button" class="ticket-action action-assign open-assign-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}" data-assigned-to="{{ $ticket->assigned_to }}">
                                            <i class="fa fa-user-plus"></i> Assign
                                        </button>
                                        @endif
                                        &nbsp;
                                        @if($ticket->assigned_to != null)
                                        <button type="button" class="ticket-action action-reassign open-reassign-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}" data-current-assigned="{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '' }}" data-assigned-to="{{ $ticket->assigned_to }}" title="Reassign Ticket">
                                            <i class="fa fa-user"></i> Reassign
                                        </button>
                                        @endif
                                        @if($ticket->stage != 'Started')
                                        <button type="button" class="ticket-action action-reject open-reject-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                        @endif
                                        &nbsp;
                                        @if($ticket->stage != 'Started' && $ticket->assigned_to == null)
                                        <div class="ticket-action text-muted">
                                            <i class="fa fa-clock"></i> Pending
                                        </div>
                                        @elseif($ticket->status == 'resolved')
                                        <div class="ticket-action text-muted">
                                            <i class="fa fa-check-circle"></i> Resolved
                                        </div>
                                        @elseif($ticket->stage == 'Started')
                                        <div class="ticket-action action-working">
                                            <i class="fa fa-cog fa-spin"></i> Working...
                                        </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="16" class="text-center">No tickets available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="pagination-wrapper">
                        {{ $allTickets->links() }}
                    </div>
                    </div>
                    <div id="grid-view-manage">
                        <style>
                        .tk-grid-container {
                            display: grid;
                            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                            gap: 1.5rem;
                            padding: 0.5rem;
                        }
                        .tk-card {
                            background: #ffffff;
                            border: 1px solid #e2e8f0;
                            border-radius: 0.75rem;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
                            transition: all 0.25s ease;
                            overflow: hidden;
                            cursor: pointer;
                        }
                        .tk-card:hover {
                            transform: translateY(-3px);
                            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
                            border-color: #cbd5e1;
                        }
                        .tk-card.tk-selected {
                            border-color: #3b82f6;
                            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                        }
                        /* Card status indicators - colored left border */
                        .tk-card.tk-status-pending-card {
                            border-left: 5px solid #f59e0b;
                        }
                        .tk-card.tk-status-assigned-card {
                            border-left: 5px solid #8c8e91;
                        }
                        .tk-card.tk-status-working-card {
                            border-left: 5px solid #8b5cf6;
                        }
                        .tk-card.tk-status-resolved-card {
                            border-left: 5px solid #10b981;
                        }
                        /* Card background colors */
                        .tk-card.tk-status-assigned-card {
                            background: #f8fafc;
                        }
                        .tk-card.tk-status-resolved-card {
                            background: #d1fae5;
                        }
                        .tk-card-header {
                            padding: 1.25rem;
                            border-bottom: 1px solid #f1f5f9;
                            display: flex;
                            align-items: flex-start;
                            justify-content: space-between;
                            gap: 0.75rem;
                        }
                        .tk-card-title {
                            font-size: 1.9rem;
                            font-weight: 800;
                            color: #001332;
                            margin: 0;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            line-height: 1.0;
                        }
                        .tk-card-title i {
                            color: #64748b;
                            font-size: 1.35rem;
                        }
                        .tk-priority-badge {
                            padding: 0.5rem 1rem;
                            border-radius: 9999px;
                            font-size: 1.1rem;
                            font-weight: 600;
                            text-transform: uppercase;
                            letter-spacing: 0.025em;
                            white-space: nowrap;
                        }
                        .tk-priority-high {
                            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
                            color: #dc2626;
                            border: 1px solid #fecaca;
                        }
                        .tk-priority-medium {
                            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
                            color: #d97706;
                            border: 1px solid #fde68a;
                        }
                        .tk-priority-low {
                            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
                            color: #16a34a;
                            border: 1px solid #bbf7d0;
                        }
                        .tk-card-body {
                            padding: 1.25rem;
                        }
                        .tk-info-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 1rem;
                        }
                        .tk-info-item {
                            display: flex;
                            flex-direction: column;

                            gap: 0.35rem;
                        }
                        .tk-info-item-full {
                            grid-column: 1 / -1;
                        }
                        .tk-info-label {
                            font-size: 1.2rem;
                            color: #64748b;
                            font-weight: 500;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                        }
                        .tk-info-label i {
                            font-size: 1.1rem;
                            width: 1.35rem;
                        }
                        .tk-info-value {
                            font-size: 1.25rem;
                            color: #030b16;
                            font-weight: 700;
                        }
                        .tk-status-badge {
                            display: inline-flex;
                            align-items: center;
                            gap: 0.5rem;
                            padding: 0.5rem 1rem;
                            border-radius: 9999px;
                            font-size: 1.15rem;
                            font-weight: 600;
                        }
                        .tk-status-pending {
                            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
                            color: #b45309;
                            border: 1px solid #fcd34d;
                        }
                        .tk-status-working {
                            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
                            color: #6d28d9;
                            border: 1px solid #c4b5fd;
                        }
                        .tk-status-resolved {
                            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
                            color: #047857;
                            border: 1px solid #6ee7b7;
                        }
                        .tk-status-assigned {
                            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                            color: #1d4ed8;
                            border: 1px solid #93c5fd;
                        }
                        /* Pulse animation for unassigned badge */
                        .tk-status-unassigned {
                            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                            color: #b45309;
                            border: 1px solid #fcd34d;
                        }
                        .tk-status-unassigned.tk-pulse {
                            animation: smoothPulse 0.8s ease-in-out;
                        }
                        @keyframes smoothPulse {
                            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(180, 83, 9, 0.4); }
                            50% { transform: scale(1.05); box-shadow: 0 0 15px 5px rgba(180, 83, 9, 0.3); }
                            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(180, 83, 9, 0); }
                        }
                        .tk-card-footer {
                            padding: 1rem 1.25rem;
                            background: #f8fafc;
                            border-top: 1px solid #e2e8f0;
                            display: flex;
                            flex-wrap: wrap;
                            gap: 0.625rem;
                        }
                        .tk-btn {
                            padding: 0.625rem 1.25rem;
                            font-size: 1.15rem;
                            font-weight: 500;
                            border-radius: 0.5rem;
                            border: none;
                            cursor: pointer;
                            transition: all 0.2s;
                            display: inline-flex;
                            align-items: center;
                            gap: 0.5rem;
                        }
                        .tk-btn:hover {
                            transform: translateY(-1px);
                        }
                        .tk-btn-primary {
                            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                            color: white;
                        }
                        .tk-btn-primary:hover {
                            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
                        }
                        .tk-btn-warning {
                            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                            color: white;
                        }
                        .tk-btn-warning:hover {
                            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
                        }
                        .tk-btn-muted {
                            background: #e2e8f0;
                            color: #64748b;
                        }
                        .tk-office-tag {
                            margin-top: 0.875rem;
                            padding: 0.625rem 1rem;
                            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                            border-radius: 0.5rem;
                            font-size: 1.15rem;
                            color: #475569;
                            font-weight: 600;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                        }
                        @media (max-width: 768px) {
                            .tk-grid-container {
                                grid-template-columns: 1fr;
                            }
                            .tk-info-grid {
                                grid-template-columns: 1fr;
                            }
                        }
                        </style>
                        <div class="tk-grid-container">
                            @forelse($allTickets as $ticket)
                            @php
                                $statusClass = '';
                                if($ticket->stage != 'Started' && $ticket->assigned_to == null) {
                                    $statusClass = 'tk-status-pending-card';
                                } elseif($ticket->status == 'resolved') {
                                    $statusClass = 'tk-status-resolved-card';
                                } elseif($ticket->stage == 'Started') {
                                    $statusClass = 'tk-status-working-card';
                                } elseif($ticket->assigned_to != null) {
                                    $statusClass = 'tk-status-assigned-card';
                                }
                            @endphp
                            <div class="tk-card {{ $statusClass }}">
                                <div class="tk-card-header view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : '—' }}" data-resolution-comment="{{ e($ticket->resolution_comment) }}" data-status="{{ $ticket->status }}">
                                    <h5 class="tk-card-title">
                                        <i class="fa fa-ticket-alt"></i>
                                        {{ \Illuminate\Support\Str::limit($ticket->name, 40, '…') }}
                                    </h5>
                                    <span class="tk-priority-badge tk-priority-{{ strtolower($ticket->priority) }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                                <div class="tk-card-body view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : '—' }}" data-resolution-comment="{{ e($ticket->resolution_comment) }}" data-status="{{ $ticket->status }}">
                                    <div class="tk-info-grid">
                                        <div class="tk-info-item">
                                            <span class="tk-info-label"><i class="fa fa-user-plus"></i> Created By</span>
                                            <span class="tk-info-value">{{ optional($ticket->openedBy)->first_name.' '.optional($ticket->openedBy)->last_name ?? optional($ticket->openedBy)->name ?? '—' }}</span>
                                        </div>
                                        <div class="tk-info-item">
                                            <span class="tk-info-label"><i class="fa fa-user"></i> Assigned</span>
                                            <span class="tk-info-value">{{ optional($ticket->assignedTo)->first_name.' '.optional($ticket->assignedTo)->last_name ?? optional($ticket->assignedTo)->name ?? '—' }}</span>
                                        </div>
                                        <div class="tk-info-item">
                                            <span class="tk-info-label"><i class="fa fa-tasks"></i> Stage</span>
                                            <span class="tk-info-value">{{ $ticket->stage ?? '—' }}</span>
                                        </div>
                                        <div class="tk-info-item">
                                            <span class="tk-info-label"><i class="fa fa-tag"></i> Category</span>
                                            <span class="tk-info-value">{{ optional($ticket->issueCategory)->name ?? '—' }}</span>
                                        </div>
                                        <div class="tk-info-item tk-info-item-full">
                                            <span class="tk-info-label"><i class="fa fa-calendar"></i> Opened</span>
                                            <span class="tk-info-value">{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : '—' }}</span>
                                        </div>
                                        <div class="tk-info-item tk-info-item-full">
                                            <span class="tk-info-label"><i class="fa fa-info-circle"></i> Status</span>
                                            @if($ticket->stage != 'Started' && $ticket->assigned_to == null)
                                                <span class="tk-status-badge tk-status-pending"><i class="fa fa-clock"></i> Pending</span>
                                            @elseif($ticket->status == 'resolved')
                                                <span class="tk-status-badge tk-status-resolved"><i class="fa fa-check-circle"></i> Resolved</span>
                                            @elseif($ticket->stage == 'Started')
                                                <span class="tk-status-badge tk-status-working"><i class="fa fa-cog fa-spin"></i> Working</span>
                                            @elseif($ticket->assigned_to != null)
                                                <span class="tk-status-badge tk-status-assigned"><i class="fa fa-user-check"></i> Assigned</span>
                                            @elseif($ticket->assigned_to == null && $ticket->stage == 'Started')
                                                <span class="tk-status-badge tk-status-unassigned"><i class="fa fa-user-times"></i> Unassigned</span>
                                            @else
                                                <span class="tk-info-value">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tk-office-tag">
                                        <i class="fa fa-building"></i>
                                        {{ optional($ticket->openedBy)->office->name ?? '—' }}
                                    </div>
                                </div>
                                @if($isAdmin == 1)
                                <div class="tk-card-footer">
                                    @if($ticket->stage != 'Started')
                                    <button type="button" class="tk-btn tk-btn-primary open-assign-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}" data-assigned-to="{{ $ticket->assigned_to }}">
                                        <i class="fa fa-user-plus"></i> Assign
                                    </button>
                                    @endif
                                    @if($ticket->assigned_to != null)
                                    <button type="button" class="tk-btn tk-btn-warning open-reassign-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}" data-current-assigned="{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '' }}" data-assigned-to="{{ $ticket->assigned_to }}" title="Reassign Ticket">
                                        <i class="fa fa-user"></i> Reassign
                                    </button>
                                    @endif
                                    @if($ticket->stage != 'Started')
                                    <button type="button" class="tk-btn tk-btn-danger open-reject-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}">
                                        <i class="fa fa-times"></i> Reject
                                    </button>
                                    @endif
                                    @if($ticket->stage != 'Started' && $ticket->assigned_to == null)
                                    <span class="tk-btn tk-btn-muted"><i class="fa fa-clock"></i> Pending</span>
                                    @elseif($ticket->status == 'resolved')
                                    <span class="tk-btn tk-btn-muted"><i class="fa fa-check-circle"></i> Resolved</span>
                                    @elseif($ticket->stage == 'Started')
                                    <span class="tk-btn tk-btn-muted"><i class="fa fa-cog fa-spin"></i> Working...</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="col-12">
                                <p class="text-center">No tickets available.</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="pagination-wrapper mt-3">
                            {{ $allTickets->links() }}
                        </div>
                    </div>

                    

                    <script>
                    $(document).ready(function(){
                        $('#table-view-btn-manage').click(function(){
                            $('#table-view-manage').show();
                            $('#grid-view-manage').hide();
                            $(this).addClass('btn-primary').removeClass('btn-secondary');
                            $('#grid-view-btn-manage').addClass('btn-secondary').removeClass('btn-primary');
                        });
                        $('#grid-view-btn-manage').click(function(){
                            $('#grid-view-manage').show();
                            $('#table-view-manage').hide();
                            $(this).addClass('btn-primary').removeClass('btn-secondary');
                            $('#table-view-btn-manage').addClass('btn-secondary').removeClass('btn-primary');
                        });
                        // Handle card selection (but not when clicking footer buttons)
                        $(document).on('click', '.tk-card.view-ticket-info', function(e){
                            // Don't trigger if clicking on footer or footer buttons
                            if ($(e.target).closest('.tk-card-footer').length) return;
                            $('.tk-card').removeClass('tk-selected');
                            $(this).addClass('tk-selected');
                        });
                        
                        // Random pulse animation for unassigned badges (4-6 seconds interval)
                        function triggerPulse() {
                            $('.tk-status-unassigned').each(function(){
                                var $badge = $(this);
                                $badge.addClass('tk-pulse');
                                setTimeout(function(){
                                    $badge.removeClass('tk-pulse');
                                }, 800);
                            });
                            // Random interval between 4-6 seconds (4000-6000ms)
                            var nextPulse = Math.floor(Math.random() * 2000) + 4000;
                            setTimeout(triggerPulse, nextPulse);
                        }
                        // Start the pulse animation
                        setTimeout(triggerPulse, 2000);
                    });
                    </script>
                </div>
                @include('ticket.partials.view_ticket_modal')
                @include('ticket.partials.reassign_modal')
                @include('ticket.partials.reject_modal')
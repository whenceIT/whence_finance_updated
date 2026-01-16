                <div class="tab-pane" id="requested">
                    <h4>My Ticket Requests</h4>
                    <div class="view-toggle mb-3">
                        <button id="table-view-btn-requested" class="btn btn-secondary">Table View</button>
                        <button id="grid-view-btn-requested" class="btn btn-primary">Grid View</button>
                    </div>
                    @if($myTickets->count())
                    <div id="table-view-requested" style="display:none;">
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ $ticket->name }}</td>
                                <td>{{ ucfirst($ticket->priority) }}</td>
                                <td>{{ $ticket->stage ?? '—' }}</td>
                                <td>{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}</td>
                                <td>{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}</td>
                                <td>{{ optional($ticket->issueCategory)->name ?? '—' }}</td>
                                <td>{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}</td>
                                <td>{{ $ticket->date_closed ? date('d M Y H:i', strtotime($ticket->date_closed)) : ($ticket->datetime_close ? date('d M Y H:i', strtotime($ticket->datetime_close)) : '—') }}</td>
                                <td>{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffForHumans(\Carbon\Carbon::parse($ticket->date_closed), true) : '—' }}</td>
                                <td>{{ $ticket->sla_days ?? '—' }}</td>
                                <td>{{ $ticket->due_date ? date('d M Y', strtotime($ticket->due_date)) : '—' }}</td>
                                <td>{!! is_null($ticket->sla_met) ? '&#8212;' : ($ticket->sla_met ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>') !!}</td>
                                <td>
                                    @if($ticket->status == 'resolved')
                                        @if($ticket->status != 'closed')
                                            <button type="button" class="btn btn-sm btn-success open-close-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ $ticket->name }}">Completed</button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-warning open-close-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ $ticket->name }}" data-mode="rate">Rate</button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div id="grid-view-requested">
                        <style>
                        .ticket-card {
                            position: relative;
                            width: 100%;
                            height: 200px;
                            border-radius: 0;
                            border: 1px solid #dee2e6;
                            transition: transform 0.2s, box-shadow 0.2s;
                            cursor: pointer;
                            padding: 1rem;
                            margin: 1rem;
                        }
                        .ticket-card:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                        }
                        .ticket-card.clicked {
                            background-color: #f0f8ff;
                            border-color: #007bff;
                        }
                        .ticket-card-body {
                            position: absolute;
                            top: 0;
                            left: 0;
                            right: 0;
                            bottom: 0;
                            display: flex;
                            flex-direction: column;
                            padding: 0.75rem;
                        }
                        .ticket-title {
                            font-weight: bold;
                            margin-bottom: 0.5rem;
                        }
                        .ticket-info {
                            flex-grow: 1;
                            display: flex;
                            flex-direction: column;
                            gap: 0.2rem;
                        }
                        .ticket-info-row {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                        }
                        .ticket-info-label {
                            font-weight: 600;
                            color: #6c757d;
                            font-size: 1.3rem;
                        }
                        .ticket-info-value {
                            font-size: 1.3rem;
                        }
                        .ticket-actions {
                            margin-top: auto;
                            display: flex;
                            gap: 0.5rem;
                            flex-wrap: wrap;
                        }
                        .ticket-btn {
                            font-size: 0.75rem;
                            padding: 0.25rem 0.5rem;
                        }
                        @media (max-width: 767px) {
                            .ticket-card {
                                height: auto;
                                min-height: 200px;
                            }
                            .ticket-card-body {
                                position: static;
                                padding: 0.75rem;
                            }
                        }
                        </style>
                        <div class="row">
                            @foreach($myTickets as $ticket)
                            <div class="col-12 col-md-6 col-lg-3 mb-3">
                                <div class="card shadow-none ticket-card view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}">
                                    <div class="ticket-card-body">
                                        <h5 class="ticket-title">
                                            <i class="fa fa-ticket-alt"></i> {{ \Illuminate\Support\Str::limit($ticket->name, 30, '…') }}
                                        </h5>
                                        <div class="ticket-info">
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-exclamation-triangle"></i> Priority:</span>
                                                <span class="ticket-info-value">{{ ucfirst($ticket->priority) }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-tasks"></i> Stage:</span>
                                                <span class="ticket-info-value">{{ $ticket->stage ?? '—' }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-user"></i> Assigned To:</span>
                                                <span class="ticket-info-value text-warning">{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-tag"></i> Issue Category:</span>
                                                <span class="ticket-info-value">{{ optional($ticket->issueCategory)->name ?? '—' }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-calendar"></i> Opened At:</span>
                                                <span class="ticket-info-value">{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}</span>
                                            </div>
                                        </div>
                                        <div class="ticket-actions">
                                            @if($ticket->status == 'resolved')
                                            <div class="ticket-action action-resolved">
                                                <i class="fa fa-check-circle"></i> Resolved
                                            </div>
                                            @else
                                            <div class="ticket-action action-working">
                                                <i class="fa fa-cog fa-spin"></i> Working on it...
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                        <p>You haven't requested any tickets.</p>
                    @endif
                    <script>
                    $(document).ready(function(){
                        $('#table-view-btn-requested').click(function(){
                            $('#table-view-requested').show();
                            $('#grid-view-requested').hide();
                            $(this).addClass('btn-primary').removeClass('btn-secondary');
                            $('#grid-view-btn-requested').addClass('btn-secondary').removeClass('btn-primary');
                        });
                        $('#grid-view-btn-requested').click(function(){
                            $('#grid-view-requested').show();
                            $('#table-view-requested').hide();
                            $(this).addClass('btn-primary').removeClass('btn-secondary');
                            $('#table-view-btn-requested').addClass('btn-secondary').removeClass('btn-primary');
                        });
                        $(document).on('click', '.view-ticket-info', function(){
                            $('.ticket-card').removeClass('clicked');
                            $(this).closest('.ticket-card').addClass('clicked');
                        });
                    });
                    </script>
                </div>
                @include('ticket.partials.view_ticket_modal')
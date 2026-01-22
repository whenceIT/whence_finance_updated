                <div class="{{ $isAdmin == 1 ? 'tab-pane' : 'tab-pane active' }}" id="assigned">
                    <h4>Assigned to Me</h4>
                    <div class="view-toggle mb-3">
                        <button id="table-view-btn" class="btn btn-secondary">Table View</button>
                        <button id="grid-view-btn" class="btn btn-primary">Grid View</button>
                    </div>
                    @if($assignedTickets->count())
                    <div id="table-view" style="display:none;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
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
                            @foreach($assignedTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ $ticket->name }}</td>
                                <td>{{ ucfirst($ticket->priority) }}</td>
                                <td>{{ $ticket->stage ?? '—' }}</td>
                                <td>Me</td>
                                <td>{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->last_name ?? '—' }} ({{ optional($ticket->openedBy)->office->name ?? '—' }})</td>
                                <td>{{ optional($ticket->issueCategory)->name ?? '—' }}</td>
                                <td>{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}</td>
                                <td>{{ $ticket->date_closed ? date('d M Y H:i', strtotime($ticket->date_closed)) : ($ticket->datetime_close ? date('d M Y H:i', strtotime($ticket->datetime_close)) : '—') }}</td>
                                <td>{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffForHumans(\Carbon\Carbon::parse($ticket->date_closed), true) : '—' }}</td>
                                <td>{{ $ticket->sla_days ?? '—' }}</td>
                                <td>{{ $ticket->due_date ? date('d M Y', strtotime($ticket->due_date)) : '—' }}</td>
                                <td>{!! is_null($ticket->sla_met) ? '&#8212;' : ($ticket->sla_met ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>') !!}</td>
                                <td><button type="button" class="btn btn-xs btn-info view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" data-resolution-comment="{{ e($ticket->resolution_comment) }}" data-status="{{ $ticket->status }}" title="View details"><i class="fa fa-info-circle"></i></button></td>
                                <td>
                                    @if($ticket->status != 'resolved')
                                    <button class="btn btn-sm btn-info resolve-btn" data-ticket-id="{{ $ticket->id }}">Resolve</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div id="grid-view">
                        <style>
                        .ticket-card {
                            position: relative;
                            width: 100%;
                            height: 180px;
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
                                min-height: 180px;
                            }
                            .ticket-card-body {
                                position: static;
                                padding: 0.75rem;
                            }
                        }
                        </style>
                        <div class="row">
                            @foreach($assignedTickets as $ticket)
                            <div class="col-12 col-md-6 col-lg-3 mb-3">
                                <div class="card shadow-none ticket-card view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" data-resolution-comment="{{ e($ticket->resolution_comment) }}" data-status="{{ $ticket->status }}">
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
                                                <span class="ticket-info-label"><i class="fa fa-user-plus"></i> Created By:</span>
                                                <span class="ticket-info-value">{{ optional($ticket->openedBy)->first_name && optional($ticket->openedBy)->last_name ? optional($ticket->openedBy)->first_name . ' ' . optional($ticket->openedBy)->last_name : '—' }} </span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-tag"></i> Issue Category:</span>
                                                <span class="ticket-info-value">{{ optional($ticket->issueCategory)->name ?? '—' }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-calendar"></i> Opened At:</span>
                                                <span class="ticket-info-value">{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}</span>
                                            </div>

                                            <div>
                                                <b>{{ optional($ticket->openedBy)->office->name ?? '—' }}</b>
                                            </div>
                                        </div>
                                        <div class="ticket-actions">
                                            @if($ticket->status != 'resolved')
                                            <button class="btn btn-success ticket-btn resolve-btn" style="font-size: 0.9em; padding: 0.4rem 0.8rem;" onclick="event.stopPropagation()" data-ticket-id="{{ $ticket->id }}"><i class="fa fa-check"></i> Resolve</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                        <p>No tickets assigned to you.</p>
                    @endif
                    <script>
                    $(document).ready(function(){
                        $('#table-view-btn').click(function(){
                            $('#table-view').show();
                            $('#grid-view').hide();
                            $(this).addClass('btn-primary').removeClass('btn-secondary');
                            $('#grid-view-btn').addClass('btn-secondary').removeClass('btn-primary');
                        });
                        $('#grid-view-btn').click(function(){
                            $('#grid-view').show();
                            $('#table-view').hide();
                            $(this).addClass('btn-primary').removeClass('btn-secondary');
                            $('#table-view-btn').addClass('btn-secondary').removeClass('btn-primary');
                        });
                        $(document).on('click', '.view-ticket-info', function(){
                                $('.ticket-card').removeClass('clicked');
                                $(this).closest('.ticket-card').addClass('clicked');
                            });
                            $('.resolve-btn').click(function(){
                                var ticketId = $(this).data('ticket-id');
                                $('#resolveForm').attr('action', '/ticket/' + ticketId + '/update');
                                $('#resolveModal').modal('show');
                            });
                            $('#resolveModal').on('hidden.bs.modal', function () {
                                $('#resolveForm')[0].reset();
                            });
                        });
                    </script>
                </div>

                <!-- Resolve Modal -->
                <div class="modal fade" id="resolveModal" tabindex="-1" role="dialog" aria-labelledby="resolveModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="resolveModalLabel">Resolve Ticket</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="resolveForm" method="post">
                                @csrf
                                <input type="hidden" name="status" value="resolved">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="resolution_comment">Resolution Comment</label>
                                        <textarea class="form-control" id="resolution_comment" name="resolution_comment" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Resolve</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @include('ticket.partials.view_ticket_modal')
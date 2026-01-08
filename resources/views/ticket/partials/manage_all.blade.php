                <div class="tab-pane active" id="manage_all">
                    <h4>All Tickets</h4>
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
                                <!-- <th>Rating</th>
                                <th>Remarks</th> -->
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
                                <!-- <td>{{ $ticket->rating ?? '—' }}</td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ticket->remarks ?? '—' }}</td> -->
                                <td><button type="button" class="btn btn-xs btn-info view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" title="View details"><i class="fa fa-info-circle"></i></button></td>
                                <td>
                                    @if($isAdmin == 1)
                                       @if($ticket->stage != 'Started')
                                       <button type="button" class="ticket-action action-assign open-assign-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}" data-assigned-to="{{ $ticket->assigned_to }}">
                                           <i class="fa fa-user-plus"></i> Assign
                                       </button>
                                       @elseif($ticket->status == 'resolved')
                                       <div class="ticket-action action-resolved">
                                           <i class="fa fa-check-circle"></i> Resolved
                                       </div>
                                       @else
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
                </div>
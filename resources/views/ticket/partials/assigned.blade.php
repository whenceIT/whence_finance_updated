                <div class="{{ $isAdmin == 1 ? 'tab-pane' : 'tab-pane active' }}" id="assigned">
                    <div class="view-toggle mb-3">
                        <button id="table-view-btn" class="btn btn-primary">Table View</button>
                        <button id="grid-view-btn" class="btn btn-secondary">Grid View</button>
                    </div>
                    @if($assignedTickets->count())
                    <div id="table-view">
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
                                <th>Rating</th>
                                <th>Remarks</th>
                                <th>Details</th>
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
                                <td>{{ optional($ticket->createdBy)->first_name ?? optional($ticket->createdBy)->name ?? '—' }}</td>
                                <td>{{ optional($ticket->issueCategory)->name ?? '—' }}</td>
                                <td>{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}</td>
                                <td>{{ $ticket->date_closed ? date('d M Y H:i', strtotime($ticket->date_closed)) : ($ticket->datetime_close ? date('d M Y H:i', strtotime($ticket->datetime_close)) : '—') }}</td>
                                <td>{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffForHumans(\Carbon\Carbon::parse($ticket->date_closed), true) : '—' }}</td>
                                <td>{{ $ticket->sla_days ?? '—' }}</td>
                                <td>{{ $ticket->due_date ? date('d M Y', strtotime($ticket->due_date)) : '—' }}</td>
                                <td>{!! is_null($ticket->sla_met) ? '&#8212;' : ($ticket->sla_met ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>') !!}</td>
                                <td>{{ $ticket->rating ?? '—' }}</td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ticket->remarks ?? '—' }}</td>
                                <td><button type="button" class="btn btn-xs btn-info view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" title="View details"><i class="fa fa-info-circle"></i></button></td>
                                <td>
                                    @if($ticket->status != 'resolved')
                                    <form method="post" action="{{ url('ticket/'.$ticket->id.'/update') }}" style="display:inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="resolved">
                                        <button class="btn btn-sm btn-info" onclick="return confirm('Mark ticket as resolved?')">Resolve</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div id="grid-view" style="display:none;">
                        <div class="row">
                            @foreach($assignedTickets as $ticket)
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $ticket->name }}</h5>
                                        <p class="card-text">
                                            <strong>Priority:</strong> {{ ucfirst($ticket->priority) }}<br>
                                            <strong>Stage:</strong> {{ $ticket->stage ?? '—' }}<br>
                                            <strong>Assigned To:</strong> Me<br>
                                            <strong>Created By:</strong> {{ optional($ticket->createdBy)->first_name ?? optional($ticket->createdBy)->name ?? '—' }}<br>
                                            <strong>Issue Category:</strong> {{ optional($ticket->issueCategory)->name ?? '—' }}<br>
                                            <strong>Opened At:</strong> {{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}<br>
                                            <strong>Actions:</strong>
                                            <button type="button" class="btn btn-xs btn-info view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" title="View details"><i class="fa fa-info-circle"></i></button>
                                            @if($ticket->status != 'resolved')
                                            <form method="post" action="{{ url('ticket/'.$ticket->id.'/update') }}" style="display:inline-block">
                                                @csrf
                                                <input type="hidden" name="status" value="resolved">
                                                <button class="btn btn-sm btn-info" onclick="return confirm('Mark ticket as resolved?')">Resolve</button>
                                            </form>
                                            @endif
                                        </p>
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
                    });
                    </script>
                </div>
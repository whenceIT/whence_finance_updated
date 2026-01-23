<div class="tab-pane" id="my_requested_resolved">
    <h4>My Resolved Ticket Requests</h4>
    <div class="view-toggle mb-3">
        <button id="table-view-btn-requested-resolved" class="btn btn-secondary">Table View</button>
        <button id="grid-view-btn-requested-resolved" class="btn btn-primary">Grid View</button>
    </div>
    @if($myResolvedTickets->count())
        <div id="table-view-requested-resolved" style="display:none;">
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
                <tbody id="my_requested_resolved_table_body">
                    @foreach($myResolvedTickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->name }}</td>
                            <td>{{ ucfirst($ticket->priority) }}</td>
                            <td>{{ $ticket->stage ?? '—' }}</td>
                            <td>{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}
                            </td>
                            <td>{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}</td>
                            <td>{{ optional($ticket->issueCategory)->name ?? '—' }}</td>
                            <td>{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}
                            </td>
                            <td>{{ $ticket->date_closed ? date('d M Y H:i', strtotime($ticket->date_closed)) : ($ticket->datetime_close ? date('d M Y H:i', strtotime($ticket->datetime_close)) : '—') }}
                            </td>
                            <td>{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffForHumans(\Carbon\Carbon::parse($ticket->date_closed), true) : '—' }}
                            </td>
                            <td>{{ $ticket->sla_days ?? '—' }}</td>
                            <td>{{ $ticket->due_date ? date('d M Y', strtotime($ticket->due_date)) : '—' }}</td>
                            <td>{!! is_null($ticket->sla_met) ? '&#8212;' : ($ticket->sla_met ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>') !!}
                            </td>
                            <td>
                                @if($ticket->status == 'resolved')
                                    @if($ticket->status != 'closed')
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-success open-close-modal"
                                                data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ $ticket->name }}">Completed</button>
                                            <button type="button" class="btn btn-sm btn-primary open-rate-modal"
                                                data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ $ticket->name }}">Rate</button>
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-sm btn-warning open-close-modal"
                                            data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ $ticket->name }}"
                                            data-mode="rate">Rate</button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="grid-view-requested-resolved">
            <div class="row">
                @foreach($myResolvedTickets as $ticket)
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
                        <div class="card shadow-none ticket-card view-ticket-info" data-ticket-name="{{ e($ticket->name) }}"
                            data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}"
                            data-ticket-rating="{{ $ticket->rating ?? 0 }}"
                            data-ticket-description="{{ e($ticket->description) }}"
                            data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}"
                            data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}"
                            data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}"
                            data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}"
                            data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}"
                            data-resolution-comment="{{ e($ticket->resolution_comment) }}" data-status="{{ $ticket->status }}">
                            <div class="ticket-card-body">
                                <h5 class="ticket-title">
                                    <i class="fa fa-ticket-alt"></i>
                                    {{ \Illuminate\Support\Str::limit($ticket->name, 30, '…') }}
                                </h5>
                                <div class="ticket-info">
                                    <div class="ticket-info-row">
                                        <span class="ticket-info-label"><i class="fa fa-exclamation-triangle"></i>
                                            Priority:</span>
                                        <span class="ticket-info-value">{{ ucfirst($ticket->priority) }}</span>
                                    </div>
                                    <div class="ticket-info-row">
                                        <span class="ticket-info-label"><i class="fa fa-tasks"></i> Stage:</span>
                                        <span class="ticket-info-value">{{ $ticket->stage ?? '—' }}</span>
                                    </div>
                                    <div class="ticket-info-row">
                                        <span class="ticket-info-label"><i class="fa fa-user"></i> Assigned To:</span>
                                        <span
                                            class="ticket-info-value">{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}</span>
                                    </div>
                                    <div class="ticket-info-row">
                                        <span class="ticket-info-label"><i class="fa fa-tag"></i> Issue Category:</span>
                                        <span
                                            class="ticket-info-value">{{ optional($ticket->issueCategory)->name ?? '—' }}</span>
                                    </div>
                                    <div class="ticket-info-row">
                                        <span class="ticket-info-label"><i class="fa fa-calendar"></i> Opened At:</span>
                                        <span
                                            class="ticket-info-value">{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}</span>
                                    </div>
                                </div>
                                <div class="ticket-actions">
                                    @if($ticket->status == 'resolved')
                                        @if($ticket->status != 'closed')
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success ticket-btn open-close-modal"
                                                    onclick="event.stopPropagation()" data-ticket-id="{{ $ticket->id }}"
                                                    data-ticket-name="{{ $ticket->name }}">Completed</button>
                                                <button type="button" class="btn btn-primary ticket-btn open-rate-modal"
                                                    onclick="event.stopPropagation()" data-ticket-id="{{ $ticket->id }}"
                                                    data-ticket-name="{{ $ticket->name }}">Rate</button>
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-warning ticket-btn open-close-modal"
                                                onclick="event.stopPropagation()" data-ticket-id="{{ $ticket->id }}"
                                                data-ticket-name="{{ $ticket->name }}" data-mode="rate">Rate</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p>No resolved requests found.</p>
    @endif
    <script>
        $(document).ready(function () {
            $('#table-view-btn-requested-resolved').click(function () {
                $('#table-view-requested-resolved').show();
                $('#grid-view-requested-resolved').hide();
                $(this).addClass('btn-primary').removeClass('btn-secondary');
                $('#grid-view-btn-requested-resolved').addClass('btn-secondary').removeClass('btn-primary');
            });
            $('#grid-view-btn-requested-resolved').click(function () {
                $('#grid-view-requested-resolved').show();
                $('#table-view-requested-resolved').hide();
                $(this).addClass('btn-primary').removeClass('btn-secondary');
                $('#table-view-btn-requested-resolved').addClass('btn-secondary').removeClass('btn-primary');
            });

            // Handle rate modal
            $(document).on('click', '.open-rate-modal', function () {
                var ticketId = $(this).data('ticket-id');
                var ticketName = $(this).data('ticket-name');
                $('#rateTicketName').text(ticketName);
                $('#rateTicketForm').attr('action', '{{ url('ticket') }}' + '/' + ticketId + '/update');
                $('#rateTicketModal').modal('show');
            });

            // Handle predefined remarks selection
            $('#predefined_remarks').on('change', function() {
                var selectedRemark = $(this).val();
                if (selectedRemark) {
                    $('#custom_remarks').val(selectedRemark);
                }
            });

            // Reset form when modal is hidden
            $('#rateTicketModal').on('hidden.bs.modal', function () {
                $('#rateTicketForm')[0].reset();
            });
        });
    </script>

    <!-- Rate Ticket Modal -->
    <div class="modal fade" id="rateTicketModal" tabindex="-1" role="dialog" aria-labelledby="rateTicketModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" id="rateTicketForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="rateTicketModalLabel">Rate Ticket: <span id="rateTicketName"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <select name="rating" id="rating" class="form-control" required>
                                <option value="">-- Select Rating --</option>
                                <option value="1">⭐ 1 - Very Poor</option>
                                <option value="2">⭐⭐ 2 - Poor</option>
                                <option value="3">⭐⭐⭐ 3 - Fair</option>
                                <option value="4">⭐⭐⭐⭐ 4 - Good</option>
                                <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="predefined_remarks">Predefined Remarks</label>
                            <select name="predefined_remarks" id="predefined_remarks" class="form-control">
                                <option value="">-- Select a remark --</option>
                                <option value="Excellent service and quick resolution!">Excellent service and quick resolution!</option>
                                <option value="Good job, issue was resolved satisfactorily.">Good job, issue was resolved satisfactorily.</option>
                                <option value="Resolution was adequate but could be faster.">Resolution was adequate but could be faster.</option>
                                <option value="The solution worked, but communication could be better.">The solution worked, but communication could be better.</option>
                                <option value="Issue resolved, but not completely satisfied with the process.">Issue resolved, but not completely satisfied with the process.</option>
                                <option value="Very poor experience, took too long to resolve.">Very poor experience, took too long to resolve.</option>
                                <option value="Resolution was ineffective, issue persists.">Resolution was ineffective, issue persists.</option>
                                <option value="Satisfied with the outcome and support provided.">Satisfied with the outcome and support provided.</option>
                                <option value="Thank you for the prompt assistance!">Thank you for the prompt assistance!</option>
                                <option value="Resolution met expectations, good work!">Resolution met expectations, good work!</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="custom_remarks">Additional Remarks (Optional)</label>
                            <textarea name="custom_remarks" id="custom_remarks" class="form-control" rows="3" placeholder="Add any additional comments..."></textarea>
                        </div>
                        <input type="hidden" name="status" value="closed">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Rating</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
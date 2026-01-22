@extends('layouts.master')

@section('title', 'Tickets')

@section('content')
    @include('partials.fullscreen_loader')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Tickets</h3>
            <div class="box-tools pull-right">
                <a href="{{ url('ticket/create') }}" class="btn btn-success btn-sm"
                    onclick="event.preventDefault(); $('#fullscreen-loader').show(); window.location.href = '{{ url('ticket/create') }}';">New
                    Ticket</a>
            </div>
        </div>
        <div class="box-body">

            <div>
                <style>
                    /* 🎨 Highlight: Assigned & Resolved tabs */
                    .nav-tabs>li.tab-special>a {
                        background: linear-gradient(135deg, #2d9cdb, #117a8b);
                        color: #ffffff;
                        border-radius: 4px 4px 0 0;
                        margin-right: 6px;
                    }

                    .nav-tabs>li.tab-special.active>a,
                    .nav-tabs>li.tab-special>a:hover {
                        background: linear-gradient(135deg, #1b6fa8, #0f4f6a) !important;
                        color: #fff !important;
                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
                    }

                    .nav-tabs>li.tab-special>a .badge {
                        background: rgba(255, 255, 255, 0.16);
                        color: #fff;
                        margin-left: 6px;
                    }

                    /* 🟢 Highlight: Summary Report tab */
                    .nav-tabs>li.tab-summary>a {
                        background: linear-gradient(135deg, #28a745, #1e7e34);
                        color: #ffffff;
                        border-radius: 4px 4px 0 0;
                        margin-right: 6px;
                    }

                    .nav-tabs>li.tab-summary.active>a,
                    .nav-tabs>li.tab-summary>a:hover {
                        background: linear-gradient(135deg, #1e7e34, #155d27) !important;
                        color: #fff !important;
                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
                    }

                    /* 🟡 Highlight: My Ticket Requests tab */
                    .nav-tabs>li.tab-yellow>a {
                        background: orange;
                        color: #000;
                        border-radius: 4px 4px 0 0;
                        margin-right: 6px;
                    }

                    .nav-tabs>li.tab-yellow.active>a,
                    .nav-tabs>li.tab-yellow>a:hover {
                        background: rgb(255, 187, 0) !important;
                        color: #000 !important;
                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
                    }

                    /* keep transitions smooth */
                    .nav-tabs>li>a {
                        transition: all .12s ease;
                    }

                    /* Highlight summary date inputs */
                    #summaryFrom,
                    #summaryTo {
                        background-color: #fff7c6;
                        /* pale yellow */
                        border: 1px solid #f1c40f;
                        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
                    }

                    /* stronger highlight on focus */
                    #summaryFrom:focus,
                    #summaryTo:focus {
                        outline: none;
                        box-shadow: 0 0 6px rgba(241, 196, 15, 0.25);
                        border-color: #f39c12;
                    }

                    /* Cool UI for ticket status conditions */
                    .ticket-status-done {
                        background: linear-gradient(135deg, #28a745, #20c997);
                        color: white;
                        padding: 4px 8px;
                        border-radius: 4px;
                        font-weight: bold;
                        text-align: center;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }

                    .ticket-status-working {
                        background: linear-gradient(135deg, #ffc107, #fd7e14);
                        color: white;
                        padding: 4px 8px;
                        border-radius: 4px;
                        font-weight: bold;
                        text-align: center;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        animation: pulse 2s infinite;
                    }

                    @keyframes pulse {
                        0% {
                            opacity: 1;
                        }

                        50% {
                            opacity: 0.7;
                        }

                        100% {
                            opacity: 1;
                        }
                    }

                    /* Enhanced modal title */
                    #viewTicketModalLabel span {
                        font-size: 1.5em;
                        font-weight: bold;
                        color: #333;
                    }

                    /* Pro UI for ticket actions */
                    .ticket-action {
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        padding: 6px 12px;
                        border-radius: 6px;
                        font-weight: 500;
                        font-size: 0.9em;
                        text-align: center;
                        transition: all 0.2s ease;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }

                    .action-assign {
                        background: linear-gradient(135deg, #007bff, #0056b3);
                        color: white;
                        border: none;
                    }

                    .action-assign:hover {
                        background: linear-gradient(135deg, #0056b3, #004085);
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
                    }

                    .action-resolved {
                        background: linear-gradient(135deg, #28a745, #1e7e34);
                        color: white;
                    }

                    .action-working {
                        background: linear-gradient(135deg, #ffc107, #e0a800);
                        color: white;
                        animation: pulse-working 2s infinite;
                    }

                    @keyframes pulse-working {
                        0% {
                            opacity: 1;
                            transform: scale(1);
                        }

                        50% {
                            opacity: 0.8;
                            transform: scale(1.02);
                        }

                        100% {
                            opacity: 1;
                            transform: scale(1);
                        }
                    }
                </style>
                <?php
    $user = Sentinel::getUser();
    if ($user) {
        $role = $user->role->role_id;
        $office = $user->office->id;
        $isAdmin = $role;
    } else {
        $role = null;
        $office = null;
        $isAdmin = $role;
    }
                        ?>

                <ul class="nav nav-tabs">
                    @if($isAdmin == 1)
                        <li class="active"><a href="#manage_all" data-toggle="tab">All Tickets <span
                                    class="badge">{{ $allTickets->count() }}</span></a></li>
                    @endif
                    @if($isAdmin == 1)
                        <li class="tab-summary"><a href="#summary_report" data-toggle="tab">Summary Report</a>
                        </li>
                        <li class="tab-special"><a href="#assigned" data-toggle="tab">Tickets Assigned to Me <span
                            class="badge">{{ $assignedTickets->count() }}</span></a>
                        </li>
                    @else
                        <li class="tab-special active"><a href="#assigned" data-toggle="tab">Tickets Assigned to Me <span
                            class="badge">{{ $assignedTickets->count() }}</span></a>
                        </li>
                    @endif
                    <li class="tab-special"><a href="#resolved_by_me" data-toggle="tab">Tickets I Resolved <span
                        class="badge">{{ $assignedClosedTickets->count() }}</span></a>
                    </li>
                    <li class="tab-yellow"><a href="#requested" data-toggle="tab">My Ticket Requests <span
                        class="badge">{{ $myTickets->count() }}</span></a>
                    </li>
                    <li><a href="#my_requested_resolved" data-toggle="tab">My Requested Resolved Tickets <span
                                class="badge">{{ $myResolvedTickets->count() }}</span></a></li>
                    <li><a href="#closed_requests" data-toggle="tab">My Closed Issues <span
                                class="badge">{{ $myClosedTickets->count() }}</span></a></li>

                </ul>

                <div class="tab-content" style="margin-top:15px">
                    @if($isAdmin == 1)
                        @include('ticket.partials.manage_all')
                    @endif

                    @if($isAdmin == 1)
                        @include('ticket.partials.summary_report', ['slaData' => $slaData, 'officeData' => $officeData, 'categoryData' => $categoryData, 'openData' => $openData, 'closeData' => $closeData])
                    @endif

                    @include('ticket.partials.assigned')

                    @include('ticket.partials.resolved_by_me')

                    @include('ticket.partials.requested')

                    @include('ticket.partials.my_requested_resolved')

                    @include('ticket.partials.closed_requests')
                </div>
            </div>
        </div>
    </div>


    <!-- Assign Ticket Modal (Admin) -->
    <div class="modal fade" id="assignTicketModal" tabindex="-1" role="dialog" aria-labelledby="assignTicketModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" id="assignTicketForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="assignTicketModalLabel">Assign Ticket: <span
                                id="assignTicketName"></span></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="type" value="assign">
                        <div class="form-group">
                            <label for="assign_office">Office</label>
                            <select name="assign_office" id="assign_office" class="form-control">
                                <option value="">-- Select Office --</option>
                                @foreach($offices as $o)
                                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assign_role">Role</label>
                            <select name="assign_role" id="assign_role" class="form-control">
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assign_to">Assign To</label>
                            <select name="assigned_to" id="assign_to" class="form-control" disabled>
                                <option value="">-- Select User --</option>
                            </select>
                        </div>
                        <div class="help-block text-muted">Choose Office → Role to filter users, then assign.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Close Ticket Modal -->
    <div class="modal fade" id="closeTicketModal" tabindex="-1" role="dialog" aria-labelledby="closeTicketModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" id="closeTicketForm" action="">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="closeTicketModalLabel">Close Ticket: <span id="closeTicketName"></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <select name="rating" id="rating" class="form-control" required>
                                <option value="">-- Select Rating --</option>
                                <option value="1">1 - Very Poor</option>
                                <option value="2">2 - Poor</option>
                                <option value="3">3 - Fair</option>
                                <option value="4">4 - Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="4"></textarea>
                        </div>
                        <input type="hidden" name="status" value="closed">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Close Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        (function ($) {
            $(document).on('click', '.open-close-modal', function () {
                var id = $(this).data('ticket-id');
                var name = $(this).data('ticket-name');
                var mode = $(this).data('mode') || 'close';
                $('#closeTicketName').text(name);
                $('#closeTicketForm').attr('action', '{{ url('ticket') }}' + '/' + id + '/update');
                if (mode === 'rate') {
                    $('#closeTicketModalLabel').text('Rate Ticket: ' + name);
                    $('#closeTicketForm button[type="submit"]').text('Submit Rating');
                } else {
                    $('#closeTicketModalLabel').text('Close Ticket: ' + name);
                    $('#closeTicketForm button[type="submit"]').text('Close Ticket');
                }
                $('#closeTicketModal').modal('show');
            });

            // view info modal
            $(document).on('click', '.view-ticket-info', function () {
                var name = $(this).data('ticket-name');
                var ticketNumber = $(this).data('ticket-number') || '—';
                var remarks = $(this).data('ticket-remarks') || '';
                var resolutionComment = $(this).data('resolution-comment') || '';
                var rating = parseInt($(this).data('ticket-rating')) || 0;
                var description = $(this).data('ticket-description') || '';
                var days = $(this).data('ticket-days') || '—';
                var openedBy = $(this).data('opened-by') || '—';
                var openedPhone = $(this).data('opened-phone') || '—';
                var openedEmail = $(this).data('opened-email') || '—';
                var openedAt = $(this).data('opened-at') || '—';
                var status = $(this).data('status') || '';

                $('#viewTicketName').text(name);
                $('#ticketNumber').text(ticketNumber);
                $('#ticketOpenedBy').text(openedBy);
                $('#ticketOpenedPhone').text(openedPhone);
                $('#ticketOpenedEmail').text(openedEmail);
                $('#ticketOpenedAt').text(openedAt);
                $('#ticketDescription').text(description ? description : 'No description provided.');
                $('#ticketDays').text(days);
                $('#ticketRemarks').text(remarks ? remarks : 'No remarks provided.');
                $('#ticketResolutionComment').text(resolutionComment ? resolutionComment : 'No resolution comment provided.');
                var stars = '';
                for (var i = 1; i <= 5; i++) {
                    if (i <= rating) stars += '<i class="fa fa-star text-warning"></i> ';
                    else stars += '<i class="fa fa-star-o text-muted"></i> ';
                }
                if (rating === 0) stars = '<span class="text-muted">No rating</span>';
                $('#ticketRatingStars').html(stars);

                // Handle resolved ticket visual indicator and resolution comment visibility
                if (status === 'resolved') {
                    $('#resolvedBadge').show();
                    $('#modalHeader').addClass('bg-success text-white');
                    $('#resolutionCommentGroup').show();
                } else {
                    $('#resolvedBadge').hide();
                    $('#modalHeader').removeClass('bg-success text-white');
                    $('#resolutionCommentGroup').hide();
                }

                $('#viewTicketModal').modal('show');
            });

            // Reset modal styles when hidden
            $('#viewTicketModal').on('hidden.bs.modal', function () {
                $('#resolvedBadge').hide();
                $('#modalHeader').removeClass('bg-success text-white');
                $('#resolutionCommentGroup').hide();
            });

            // open assign modal (admin)
            $(document).on('click', '.open-assign-modal', function () {
                var id = $(this).data('ticket-id');
                var name = $(this).data('ticket-name');
                $('#assignTicketName').text(name);
                // set form action to update endpoint
                $('#assignTicketForm').attr('action', '{{ url('ticket') }}' + '/' + id + '/update');
                // reset selects
                $('#assign_office').val('');
                $('#assign_role').val('');
                $('#assign_to').html('<option value="">-- Select User --</option>').attr('disabled', true);
                $('#assignTicketModal').modal('show');
            });

            function refreshAssignUsers() {
                var office = $('#assign_office').val();
                var role = $('#assign_role').val();
                $('#assign_to').attr('disabled', true).html('<option value="">Loading...</option>');
                if (!office || !role) {
                    $('#assign_to').html('<option value="">-- Select User --</option>').attr('disabled', true);
                    return;
                }
                $.get('{{ url("ticket/users") }}', { office_id: office, role_id: role, type: 'assign' })
                    .done(function (resp) {
                        if (resp.success) {
                            var opts = '<option value="">-- Unassigned --</option>';
                            resp.users.forEach(function (u) {
                                opts += '<option value="' + u.id + '">' + u.display + '</option>';
                            });
                            $('#assign_to').html(opts).attr('disabled', false);
                        } else {
                            var msg = resp.message ? ' (' + resp.message + ')' : '';
                            $('#assign_to').html('<option value="">-- No users' + msg + ' --</option>').attr('disabled', true);
                        }
                    })
                    .fail(function (xhr, status, err) {
                        console.error('Failed to fetch assign users', status, err, xhr.responseText);
                        $('#assign_to').html('<option value="">-- Error loading users --</option>').attr('disabled', true);
                    });
            }

            $('#assign_office').on('change', function () {
                if ($(this).val()) {
                    $('#assign_role').attr('disabled', false);
                    if ($('#assign_role').val()) {
                        refreshAssignUsers();
                    }
                } else {
                    $('#assign_role').val('').attr('disabled', true);
                    $('#assign_to').val('').attr('disabled', true);
                }
            });

            $('#assign_role').on('change', function () {
                refreshAssignUsers();
            });

            // Initialize DataTables for ticket tables with pagination
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr('href');

                // Trigger charts if summary tab is shown
                if (target === '#summary_report' && typeof renderSummaryCharts === 'function') {
                    renderSummaryCharts();
                }

                if ($(target + ' table').length) {
                    if ($.fn.DataTable.isDataTable(target + ' table')) {
                        $(target + ' table').DataTable().destroy();
                    }
                    $(target + ' table').DataTable({
                        pageLength: 20,
                        searching: false,
                        lengthChange: false,
                        order: [[0, 'desc']]
                    });
                }
            });

            // Initialize the active tab's table on page load
            var activeTab = $('.nav-tabs li.active a').attr('href');

            if (activeTab === '#summary_report' && typeof renderSummaryCharts === 'function') {
                renderSummaryCharts();
            }

            if ($(activeTab + ' table').length) {
                $(activeTab + ' table').DataTable({
                    pageLength: 20,
                    searching: false,
                    lengthChange: false,
                    order: [[0, 'desc']]
                });
            }

        })(jQuery);
    </script>
@endsection
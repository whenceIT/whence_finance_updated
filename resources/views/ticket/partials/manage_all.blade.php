                <div class="tab-pane active" id="manage_all">
                    <h4>All Tickets</h4>
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
                    <div id="grid-view-manage">
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
                            margin-top: -1px;
                            display: flex;
                            flex-wrap: wrap;
                            background-color: #f8f9fa;
                            padding: 0.5rem;
                            border-top: 1px solid #dee2e6;
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
                            @forelse($allTickets as $ticket)
                            <div class="col-12 col-md-6 col-lg-3 mb-3">
                                <div class="card shadow-none ticket-card view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-phone="{{ optional($ticket->openedBy)->phone ?? '—' }}" data-opened-email="{{ optional($ticket->openedBy)->email ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : '—' }}">
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
                                                <span class="ticket-info-value">{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-user-plus"></i> Created By:</span>
                                                <span class="ticket-info-value">{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-tag"></i> Issue Category:</span>
                                                <span class="ticket-info-value">{{ optional($ticket->issueCategory)->name ?? '—' }}</span>
                                            </div>
                                            <div class="ticket-info-row">
                                                <span class="ticket-info-label"><i class="fa fa-calendar"></i> Opened At:</span>
                                                <span class="ticket-info-value">{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : '—' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ticket-actions" style="margin-top: -8px; margin-left: 5%;">
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
                                            <i class="fa fa-cog fa-spin"></i> Working on it...
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <p class="text-center">No tickets available.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-6">
                            <h4>SLA Compliance</h4>
                            <canvas id="slaChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h4>Tickets by Office</h4>
                            <canvas id="officeChart"></canvas>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-6">
                            <h4>Tickets by Issue Category</h4>
                            <canvas id="categoryChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h4>Tickets Opened Over Time</h4>
                            <canvas id="openChart"></canvas>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-6">
                            <h4>Average Days to Close by Office</h4>
                            <canvas id="closeChart"></canvas>
                        </div>
                    </div>

                    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
                    <script>
                        // SLA Pie Chart
                        var ctx = document.getElementById('slaChart').getContext('2d');
                        var slaChart = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['Met', 'Not Met'],
                                datasets: [{
                                    data: [{{ $slaData['met'] }}, {{ $slaData['not_met'] }}],
                                    backgroundColor: ['#28a745', '#dc3545']
                                }]
                            }
                        });

                        // Office Bar Chart
                        var ctx2 = document.getElementById('officeChart').getContext('2d');
                        var officeChart = new Chart(ctx2, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode(array_keys($officeData->toArray())) !!},
                                datasets: [{
                                    label: 'Tickets',
                                    data: {!! json_encode(array_values($officeData->toArray())) !!},
                                    backgroundColor: '#007bff'
                                }]
                            }
                        });

                        // Category Pie Chart
                        var ctx3 = document.getElementById('categoryChart').getContext('2d');
                        var categoryChart = new Chart(ctx3, {
                            type: 'pie',
                            data: {
                                labels: {!! json_encode(array_keys($categoryData->toArray())) !!},
                                datasets: [{
                                    data: {!! json_encode(array_values($categoryData->toArray())) !!},
                                    backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#ff9f40', '#4bc0c0']
                                }]
                            }
                        });

                        // Open Time Line Chart
                        var ctx4 = document.getElementById('openChart').getContext('2d');
                        var openChart = new Chart(ctx4, {
                            type: 'line',
                            data: {
                                labels: {!! json_encode(array_keys($openData->toArray())) !!},
                                datasets: [{
                                    label: 'Tickets Opened',
                                    data: {!! json_encode(array_values($openData->toArray())) !!},
                                    borderColor: '#28a745',
                                    fill: false
                                }]
                            }
                        });

                        // Close Bar Chart
                        var ctx5 = document.getElementById('closeChart').getContext('2d');
                        var closeChart = new Chart(ctx5, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode(array_keys($closeData->toArray())) !!},
                                datasets: [{
                                    label: 'Average Days',
                                    data: {!! json_encode(array_values($closeData->toArray())) !!},
                                    backgroundColor: '#ffc107'
                                }]
                            }
                        });
                    </script>
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
                        $(document).on('click', '.view-ticket-info', function(){
                            $('.ticket-card').removeClass('clicked');
                            $(this).closest('.ticket-card').addClass('clicked');
                        });
                    });
                    </script>
                </div>
                @include('ticket.partials.view_ticket_modal')
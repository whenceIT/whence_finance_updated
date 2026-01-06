@extends('layouts.master')

@section('title', 'Tickets')

@section('content')
@include('partials.fullscreen_loader')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Tickets</h3>
        <div class="box-tools pull-right">
            <a href="{{ url('ticket/create') }}" class="btn btn-success btn-sm" onclick="event.preventDefault(); $('#fullscreen-loader').show(); window.location.href = '{{ url('ticket/create') }}';">New Ticket</a>
        </div>
    </div>
    <div class="box-body">

        <div>
            <style>
                /* 🎨 Highlight: Assigned & Resolved tabs */
                .nav-tabs > li.tab-special > a {
                    background: linear-gradient(135deg,#2d9cdb,#117a8b);
                    color: #ffffff;
                    border-radius: 4px 4px 0 0;
                    margin-right: 6px;
                }

                .nav-tabs > li.tab-special.active > a,
                .nav-tabs > li.tab-special > a:hover {
                    background: linear-gradient(135deg,#1b6fa8,#0f4f6a) !important;
                    color: #fff !important;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
                }

                .nav-tabs > li.tab-special > a .badge {
                    background: rgba(255,255,255,0.16);
                    color: #fff;
                    margin-left: 6px;
                }

                /* keep transitions smooth */
                .nav-tabs > li > a { transition: all .12s ease; }

                /* Highlight summary date inputs */
                #summaryFrom, #summaryTo {
                    background-color: #fff7c6; /* pale yellow */
                    border: 1px solid #f1c40f;
                    box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
                }

                /* stronger highlight on focus */
                #summaryFrom:focus, #summaryTo:focus {
                    outline: none;
                    box-shadow: 0 0 6px rgba(241,196,15,0.25);
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
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                .ticket-status-working {
                    background: linear-gradient(135deg, #ffc107, #fd7e14);
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-weight: bold;
                    text-align: center;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0% { opacity: 1; }
                    50% { opacity: 0.7; }
                    100% { opacity: 1; }
                }

                /* Enhanced modal title */
                #viewTicketModalLabel span {
                    font-size: 1.5em;
                    font-weight: bold;
                    color: #333;
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
                    <li class="active"><a href="#manage_all" data-toggle="tab">All Tickets <span class="badge">{{ $allTickets->count() }}</span></a></li>
                @endif
                @if($isAdmin == 1)
                    <li class="tab-summary"><a href="#summary_report" data-toggle="tab">Summary Report <span class="badge" id="summaryBadge">{{ $assignedClosedTickets->pluck('id')->merge($myClosedTickets->pluck('id'))->unique()->count() }}</span></a></li>
                    <li class="tab-special"><a href="#assigned" data-toggle="tab">Tickets Assigned to Me <span class="badge">{{ $assignedTickets->count() }}</span></a></li>
                @else
                    <li class="tab-special active"><a href="#assigned" data-toggle="tab">Tickets Assigned to Me <span class="badge">{{ $assignedTickets->count() }}</span></a></li>
                @endif
                <li class="tab-special"><a href="#resolved_by_me" data-toggle="tab">Tickets I Resolved <span class="badge">{{ $assignedClosedTickets->count() }}</span></a></li>
                <li><a href="#requested" data-toggle="tab">My Ticket Requests <span class="badge">{{ $myTickets->count() }}</span></a></li>
                <li><a href="#closed_requests" data-toggle="tab">My Requested Closed Issues <span class="badge">{{ $myClosedTickets->count() }}</span></a></li>

            </ul>

            <div class="tab-content" style="margin-top:15px">
                <div class="{{ $isAdmin == 1 ? 'tab-pane' : 'tab-pane active' }}" id="assigned">
                    @if($assignedTickets->count())
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
                                <!-- <td>{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}</td> -->
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

                                    @if($ticket->status != 'closed')
                                    <!-- <button type="button" class="btn btn-sm btn-danger open-close-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ $ticket->name }}">Close</button> -->
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>No tickets assigned to you.</p>
                    @endif
                </div>

                <div class="tab-pane" id="resolved_by_me">
                    @if($assignedClosedTickets->count())
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
                                <th>Rating</th>
                                <th>Remarks</th>
                                <th>Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedClosedTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>{{ $ticket->name }}</td>
                                <td>{{ ucfirst($ticket->priority) }}</td>
                                <td>{{ $ticket->stage ?? '—' }}</td>
                                <td>{{ optional($ticket->assignedTo)->first_name ?? optional($ticket->assignedTo)->name ?? '—' }}</td>
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
                                <td></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>No closed tickets assigned to you.</p>
                    @endif
                </div>

                @if($isAdmin == 1)
                <div class="tab-pane" id="summary_report">
                    <h4>Summary Report</h4>

                    <!-- Ticket Dashboard (Auto-Calculating) -->
                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardTotal">{{ $dashboardTotals['totalTickets'] }}</strong><div>Total tickets logged</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardOpen">{{ $dashboardTotals['openTicketsCount'] }}</strong><div>Open tickets</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardClosed">{{ $dashboardTotals['closedTicketsCount'] }}</strong><div>Closed tickets</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardSlaPercent">{{ $dashboardTotals['slaCompliancePercent'] }}</strong><div>SLA compliance</div></div></div>
                    </div>

                    <hr>

                    <!-- Ticket Dashboard Search -->
                    <form id="summaryFilter" class="form-inline" onsubmit="return false;">
                        <div class="form-group">
                            <label for="summaryFrom">From</label>
                            <input type="date" id="summaryFrom" class="form-control text-warning" />
                        </div>
                        <div class="form-group" style="margin-left:10px;">
                            <label for="summaryTo">To</label>
                            <input type="date" id="summaryTo" class="form-control text-warning" />
                        </div>
                        <button id="applySummary" class="btn btn-primary" style="margin-left:10px;">Apply</button>
                        <button id="resetSummary" class="btn btn-default" style="margin-left:6px;">Reset</button>
                    </form>

                    <hr>

                    <!-- Advanced Ticket Analysis -->
                    <h5>Advanced Analysis</h5>
                    <form id="analysisFilter" class="form-inline" onsubmit="return false;">
                        <div class="form-group">
                            <label for="analysisBranch">Branch</label>
                            <select id="analysisBranch" class="form-control">
                                <option value="">All Branches</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-left:10px;">
                            <label for="analysisUser">User</label>
                            <select id="analysisUser" class="form-control" disabled>
                                <option value="">All Users</option>
                            </select>
                        </div>
                        <button id="applyAnalysis" class="btn btn-success" style="margin-left:10px;">Analyze</button>
                        <button id="resetAnalysis" class="btn btn-default" style="margin-left:6px;">Reset</button>
                    </form>

                    <div id="analysisResults" style="display:none; margin-top:20px;">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Performance Leaderboard</h6>
                                <div id="leaderboardContainer">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Tickets Resolved</th>
                                                <th>Avg Resolution Time</th>
                                                <th>SLA Compliance</th>
                                            </tr>
                                        </thead>
                                        <tbody id="leaderboardBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Issue Category Analysis</h6>
                                <canvas id="categoryChart" height="200"></canvas>
                            </div>
                        </div>
                        <div class="row" style="margin-top:20px;">
                            <div class="col-md-6">
                                <h6>SLA Compliance by Category</h6>
                                <canvas id="slaChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6>Resolution Time Distribution</h6>
                                <canvas id="timeChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Display chosen range as a prominent human-readable title -->
                    <div style="margin-top:8px; margin-bottom:6px;">
                        <h3 id="summaryRange" class="text-bold" style="font-weight:700;margin:0;">Tickets — All time</h3>
                    </div>

                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-3"><div class="well text-center"><strong id="totalResolved">0</strong><div>Resolved Tickets</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="avgRating">—</strong><div>Avg Rating</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="avgTime">—</strong><div>Avg Resolution Time</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="closedPercent">—</strong><div>% Closed (in dataset)</div></div></div>
                    </div>

                    <div class="row">
                        <div class="col-md-8"><canvas id="resolvedChart" height="140"></canvas></div>
                        <div class="col-md-4"><canvas id="priorityChart" height="140"></canvas></div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        (function($){
                            var assignedClosed = @json($assignedClosedTickets->toArray());
                            var myClosed = @json($myClosedTickets->toArray());
                            // combine datasets and dedupe by id
                            var combined = assignedClosed.concat(myClosed);
                            var closedTickets = [];
                            var seenIds = {};
                            combined.forEach(function(t){
                                if(t && t.id && !seenIds[t.id]){
                                    seenIds[t.id] = true;
                                    closedTickets.push(t);
                                }
                            });

                            function parseDateStr(dt){
                                if(!dt) return null;
                                var s = dt.replace(' ', 'T');
                                var d = new Date(s);
                                if(isNaN(d.getTime())){
                                    d = new Date(dt.replace(' ', 'T') + 'Z');
                                }
                                return d;
                            }

                            function computeStats(list, fromDate, toDate){
                                var filtered = list.filter(function(t){
                                    var d = parseDateStr(t.datetime_close || t.updated_at || t.datetime_open || t.created_at);
                                    if(!d) return false;
                                    if(fromDate && d < fromDate) return false;
                                    if(toDate && d > toDate) return false;
                                    return true;
                                });

                                var total = filtered.length;
                                var ratings = filtered.map(function(t){ return t.rating ? parseFloat(t.rating) : null; }).filter(Boolean);
                                var avgRating = ratings.length ? (ratings.reduce((a,b)=>a+b,0)/ratings.length).toFixed(2) : '—';

                                var slaMetCount = filtered.filter(function(t){ return !!t.sla_met; }).length;
                                var slaMetPercent = filtered.length ? Math.round((slaMetCount/filtered.length)*100)+'%' : '—';

                                var times = filtered.map(function(t){
                                    var open = parseDateStr(t.datetime_open || t.created_at);
                                    var close = parseDateStr(t.datetime_close || t.updated_at);
                                    if(!open || !close) return null;
                                    return (close - open)/1000;
                                }).filter(Boolean);
                                var avgTime = times.length ? (times.reduce((a,b)=>a+b,0)/times.length) : null;
                                var avgTimeStr = avgTime ? secondsToHMS(avgTime) : '—';

                                var prios = {};
                                filtered.forEach(function(t){
                                    var p = (t.priority || 'unknown').toLowerCase();
                                    prios[p] = (prios[p] || 0) + 1;
                                });
                                return { total: total, avgRating: avgRating, avgTimeStr: avgTimeStr, prios: prios, filtered: filtered, slaMetPercent: slaMetPercent };
                            }

                            function secondsToHMS(sec){
                                sec = Math.round(sec);
                                var d = Math.floor(sec / 86400); sec %= 86400;
                                var h = Math.floor(sec / 3600); sec %= 3600;
                                var m = Math.floor(sec / 60);
                                var s = sec % 60;
                                var parts = [];
                                if(d) parts.push(d+'d');
                                if(h) parts.push(h+'h');
                                if(m) parts.push(m+'m');
                                if(s) parts.push(s+'s');
                                return parts.length ? parts.join(' ') : '0s';
                            }

                            var resolvedChartCtx, resolvedChart, priorityChartCtx, priorityChart;

                            function renderCharts(stats){
                                var countsByDay = {};
                                stats.filtered.forEach(function(t){
                                    var d = parseDateStr(t.datetime_close || t.updated_at || t.datetime_open || t.created_at);
                                    if(!d) return;
                                    var day = d.toISOString().slice(0,10);
                                    countsByDay[day] = (countsByDay[day] || 0) + 1;
                                });
                                var days = Object.keys(countsByDay).sort();
                                var counts = days.map(function(d){ return countsByDay[d]; });

                                if(!resolvedChart){
                                    resolvedChartCtx = document.getElementById('resolvedChart').getContext('2d');
                                    resolvedChart = new Chart(resolvedChartCtx, {
                                        type: 'bar',
                                        data: { labels: days, datasets: [{ label: 'Resolved', backgroundColor: '#4e73df', data: counts }] },
                                        options: { responsive: true, maintainAspectRatio: false }
                                    });
                                } else {
                                    resolvedChart.data.labels = days;
                                    resolvedChart.data.datasets[0].data = counts;
                                    resolvedChart.update();
                                }

                                var labels = Object.keys(stats.prios);
                                var values = labels.map(function(k){ return stats.prios[k]; });
                                var colors = ['#36a2eb','#ff6384','#ffcd56','#4bc0c0','#9966ff'];
                                if(!priorityChart){
                                    priorityChartCtx = document.getElementById('priorityChart').getContext('2d');
                                    priorityChart = new Chart(priorityChartCtx, {
                                        type: 'doughnut',
                                        data: { labels: labels, datasets: [{ data: values, backgroundColor: colors }] },
                                        options: { responsive: true, maintainAspectRatio: false }
                                    });
                                } else {
                                    priorityChart.data.labels = labels;
                                    priorityChart.data.datasets[0].data = values;
                                    priorityChart.update();
                                }
                            }

                            function updateUI(stats){
                                $('#totalResolved').text(stats.total);
                                $('#avgRating').text(stats.avgRating);
                                $('#avgTime').text(stats.avgTimeStr);
                                $('#closedPercent').text(stats.slaMetPercent || '—');
                                $('#summaryBadge').text(stats.total);
                                // update dashboard closed and SLA percent with filtered values
                                $('#dashboardClosed').text(stats.total);
                                $('#dashboardSlaPercent').text(stats.slaMetPercent || '{{ $dashboardTotals['slaCompliancePercent'] }}');
                                renderCharts(stats);
                            }

                            function humanDate(s){
                                if(!s) return null;
                                var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                var p = s.split('-');
                                if(p.length !== 3) return s;
                                return parseInt(p[2],10) + ' ' + months[parseInt(p[1],10)-1] + ' ' + p[0];
                            }

                            function updateRangeTitle(from, to){
                                var title = 'Tickets — All time';
                                if(from && to){ title = 'Tickets from ' + humanDate(from) + ' to ' + humanDate(to); }
                                else if(from){ title = 'Tickets from ' + humanDate(from); }
                                else if(to){ title = 'Tickets up to ' + humanDate(to); }
                                $('#summaryRange').text(title);
                            }

                            function applyFilter(){
                                var from = $('#summaryFrom').val();
                                var to = $('#summaryTo').val();
                                // persist selection to localStorage (if available)
                                try{
                                    if(from) localStorage.setItem('ticketSummaryFrom', from); else localStorage.removeItem('ticketSummaryFrom');
                                    if(to) localStorage.setItem('ticketSummaryTo', to); else localStorage.removeItem('ticketSummaryTo');
                                } catch(e){ console.warn('localStorage not available', e); }

                                updateRangeTitle(from, to);
                                var fromDate = from ? new Date(from+'T00:00:00') : null;
                                var toDate = to ? new Date(to+'T23:59:59') : null;
                                var stats = computeStats(closedTickets, fromDate, toDate);
                                updateUI(stats);
                            }

                            $('#applySummary').on('click', function(){ applyFilter(); });
                            $('#resetSummary').on('click', function(){ $('#summaryFrom, #summaryTo').val(''); try{ localStorage.removeItem('ticketSummaryFrom'); localStorage.removeItem('ticketSummaryTo'); }catch(e){} applyFilter(); });

                            // Advanced Analysis
                            var categoryChart, slaChart, timeChart;

                            $('#analysisBranch').on('change', function(){
                                loadUsersForBranch();
                            });

                            function loadUsersForBranch(){
                                var branchId = $('#analysisBranch').val();
                                $('#analysisUser').attr('disabled', true).html('<option value="">Loading...</option>');
                                if(!branchId){
                                    $('#analysisUser').html('<option value="">All Users</option>').attr('disabled', true);
                                    return;
                                }
                                $.get('{{ url("ticket/users") }}', { office_id: branchId, type: 'analysis' })
                                 .done(function(resp){
                                     if(resp.success){
                                         var opts = '<option value="">All Users</option>';
                                         resp.users.forEach(function(u){
                                             opts += '<option value="'+u.id+'">'+u.display+'</option>';
                                         });
                                         $('#analysisUser').html(opts).attr('disabled', false);
                                     } else {
                                         $('#analysisUser').html('<option value="">No users</option>').attr('disabled', true);
                                     }
                                 })
                                 .fail(function(){
                                     $('#analysisUser').html('<option value="">Error loading</option>').attr('disabled', true);
                                 });
                            }

                            $('#applyAnalysis').on('click', function(){
                                applyAnalysis();
                            });

                            $('#resetAnalysis').on('click', function(){
                                $('#analysisBranch, #analysisUser').val('');
                                $('#analysisUser').attr('disabled', true);
                                $('#analysisResults').hide();
                            });

                            function applyAnalysis(){
                                var branchId = $('#analysisBranch').val();
                                var userId = $('#analysisUser').val();
                                var from = $('#summaryFrom').val();
                                var to = $('#summaryTo').val();

                                var fromDate = from ? new Date(from+'T00:00:00') : null;
                                var toDate = to ? new Date(to+'T23:59:59') : null;

                                // Filter tickets
                                var filtered = closedTickets.filter(function(t){
                                    var d = parseDateStr(t.datetime_close || t.updated_at || t.datetime_open || t.created_at);
                                    if(!d) return false;
                                    if(fromDate && d < fromDate) return false;
                                    if(toDate && d > toDate) return false;
                                    if(branchId && t.opened_by_office_id != branchId) return false;
                                    if(userId && t.assigned_to != userId) return false;
                                    return true;
                                });

                                renderAnalysis(filtered);
                                $('#analysisResults').show();
                            }

                            function renderAnalysis(tickets){
                                // Leaderboard
                                var userStats = {};
                                tickets.forEach(function(t){
                                    var userId = t.assigned_to;
                                    var userName = t.assigned_to_name || 'Unknown';
                                    if(!userStats[userId]){
                                        userStats[userId] = { name: userName, resolved: 0, times: [], slaMet: 0, total: 0 };
                                    }
                                    userStats[userId].resolved++;
                                    if(t.datetime_close && t.datetime_open){
                                        var time = (new Date(t.datetime_close) - new Date(t.datetime_open)) / (1000 * 60 * 60); // hours
                                        userStats[userId].times.push(time);
                                    }
                                    if(t.sla_met) userStats[userId].slaMet++;
                                    userStats[userId].total++;
                                });

                                var leaderboard = Object.values(userStats).sort(function(a,b){ return b.resolved - a.resolved; });
                                var tbody = '';
                                leaderboard.forEach(function(stat){
                                    var avgTime = stat.times.length ? (stat.times.reduce((a,b)=>a+b,0)/stat.times.length).toFixed(1) + 'h' : '—';
                                    var slaPercent = stat.total ? Math.round((stat.slaMet/stat.total)*100) + '%' : '—';
                                    tbody += '<tr><td>'+stat.name+'</td><td>'+stat.resolved+'</td><td>'+avgTime+'</td><td>'+slaPercent+'</td></tr>';
                                });
                                $('#leaderboardBody').html(tbody);

                                // Category Chart
                                var categoryCounts = {};
                                tickets.forEach(function(t){
                                    var cat = t.issue_category_name || 'Uncategorized';
                                    categoryCounts[cat] = (categoryCounts[cat] || 0) + 1;
                                });
                                var labels = Object.keys(categoryCounts);
                                var data = labels.map(function(l){ return categoryCounts[l]; });

                                if(!categoryChart){
                                    var ctx = document.getElementById('categoryChart').getContext('2d');
                                    categoryChart = new Chart(ctx, {
                                        type: 'pie',
                                        data: { labels: labels, datasets: [{ data: data, backgroundColor: ['#ff6384','#36a2eb','#ffcd56','#4bc0c0','#9966ff','#ff9f40'] }] },
                                        options: { responsive: true, maintainAspectRatio: false }
                                    });
                                } else {
                                    categoryChart.data.labels = labels;
                                    categoryChart.data.datasets[0].data = data;
                                    categoryChart.update();
                                }

                                // SLA Chart
                                var slaStats = {};
                                tickets.forEach(function(t){
                                    var cat = t.issue_category_name || 'Uncategorized';
                                    if(!slaStats[cat]) slaStats[cat] = { met: 0, total: 0 };
                                    slaStats[cat].total++;
                                    if(t.sla_met) slaStats[cat].met++;
                                });
                                var slaLabels = Object.keys(slaStats);
                                var slaData = slaLabels.map(function(l){ return Math.round((slaStats[l].met / slaStats[l].total) * 100); });

                                if(!slaChart){
                                    var ctx = document.getElementById('slaChart').getContext('2d');
                                    slaChart = new Chart(ctx, {
                                        type: 'bar',
                                        data: { labels: slaLabels, datasets: [{ label: 'SLA Compliance %', data: slaData, backgroundColor: '#28a745' }] },
                                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100 } } }
                                    });
                                } else {
                                    slaChart.data.labels = slaLabels;
                                    slaChart.data.datasets[0].data = slaData;
                                    slaChart.update();
                                }

                                // Time Chart
                                var timeBuckets = { '0-1h': 0, '1-4h': 0, '4-24h': 0, '1-7d': 0, '7d+': 0 };
                                tickets.forEach(function(t){
                                    if(t.datetime_close && t.datetime_open){
                                        var hours = (new Date(t.datetime_close) - new Date(t.datetime_open)) / (1000 * 60 * 60);
                                        if(hours <= 1) timeBuckets['0-1h']++;
                                        else if(hours <= 4) timeBuckets['1-4h']++;
                                        else if(hours <= 24) timeBuckets['4-24h']++;
                                        else if(hours <= 168) timeBuckets['1-7d']++;
                                        else timeBuckets['7d+']++;
                                    }
                                });
                                var timeLabels = Object.keys(timeBuckets);
                                var timeData = timeLabels.map(function(l){ return timeBuckets[l]; });

                                if(!timeChart){
                                    var ctx = document.getElementById('timeChart').getContext('2d');
                                    timeChart = new Chart(ctx, {
                                        type: 'doughnut',
                                        data: { labels: timeLabels, datasets: [{ data: timeData, backgroundColor: ['#e74c3c','#f39c12','#f1c40f','#27ae60','#3498db'] }] },
                                        options: { responsive: true, maintainAspectRatio: false }
                                    });
                                } else {
                                    timeChart.data.labels = timeLabels;
                                    timeChart.data.datasets[0].data = timeData;
                                    timeChart.update();
                                }
                            }

                            $(document).ready(function(){
                                // restore stored values if present
                                try{
                                    var sf = localStorage.getItem('ticketSummaryFrom');
                                    var st = localStorage.getItem('ticketSummaryTo');
                                    if(sf) $('#summaryFrom').val(sf);
                                    if(st) $('#summaryTo').val(st);
                                } catch(e){ }
                                applyFilter();
                            });
                        })(jQuery);
                    </script>
                </div>
                @endif

                @if($isAdmin == 1)
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
                                       <button type="button" class="btn btn-sm btn-primary open-assign-modal" data-ticket-id="{{ $ticket->id }}" data-ticket-name="{{ e($ticket->name) }}" data-assigned-to="{{ $ticket->assigned_to }}">Assign</button>
                                       @elseif($ticket->status == 'closed')
                                       <p>DONE</p>
                                       @else
                                       <i>Working...</i>
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
                @endif

                <div class="tab-pane" id="requested">
                    @if($myTickets->count())
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
                                <th>Rating</th>
                                <th>Remarks</th>
                                <th>Details</th>
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
                                <td>{{ $ticket->rating ?? '—' }}</td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ticket->remarks ?? '—' }}</td>
                                <td><button type="button" class="btn btn-xs btn-info view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-number="{{ $ticket->ticket_number }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" title="View details"><i class="fa fa-info-circle"></i></button></td>
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
                    @else
                        <p>You haven't requested any tickets.</p>
                    @endif
                </div>

                <div class="tab-pane" id="closed_requests">
                    @if($myClosedTickets->count())
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
                                <th>Rating</th>
                                <th>Remarks</th>
                                <th>Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myClosedTickets as $ticket)
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
                                <td>{{ $ticket->rating ?? '—' }}</td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ticket->remarks ?? '—' }}</td>
                                <td><button type="button" class="btn btn-xs btn-info view-ticket-info" data-ticket-name="{{ e($ticket->name) }}" data-ticket-remarks="{{ e($ticket->remarks) }}" data-ticket-rating="{{ $ticket->rating ?? 0 }}" data-ticket-description="{{ e($ticket->description) }}" data-ticket-days="{{ $ticket->date_closed ? \Carbon\Carbon::parse($ticket->date_raised ?? $ticket->datetime_open)->diffInDays(\Carbon\Carbon::parse($ticket->date_closed)) : '—' }}" data-opened-by="{{ optional($ticket->openedBy)->first_name ?? optional($ticket->openedBy)->name ?? '—' }}" data-opened-at="{{ $ticket->date_raised ? \Carbon\Carbon::parse($ticket->date_raised)->diffForHumans() : ($ticket->datetime_open ? \Carbon\Carbon::parse($ticket->datetime_open)->diffForHumans() : '—') }}" title="View details"><i class="fa fa-info-circle"></i></button></td>
                                <td></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p>You have no closed ticket requests.</p>
                    @endif
                </div>
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
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="assignTicketModalLabel">Assign Ticket: <span id="assignTicketName"></span></h4>
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
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="closeTicketModalLabel">Close Ticket: <span id="closeTicketName"></span></h4>
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

<!-- View Ticket Modal -->
<div class="modal fade" id="viewTicketModal" tabindex="-1" role="dialog" aria-labelledby="viewTicketModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title" id="viewTicketModalLabel"><svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-ticket-perforated-fill" viewBox="0 0 16 16">
                <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zm4-1v1h1v-1zm1 3v-1H4v1zm7 0v-1h-1v1zm-1-2h1v-1h-1zm-6 3H4v1h1zm7 1v-1h-1v1zm-7 1H4v1h1zm7 1v-1h-1v1zm-8 1v1h1v-1zm7 1h1v-1h-1z"/>
                </svg><span id="viewTicketName"></span>
            </h5>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Ticket Number</label>
                <div id="ticketNumber">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Opened By</label>
                <div id="ticketOpenedBy">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <div id="ticketOpenedPhone">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <div id="ticketOpenedEmail">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Time ago Opened</label>
                <div id="ticketOpenedAt">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <div id="ticketDescription" style="white-space: pre-wrap;">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Days to Close</label>
                <div id="ticketDays">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <div id="ticketRemarks" style="white-space: pre-wrap;">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Rating</label>
                <div id="ticketRatingStars"></div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

<script>
    (function($){
        $(document).on('click', '.open-close-modal', function(){
            var id = $(this).data('ticket-id');
            var name = $(this).data('ticket-name');
            var mode = $(this).data('mode') || 'close';
            $('#closeTicketName').text(name);
            $('#closeTicketForm').attr('action', '{{ url('ticket') }}' + '/' + id + '/update');
            if(mode === 'rate'){
                $('#closeTicketModalLabel').text('Rate Ticket: ' + name);
                $('#closeTicketForm button[type="submit"]').text('Submit Rating');
            } else {
                $('#closeTicketModalLabel').text('Close Ticket: ' + name);
                $('#closeTicketForm button[type="submit"]').text('Close Ticket');
            }
            $('#closeTicketModal').modal('show');
        });

        // view info modal
        $(document).on('click', '.view-ticket-info', function(){
            var name = $(this).data('ticket-name');
            var ticketNumber = $(this).data('ticket-number') || '—';
            var remarks = $(this).data('ticket-remarks') || '';
            var rating = parseInt($(this).data('ticket-rating')) || 0;
            var description = $(this).data('ticket-description') || '';
            var days = $(this).data('ticket-days') || '—';
            var openedBy = $(this).data('opened-by') || '—';
            var openedPhone = $(this).data('opened-phone') || '—';
            var openedEmail = $(this).data('opened-email') || '—';
            var openedAt = $(this).data('opened-at') || '—';

            $('#viewTicketName').text(name);
            $('#ticketNumber').text(ticketNumber);
            $('#ticketOpenedBy').text(openedBy);
            $('#ticketOpenedPhone').text(openedPhone);
            $('#ticketOpenedEmail').text(openedEmail);
            $('#ticketOpenedAt').text(openedAt);
            $('#ticketDescription').text(description ? description : 'No description provided.');
            $('#ticketDays').text(days);
            $('#ticketRemarks').text(remarks ? remarks : 'No remarks provided.');
            var stars = '';
            for(var i=1;i<=5;i++){
                if(i<=rating) stars += '<i class="fa fa-star text-warning"></i> ';
                else stars += '<i class="fa fa-star-o text-muted"></i> ';
            }
            if(rating === 0) stars = '<span class="text-muted">No rating</span>';
            $('#ticketRatingStars').html(stars);
            $('#viewTicketModal').modal('show');
        });

        // open assign modal (admin)
        $(document).on('click', '.open-assign-modal', function(){
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

        function refreshAssignUsers(){
            var office = $('#assign_office').val();
            var role = $('#assign_role').val();
            $('#assign_to').attr('disabled', true).html('<option value="">Loading...</option>');
            if(!office || !role){
                $('#assign_to').html('<option value="">-- Select User --</option>').attr('disabled', true);
                return;
            }
            $.get('{{ url("ticket/users") }}', { office_id: office, role_id: role, type: 'assign' })
             .done(function(resp){
                if(resp.success){
                    var opts = '<option value="">-- Unassigned --</option>';
                    resp.users.forEach(function(u){
                        opts += '<option value="'+u.id+'">'+u.display+'</option>';
                    });
                    $('#assign_to').html(opts).attr('disabled', false);
                } else {
                    var msg = resp.message ? ' ('+resp.message+')' : '';
                    $('#assign_to').html('<option value="">-- No users'+msg+' --</option>').attr('disabled', true);
                }
             })
             .fail(function(xhr, status, err){
                console.error('Failed to fetch assign users', status, err, xhr.responseText);
                $('#assign_to').html('<option value="">-- Error loading users --</option>').attr('disabled', true);
             });
        }

        $('#assign_office').on('change', function(){
            if($(this).val()){
                $('#assign_role').attr('disabled', false);
                if($('#assign_role').val()){
                    refreshAssignUsers();
                }
            } else {
                $('#assign_role').val('').attr('disabled', true);
                $('#assign_to').val('').attr('disabled', true);
            }
        });

        $('#assign_role').on('change', function(){
            refreshAssignUsers();
        });

    })(jQuery);
</script>

@endsection
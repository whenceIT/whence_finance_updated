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

                    <!-- Filtered Tickets Results -->
                    <div id="filteredResults" style="display:none;">
                        <h5>Filtered Tickets</h5>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table id="filteredTable" class="table table-bordered table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>ID</th>
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
                                    </tr>
                                </thead>
                                <tbody id="filteredTableBody">
                                </tbody>
                            </table>
                        </div>
                        <div id="noFilteredResults" style="display:none; text-align:center; padding:20px;">
                            <p>No tickets found for the selected date range.</p>
                        </div>
                    </div>

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
                        <!-- <button id="applyAnalysis" class="btn btn-success" style="margin-left:10px;">Analyze</button>
                        <button id="resetAnalysis" class="btn btn-default" style="margin-left:6px;">Reset</button> -->
                    </form>

                    <div id="analysisResults" style="display:none; margin-top:10px;">
                        <div class="row">
                            <div class="col-md-12" style="padding: 5px;">
                                <h6>Resolution Time Distribution</h6>
                                <canvas id="timeChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:5px;">
                        <div class="col-md-8" style="padding: 5px;"><canvas id="resolvedChart" height="100"></canvas></div>
                        <div class="col-md-4" style="padding: 5px;"><canvas id="priorityChart" height="100"></canvas></div>
                    </div>

                    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
                    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
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

                            function renderFilteredTable(filtered){
                                var tbody = '';
                                filtered.forEach(function(t){
                                    var slaMet = t.sla_met === null ? '—' : (t.sla_met ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>');
                                    var openedAt = '—';
                                    if(t.date_raised){
                                        var d = new Date(t.date_raised);
                                        openedAt = d.toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit'});
                                    } else if(t.datetime_open){
                                        var d = new Date(t.datetime_open);
                                        openedAt = d.toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit'});
                                    }
                                    var closedAt = '—';
                                    if(t.date_closed){
                                        var d = new Date(t.date_closed);
                                        closedAt = d.toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit'});
                                    } else if(t.datetime_close){
                                        var d = new Date(t.datetime_close);
                                        closedAt = d.toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit'});
                                    }
                                    var timeToClose = '—';
                                    if(t.date_closed){
                                        var openDate = t.date_raised ? new Date(t.date_raised) : new Date(t.datetime_open);
                                        var closeDate = new Date(t.date_closed);
                                        var diffMs = closeDate - openDate;
                                        timeToClose = secondsToHMS(Math.floor(diffMs / 1000));
                                    }
                                    var priority = t.priority ? t.priority.charAt(0).toUpperCase() + t.priority.slice(1).toLowerCase() : '—';
                                    var assignedTo = t.assignedTo ? (t.assignedTo.first_name || t.assignedTo.name || '—') : '—';
                                    var createdBy = t.createdBy ? (t.createdBy.first_name || t.createdBy.name || '—') : '—';
                                    var issueCategory = t.issueCategory ? t.issueCategory.name : '—';
                                    var dueDate = t.due_date ? new Date(t.due_date).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'}) : '—';
                                    tbody += '<tr>' +
                                        '<td>' + (t.id || '—') + '</td>' +
                                        '<td>' + (t.name || '—') + '</td>' +
                                        '<td>' + priority + '</td>' +
                                        '<td>' + (t.stage || '—') + '</td>' +
                                        '<td>' + assignedTo + '</td>' +
                                        '<td>' + createdBy + '</td>' +
                                        '<td>' + issueCategory + '</td>' +
                                        '<td>' + openedAt + '</td>' +
                                        '<td>' + closedAt + '</td>' +
                                        '<td>' + timeToClose + '</td>' +
                                        '<td>' + (t.sla_days || '—') + '</td>' +
                                        '<td>' + dueDate + '</td>' +
                                        '<td>' + slaMet + '</td>' +
                                        '<td>' + (t.rating || '—') + '</td>' +
                                        '<td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + (t.remarks || '—') + '</td>' +
                                        '</tr>';
                                });
                                if(filtered.length > 0){
                                    $('#filteredTableBody').html(tbody);
                                    $('#filteredResults .table-responsive').show();
                                    $('#noFilteredResults').hide();
                                    $('#filteredResults').show();
                                    $('#filteredTable').DataTable({
                                        pageLength: 20,
                                        searching: false,
                                        lengthChange: false,
                                        destroy: true,
                                        order: [[0, 'desc']]
                                    });
                                } else {
                                    $('#filteredResults .table-responsive').hide();
                                    $('#noFilteredResults').show();
                                    $('#filteredResults').show();
                                }
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
                                if(from || to){
                                    renderFilteredTable(stats.filtered);
                                } else {
                                    $('#filteredResults').hide();
                                }
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
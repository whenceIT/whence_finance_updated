                <div class="tab-pane" id="summary_report">
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
                    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                    <div class="row">
                        <div class="col-md-12 d-flex align-items-center justify-content-between mb-3">
                            <h3 class="box-title" style="margin-bottom: 20px; font-weight: bold; color: #333;">
                                <i class="fa fa-bar-chart"></i> Ticket Summary & Performance Analytics
                            </h3>

                            <div class="d-flex align-items-center gap-2">
                                <form method="GET" action="{{ url('ticket') }}" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="tab" value="summary_report">
                                    <select name="year" class="form-control input-sm" style="width: 120px;" onchange="this.form.submit()">
                                        <option value="">All Years</option>
                                        @foreach($availableYears as $year)
                                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <a href="{{ url('ticket') }}" class="text-muted btn btn-sm btn-secondary" style="font-size: 14px;">
                                    <i class="fa fa-info-circle"></i> Reset Filter
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Dashboard (Auto-Calculating) -->
                    <div class="row" style="margin-bottom:20px;">
                        <div class="col-md-2">
                            <div class="small-box bg-aqua shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardTotal">{{ $dashboardTotals['totalTickets'] }}</h3>
                                    <p>Total Tickets Logged</p>
                                </div>
                                <div class="icon"><i class="fa fa-ticket"></i></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="small-box bg-yellow shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardOpen">{{ $dashboardTotals['openTicketsCount'] }}</h3>
                                    <p>Open Tickets</p>
                                </div>
                                <div class="icon"><i class="fa fa-folder-open"></i></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="small-box bg-teal shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardResolved">{{ $dashboardTotals['resolvedTicketsCount'] }}</h3>
                                    <p>Resolved Tickets</p>
                                </div>
                                <div class="icon"><i class="fa fa-check-square-o"></i></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="small-box bg-green shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardClosed">{{ $dashboardTotals['closedTicketsCount'] }}</h3>
                                    <p>Closed Tickets</p>
                                </div>
                                <div class="icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-purple shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardSlaPercent">{{ $dashboardTotals['slaCompliancePercent'] }}</h3>
                                    <p>SLA Compliance</p>
                                </div>
                                <div class="icon"><i class="fa fa-clock-o"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #00c0ef;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">SLA Compliance</h4>
                                </div>
                                <div class="box-body" style="position: relative;">
                                    <div id="slaChart" style="min-height: 300px;"></div>
                                    <div id="slaChartPlaceholder" class="text-muted text-center" style="display:none; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">
                                        <i class="fa fa-info-circle"></i> No SLA data available
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #dd4b39;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tickets by Status</h4>
                                </div>
                                <div class="box-body" style="position: relative;">
                                    <div id="statusChart" style="min-height: 300px;"></div>
                                    <div id="statusChartPlaceholder" class="text-muted text-center" style="display:none; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">
                                        <i class="fa fa-info-circle"></i> No status data available
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #f39c12;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tickets by Office</h4>
                                </div>
                                <div class="box-body" style="position: relative;">
                                    <div id="officeChart" style="min-height: 300px;"></div>
                                    <div id="officeChartPlaceholder" class="text-muted text-center" style="display:none; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">
                                        <i class="fa fa-info-circle"></i> No office data available
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #605ca8;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tickets by Issue Category</h4>
                                </div>
                                <div class="box-body" style="position: relative;">
                                    <div id="categoryChart" style="min-height: 350px;"></div>
                                    <div id="categoryChartPlaceholder" class="text-muted text-center" style="display:none; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">
                                        <i class="fa fa-info-circle"></i> No category data available
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #00a65a;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tickets Opened Over Time</h4>
                                </div>
                                <div class="box-body" style="position: relative;">
                                    <div id="openChart" style="min-height: 350px;"></div>
                                    <div id="openChartPlaceholder" class="text-muted text-center" style="display:none; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">
                                        <i class="fa fa-info-circle"></i> No ticketing history available
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #dd4b39;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Efficiency: Average Days to Close by Office</h4>
                                </div>
                                <div class="box-body" style="position: relative;">
                                    <div id="closeChart" style="min-height: 400px;"></div>
                                    <div id="closeChartPlaceholder" class="text-muted text-center" style="display:none; position: absolute; top:50%; left:50%; transform: translate(-50%, -50%);">
                                        <i class="fa fa-info-circle"></i> No efficiency data available
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        var chartsInitialized = false;

                        function renderSummaryCharts() {
                            if (chartsInitialized) {
                                return;
                            }
                            
                            if (typeof ApexCharts === 'undefined') {
                                console.warn('ApexCharts is not loaded. Retrying in 1s...');
                                setTimeout(renderSummaryCharts, 1000);
                                return;
                            }

                            chartsInitialized = true;

                            // Helper to check for data
                            function hasData(array) {
                                return array && array.length > 0 && array.some(val => val > 0);
                            }

                            // 1. SLA Polar Area Chart
                            var slaSeries = [{{ isset($slaData['met']) ? $slaData['met'] : 0 }}, {{ isset($slaData['not_met']) ? $slaData['not_met'] : 0 }}];
                            if (hasData(slaSeries)) {
                                var optionsSla = {
                                    series: slaSeries,
                                    chart: { type: 'polarArea', height: 300 },
                                    labels: ['Met', 'Not Met'],
                                    colors: ['#28a745', '#dc3545'],
                                    stroke: { colors: ['#fff'] },
                                    fill: { opacity: 0.8 },
                                    legend: { position: 'bottom' }
                                };
                                new ApexCharts(document.querySelector("#slaChart"), optionsSla).render();
                            } else {
                                document.getElementById('slaChartPlaceholder').style.display = 'block';
                            }

                            // 2. Status Pie Chart
                            var statusSeries = {!! isset($statusData) ? json_encode(array_values($statusData->toArray())) : '[]' !!};
                            if (hasData(statusSeries)) {
                                var optionsStatus = {
                                    series: statusSeries,
                                    chart: { type: 'pie', height: 300 },
                                    labels: {!! isset($statusData) ? json_encode(array_keys($statusData->toArray())) : '[]' !!},
                                    colors: ['#00c0ef', '#f39c12', '#00a65a', '#dd4b39', '#605ca8'],
                                    legend: { position: 'bottom' }
                                };
                                new ApexCharts(document.querySelector("#statusChart"), optionsStatus).render();
                            } else {
                                document.getElementById('statusChartPlaceholder').style.display = 'block';
                            }

                            // 3. Office Bar Chart
                            var officeSeries = {!! isset($officeData) ? json_encode(array_values($officeData->toArray())) : '[]' !!};
                            if (hasData(officeSeries)) {
                                var optionsOffice = {
                                    series: [{ name: 'Tickets', data: officeSeries }],
                                    chart: { type: 'bar', height: 300 },
                                    plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                                    dataLabels: { enabled: true },
                                    xaxis: { categories: {!! isset($officeData) ? json_encode(array_keys($officeData->toArray())) : '[]' !!} },
                                    colors: ['#f39c12']
                                };
                                new ApexCharts(document.querySelector("#officeChart"), optionsOffice).render();
                            } else {
                                document.getElementById('officeChartPlaceholder').style.display = 'block';
                            }

                            // 4. Category Polar Area Chart
                            var categorySeries = {!! isset($categoryData) ? json_encode(array_values($categoryData->toArray())) : '[]' !!};
                            if (hasData(categorySeries)) {
                                var optionsCategory = {
                                    series: categorySeries,
                                    chart: { type: 'polarArea', height: 350 },
                                    labels: {!! isset($categoryData) ? json_encode(array_keys($categoryData->toArray())) : '[]' !!},
                                    colors: ['#605ca8', '#36a2eb', '#cc65fe', '#ffce56', '#ff9f40', '#4bc0c0'],
                                    fill: { opacity: 0.8 },
                                    legend: { position: 'bottom' }
                                };
                                new ApexCharts(document.querySelector("#categoryChart"), optionsCategory).render();
                            } else {
                                document.getElementById('categoryChartPlaceholder').style.display = 'block';
                            }

                            // 5. Open Time Line Chart
                            var openSeries = {!! isset($openData) ? json_encode(array_values($openData->toArray())) : '[]' !!};
                            if (hasData(openSeries)) {
                                var optionsOpen = {
                                    series: [{ name: 'Tickets Opened', data: openSeries }],
                                    chart: { height: 350, type: 'area', zoom: { enabled: false } },
                                    dataLabels: { enabled: false },
                                    stroke: { curve: 'smooth' },
                                    xaxis: { categories: {!! isset($openData) ? json_encode(array_keys($openData->toArray())) : '[]' !!} },
                                    colors: ['#00a65a'],
                                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3, stops: [0, 90, 100] } }
                                };
                                new ApexCharts(document.querySelector("#openChart"), optionsOpen).render();
                            } else {
                                document.getElementById('openChartPlaceholder').style.display = 'block';
                            }

                            // 6. Close Bar Chart
                            var closeSeries = {!! isset($closeData) ? json_encode(array_values($closeData->toArray())) : '[]' !!};
                            if (hasData(closeSeries)) {
                                var optionsClose = {
                                    series: [{ name: 'Avg Days to Close', data: closeSeries }],
                                    chart: { type: 'bar', height: 400 },
                                    plotOptions: { bar: { columnWidth: '45%', distributed: true } },
                                    dataLabels: { enabled: true, formatter: function (val) { return val + " days"; } },
                                    legend: { show: false },
                                    xaxis: { 
                                        categories: {!! isset($closeData) ? json_encode(array_keys($closeData->toArray())) : '[]' !!},
                                        labels: { rotate: -45, style: { fontSize: '12px' } }
                                    }
                                };
                                new ApexCharts(document.querySelector("#closeChart"), optionsClose).render();
                            } else {
                                document.getElementById('closeChartPlaceholder').style.display = 'block';
                            }
                        }
                    </script>
                </div>

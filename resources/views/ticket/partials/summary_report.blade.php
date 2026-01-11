                <div class="tab-pane" id="summary_report">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="box-title" style="margin-bottom: 20px; font-weight: bold; color: #333;">
                                <i class="fa fa-bar-chart"></i> Ticket Summary & Performance Analytics
                            </h3>
                        </div>
                    </div>

                    <!-- Ticket Dashboard (Auto-Calculating) -->
                    <div class="row" style="margin-bottom:20px;">
                        <div class="col-md-3">
                            <div class="small-box bg-aqua shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardTotal">{{ $dashboardTotals['totalTickets'] }}</h3>
                                    <p>Total Tickets Logged</p>
                                </div>
                                <div class="icon"><i class="fa fa-ticket"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-yellow shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardOpen">{{ $dashboardTotals['openTicketsCount'] }}</h3>
                                    <p>Open Tickets</p>
                                </div>
                                <div class="icon"><i class="fa fa-folder-open"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-green shadow-sm" style="border-radius: 8px;">
                                <div class="inner">
                                    <h3 id="dashboardClosed">{{ $dashboardTotals['closedTicketsCount'] }}</h3>
                                    <p>Closed Tickets</p>
                                </div>
                                <div class="icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                                <div class="box-body">
                                    <div id="slaChart" style="min-height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #dd4b39;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tickets by Status</h4>
                                </div>
                                <div class="box-body">
                                    <div id="statusChart" style="min-height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #f39c12;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tickets by Office</h4>
                                </div>
                                <div class="box-body">
                                    <div id="officeChart" style="min-height: 300px;"></div>
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
                                <div class="box-body">
                                    <div id="categoryChart" style="min-height: 350px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box box-default shadow-sm" style="border-radius: 8px; border-top: 3px solid #00a65a;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tickets Opened Over Time</h4>
                                </div>
                                <div class="box-body">
                                    <div id="openChart" style="min-height: 350px;"></div>
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
                                <div class="box-body">
                                    <div id="closeChart" style="min-height: 400px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        var chartsInitialized = false;

                        function renderSummaryCharts() {
                            if (chartsInitialized) {
                                // Just resize/update if already exists
                                return;
                            }
                            
                            if (typeof ApexCharts === 'undefined') {
                                console.error('ApexCharts is not loaded yet.');
                                return;
                            }

                            chartsInitialized = true;

                            // SLA Polar Area Chart
                            var optionsSla = {
                                series: [{{ $slaData['met'] }}, {{ $slaData['not_met'] }}],
                                chart: { type: 'polarArea', height: 300 },
                                labels: ['Met', 'Not Met'],
                                colors: ['#28a745', '#dc3545'],
                                stroke: { colors: ['#fff'] },
                                fill: { opacity: 0.8 },
                                legend: { position: 'bottom' }
                            };
                            new ApexCharts(document.querySelector("#slaChart"), optionsSla).render();

                            // Status Pie Chart
                            var optionsStatus = {
                                series: {!! json_encode(array_values($statusData->toArray())) !!},
                                chart: { type: 'pie', height: 300 },
                                labels: {!! json_encode(array_keys($statusData->toArray())) !!},
                                colors: ['#00c0ef', '#f39c12', '#00a65a', '#dd4b39', '#605ca8'],
                                legend: { position: 'bottom' }
                            };
                            new ApexCharts(document.querySelector("#statusChart"), optionsStatus).render();

                            // Office Bar Chart
                            var optionsOffice = {
                                series: [{
                                    name: 'Tickets',
                                    data: {!! json_encode(array_values($officeData->toArray())) !!}
                                }],
                                chart: { type: 'bar', height: 300 },
                                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                                dataLabels: { enabled: true },
                                xaxis: { categories: {!! json_encode(array_keys($officeData->toArray())) !!} },
                                colors: ['#f39c12']
                            };
                            new ApexCharts(document.querySelector("#officeChart"), optionsOffice).render();

                            // Category Polar Area Chart
                            var optionsCategory = {
                                series: {!! json_encode(array_values($categoryData->toArray())) !!},
                                chart: { type: 'polarArea', height: 350 },
                                labels: {!! json_encode(array_keys($categoryData->toArray())) !!},
                                colors: ['#605ca8', '#36a2eb', '#cc65fe', '#ffce56', '#ff9f40', '#4bc0c0'],
                                fill: { opacity: 0.8 },
                                responsive: [{
                                    breakpoint: 480,
                                    options: { chart: { width: 200 }, legend: { position: 'bottom' } }
                                }]
                            };
                            new ApexCharts(document.querySelector("#categoryChart"), optionsCategory).render();

                            // Open Time Line Chart
                            var optionsOpen = {
                                series: [{
                                    name: 'Tickets Opened',
                                    data: {!! json_encode(array_values($openData->toArray())) !!}
                                }],
                                chart: { height: 350, type: 'area', zoom: { enabled: false } },
                                dataLabels: { enabled: false },
                                stroke: { curve: 'smooth' },
                                xaxis: { categories: {!! json_encode(array_keys($openData->toArray())) !!} },
                                colors: ['#00a65a'],
                                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3, stops: [0, 90, 100] } }
                            };
                            new ApexCharts(document.querySelector("#openChart"), optionsOpen).render();

                            // Close Bar Chart
                            var optionsClose = {
                                series: [{
                                    name: 'Avg Days to Close',
                                    data: {!! json_encode(array_values($closeData->toArray())) !!}
                                }],
                                chart: { type: 'bar', height: 400 },
                                plotOptions: { bar: { columnWidth: '45%', distributed: true } },
                                dataLabels: { enabled: true, formatter: function (val) { return val + " days"; } },
                                legend: { show: false },
                                xaxis: { 
                                    categories: {!! json_encode(array_keys($closeData->toArray())) !!},
                                    labels: { rotate: -45, style: { fontSize: '12px' } }
                                }
                            };
                            new ApexCharts(document.querySelector("#closeChart"), optionsClose).render();
                        }
                    </script>

                </div>

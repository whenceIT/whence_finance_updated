                <div class="tab-pane" id="summary_report">
                    <h4>Summary Report</h4>

                    <!-- Ticket Dashboard (Auto-Calculating) -->
                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardTotal">{{ $dashboardTotals['totalTickets'] }}</strong><div>Total tickets logged</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardOpen">{{ $dashboardTotals['openTicketsCount'] }}</strong><div>Open tickets</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardClosed">{{ $dashboardTotals['closedTicketsCount'] }}</strong><div>Closed tickets</div></div></div>
                        <div class="col-md-3"><div class="well text-center"><strong id="dashboardSlaPercent">{{ $dashboardTotals['slaCompliancePercent'] }}</strong><div>SLA compliance</div></div></div>
                    </div>

                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-6">
                            <h4>SLA Compliance</h4>
                            <div id="slaChart"></div>
                        </div>
                        <div class="col-md-6">
                            <h4>Tickets by Office</h4>
                            <div id="officeChart"></div>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-6">
                            <h4>Tickets by Issue Category</h4>
                            <div id="categoryChart"></div>
                        </div>
                        <div class="col-md-6">
                            <h4>Tickets Opened Over Time</h4>
                            <div id="openChart"></div>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:12px;">
                        <div class="col-md-6">
                            <h4>Average Days to Close by Office</h4>
                            <div id="closeChart"></div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                    <script>
                        // SLA Polar Area Chart
                        var optionsSla = {
                            series: [{{ $slaData['met'] }}, {{ $slaData['not_met'] }}],
                            chart: {
                                type: 'polarArea',
                                height: 350
                            },
                            labels: ['Met', 'Not Met'],
                            colors: ['#28a745', '#dc3545']
                        };
                        var slaChart = new ApexCharts(document.querySelector("#slaChart"), optionsSla);
                        slaChart.render();

                        // Office Bar Chart
                        var optionsOffice = {
                            series: [{
                                data: {!! json_encode(array_values($officeData->toArray())) !!}
                            }],
                            chart: {
                                type: 'bar',
                                height: 350
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                categories: {!! json_encode(array_keys($officeData->toArray())) !!},
                            }
                        };
                        var officeChart = new ApexCharts(document.querySelector("#officeChart"), optionsOffice);
                        officeChart.render();

                        // Category Polar Area Chart
                        var optionsCategory = {
                            series: {!! json_encode(array_values($categoryData->toArray())) !!},
                            chart: {
                                type: 'polarArea',
                                height: 350
                            },
                            labels: {!! json_encode(array_keys($categoryData->toArray())) !!},
                            colors: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#ff9f40', '#4bc0c0']
                        };
                        var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), optionsCategory);
                        categoryChart.render();

                        // Open Time Line Chart
                        var optionsOpen = {
                            series: [{
                                name: 'Tickets Opened',
                                data: {!! json_encode(array_values($openData->toArray())) !!}
                            }],
                            chart: {
                                height: 350,
                                type: 'line',
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                categories: {!! json_encode(array_keys($openData->toArray())) !!},
                            }
                        };
                        var openChart = new ApexCharts(document.querySelector("#openChart"), optionsOpen);
                        openChart.render();

                        // Close Bar Chart
                        var optionsClose = {
                            series: [{
                                name: 'Average Days',
                                data: {!! json_encode(array_values($closeData->toArray())) !!}
                            }],
                            chart: {
                                type: 'bar',
                                height: 350
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                categories: {!! json_encode(array_keys($closeData->toArray())) !!},
                            }
                        };
                        var closeChart = new ApexCharts(document.querySelector("#closeChart"), optionsClose);
                        closeChart.render();
                    </script>

                </div>
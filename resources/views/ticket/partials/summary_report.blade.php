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
                        if(!window.slaChart){
                            var ctx = document.getElementById('slaChart').getContext('2d');
                            window.slaChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Met', 'Not Met'],
                                    datasets: [{
                                        data: [{{ $slaData['met'] }}, {{ $slaData['not_met'] }}],
                                        backgroundColor: ['#28a745', '#dc3545']
                                    }]
                                }
                            });
                        }

                        // Office Bar Chart
                        if(!window.officeChart){
                            var ctx2 = document.getElementById('officeChart').getContext('2d');
                            window.officeChart = new Chart(ctx2, {
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
                        }

                        // Category Pie Chart
                        if(!window.categoryChart){
                            var ctx3 = document.getElementById('categoryChart').getContext('2d');
                            window.categoryChart = new Chart(ctx3, {
                                type: 'pie',
                                data: {
                                    labels: {!! json_encode(array_keys($categoryData->toArray())) !!},
                                    datasets: [{
                                        data: {!! json_encode(array_values($categoryData->toArray())) !!},
                                        backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#ff9f40', '#4bc0c0']
                                    }]
                                }
                            });
                        }

                        // Open Time Line Chart
                        if(!window.openChart){
                            var ctx4 = document.getElementById('openChart').getContext('2d');
                            window.openChart = new Chart(ctx4, {
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
                        }

                        // Close Bar Chart
                        if(!window.closeChart){
                            var ctx5 = document.getElementById('closeChart').getContext('2d');
                            window.closeChart = new Chart(ctx5, {
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
                        }
                    </script>

                </div>
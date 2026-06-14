<div id="pltChartContainer" style="margin-top: 20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 10px;">
        <h4 style="margin:0;">Income vs Expenses (Live)</h4>
        <div style="font-size:12px; color:#666;">
            <span id="pltLastUpdate" style="margin-right:10px;">Last updated: --</span>
            <i class="fa fa-sync fa-spin" id="pltRefreshIcon" style="display:none;"></i>
        </div>
    </div>
    <div style="width:100%; height:350px;">
        <div id="pltIncomeExpenseChart"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function() {
    var chart;
    var incomeData = [];
    var expenseData = [];
    var categories = [];

    function initChart() {
        var options = {
            series: [{
                name: 'Income',
                data: incomeData
            }, {
                name: 'Expenses',
                data: expenseData
            }],
            chart: {
                height: 350,
                type: 'area',
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: 'smooth',
            },
            xaxis: {
                type: 'datetime',
                categories: categories,
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm',
                },
                y: {
                    formatter: function(val) {
                        return 'K' + val.toFixed(2);
                    }
                }
            },
            colors: ['#27ae60', '#e74c3c'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    opacity: 0.3
                }
            }
        };

        chart = new ApexCharts(document.querySelector('#pltIncomeExpenseChart'), options);
        chart.render();
        fetchChartData();
    }

    function fetchChartData() {
        $('#pltRefreshIcon').show();
        $.get('{{ route("api.provincial-ledger.index") }}', function(response) {
            $('#pltRefreshIcon').hide();
            if (response.success && response.data) {
                incomeData = [];
                expenseData = [];
                categories = [];

                var allTransactions = response.data;
                allTransactions.forEach(function(tx) {
                    categories.push(tx.transaction_date || tx.created_at);
                    var amount = parseFloat(tx.amount) || 0;
                    if (tx.type === 'income') {
                        incomeData.push(amount);
                        expenseData.push(0);
                    } else {
                        incomeData.push(0);
                        expenseData.push(amount);
                    }
                });

                chart.updateSeries([{
                    name: 'Income',
                    data: incomeData
                }, {
                    name: 'Expenses',
                    data: expenseData
                }]);
                chart.updateOptions({
                    xaxis: {
                        categories: categories
                    }
                });

                $('#pltLastUpdate').text('Last updated: ' + new Date().toLocaleTimeString());
            }
        }).fail(function() {
            $('#pltRefreshIcon').hide();
        });
    }

    $(document).ready(function() {
        initChart();
        setInterval(fetchChartData, 10000);
    });
})();
</script>
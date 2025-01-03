@extends('layouts.master')

@section('title') 
Performance Metricss
@endsection

<style>
    .fixed-width-name {
        width: 150px;
    }
    .fixed-width-branch {
        width: 150px;
    }
    .fixed-width-target {
        width: 100px;
    }
    .table-container {
        overflow-x: auto;
    }
    .met-target {
        color: green;
        font-weight: bold;
    }
    .not-met-target {
        color: red;
        font-weight: bold;
    }
    .container {
        max-width: 1200px; 
        margin: 0 auto;
    }
    .table-responsive {
        overflow-x: auto; 
    }
    #data-table {
        width: 100%; 
    }
    /*list container */
    ul#usersMetTargets,
    ul#usersNotMetTargets {
        list-style-type: none; 
        padding: 0;
    }

    ul#usersMetTargets li,
    ul#usersNotMetTargets li {
        background-color: #f4f4f4; 
        border: 1px solid #ddd; 
        border-radius: 4px; 
        padding: 10px; 
        margin-bottom: 5px; 
        font-size: 16px; 
        color: #333; 
    }
    
    ul#usersMetTargets li:hover,
    ul#usersNotMetTargets li:hover {
        background-color: #e0e0e0; 
        cursor: pointer;
    }
    .chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }

    .row.justify-content-center {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .col-md-6, .col-md-12 {
        display: flex;
        justify-content: center;
    }
</style>

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">

        <div style="display: flex; align-items: center; justify-content: center; padding-bottom: 10px;">
            <a href="{{ route('performance_metrics.targets') }}" style="margin: 10px;">
                <span class="label label-success" style="font-size: 15px;">Targets</span>
            </a>

            <a href="{{ route('performance_metrics.uncollected') }}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Staff Uncollected amounts</span>
            </a>

            <a href="{{ route('performance_metrics.low_performance') }}" style="margin: 10px;">
                <span class="label label-danger" style="font-size: 15px;">Low Performance</span>
            </a>

            <a href="{{ route('performance_metrics.defaulted') }}" style="margin: 10px;">
                <span class="label label-warning" style="font-size: 15px;">Staff Defaulted Loans</span>
            </a>
        </div>

        <p style="text-align: center;">Displaying percentage of targets met this in current cycle and percentage of LC's meeting their targets and reducing their uncollected by 90%.
            Click tabs above for more info.
        </p>

        <?php

            $todaysDate = date('Y-m-d');
            $use = date('Y-m-');
            $num = 24;
            $targetDate = $use . $num;
            $targetDate = date('Y-m-d', strtotime($targetDate));

            if ($todaysDate > $targetDate) {
                $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 month'));
            }

            $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 month'));

            $metCounts = [];
            $notMetCounts = [];
            $labels = [];
            $newLoanCounts = [];
            $reloanCounts = [];
        
            $carryOver = $loanConsultants->mapWithKeys(function ($consultant) {
                return [$consultant->id => 0];
            })->toArray();

            for ($i = 0; $i < 6; $i++) {
                $currentTargetDate = date('Y-m-d', strtotime($targetDate . " -$i months"));
                $currentCompareDate = date('Y-m-d', strtotime($compareDate . " -$i months"));

                $startMonth = date('M', strtotime($currentCompareDate));
                $endMonth = date('M', strtotime($currentTargetDate));
                $labels[] = "$startMonth - $endMonth";
                
                $monthlyMetCount = 0;
                $monthlyNotMetCount = 0;
                $monthlyNewLoanCount = 0;
                $monthlyReloanCount = 0;

                $loanConsultants->each(function ($user) use (&$carryOver, &$monthlyMetCount, &$monthlyNotMetCount, &$monthlyNewLoanCount, &$monthlyReloanCount, $currentCompareDate, $currentTargetDate) {
                    $new_loan_total = 0;
                    $reloan_total = 0;

                    foreach ($user->loan as $loan) {
                        foreach ($loan->transactions as $transaction) {
                            $transactionDate = \Carbon\Carbon::parse($transaction->date);

                            if ($transactionDate > $currentCompareDate && $transactionDate <= $currentTargetDate) {
                                if ($transaction->transaction_type == 'disbursement') {
                                    $new_loan_total += $transaction->debit;
                                    $monthlyNewLoanCount++;
                                } elseif ($transaction->transaction_type == 'interest') {
                                    $principal = $transaction->debit / 0.4;
                                    $reloan_total += $principal;
                                    $monthlyReloanCount++;
                                }
                            }
                        }
                    }

                    
                    $totalAmount = $new_loan_total + $reloan_total + $carryOver[$user->id];

                    if ($totalAmount >= 40000) {
                        $monthlyMetCount++;
                        // Calculate new carry-over
                        $carryOver[$user->id] = $totalAmount - 40000;
                    } else {
                        $monthlyNotMetCount++;
                        // Reset carry-over if target not met
                        $carryOver[$user->id] = 0;
                    }
                });

                // Add the results to the arrays
                $metCounts[] = $monthlyMetCount;
                $notMetCounts[] = $monthlyNotMetCount;
                $newLoanCounts[] = $monthlyNewLoanCount;
                $reloanCounts[] = $monthlyReloanCount;
            }

        // Reverse the arrays to maintain chronological order
        $labels = array_reverse($labels);
        $metCounts = array_reverse($metCounts);
        $notMetCounts = array_reverse($notMetCounts);
        $newLoanCounts = array_reverse($newLoanCounts);
        $reloanCounts = array_reverse($reloanCounts);

        // Data for the current month doughnut chart

        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $num = 24;
        $targetDate = $use . $num;
        $targetDate = date('Y-m-d', strtotime($targetDate));

        if ($todaysDate > $targetDate) {
            $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 month'));
        }

        $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 month'));
        //$previousMonthStart = date('Y-m-25', strtotime("-" . ($i + 1) . " months"));

        $currentMetCount = 0;
        $currentNotMetCount = 0;
        $metUsers = [];
        $notMetUsers = [];

        $carryOver = $loanConsultants->mapWithKeys(function ($consultant) {
            return [$consultant->id => 0];
        })->toArray();

        foreach ($loanConsultants as $user) {
            $new_loan_total = 0;
            $reloan_total = 0;


            foreach ($user->loan as $loan) {
                foreach ($loan->transactions as $transaction) {
                    $transactionDate = \Carbon\Carbon::parse($transaction->date);

                    if ($transactionDate > $compareDate && $transactionDate <= $targetDate) {
                        if ($transaction->transaction_type == 'disbursement') {
                            $new_loan_total += $transaction->debit;
                        } elseif ($transaction->transaction_type == 'interest') {
                            $principal = $transaction->debit / 0.4;
                            $reloan_total += $principal;
                        }
                    }
                }
            }

            $totalAmount = $new_loan_total + $reloan_total + $carryOver[$user->id];

            if ($totalAmount >= 40000) {
                $currentMetCount++;
                $metUsers[] = $user-> first_name.' '. $user-> last_name.'     '. $user-> office->name; 
                $carryOver[$user->id] = $totalAmount - 40000;
            } else {
                $currentNotMetCount++;
                $notMetUsers[] = $user->first_name.' '.$user-> last_name.'     '. $user-> office->name;
                $carryOver[$user->id] = 0;
            }
        }

        

        //for those who have reduced their uncollected by 90%

        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $num = 24;
        $targetDate = $use . $num;
        $targetDate = date('Y-m-d', strtotime($targetDate));

        if ($todaysDate > $targetDate) {
            $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 month'));
        }

        $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 month'));
        //$previousMonthStart = date('Y-m-25', strtotime("-" . ($i + 1) . " months"));

        

        $reducedByNinety = 0;
        $notReducedByNinety = 0;

        // Initialize the carry-over array using the collection
        $carryOver = $loanConsultants->mapWithKeys(function ($consultant) {
            return [$consultant->id => 0];
        })->toArray();

        $loanConsultants->each(function ($user) use (&$carryOver, &$reducedByNinety, &$notReducedByNinety, $compareDate, $targetDate) {
            $initialUncollected = 0;
            $finalUncollected = 0;
            $new_loan_total = 0;
            $reloan_total = 0;
            $totalCollected = 0;

            foreach ($user->loan as $loan) {
                foreach ($loan->transactions as $transaction) {
                    $transactionDate = \Carbon\Carbon::parse($transaction->date);

                    // Calculate initial uncollected (before compare date)
                    if ($transactionDate <= $compareDate) {
                        if ($transaction->transaction_type != 'specified_due_date_fee') {
                            $initialUncollected += $transaction->debit - $transaction->credit;
                        }
                    }

                    // Calculate transactions within the cycle
                    if ($transactionDate > $compareDate && $transactionDate <= $targetDate) {
                        if ($transaction->transaction_type == 'disbursement') {
                            $new_loan_total += $transaction->debit;
                        } elseif ($transaction->transaction_type == 'interest') {
                            $principal = $transaction->debit / 0.4;
                            $reloan_total += $principal;
                        }

                        $totalCollected += $transaction->credit;
                    }
                }
            }

            // Calculate final uncollected
            $finalUncollected = $initialUncollected + $new_loan_total + $reloan_total - $totalCollected;
            if ($finalUncollected < 0) {
                $finalUncollected = 0;
            }

            // Calculate total amount for target achievement
            $totalAmount = $new_loan_total + $reloan_total + $carryOver[$user->id];

            // Check if target is met and uncollected is reduced by 90%
            if ($totalAmount >= 40000 && $finalUncollected <= ($initialUncollected * 0.1)) {
                $reducedByNinety++;
                // Calculate new carry-over
                $carryOver[$user->id] = $totalAmount - 40000;
            } else {
                $notReducedByNinety++;
                // Reset carry-over if conditions not met
                $carryOver[$user->id] = 0;
            }
        });


        ?>

        <!-- Doughnut Chart for Current Month --
        <div class="row justify-content-center">
            <div class="col-md-6 d-flex justify-content-center" style="max-width: 800px; margin-top: 50px;">
                <div class="chart-container d-flex justify-content-center align-items-center">
                    <canvas id="currentMonthDoughnutChart" style="max-width: 450px; max-height: 450px;"></canvas>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-center" style="max-width: 800px; margin-top: 50px;">
                <div class="chart-container d-flex justify-content-center align-items-center">
                    <canvas id="targetsNotReducingDoughnutChart" style="max-width: 450px; max-height: 450px;"></canvas>
                </div>
            </div>
        </div>

        <!-- User lists --
        <div class="row">
            <div class="col-md-6">
                <h4 id="metTargetsHeader" class="hidden">Users Who Met Targets</h4>
                <ul id="usersMetTargets" class="hidden"></ul>
            </div>
            <div class="col-md-6">
                <h4 id="notMetTargetsHeader" class="hidden">Users Who Did Not Meet Targets</h4>
                <ul id="usersNotMetTargets" class="hidden"></ul>
            </div>
        </div>

            
    </div>

        <!-- Bar Chart for Yearly Data --
        <div class="row justify-content-center">
            <div class="col-md-8" style="max-width: 1000px; margin: 0 auto; margin-top: 50px; display: flex; justify-content: center;">
                <canvas id="monthlyPerformanceBarChart" style="max-width: 800px; max-height: 400px;"></canvas>
            </div>
        </div>
        <canvas id="newClientsChart"></canvas>
    </div>-->
</div> 
@endsection

@section('footer-scripts') 
<script src="{{ asset('js/chart.js') }}"></script>
<script>

    var metUsers = @json($metUsers);
    var notMetUsers = @json($notMetUsers);

    // Doughnut Chart for Current Month
    var ctx1 = document.getElementById('currentMonthDoughnutChart').getContext('2d');
    var data1 = {
        labels: ['Met Targets', 'Did Not Meet Targets'],
        datasets: [{
            data: [{{ $currentMetCount }}, {{ $currentNotMetCount }}],
            backgroundColor: ['#36A2EB', '#FFCE56']
        }]
    };
    var currentMonthDoughnutChart = new Chart(ctx1, {
        type: 'doughnut',
        data: data1,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    var index = elements[0].index;
                    var label = data1.labels[index];

                    // Display the corresponding list
                    var metTargetsHeader = document.getElementById('metTargetsHeader');
                    var notMetTargetsHeader = document.getElementById('notMetTargetsHeader');
                    var usersMetTargets = document.getElementById('usersMetTargets');
                    var usersNotMetTargets = document.getElementById('usersNotMetTargets');

                    if (label === 'Met Targets') {
                        if (usersMetTargets.classList.contains('hidden')) {
                            usersMetTargets.classList.remove('hidden');
                            usersMetTargets.innerHTML = metUsers.map(user => `<li>${user}</li>`).join('');
                            metTargetsHeader.classList.remove('hidden');
                            notMetTargetsHeader.classList.add('hidden');
                            usersNotMetTargets.classList.add('hidden');
                        } else {
                            usersMetTargets.classList.add('hidden');
                            metTargetsHeader.classList.add('hidden');
                        }
                    } else if (label === 'Did Not Meet Targets') {
                        if (usersNotMetTargets.classList.contains('hidden')) {
                            usersNotMetTargets.classList.remove('hidden');
                            usersNotMetTargets.innerHTML = notMetUsers.map(user => `<li>${user}</li>`).join('');
                            notMetTargetsHeader.classList.remove('hidden');
                            metTargetsHeader.classList.add('hidden');
                            usersMetTargets.classList.add('hidden');
                        } else {
                            usersNotMetTargets.classList.add('hidden');
                            notMetTargetsHeader.classList.add('hidden');
                        }
                    }
                }
            }
        }
    });


    // Bar Chart for Yearlyy Data
    var ctx2 = document.getElementById('monthlyPerformanceBarChart').getContext('2d');
    var data2 = {
        labels: {!! json_encode($labels) !!},
        datasets: [
            {
                label: 'Met Targets',
                data: {!! json_encode($metCounts) !!},
                backgroundColor: '#36A2EB',
            },
            {
                label: 'Did Not Meet Targets',
                data: {!! json_encode($notMetCounts) !!},
                backgroundColor: '#FFCE56',
            }
        ]
    };
    var monthlyPerformanceBarChart = new Chart(ctx2, {
        type: 'bar',
        data: data2,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    beginAtZero: true, //and here
                    ticks: {
                    autoSkip: false,
                    maxRotation: 45,
                    minRotation: 45
                }
                },
                y: {
                    beginAtZero: true
                }
            },
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    var monthIndex = elements[0].index;
                    var monthLabel = data2.labels[monthIndex];

                     // Display the corresponding list
                     var metTargetsHeader = document.getElementById('metTargetsHeader');
                    var notMetTargetsHeader = document.getElementById('notMetTargetsHeader');
                    var usersMetTargets = document.getElementById('usersMetTargets');
                    var usersNotMetTargets = document.getElementById('usersNotMetTargets');

                    if (label === 'Met Targets') {
                        if (usersMetTargets.classList.contains('hidden')) {
                            usersMetTargets.classList.remove('hidden');
                            usersMetTargets.innerHTML = metUsers.map(user => `<li>${user}</li>`).join('');
                            metTargetsHeader.classList.remove('hidden');
                            notMetTargetsHeader.classList.add('hidden');
                            usersNotMetTargets.classList.add('hidden');
                        } else {
                            usersMetTargets.classList.add('hidden');
                            metTargetsHeader.classList.add('hidden');
                        }
                    } else if (label === 'Did Not Meet Targets') {
                        if (usersNotMetTargets.classList.contains('hidden')) {
                            usersNotMetTargets.classList.remove('hidden');
                            usersNotMetTargets.innerHTML = notMetUsers.map(user => `<li>${user}</li>`).join('');
                            notMetTargetsHeader.classList.remove('hidden');
                            metTargetsHeader.classList.add('hidden');
                            usersMetTargets.classList.add('hidden');
                        } else {
                            usersNotMetTargets.classList.add('hidden');
                            notMetTargetsHeader.classList.add('hidden');
                        }
                    }
                }
            }
        }
    });

    //3rd chart
    var ctx3 = document.getElementById('targetsNotReducingDoughnutChart').getContext('2d');
    var data3 = {
        labels: ['Met Targets and Reduced Uncollected Amount by 90%', 'Did Not Reduce by 90%'],
        datasets: [{
            data: [{{ $reducedByNinety }}, {{ $notReducedByNinety }}],
            backgroundColor: ['#4BC0C0', '#FF9F40']
        }]
    };
    console.log('Reduced by 90%:', {{ $reducedByNinety }});
    console.log('Not Reduced by 90%:', {{ $notReducedByNinety }});


    var myDoughnutChart3 = new Chart(ctx3, {
        type: 'doughnut',
        data: data3,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                display: true,
                position: 'top', // Position the legend at the bottom
                labels: {
                    boxWidth: 30, // Width of the color box
                    padding: 0, // Padding between legend items
                    font: {
                        size: 12 // Font size for the labels
                    }
                }
            },
                datalabels: {
                    formatter: function(value) {
                        return value;
                        //var total = context.dataset.data.reduce((a, b) => a + b, 0);
                        //var percentage = (value / total * 100).toFixed(2);
                        //return percentage + '%';
                    },
                    color: '#fff',
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            var value = tooltipItem.raw;
                            return tooltipItem.label + ': ' + value; 
                            //var total = tooltipItem.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            //var percentage = (value / total * 100).toFixed(2);
                            //return tooltipItem.label + ': ' + percentage + '%';
                        }
                    }
                }
            }
        }
    });

    //newclients chart
    /*var ctx = document.getElementById('newClientsChart').getContext('2d');
    var newClientsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels), // Month labels for each cycle
            datasets: [{
                label: '# of New Clients',
                data: @json($newClientsData), // New clients count for each cycle
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });*/


</script>
@endsection


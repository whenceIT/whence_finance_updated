@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Completion Report - {{ $quiz->title }}</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quiz Completion Dashboard</h3>
                        <div class="box-tools">
                            <a href="{{ route('policy.quizzes.report', $quiz->id) }}" class="btn btn-success btn-sm">
                                <i class="fa fa-download"></i> Export CSV
                            </a>
                            <a href="{{ route('admin.policy-quizzes.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to Quizzes
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Statistics -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Users</span>
                                        <span class="info-box-number">{{ $totalUsers ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-blue">
                                    <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Completed</span>
                                        <span class="info-box-number">{{ $completedAttempts }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $totalUsers > 0 ? ($completedAttempts / $totalUsers * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $totalUsers > 0 ? round($completedAttempts / $totalUsers * 100, 1) : 0 }}% completion rate
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending</span>
                                        <span class="info-box-number">{{ $pendingUsers }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $totalUsers > 0 ? ($pendingUsers / $totalUsers * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $totalUsers > 0 ? round($pendingUsers / $totalUsers * 100, 1) : 0 }}% still pending
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Failed</span>
                                        @php
                                            $failedAttempts = $attempts->where('passed', false)->count();
                                        @endphp
                                        <span class="info-box-number">{{ $failedAttempts }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $completedAttempts > 0 ? ($failedAttempts / $completedAttempts * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $completedAttempts > 0 ? round($failedAttempts / $completedAttempts * 100, 1) : 0 }}% failure rate
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quiz Details -->
                        <div class="well">
                            <h4>Quiz Details</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <p><strong>Title:</strong> {{ $quiz->title }}</p>
                                    <p><strong>Open Date:</strong> {{ $quiz->open_date->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Close Date:</strong> {{ $quiz->close_date->format('M d, Y H:i') }}</p>
                                    <p><strong>Status:</strong> 
                                        @if($quiz->isOpen())
                                            <span class="label label-success">Active & Open</span>
                                        @elseif($quiz->active && $quiz->open_date > now())
                                            <span class="label label-info">Scheduled</span>
                                        @else
                                            <span class="label label-default">Closed</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Passing Threshold:</strong> {{ $quiz->passing_threshold }}%</p>
                                    <p><strong>Time Limit:</strong> {{ $quiz->time_limit_minutes }} minutes</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Max Questions:</strong> {{ $quiz->max_questions }}</p>
                                    <p><strong>Total Questions:</strong> {{ $quiz->questions()->count() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Completion List -->
                        @if($attempts->count() > 0)
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Completed Attempts ({{ $attempts->count() }})</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User</th>
                                                <th>Office</th>
                                                <th>Score</th>
                                                <th>Status</th>
                                                <th>Date Taken</th>
                                                <th>Time Taken</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attempts as $attempt)
                                                @php
                                                    $user = $attempt->user;
                                                    $timeTaken = $attempt->started_at->diff($attempt->completed_at);
                                                    $timeTakenStr = $timeTaken->format('%H:%I:%S');
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                                        <br><small>{{ $user->email }}</small>
                                                    </td>
                                                    <td>
                                                        @if($user->office)
                                                            {{ $user->office->name }}
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $attempt->passed ? 'green' : 'red' }}" style="font-size: 14px;">
                                                            {{ number_format($attempt->score_percentage, 1) }}%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($attempt->passed)
                                                            <span class="label label-success">Passed</span>
                                                        @else
                                                            <span class="label label-danger">Failed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $attempt->completed_at->format('M d, Y') }}</td>
                                                    <td>{{ $attempt->completed_at->format('h:i A') }}</td>
                                                    <td>
                                                        <span title="Time taken: {{ $timeTakenStr }}">
                                                            <i class="fa fa-clock-o"></i> {{ $timeTakenStr }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Score Distribution -->
                                <div class="row" style="margin-top: 30px;">
                                    <div class="col-md-6">
                                        <div class="box box-info">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Score Distribution</h3>
                                            </div>
                                            <div class="box-body">
                                                @php
                                                    $scoreRanges = [
                                                        '90-100%' => $attempts->where('score_percentage', '>=', 90)->count(),
                                                        '80-89%' => $attempts->whereBetween('score_percentage', [80, 89.99])->count(),
                                                        '70-79%' => $attempts->whereBetween('score_percentage', [70, 79.99])->count(),
                                                        '60-69%' => $attempts->whereBetween('score_percentage', [60, 69.99])->count(),
                                                        '0-59%' => $attempts->where('score_percentage', '<', 60)->count(),
                                                    ];
                                                @endphp
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Score Range</th>
                                                            <th>Count</th>
                                                            <th>Percentage</th>
                                                            <th>Progress</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($scoreRanges as $range => $count)
                                                            @php
                                                                $percentage = $attempts->count() > 0 ? ($count / $attempts->count() * 100) : 0;
                                                                $color = match(true) {
                                                                    $range === '90-100%' => 'success',
                                                                    $range === '80-89%' => 'info',
                                                                    $range === '70-79%' => 'warning',
                                                                    default => 'danger',
                                                                };
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $range }}</td>
                                                                <td>{{ $count }}</td>
                                                                <td>{{ round($percentage, 1) }}%</td>
                                                                <td>
                                                                    <div class="progress progress-xs">
                                                                        <div class="progress-bar progress-bar-{{ $color }}" style="width: {{ $percentage }}%"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="box box-success">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Pass/Fail Summary</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="text-center">
                                                    <canvas id="passFailChart" width="200" height="200"></canvas>
                                                </div>
                                                <table class="table" style="margin-top: 20px;">
                                                    <tr>
                                                        <td><span class="label label-success">Passed</span></td>
                                                        <td>{{ $attempts->where('passed', true)->count() }}</td>
                                                        <td>{{ $attempts->count() > 0 ? round($attempts->where('passed', true)->count() / $attempts->count() * 100, 1) : 0 }}%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="label label-danger">Failed</span></td>
                                                        <td>{{ $attempts->where('passed', false)->count() }}</td>
                                                        <td>{{ $attempts->count() > 0 ? round($attempts->where('passed', false)->count() / $attempts->count() * 100, 1) : 0 }}%</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> No one has completed this quiz yet.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Pass/Fail Chart
    const ctx = document.getElementById('passFailChart').getContext('2d');
    const passed = {{ $attempts->where('passed', true)->count() }};
    const failed = {{ $attempts->where('passed', false)->count() }};
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Failed'],
            datasets: [{
                data: [passed, failed],
                backgroundColor: [
                    '#00a65a',
                    '#dd4b39'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = passed + failed;
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
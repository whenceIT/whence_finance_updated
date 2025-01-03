@extends('layouts.master')

@section('title') 
    Targets
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
</style>

@section('content')
<div class="box box-success">
    <div class="box-header with-border">
    <div style="display: flex; align-items: center; justify-content: center; padding-bottom: 10px; ">
                <a href="{{ route('performance_metrics.targets') }}" style="margin: 10px;">
                <span class="label label-success" style="font-size: 15px;">Targets</span>
                </a>

                <a href="{{ route('performance_metrics.uncollected')}}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Staff Uncollected amounts</span>
                </a>

                <a href="{{ route('performance_metrics.low_performance')}}" style="margin: 10px;">
                <span class="label label-danger" style="font-size: 15px;">Low Performance</span>
                </a>

                <a href="{{ route('performance_metrics.defaulted')}}" style="margin: 10px;">
                <span class="label label-warning" style="font-size: 15px;">Staff Defaulted Loans</span>
                </a>
            </div>

            <p style="text-align: center;">Targets in the last 6 months. Highlights possible misuse of grace period.
            </p>
            <div class="d-flex justify-content-center align-items-center mb-3" style="margin-bottom: -50px">
                <form method="GET" action="{{ route('performance_metrics.targets') }}" class="form-inline">
                    <div class="form-group">
                        <label for="office_id">Filter by Branch:</label>
                        <select name="office_id" id="office_id" class="form-control">
                            <option value="">All Branches</option>
                            @if($user_role == 1)
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif($user_role == 4)
                                @foreach($offices->where('id', $user_branch) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif($user_role == 6)
                                @foreach($offices->where('province_id', $user_province) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                <!-- Filter for Met/Not Met -->
                <!-- <form method="GET" action="{{ route('performance_metrics.targets') }}" class="form-inline">
                    <div class="form-group">
                        <label for="target_status">Target Status:</label>
                        <select name="target_status" id="target_status" class="form-control">
                            <option value="">All</option>
                            <option value="met" {{ request('target_status') == 'met' ? 'selected' : '' }}>Met</option>
                            <option value="not_met" {{ request('target_status') == 'not_met' ? 'selected' : '' }}>Not Met</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>-->

            </div>


            <div class="box-body table-responsive">
                <table id="data-table" class="table table-bordered table-striped table-hover no-footer" style="margin_top: 400px;">
    
                    <thead>
                        <tr>
                            <th rowspan="2" class="fixed-width-name">Name</th>
                            <th rowspan="2" class="fixed-width-branch">Branch</th>
                           
                        </tr>
                        <tr>
                            @for ($i = 1; $i >= 0; $i--)
                                <th class="fixed-width-target">
                                    {{ \Carbon\Carbon::parse($compare_dates[$i])->format('M') }} - {{ \Carbon\Carbon::parse($target_dates[$i])->format('M') }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loanConsultants as $user)
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
                        
                        // Initialize variables for calculations
                        $targetAmounts = array_fill(0, 2, 0);
                        $newLoans = array_fill(0, 2, 0);
                        $reloans = array_fill(0, 2, 0);
                        $metTargets = array_fill(0, 2, false);
                        $carryOver = 0;
                        
                        // Generate date ranges for 6 months
                        $compareDates = [];
                        $targetDates = [];
                        for ($i = 1; $i >= 0; $i--) {
                            $compareDates[$i] = date('Y-m-d', strtotime($compareDate . " -$i months"));
                            $targetDates[$i] = date('Y-m-d', strtotime($targetDate . " -$i months"));
                        }
                        
                        // Calculate targets for the last 6 months
                        for ($i = 1; $i >= 0; $i--) {
                            foreach ($user->loan as $loan) {
                                foreach ($loan->transactions as $transaction) {
                                    $transactionDate = $transaction->date;
                                    
                                    if ($transactionDate > $compareDates[$i] && $transactionDate <= $targetDates[$i]) {
                                        if ($transaction->transaction_type == 'disbursement') {
                                            $newLoans[$i] += $transaction->debit;
                                        } elseif ($transaction->transaction_type == 'interest') {
                                            $principal = $transaction->debit / 0.4;
                                            $reloans[$i] += $principal;
                                        }
                                    }
                                }
                            }
                            
                            $totalAmount = $newLoans[$i] + $reloans[$i] + $carryOver;
                            $targetAmounts[$i] = $totalAmount;
                            
                            if ($totalAmount >= 40000) {
                                $metTargets[$i] = true;
                                $carryOver = $totalAmount - 40000;
                            } else {
                                $metTargets[$i] = false;
                                $carryOver = 0;
                            }
                        }
                            
                            // Apply the filter
                            $shouldDisplay = true;
                            if (request('target_status') === 'met' && !$metTargets[0]) {
                                $shouldDisplay = false;
                            } elseif (request('target_status') === 'not_met' && $metTargets[0]) {
                                $shouldDisplay = false;
                            }

                            ?>

                            @if($shouldDisplay)
                            <tr>
                                <td class="fixed-width-name">{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td class="fixed-width-branch">{{ $user->office ? $user->office->name : 'N/A' }}</td>
                                @for ($i = 1; $i >= 0; $i--)
                                    <td class="fixed-width-target">{{ number_format($targetAmounts[$i], 2) }}
                                        <span class="{{ $metTargets[$i] ? 'met-target' : 'not-met-target' }}">
                                            {{ $metTargets[$i] ? '✓' : '✗' }}
                                        </span>
                                    </td>
                                @endfor
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts') 
<script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                dom: 'frtip',
                "paging": true,
                "lengthChange": false,
                "displayLength": 20,
                "searching": true,
                "ordering": false,
                "autoWidth": false,
                
                "language": {
                    "search": "{{ trans('general.search') }}",
                    "paginate": {
                        "first": "{{ trans('general.first') }}",
                        "last": "{{ trans('general.last') }}",
                        "next": "{{ trans('general.next') }}",
                        "previous": "{{ trans('general.previous') }}"
                    }
                },
                responsive: false
            });
        });
    </script>

@endsection




@extends('layouts.master')

@section('title')
    Recovery Specialists
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

{{-- Date Filter --}}
<div class="box box-default">
    <div class="box-body">
        <form method="GET" action="{{ url('recovery/specialist/data') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
            <div style="flex:1;min-width:140px">
                <label style="display:block;margin-bottom:5px;font-weight:normal;">Time Period</label>
                <select name="period" id="period" class="form-control">
                    <option value="all" {{ request('period', 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            
            <div id="customDateRange" style="display:{{ request('period') === 'custom' ? 'flex' : 'none' }};gap:12px;flex-wrap:wrap;">
                <div style="flex:1;min-width:140px">
                    <label style="display:block;margin-bottom:5px;font-weight:normal;">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div style="flex:1;min-width:140px">
                    <label style="display:block;margin-bottom:5px;font-weight:normal;">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
                <i class="fa fa-filter"></i> Apply Filter
            </button>
            <a href="{{ url('recovery/specialist/data') }}" class="btn btn-default" style="margin-bottom:0;">
                <i class="fa fa-refresh"></i> Clear
            </a>
        </form>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-users"></i> Recovery Specialists
            @if(request('period'))
                <small class="text-muted">
                    - {{ 
                        match(request('period')) {
                            'today' => 'Today',
                            'week' => 'This Week',
                            'month' => 'This Month',
                            'quarter' => 'This Quarter',
                            'year' => 'This Year',
                            'custom' => 'Custom Range',
                            default => 'All Time'
                        }
                    }}
                </small>
            @endif
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSpecialistModal">
                <i class="fa fa-plus"></i> Add Specialist
            </button>
        </div>
    </div>
    <div class="box-body no-padding">
        <table class="table table-hover table-striped" style="margin-bottom:0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Specialist</th>
                    <th>Recovered <i class="fa fa-sort-amount-desc text-muted"></i></th>
                    <th>Active Cases</th>
                    <th>Resolved Cases</th>
                    <th>Success Rate</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($specialists as $index => $row)
            <tr>
                <td>
                    @if($index === 0)
                        <span class="badge" style="background:#FFD700;color:#333;"><i class="fa fa-trophy"></i></span>
                    @elseif($index === 1)
                        <span class="badge" style="background:#C0C0C0;color:#333;"><i class="fa fa-trophy"></i></span>
                    @elseif($index === 2)
                        <span class="badge" style="background:#CD7F32;color:#fff;"><i class="fa fa-trophy"></i></span>
                    @else
                        {{ $index + 1 }}
                    @endif
                </td>
                <td>
                    <strong>{{ trim(($row['specialist']->first_name ?? '') . ' ' . ($row['specialist']->last_name ?? '')) ?: $row['specialist']->email }}</strong>
                </td>
                <td>
                    <strong class="text-success">K {{ number_format($row['total_recovered'], 0) }}</strong>
                </td>
                <td>
                    <span class="badge bg-blue">{{ $row['active_cases'] }}</span>
                </td>
                <td>
                    <span class="badge bg-green">{{ $row['resolved_cases'] }}</span>
                </td>
                <td>
                    @php
                        $totalCases = $row['active_cases'] + $row['resolved_cases'];
                        $successRate = $totalCases > 0 ? round(($row['resolved_cases'] / $totalCases) * 100) : 0;
                    @endphp
                    <div class="progress" style="margin-bottom:0;height:20px;">
                        <div class="progress-bar progress-bar-success" role="progressbar" 
                             style="width: {{ $successRate }}%">
                            {{ $successRate }}%
                        </div>
                    </div>
                </td>
                <td>
                    <a href="{{ url('recovery/specialist/' . $row['specialist']->id . '/show') }}"
                       class="btn btn-xs btn-default">
                        <i class="fa fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted" style="padding:48px">
                    No specialist data for the selected period.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Specialist Modal -->
<div class="modal fade" id="addSpecialistModal" tabindex="-1" role="dialog" aria-labelledby="addSpecialistModalLabel">
    <div class="modal-dialog" role="document">
        <form action="{{ url('recovery/specialist/store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="addSpecialistModalLabel">Assign Specialist</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">Select User</label>
                        <select name="user_id" id="user_id" class="form-control select2-search" style="width: 100%;" required>
                            <option value="">Search and select a user...</option>
                            @foreach(App\User::orderBy('first_name')->orderBy('last_name')->get() as $user)
                                <option value="{{ $user->id }}">
                                    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }} - {{ $user->office->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any notes about this specialist..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Specialist</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    // Show/hide custom date range based on period selection
    $('#period').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customDateRange').show();
        } else {
            $('#customDateRange').hide();
        }
    });
    
    var $select = $('#user_id');
    
    $('#addSpecialistModal').on('shown.bs.modal', function() {
        if (typeof $.fn.select2 !== 'undefined' && !$select.hasClass('select2-hidden-accessible')) {
            $select.select2({
                placeholder: 'Search and select a user...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addSpecialistModal .modal-body')
            });
        }
    });
    
    $('#addSpecialistModal').on('hidden.bs.modal', function() {
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
    });
    
    $('.select2-search').select2({
        placeholder: 'Search...',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection

@extends('layouts.master')

@section('title')
    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email }} — Specialist Profile
@endsection

@section('content')
@php
    $categories     = \App\Models\RecoveryCase::CATEGORIES;
    $specialistName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email;
    $months         = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
                       7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
@endphp

{{-- Page Header --}}
<div class="row">
    <div class="col-sm-8">
        <h4 style="margin:0 0 4px">
            <i class="fa fa-user"></i> {{ $specialistName }}
        </h4>
        <small class="text-muted">{{ $user->email }}</small>
    </div>
    <div class="col-sm-4 text-right">
        <a href="{{ url('recovery/specialist/data') }}" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> Back to Specialists
        </a>
    </div>
</div>
<br>

{{-- Period filter --}}
<div class="box box-default">
    <div class="box-body" style="padding:10px 15px">
        <form method="GET" class="form-inline">
            <div class="form-group">
                <label style="margin-right:8px">Period:</label>
                <select name="period" class="form-control input-sm" onchange="this.form.submit()">
                    <option value="month"  {{ $period === 'month'   ? 'selected' : '' }}>This Month</option>
                    <option value="quarter"{{ $period === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="year"   {{ $period === 'year'    ? 'selected' : '' }}>This Year</option>
                    <option value="all"    {{ $period === 'all'     ? 'selected' : '' }}>All Time</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="row">

{{-- LEFT: Cases + Activity --}}
<div class="col-md-8">

    {{-- Assigned Cases --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-folder-open"></i> Assigned Cases</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            <table class="table table-hover table-striped" style="margin-bottom:0">
                <thead>
                    <tr>
                        <th>Case #</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Outstanding</th>
                        <th>Recovered</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cases as $case)
                @php
                    $clientName = ($case->client->client_type ?? '') === 'business'
                        ? ($case->client->full_name ?? '—')
                        : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—');
                    $catMap = [
                        'cross_branch'=>['Cross-Branch','label-primary'],
                        'escalated'   =>['Escalated',   'label-warning'],
                        'dormant'     =>['Dormant',      'label-default'],
                        'legal'       =>['Legal',        'label-danger'],
                        'skip_trace'  =>['Skip Trace',   'label-success'],
                    ];
                    [$catLabel,$catClass] = $catMap[$case->category] ?? [$case->category,'label-default'];
                    $stMap = [
                        'open'                 =>['Open',          'label-primary'],
                        'in_progress'          =>['In Progress',   'label-info'],
                        'pending_legal'        =>['Pending Legal', 'label-warning'],
                        'pending_payment'      =>['Pending Payment','label-warning'],
                        'resolved_paid'        =>['Resolved',      'label-success'],
                        'resolved_written_off' =>['Written Off',   'label-default'],
                        'closed'               =>['Closed',        'label-default'],
                    ];
                    [$stLabel,$stClass] = $stMap[$case->status] ?? [ucwords(str_replace('_',' ',$case->status)),'label-default'];
                @endphp
                <tr>
                    <td><small>{{ $case->case_number }}</small></td>
                    <td>{{ $clientName }}</td>
                    <td><span class="label {{ $catClass }}">{{ $catLabel }}</span></td>
                    <td>K {{ number_format($case->loan_outstanding_amount, 2) }}</td>
                    <td>K {{ number_format($case->amount_recovered, 2) }}</td>
                    <td><span class="label {{ $stClass }}">{{ $stLabel }}</span></td>
                    <td>
                        <a href="{{ url('recovery/case/' . $case->id . '/show') }}"
                           class="btn btn-xs btn-default">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding:24px">
                        No cases assigned for this period.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($cases->hasPages())
        <div class="box-footer">
            {{ $cases->appends(['period' => $period])->links() }}
        </div>
        @endif
    </div>

    {{-- Recent Activity --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list-ul"></i> Recent Activity</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            @forelse($activityLog as $act)
            @php
                $actClientName = ($act->recoveryCase->client->client_type ?? '') === 'business'
                    ? ($act->recoveryCase->client->full_name ?? '—')
                    : (trim(($act->recoveryCase->client->first_name ?? '') . ' ' . ($act->recoveryCase->client->last_name ?? '')) ?: '—');
            @endphp
            <div class="callout callout-info" style="margin:0;border-radius:0;border-left:3px solid #00c0ef">
                <p style="margin:0;font-size:13px">{{ $act->description }}</p>
                <small class="text-muted">
                    {{ $act->created_at->diffForHumans() }}
                    &nbsp;·&nbsp;
                    <a href="{{ url('recovery/case/' . $act->recovery_case_id . '/show') }}">
                        {{ $act->recoveryCase->case_number ?? '—' }}
                    </a>
                    &nbsp;·&nbsp; {{ $actClientName }}
                </small>
            </div>
            @empty
            <div class="text-center text-muted" style="padding:24px">No recent activity.</div>
            @endforelse
        </div>
    </div>

</div>{{-- /.col-md-8 --}}

{{-- RIGHT: Targets --}}
<div class="col-md-4">

    {{-- Set Target --}}
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bullseye"></i> Set Target</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <form method="POST" action="{{ url('recovery/specialist/' . $user->id . '/target/store') }}">
                @csrf
                <div class="form-group">
                    <label>Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-control input-sm" required>
                        @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Month <span class="text-danger">*</span></label>
                    <select name="month" class="form-control input-sm" required>
                        @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control input-sm"
                           value="{{ now()->year }}" min="2020" required>
                </div>
                <div class="form-group">
                    <label>Target Amount (K) <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon">K</span>
                        <input type="number" name="target_amount" class="form-control"
                               step="0.01" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Target Cases <span class="text-danger">*</span></label>
                    <input type="number" name="target_cases" class="form-control input-sm"
                           min="0" required>
                </div>
                <button type="submit" class="btn btn-warning btn-block">
                    <i class="fa fa-save"></i> Save Target
                </button>
            </form>
        </div>
    </div>

    {{-- Target History --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-table"></i> Target History ({{ now()->year }})</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            @if($targets->count())
            <table class="table table-condensed table-hover" style="margin-bottom:0">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Category</th>
                        <th class="text-right">Target (K)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($targets as $t)
                @php
                    $catMap2 = [
                        'cross_branch'=>['Cross-Branch','label-primary'],
                        'escalated'   =>['Escalated',   'label-warning'],
                        'dormant'     =>['Dormant',      'label-default'],
                        'legal'       =>['Legal',        'label-danger'],
                        'skip_trace'  =>['Skip Trace',   'label-success'],
                    ];
                    [$tLabel,$tClass] = $catMap2[$t->category] ?? [$t->category,'label-default'];
                @endphp
                <tr>
                    <td>{{ $months[$t->month] ?? $t->month }} {{ $t->year }}</td>
                    <td><span class="label {{ $tClass }}">{{ $tLabel }}</span></td>
                    <td class="text-right">{{ number_format($t->target_amount, 2) }}</td>
                    <td>
                        <a href="{{ url('recovery/specialist/' . $user->id . '/target/' . $t->id . '/delete') }}"
                           class="btn btn-xs btn-danger"
                           onclick="return confirm('Delete this target?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center text-muted" style="padding:24px">No targets set yet.</div>
            @endif
        </div>
    </div>

</div>{{-- /.col-md-4 --}}
</div>{{-- /.row --}}

@endsection

@extends('layouts.master')

@section('title')
    Recovery Specialists
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-users"></i> Recovery Specialists</h3>
    </div>
    <div class="box-body no-padding">
        <table class="table table-hover table-striped" style="margin-bottom:0">
            <thead>
                <tr>
                    <th>Specialist</th>
                    <th>Category</th>
                    <th>Recovered</th>
                    <th>Active Cases</th>
                    <th>Resolved</th>
                    <th>Target Progress</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($specialists as $row)
            @php
                $cat = $row['category'] ?? 'escalated';
                $catClassMap = [
                    'cross_branch' => 'label-primary',
                    'escalated'    => 'label-warning',
                    'dormant'      => 'label-default',
                    'legal'        => 'label-danger',
                    'skip_trace'   => 'label-success',
                ];
                $statusMap = [
                    'exceeding' => ['label-success', 'Exceeding'],
                    'on_track'  => ['label-success', 'On Track'],
                    'at_risk'   => ['label-warning', 'At Risk'],
                    'behind'    => ['label-danger',  'Behind'],
                ];
                [$statusClass, $statusLabel] = $statusMap[$row['status']] ?? ['label-default', '—'];
                $specialistName = trim(($row['specialist']->first_name ?? '') . ' ' . ($row['specialist']->last_name ?? '')) ?: $row['specialist']->email;
                $initials = strtoupper(substr($row['specialist']->first_name ?? 'S', 0, 1) . substr($row['specialist']->last_name ?? '', 0, 1));
            @endphp
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span class="badge bg-aqua" style="font-size:12px;min-width:34px;line-height:34px;border-radius:50%">
                            {{ $initials }}
                        </span>
                        <div>
                            <div style="font-weight:600">{{ $specialistName }}</div>
                            <small class="text-muted">{{ $row['specialist']->email }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="label {{ $catClassMap[$cat] ?? 'label-default' }}">
                        {{ $categories[$cat] ?? $cat }}
                    </span>
                </td>
                <td><strong>K {{ number_format($row['total_recovered'], 0) }}</strong></td>
                <td>
                    <span class="badge bg-aqua">{{ $row['active_cases'] }}</span>
                </td>
                <td>
                    <span class="badge bg-green">{{ $row['resolved_cases'] }}</span>
                </td>
                <td style="min-width:160px">
                    <div class="progress progress-xs" style="margin-bottom:4px">
                        <div class="progress-bar progress-bar-aqua"
                             style="width:{{ min($row['target_pct'], 100) }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ $row['target_pct'] }}% of K {{ number_format($row['target_amount'], 0) }}
                    </small>
                </td>
                <td>
                    <span class="label {{ $statusClass }}">{{ $statusLabel }}</span>
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
                <td colspan="8" class="text-center text-muted" style="padding:48px">
                    No specialist data found.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

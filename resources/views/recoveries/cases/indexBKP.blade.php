@extends('layouts.master')

@section('title')
  Recovery Cases
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp


{{-- Filters --}}
<div class="box box-default">
  <div class="box-body">
    <form method="GET" action="{{ url('recovery/case/data') }}"
          style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
      <div style="flex:2;min-width:200px">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Search client name, case #…">
      </div>
      <div style="flex:1;min-width:140px">
        <select name="status" class="form-control">
          <option value="">All Statuses</option>
          @foreach(['runaway_pending_confirmation' => 'Pending Confirmation',
            'runaway_active_recovery'         => 'Active Recovery',
            'recovered_runaway'     => 'Recovered (Runaway)',
            'escalated_handover'    => 'Handover',
            'escalated_in_review'   => 'In Review',
            'escalated_active_recovery'       => 'Active Recovery',
            'recovered_post_escalation'       => 'Recovered (Escalated)',
            'dormant_for_revival'   => 'For Revival',
            'recovery_revived'      => 'Revived',
            'pre_litigation_review' => 'Pre-Litigation Review',
            'legal_filed' => 'Legal Filed',
            'legal_active'=> 'Legal Active',
            'legal_judgment_won'    => 'Judgment Won',
            'recovered_legal'       => 'Recovered (Legal)',
            'skip_trace_required'   => 'Trace Required',
            'skip_trace_digital_review'       => 'Digital Review',
            'skip_trace_contact_reengagement' => 'Re-engagement',
            'skip_trace_field_intel_active'   => 'Field Intel Active',
            'located_for_recovery'  => 'Located',
            'closed'      => 'Closed',
            'written_off' => 'Written Off'] as $stVal => $stName)
            <option value="{{ $stVal }}" {{ request('status') === $stVal ? 'selected' : '' }}>
              {{ $stName }}
            </option>
          @endforeach
        </select>
      </div>
      <div style="flex:1;min-width:140px">
        <select name="specialist_id" class="form-control select2-search" style="width: 100%;">
        <option value="">All Specialists</option>
        @php $specialists = \App\Models\User::orderBy('first_name')->get(); @endphp
        @foreach($specialists as $sp)
          <option value="{{ $sp->id }}" {{ request('specialist_id') == $sp->id ? 'selected' : '' }}>
            {{ trim(($sp->first_name ?? '') . ' ' . ($sp->last_name ?? '')) ?: $sp->email }}
          </option>
        @endforeach
      </select>
      </div>
      <button type="submit" class="btn btn-default">Filter</button>
      <a href="{{ url('recovery/case/data') }}" class="btn btn-default">Clear</a>
    </form>
  </div>
</div>

{{-- Category tabs --}}
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
  <a href="{{ url('recovery/case/data') }}"
     class="btn btn-sm {{ !request()->is('recovery/case/*') || request()->is('recovery/case/data') ? 'btn-primary' : 'btn-default' }}">
    All Cases
  </a>
  @foreach($categories as $slug => $label)
    <a href="{{ url('recovery/case/' . $slug) }}"
       class="btn btn-sm {{ request()->is('recovery/case/' . $slug) ? 'btn-primary' : 'btn-default' }}">
      {{ $label }}
      <span class="badge">{{ $categoryCounts[$slug] ?? 0 }}</span>
    </a>
  @endforeach
  <a href="{{ url('recovery/case/resolved') }}"
     class="btn btn-sm {{ request()->is('recovery/case/resolved') ? 'btn-primary' : 'btn-default' }}">
    Resolved
  </a>
</div>

{{-- Table --}}
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Cases ({{ $cases->total() }})</h3>
    <div class="box-tools">
      @if(Sentinel::hasAccess('recoveries.create'))
        <a href="{{ url('recovery/case/create') }}" class="btn btn-primary btn-sm">
          <i class="fa fa-plus"></i> New Case
        </a>
      @endif
    </div>
  </div>
  <div class="box-body no-padding">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>Case #</th>
          <th>Client</th>
          <th>Category</th>
          <th>Outstanding</th>
          <th>Recovered</th>
          <th>Status</th>
          <th>Specialist</th>
          <th>Branch</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($cases as $case)
          <tr>
            <td>
              <a href="{{ url('recovery/case/' . $case->id . '/show') }}">
                {{ $case->case_number }}
              </a>
            </td>
            <td>
              <strong>{{ ($case->client->client_type ?? '') === 'business' ? ($case->client->full_name ?? '—') : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—') }}</strong>
            </td>
            <td>
              <span class="label label-default">
                {{ $categories[$case->category] ?? ucwords(str_replace('_',' ',$case->category)) }}
              </span>
            </td>
            <td>{{ number_format($case->loan_outstanding_amount, 2) }}</td>
            <td>{{ number_format($case->amount_recovered, 2) }}</td>
            <td>
              @php
                $statusMap = [
                  'runaway_pending_confirmation'    => ['Pending Confirmation', 'label-warning'],
                  'runaway_active_recovery'         => ['Active Recovery',      'label-primary'],
                  'recovered_runaway'               => ['Recovered',            'label-success'],
                  'escalated_handover'              => ['Handover',             'label-default'],
                  'escalated_in_review'             => ['In Review',            'label-info'],
                  'escalated_active_recovery'       => ['Active Recovery',      'label-primary'],
                  'recovered_post_escalation'       => ['Recovered',            'label-success'],
                  'dormant_for_revival'             => ['For Revival',          'label-warning'],
                  'recovery_revived'                => ['Revived',              'label-success'],
                  'pre_litigation_review'           => ['Pre-Litigation',       'label-warning'],
                  'legal_filed'                     => ['Legal Filed',          'label-danger'],
                  'legal_active'                    => ['Legal Active',         'label-danger'],
                  'legal_judgment_won'              => ['Judgment Won',         'label-info'],
                  'recovered_legal'                 => ['Recovered',            'label-success'],
                  'skip_trace_required'             => ['Trace Required',       'label-warning'],
                  'skip_trace_digital_review'       => ['Digital Review',       'label-info'],
                  'skip_trace_contact_reengagement' => ['Re-engagement',        'label-primary'],
                  'skip_trace_field_intel_active'   => ['Field Intel',          'label-primary'],
                  'located_for_recovery'            => ['Located',              'label-info'],
                  'closed'                          => ['Closed',               'label-default'],
                  'written_off'                     => ['Written Off',          'label-default'],
                ];
                [$stLabel, $stClass] = $statusMap[$case->status] ?? [ucwords(str_replace('_',' ',$case->status)), 'label-default'];
              @endphp
              <span class="label {{ $stClass }}">{{ $stLabel }}</span>
            </td>
            <td>{{ $case->assignedSpecialist ? (trim(($case->assignedSpecialist->first_name ?? '') . ' ' . ($case->assignedSpecialist->last_name ?? '')) ?: '—') : '—' }}</td>
            <td>{{ $case->originBranch ? $case->originBranch->name : '—' }}</td>
            <td>
              <a href="{{ url('recovery/case/' . $case->id . '/show') }}" class="btn btn-xs btn-default">View</a>
              <a href="{{ url('recovery/case/' . $case->id . '/edit') }}" class="btn btn-xs btn-default">Edit</a>
              <form action="{{ url('recovery/case/' . $case->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this case?')">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center" style="padding:30px;color:#999">
              No cases found.
              @if(Sentinel::hasAccess('recoveries.create'))
                <a href="{{ url('recovery/case/create') }}">Create one?</a>
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($cases->hasPages())
    <div class="box-footer">{{ $cases->links() }}</div>
  @endif
</div>

<script>
  $(document).ready(function() {
      $('.select2-search').select2({
          placeholder: 'Search...',
          allowClear: true,
          width: '100%'
      });
  });
</script>
@endsection

@extends('layouts.master')

@section('title')
  Recovery Cases
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="box box-default">
  <div class="box-body">
    <form method="GET" action="{{ url('recovery/case/data') }}"
          style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
      <div style="flex:2;min-width:200px">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Search client name, case #…">
      </div>
      <button type="submit" class="btn btn-default">Filter</button>
      <a href="{{ url('recovery/case/data') }}" class="btn btn-default">Clear</a>
    </form>
  </div>
</div>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Recov Cases</h3>
    <div class="box-tools">
      <a href="{{ url('recovery/case/create') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> New Case
      </a>
    </div>
  </div>
  <div class="box-body no-padding">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>Case #</th>
          <th>Client</th>
          <th>Categ</th>
          <th>Outstanding</th>
          <th>Recovered</th>
          <th>Status</th>
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
            <td>{{ ($case->client->client_type ?? '') === 'business' ? ($case->client->full_name ?? '—') : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—') }}</td>
            <td>{{ $categories[$case->category] ?? ucwords(str_replace('_',' ',$case->category)) }}</td>
            <td>{{ number_format($case->loan_outstanding_amount, 2) }}</td>
            <td>{{ number_format($case->amount_recovered, 2) }}</td>
            <td>{{ $case->status }}</td>
            <td>
              <a href="{{ url('recovery/case/' . $case->id . '/show') }}" class="btn btn-xs btn-default">View</a>
              <a href="{{ url('recovery/case/' . $case->id . '/edit') }}" class="btn btn-xs btn-default">Edit</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center" style="padding:30px;color:#999">
              No cases found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@extends('layouts.master')

@section('title')
    Risk Detection System
@endsection

@section('content')
{{-- Filters --}}
<div class="box box-default">
  <div class="box-body">
    <form method="GET" action="{{ route('audits.index') }}"
          style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
      <div style="flex:2;min-width:200px">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Search name, email...">
      </div>
      <div style="flex:1;min-width:140px">
        <select name="status" class="form-control">
          <option value="">All Statuses</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-default">Filter</button>
      <a href="{{ route('audits.index') }}" class="btn btn-default">Clear</a>
    </form>
  </div>
</div>

{{-- Table --}}
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Users ({{ $users->total() }})</h3>
    <div class="box-tools">
        <a href="#" class="btn btn-primary btn-sm">
          <i class="fa fa-plus"></i> New Case
        </a>
    </div>
  </div>
  <div class="box-body no-padding">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr class="user-row" data-user-id="{{ $user->id }}" style="cursor: pointer;">
            <td>{{ $user->id }}</td>
            <td>
              <strong>{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email }}</strong>
            </td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone ?: '—' }}</td>
            <td>
              <span class="label {{ $user->status === 'active' ? 'label-success' : 'label-default' }}">
                {{ ucfirst($user->status ?? 'inactive') }}
              </span>
            </td>
            <td>
              <a href="{{ route('audits.user', $user->id) }}" class="btn btn-xs btn-default">View</a>
            </td>
          </tr>
          <tr id="audit-{{ $user->id }}" class="audit-row" style="display: none;">
            <td colspan="6">
              <div id="audit-content-{{ $user->id }}" class="audit-timeline">
                <div class="text-center">
                  <i class="fa fa-spinner fa-spin"></i> Loading audit timeline...
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center" style="padding:30px;color:#999">
              No users found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
    <div class="box-footer">{{ $users->links() }}</div>
  @endif
</div>

@endsection

@section('styles')
<style>
.audit-timeline {
    margin: 10px 0;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #ddd;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    background: #007bff;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #fff;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-left: 3px solid #007bff;
}

.timeline-content h5 {
    margin-top: 0;
    color: #007bff;
}

.user-row:hover {
    background-color: #f5f5f5 !important;
}
</style>
@endsection

@section('footer-scripts')

<script src="{{ asset('js/audit-timeline.js') }}"></script>
@endsection
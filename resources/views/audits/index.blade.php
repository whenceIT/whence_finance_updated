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
      <div style="flex:1;min-width:140px">
        <select name="role_id" class="form-control">
          <option value="">All Roles</option>
          @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
          @endforeach
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
          <th>Office</th>
          <th>District</th>
          <th>Province</th>
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
            <td>{{ $user->office->name ?? 'N/A' }}</td>
            <td>{{ $user->office->district->name ?? 'N/A' }}</td>
            <td>{{ $user->office->province->name ?? 'N/A' }}</td>
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
            <td colspan="9">
              <div id="audit-content-{{ $user->id }}" class="audit-timeline">
                <div class="text-center">
                  <i class="fa fa-spinner fa-spin"></i> Loading audit timeline...
                </div>
                <div class="text-center" style="margin-top: 10px;">
                  <button class="btn btn-sm btn-default" onclick="fetchAuditTimeline({{ $user->id }})">
                    <i class="fa fa-refresh"></i> Refresh Timeline
                  </button>
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
    margin: 15px 0;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
}

.audit-timeline-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.audit-timeline-wrapper .timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 12px 18px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    border: 1px solid #dee2e6;
}

.audit-timeline-wrapper .timeline-header span {
    font-size: 13px;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 8px;
}

.audit-timeline-wrapper .refresh-audit-btn {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #fff !important;
    border: none !important;
    border-radius: 20px !important;
    padding: 8px 16px !important;
    font-size: 12px !important;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.audit-timeline-wrapper .refresh-audit-btn:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}

.audit-timeline-wrapper .timeline {
    position: relative;
    padding-left: 50px;
    padding-top: 10px;
}

.audit-timeline-wrapper .timeline-line {
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #007bff 0%, #764ba2 100%);
    border-radius: 2px;
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
}

.audit-timeline-wrapper .date-group {
    margin-bottom: 35px;
}

.audit-timeline-wrapper .date-header {
    font-weight: 600;
    font-size: 13px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    display: inline-block;
    color: #fff !important;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.audit-timeline-wrapper .timeline-item {
    position: relative;
    margin-bottom: 25px;
    padding-left: 20px;
}

.audit-timeline-wrapper .timeline-marker {
    position: absolute;
    left: -38px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 50%;
    border: 4px solid #fff;
    box-shadow: 0 3px 10px rgba(0, 123, 255, 0.5);
    z-index: 1;
}

.audit-timeline-wrapper .timeline-content {
    background: #fff;
    padding: 20px 25px;
    border-radius: 14px;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.audit-timeline-wrapper .timeline-content:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0, 123, 255, 0.15);
    border-color: #007bff;
}

.audit-timeline-wrapper .timeline-event {
    font-size: 16px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.audit-timeline-wrapper .timeline-event::before {
    content: '\f046';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    color: #007bff;
    font-size: 14px;
}

.audit-timeline-wrapper .timeline-meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.audit-timeline-wrapper .timeline-time {
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: #f8f9fa;
    border-radius: 20px;
}

.audit-timeline-wrapper .timeline-time.after-hours {
    color: #dc3545;
    background: #fff5f5;
    font-weight: 600;
}

.audit-timeline-wrapper .timeline-time.after-hours i {
    color: #dc3545;
}

.audit-timeline-wrapper .timeline-btn {
    display: inline-flex !important;
    align-items: center;
    gap: 8px !important;
    padding: 10px 20px !important;
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    color: #fff !important;
    text-decoration: none !important;
    border-radius: 25px !important;
    font-size: 13px !important;
    font-weight: 500;
    transition: all 0.3s ease;
}

.audit-timeline-wrapper .timeline-btn:hover {
    background: linear-gradient(135deg, #138496 0%, #117a8b 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
    color: #fff !important;
}

.user-row:hover {
    background-color: #f5f5f5 !important;
}

.audit-row td {
    padding: 20px !important;
    background: #fafafa;
}
</style>
@endsection

@section('footer-scripts')

<script src="{{ asset('js/audit-timeline.js') }}"></script>
@endsection
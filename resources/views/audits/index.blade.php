@extends('layouts.master')

@section('title')
    Users List
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
      @if(Sentinel::hasAccess('users.create'))
        <a href="{{ url('user/create') }}" class="btn btn-primary btn-sm">
          <i class="fa fa-plus"></i> New User
        </a>
      @endif
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
          <tr class="user-row" data-user-id="{{ $user->id }}" style="cursor:pointer;">
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
              <a href="{{ url('user/' . $user->id . '/show') }}" class="btn btn-xs btn-default" onclick="event.stopPropagation();">View</a>
            </td>
          </tr>
          <tr class="audit-row" id="audit-{{ $user->id }}" style="display:none;">
            <td colspan="6">
              <div class="audit-details" style="padding:10px;background:#f9f9f9;">
                <h5>Audit Logs for {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email }}</h5>
                <div class="audit-content" id="audit-content-{{ $user->id }}">
                  Loading...
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

<script>
$(document).ready(function() {
    $('.user-row').on('click', function() {
        var userId = $(this).data('user-id');
        var auditRow = $('#audit-' + userId);
        var auditContent = $('#audit-content-' + userId);

        if (auditRow.is(':visible')) {
            auditRow.hide();
        } else {
            if (auditContent.html() === 'Loading...') {
                // Fetch audits
                $.ajax({
                    url: '{{ route("audits.user", ":userId") }}'.replace(':userId', userId),
                    dataType: 'json',
                    success: function(data) {
                        if (data.data && data.data.length > 0) {
                            var html = '<table class="table table-sm"><thead><tr><th>Date</th><th>Event</th><th>Auditable</th><th>Details</th></tr></thead><tbody>';
                            data.data.forEach(function(audit) {
                                html += '<tr>';
                                html += '<td>' + audit.created_at + '</td>';
                                html += '<td>' + audit.event + '</td>';
                                html += '<td>' + (audit.auditable ? audit.auditable_type + ' #' + audit.auditable_id : 'N/A') + '</td>';
                                html += '<td>' + JSON.stringify(audit.new_values) + '</td>';
                                html += '</tr>';
                            });
                            html += '</tbody></table>';
                            auditContent.html(html);
                        } else {
                            auditContent.html('<p>No audit logs found for this user.</p>');
                        }
                    },
                    error: function() {
                        auditContent.html('<p>Error loading audit logs.</p>');
                    }
                });
            }
            auditRow.show();
        }
    });
});
</script>
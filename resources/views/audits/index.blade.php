@extends('layouts.master')

@section('title')
    Audit Logs
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Logs</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('audits.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="auditable_type">Auditable Type</label>
                                <select name="auditable_type" class="form-control">
                                    <option value="">All</option>
                                    @foreach($auditableTypes as $type)
                                        <option value="{{ $type }}" {{ $filters['auditable_type'] ?? '' == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="event">Event</label>
                                <select name="event" class="form-control">
                                    <option value="">All</option>
                                    @foreach($auditEvents as $event)
                                        <option value="{{ $event }}" {{ $filters['event'] ?? '' == $event ? 'selected' : '' }}>{{ $event }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="user_id">User ID</label>
                                <input type="text" name="user_id" class="form-control" value="{{ $filters['user_id'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label for="auditable_id">Auditable ID</label>
                                <input type="text" name="auditable_id" class="form-control" value="{{ $filters['auditable_id'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label for="created_at_from">From Date</label>
                                <input type="date" name="created_at_from" class="form-control" value="{{ $filters['created_at_from'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label for="created_at_to">To Date</label>
                                <input type="date" name="created_at_to" class="form-control" value="{{ $filters['created_at_to'] ?? '' }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Filter</button>
                        <a href="{{ route('audits.index') }}" class="btn btn-secondary mt-2">Clear</a>
                    </form>

                    <!-- Audits Table -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Auditable Type</th>
                                <th>Auditable ID</th>
                                <th>Event</th>
                                <th>User</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audits as $audit)
                                <tr>
                                    <td>{{ $audit->id }}</td>
                                    <td>{{ $audit->auditable_type }}</td>
                                    <td>{{ $audit->auditable_id }}</td>
                                    <td>{{ $audit->event }}</td>
                                    <td>{{ $audit->user_id ? $audit->user->name ?? 'N/A' : 'System' }}</td>
                                    <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('audits.show', $audit->id) }}" class="btn btn-info btn-sm">View</a>
                                        <form method="POST" action="{{ route('audits.destroy', $audit->id) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    {{ $audits->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.master')

@section('title')
    Audit Detail
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Detail #{{ $audit->id }}</h3>
                    <a href="{{ route('audits.index') }}" class="btn btn-secondary float-right">Back to List</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{ $audit->id }}</td>
                        </tr>
                        <tr>
                            <th>Auditable Type</th>
                            <td>{{ $audit->auditable_type }}</td>
                        </tr>
                        <tr>
                            <th>Auditable ID</th>
                            <td>{{ $audit->auditable_id }}</td>
                        </tr>
                        <tr>
                            <th>Event</th>
                            <td>{{ $audit->event }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $audit->user_id ? $audit->user->name ?? 'N/A' : 'System' }}</td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <td>{{ $audit->url }}</td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td>{{ $audit->ip_address }}</td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td>{{ $audit->user_agent }}</td>
                        </tr>
                        <tr>
                            <th>Tags</th>
                            <td>{{ $audit->tags ? implode(', ', $audit->tags) : 'None' }}</td>
                        </tr>
                        <tr>
                            <th>Old Values</th>
                            <td><pre>{{ json_encode($audit->old_values, JSON_PRETTY_PRINT) }}</pre></td>
                        </tr>
                        <tr>
                            <th>New Values</th>
                            <td><pre>{{ json_encode($audit->new_values, JSON_PRETTY_PRINT) }}</pre></td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $audit->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
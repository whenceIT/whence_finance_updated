@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>
        Audit Trail
        <small>Loan #{{ $loan->id }}</small>
    </h1>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Full Audit Trail</h3>
                    <a href="{{ route('motor-vehicle-loans.show', $loan->id) }}" class="btn btn-default btn-xs pull-right">
                        <i class="fa fa-arrow-left"></i> Back to Loan
                    </a>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                                <th>User</th>
                                <th>Branch</th>
                                <th>District</th>
                                <th>Province</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditLogs as $log)
                            <tr>
                                <td>{{ $log->actioned_at->format('Y-m-d H:i') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
                                <td>{{ $log->old_value ?? '-' }}</td>
                                <td>{{ $log->new_value ?? '-' }}</td>
                                <td>{{ $log->user->first_name ?? 'N/A' }} {{ $log->user->last_name ?? '' }}</td>
                                <td>{{ $log->branch->name ?? 'N/A' }}</td>
                                <td>{{ $log->district->name ?? 'N/A' }}</td>
                                <td>{{ $log->province->name ?? 'N/A' }}</td>
                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No audit logs found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="box-footer clearfix">
                    {{ $auditLogs->links() }}
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

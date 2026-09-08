@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Roll Calls</h1>
</section>

<section class="content">

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">All Roll Call Records</h3>
        <div class="box-tools pull-right">
            <a href="{{ url('vehicles') }}" class="btn btn-info btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Vehicles
            </a>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Vehicle</th>
                    <th>Officer</th>
                    <th>Location Confirmed</th>
                    <th>Vehicle Present</th>
                    <th>Status</th>
                    <th>Mileage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rollCalls as $rollCall)
                <tr>
                    <td>{{ $rollCall->verification_date }}</td>
                    <td>
                        {{ $rollCall->vehicle->make ?? 'N/A' }} {{ $rollCall->vehicle->model ?? '' }}
                        <br><small>{{ $rollCall->vehicle->registration_number ?? '' }}</small>
                    </td>
                    <td>{{ $rollCall->officer->name ?? 'N/A' }}</td>
                    <td>
                        @if($rollCall->location_confirmed)
                            <span class="label label-success">Yes</span>
                        @else
                            <span class="label label-danger">No</span>
                        @endif
                    </td>
                    <td>
                        @if($rollCall->vehicle_present)
                            <span class="label label-success">Yes</span>
                        @else
                            <span class="label label-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <span class="label label-{{ $rollCall->status == 'verified' ? 'success' : ($rollCall->status == 'missing' ? 'danger' : 'warning') }}">
                            {{ ucfirst($rollCall->status) }}
                        </span>
                    </td>
                    <td>{{ $rollCall->current_mileage ?? '-' }}</td>
                    <td>
                        <a href="{{ url('vehicles/'.$rollCall->vehicle_id.'/roll-calls') }}" class="btn btn-xs btn-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">No roll calls recorded</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $rollCalls->links() }}
    </div>
</div>

</section>

@endsection

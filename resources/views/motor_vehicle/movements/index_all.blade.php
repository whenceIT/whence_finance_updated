@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Movements</h1>
</section>

<section class="content">

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">All Vehicle Movements</h3>
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
                    <th>Previous Location</th>
                    <th>New Location</th>
                    <th>Authorized By</th>
                    <th>Moved By</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date }}</td>
                    <td>
                        {{ $movement->vehicle->make ?? 'N/A' }} {{ $movement->vehicle->model ?? '' }}
                        <br><small>{{ $movement->vehicle->registration_number ?? '' }}</small>
                    </td>
                    <td>{{ $movement->previous_location }}</td>
                    <td>{{ $movement->new_location }}</td>
                    <td>{{ $movement->authorizedBy->name ?? 'N/A' }}</td>
                    <td>{{ $movement->movedBy->name ?? 'N/A' }}</td>
                    <td>{{ $movement->reason ?? '-' }}</td>
                    <td>
                        <a href="{{ url('vehicles/'.$movement->vehicle_id.'/movements') }}" class="btn btn-xs btn-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">No movements recorded</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $movements->links() }}
    </div>
</div>

</section>

@endsection

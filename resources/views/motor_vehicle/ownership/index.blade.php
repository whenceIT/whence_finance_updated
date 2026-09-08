@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Ownership Verification Records</h1>
</section>

<section class="content">

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">All Ownership Records</h3>
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
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Registered Owner</th>
                    <th>Seller Name</th>
                    <th>Company Name</th>
                    <th>Authorized Rep.</th>
                    <th>Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>
                        {{ $record->vehicle->make ?? 'N/A' }} {{ $record->vehicle->model ?? '' }}
                        <br><small>{{ $record->vehicle->registration_number ?? '' }}</small>
                    </td>
                    <td>{{ ucfirst($record->ownership_type) }}</td>
                    <td>{{ $record->registered_owner_name }}</td>
                    <td>{{ $record->seller_name ?? '-' }}</td>
                    <td>{{ $record->company_name ?? '-' }}</td>
                    <td>{{ $record->authorized_representative_name ?? '-' }}</td>
                    <td>
                        @if($record->verified)
                            <span class="label label-success">Yes</span>
                        @else
                            <span class="label label-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('vehicles/'.$record->vehicle_id.'/ownership-verification') }}" class="btn btn-xs btn-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">No ownership records found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $records->links() }}
    </div>
</div>

</section>

@endsection

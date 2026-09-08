@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Custody Register</h1>
</section>

<section class="content">

<div class="box">

    <div class="box-header">
        <h3 class="box-title">Custody Records</h3>
    </div>

    <div class="box-body">

        <table class="table table-bordered table-striped">

            <thead>
            <tr>
                <th>Vehicle</th>
                <th>Garage Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Storage Start Date</th>
                <th>Approved</th>
            </tr>
            </thead>

            <tbody>

            @forelse($custodies as $custody)
            <tr>
                <td>
                    @if($custody->vehicle)
                        <a href="{{ url('vehicles/'.$custody->vehicle->id) }}">{{ $custody->vehicle->registration_number }}</a>
                    @else
                        {{ optional($custody->vehicle)->registration_number ?? 'N/A' }}
                    @endif
                </td>
                <td>{{ $custody->garage_name ?? 'N/A' }}</td>
                <td>{{ $custody->garage_contact_person ?? 'N/A' }}</td>
                <td>{{ $custody->garage_contact_phone ?? 'N/A' }}</td>
                <td>{{ ucfirst($custody->status ?? 'N/A') }}</td>
                <td>{{ $custody->received_at ? \Carbon\Carbon::parse($custody->received_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                <td>
                    @if($custody->approved)
                        <span class="label label-success">Yes</span>
                    @else
                        <span class="label label-danger">No</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No custody records found.</td>
            </tr>
            @endforelse

            </tbody>

        </table>

    </div>

</div>

</section>

@endsection

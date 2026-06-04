@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Registry</h1>
</section>

<section class="content">

<div class="box">

    <div class="box-header">

        <a href="{{ url('vehicles/create') }}"
           class="btn btn-primary">

            Add Vehicle

        </a>

    </div>

    <div class="box-body">

        <table class="table table-bordered">

            <thead>
            <tr>
                <th>Code</th>
                <th>Owner</th>
                <th>Make</th>
                <th>Model</th>
                <th>Registration</th>
                <th>Market Value</th>
                <th></th>
            </tr>
            </thead>

            <tbody>

            @foreach($vehicles as $vehicle)

            <tr>

                <td>{{ $vehicle->vehicle_code }}</td>

                <td>
                    {{ optional($vehicle->client)->first_name }} {{ optional($vehicle->client)->last_name }}
                </td>

                <td>{{ $vehicle->make }}</td>

                <td>{{ $vehicle->model }}</td>

                <td>{{ $vehicle->registration_number }}</td>

                <td>
                    {{ number_format($vehicle->market_value,2) }}
                </td>
<td>

    <a href="{{ url('vehicles/'.$vehicle->id) }}"
       class="btn btn-xs btn-info">
        View
    </a>

    <a href="{{ url('vehicles/'.$vehicle->id.'/edit') }}"
       class="btn btn-xs btn-primary">
        Edit
    </a>

</td>

            </tr>

            @endforeach

            </tbody>

        </table>

        {{ $vehicles->links() }}

    </div>

</div>

</section>

@endsection
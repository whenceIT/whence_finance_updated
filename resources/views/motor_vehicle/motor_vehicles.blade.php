@extends('layouts.master')
@section('title')
    Motor Vehicles
@endsection 
@section('content')  
    <div class="row">

        <div class="col-md-12">

            <div class="box box-info">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        Vehicle Registry
                    </h3>

                </div>

                <div class="box-body table-responsive">

                <div class="box-body">

<form method="GET">

<div class="row">

    <div class="col-md-3">

        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search vehicle..."
               value="{{ request('search') }}">

    </div>

    <div class="col-md-2">

        <select name="status" class="form-control">

            <option value="">All Statuses</option>

            <option value="available"
                {{ request('status')=='available' ? 'selected' : '' }}>
                Available
            </option>

            <option value="pledged"
                {{ request('status')=='pledged' ? 'selected' : '' }}>
                Pledged
            </option>

            <option value="repossessed"
                {{ request('status')=='repossessed' ? 'selected' : '' }}>
                Repossessed
            </option>

        </select>

    </div>

    <div class="col-md-2">

        <input type="date"
               name="date"
               class="form-control"
               value="{{ request('date') }}">

    </div>

    <div class="col-md-3">

        <select name="office" class="form-control">

            <option value="">All Branches</option>

            @foreach($offices as $office)

                <option value="{{ $office->id }}"
                    {{ request('office')==$office->id ? 'selected' : '' }}>

                    {{ $office->name }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-2">

        <button class="btn btn-info">
            <i class="fa fa-search"></i> Search
        </button>

        <a href="{{ url()->current() }}"
           class="btn btn-default">
            Reset
        </a>

    </div>

</div>

</form>

</div>

                    <table class="table table-bordered table-striped">

                        <thead>

                        <tr>
                            <th>ID</th>
                            <th>Vehicle Code</th>
                            <th>Owner</th>
                            <th>Registration</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Market Value</th>
                            <th>Status</th>

                        </tr>

                        </thead>

                        <tbody>

                        @forelse($vehicles as $vehicle)

                            <tr>
                                
                            <td>
                                 <a href="{{ url('vehicles/'.$vehicle->id) }}">
    {{$vehicle->id}}
    </a>
                            </td>

                                <td>
                                   
                                        {{ $vehicle->vehicle_code }}
                                
                                </td>

                                <td>
                                    {{ optional($vehicle->client)->first_name }}
                                    {{ optional($vehicle->client)->last_name }}
                                </td>

                                <td>
                                    {{ $vehicle->registration_number }}
                                </td>

                                <td>
                                    {{ $vehicle->make }}
                                </td>

                                <td>
                                    {{ $vehicle->model }}
                                </td>

                                <td>
                                    K{{ number_format($vehicle->market_value,2) }}
                                </td>

                                <td>

                                    @if($vehicle->status == 'available')
                                        <span class="label label-success">
                                            Available
                                        </span>
                                    @elseif($vehicle->status == 'pledged')
                                        <span class="label label-warning">
                                            Pledged
                                        </span>
                                    @elseif($vehicle->status == 'repossessed')
                                        <span class="label label-danger">
                                            Repossessed
                                        </span>
                                    @else
                                        <span class="label label-default">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center">
                                    No vehicles found.
                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection 
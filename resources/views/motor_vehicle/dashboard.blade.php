@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>
        Motor Vehicle Loans Dashboard
        <small>Portfolio Overview</small>
    </h1>
</section>

<section class="content">

    {{-- KPI CARDS --}}
    <div class="row">

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ number_format($totalVehicles) }}</h3>
                    <p>Total Vehicles</p>
                </div>
                <div class="icon">
                    <i class="fa fa-car"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ number_format($activeLoans) }}</h3>
                    <p>Active Vehicle Loans</p>
                </div>
                <div class="icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>K{{ number_format($totalVehicleValue,2) }}</h3>
                    <p>Total Vehicle Value</p>
                </div>
                <div class="icon">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>K{{ number_format($activeLoansPrincipal,2) }}</h3>
                    <p>Total Given Out</p>
                </div>
                <div class="icon">
                    <i class="fa fa-warning"></i>
                </div>
            </div>
        </div>

    

    </div>

    <!-- {{-- PORTFOLIO SUMMARY + ATTENTION --}}
    <div class="row">

        <div class="col-md-8">

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        Portfolio Summary
                    </h3>
                </div>

                <div class="box-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>Total Vehicles</th>
                            <td>{{ number_format($totalVehicles) }}</td>
                        </tr>

                        <tr>
                            <th>Total Vehicle Value</th>
                            <td>K{{ number_format($totalVehicleValue,2) }}</td>
                        </tr>

                        @isset($totalForcedSaleValue)
                        <tr>
                            <th>Total Forced Sale Value</th>
                            <td>K{{ number_format($totalForcedSaleValue,2) }}</td>
                        </tr>
                        @endisset

                        @isset($outstandingBalance)
                        <tr>
                            <th>Total Outstanding Balance</th>
                            <td>K{{ number_format($outstandingBalance,2) }}</td>
                        </tr>
                        @endisset

                        @isset($amountCollected)
                        <tr>
                            <th>Total Amount Collected</th>
                            <td>K{{ number_format($amountCollected,2) }}</td>
                        </tr>
                        @endisset

                    </table>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="box box-danger">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        Vehicles Requiring Attention
                    </h3>
                </div>

                <div class="box-body">

                    <ul class="list-group">

                        <li class="list-group-item">
                            No Photos
                            <span class="badge">
                                {{ $vehiclesWithoutPhotos }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            No Inspection
                            <span class="badge">
                                {{ $vehiclesWithoutInspection }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            No Documents
                            <span class="badge">
                                {{ $vehiclesWithoutDocuments }}
                            </span>
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div> -->

    {{-- RECENT VEHICLE LOANS --}}
    <div class="row">

        <div class="col-md-12">

            <div class="box box-success">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        Motor Vehicle Loans
                    </h3>
                </div>

                <div class="box-body table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead>

                        <tr>

                            <th>ID</th>
                            <th>Client</th>
                            <th>Office</th>
                            <th>Principal</th>
                            <th>Status</th>

                        </tr>

                        </thead>

                        <tbody>

                        @forelse($recentLoans as $loan)

                            <tr>

                                <td>
                               <a href="{{ url('loan/'.$loan->id.'/show') }}">
                                               {{$loan->id}}</a>
                                </td>

                                <td>
                                    {{ optional($loan->client)->first_name }}
                                    {{ optional($loan->client)->last_name }}
                                </td>

                                    <td>
                                    {{ optional($loan->office)->name }}
                                </td>


                                <td>
                                    K{{ number_format($loan->approved_amount,2) }}
                                </td>

                                <td>

                                    @if($loan->status == 'active')
                                        <span class="label label-success">
                                            Active
                                        </span>
                                    @elseif($loan->status == 'defaulted')
                                        <span class="label label-danger">
                                            Defaulted
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center">
                                    No vehicle loans found.
                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                       <div class="text-center">
        {{ $recentLoans->links() }}
    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- VEHICLE REGISTRY --}}
    <div class="row">

        <div class="col-md-12">

            <div class="box box-info">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        Vehicle Registry
                    </h3>

                </div>

                <div class="box-body table-responsive">

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

</section>

@endsection
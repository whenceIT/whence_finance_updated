@extends('layouts.master')

@section('content')

<section class="content-header">

<h1>

Vehicle Sales

</h1>

</section>

<section class="content">

<div class="row">

    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $carsSold }}</h3>
                <p>Cars Sold</p>
            </div>
            <div class="icon">
                <i class="fa fa-car"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>K{{ number_format($totalSales,2) }}</h3>
                <p>Total Sales</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>K{{ number_format($averageSale,2) }}</h3>
                <p>Average Sale</p>
            </div>
            <div class="icon">
                <i class="fa fa-line-chart"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>K{{ number_format($highestSale,2) }}</h3>
                <p>Highest Sale</p>
            </div>
            <div class="icon">
                <i class="fa fa-trophy"></i>
            </div>
        </div>
    </div>

</div>

<div class="box">

<div class="box-header with-border">

<h3 class="box-title">

Sold Vehicles

</h3>

</div>

<div class="box-body">

<form method="GET">

<div class="row">

    <div class="col-md-2">
        <label>From</label>

        <input type="date"
               name="start_date"
               class="form-control"
               value="{{ request('start_date') }}">
    </div>

    <div class="col-md-2">
        <label>To</label>

        <input type="date"
               name="end_date"
               class="form-control"
               value="{{ request('end_date') }}">
    </div>

    <div class="col-md-2">
        <label>Registration</label>

        <input type="text"
               name="registration"
               class="form-control"
               placeholder="ABC123"
               value="{{ request('registration') }}">
    </div>

    <div class="col-md-2">
        <label>Vehicle</label>

        <input type="text"
               name="vehicle"
               class="form-control"
               placeholder="Toyota"
               value="{{ request('vehicle') }}">
    </div>

    <div class="col-md-2">
        <label>&nbsp;</label>

        <button class="btn btn-primary btn-block">

            <i class="fa fa-search"></i>

            Filter

        </button>

    </div>

    <div class="col-md-2">
        <label>&nbsp;</label>

        <a href="{{ url('vehicles/sales') }}"
           class="btn btn-default btn-block">

            Clear

        </a>

    </div>

</div>

</form>

<hr>

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>Date Sold</th>

<th>Vehicle</th>

<th>Registration</th>

<th>Owner</th>

<th>Market Value</th>

<th>Sale Value</th>

<th>Profit / Loss</th>

<th></th>

</tr>

</thead>

<tbody>

@forelse($vehicles as $vehicle)

<tr>

<td>

{{ date('d M Y', strtotime($vehicle->sold_at)) }}

</td>

<td>

{{ $vehicle->make }}

{{ $vehicle->model }}

</td>

<td>

{{ $vehicle->registration_number }}

</td>

<td>

{{ optional($vehicle->client)->first_name }}

{{ optional($vehicle->client)->last_name }}

</td>

<td>

K{{ number_format($vehicle->market_value,2) }}

</td>

<td>

<strong class="text-green">

K{{ number_format($vehicle->forced_sale_value,2) }}

</strong>

</td>

<td>

@php

$difference = $vehicle->forced_sale_value - $vehicle->market_value;

@endphp

@if($difference >= 0)

<span class="text-green">

K{{ number_format($difference,2) }}

</span>

@else

<span class="text-red">

K{{ number_format($difference,2) }}

</span>

@endif

</td>

<td>

<a
href="{{ url('vehicles/'.$vehicle->id) }}"
class="btn btn-xs btn-primary">

View

</a>

</td>

</tr>

@empty

<tr>

<td colspan="8">

No sold vehicles found.

</td>

</tr>

@endforelse

</tbody>

</table>

{{ $vehicles->links() }}

</div>

</div>

</section>

@endsection
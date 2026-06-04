@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Edit Vehicle</h1>
</section>

<section class="content">

<div class="box">

<form method="POST"
      action="{{ url('vehicles/'.$vehicle->id.'/update') }}">

    @csrf
    @method('PUT')

    <div class="box-body">

<div class="form-group">

    <label>Client</label>

    <input type="text"
           class="form-control"
           value="{{ optional($vehicle->client)->first_name }} {{ optional($vehicle->client)->last_name }}"
           readonly>

</div>
        <div class="form-group">

            <label>Make</label>

            <input type="text"
                   name="make"
                   class="form-control"
                   value="{{ $vehicle->make }}">

        </div>

        <div class="form-group">

            <label>Model</label>

            <input type="text"
                   name="model"
                   class="form-control"
                   value="{{ $vehicle->model }}">

        </div>

        <div class="form-group">

            <label>Year</label>

            <input type="number"
                   name="year"
                   class="form-control"
                   value="{{ $vehicle->year }}">

        </div>

        <div class="form-group">

            <label>Registration Number</label>

            <input type="text"
                   name="registration_number"
                   class="form-control"
                   value="{{ $vehicle->registration_number }}">

        </div>

        <div class="form-group">

            <label>Market Value</label>

            <input type="number"
                   step="0.01"
                   name="market_value"
                   class="form-control"
                   value="{{ $vehicle->market_value }}">

        </div>

        <div class="form-group">

            <label>Forced Sale Value</label>

            <input type="number"
                   step="0.01"
                   name="forced_sale_value"
                   class="form-control"
                   value="{{ $vehicle->forced_sale_value }}">

        </div>

        <button type="submit"
                class="btn btn-success">

            Update Vehicle

        </button>

        <a href="{{ url('vehicles') }}"
           class="btn btn-default">

            Cancel

        </a>

    </div>

</form>

</div>

</section>

@endsection
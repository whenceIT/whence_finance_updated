@extends('layouts.master')

@section('content')

<section class="content-header">

    <h1>
        Add Vehicle Insurance
    </h1>

</section>

<section class="content">

<div class="row">

<div class="col-md-8 col-md-offset-2">

<div class="box box-success">

<div class="box-header with-border">

<h3 class="box-title">

Insurance Information

</h3>

</div>

<form
    method="POST"
    action="{{ url('vehicles/'.$vehicle->id.'/insurance/store') }}"
>

@csrf

<div class="box-body">

<div class="alert alert-info">

    Vehicle:
    <strong>

        {{ $vehicle->make }}
        {{ $vehicle->model }}

    </strong>

    <br>

    Registration:
    <strong>

        {{ $vehicle->registration_number }}

    </strong>

</div>

<div class="form-group">

<label>

Insurer Name

</label>

<input
    type="text"
    name="insurer_name"
    class="form-control"
    required
>

</div>

<div class="form-group">

<label>

Policy Number

</label>

<input
    type="text"
    name="policy_number"
    class="form-control"
    required
>

</div>

<div class="form-group">

<label>

Start Date

</label>

<input
    type="date"
    name="start_date"
    class="form-control"
    required
>

</div>

<div class="form-group">

<label>

Expiry Date

</label>

<input
    type="date"
    name="expiry_date"
    class="form-control"
    required
>

</div>

<div class="form-group">

<label>

Insured Value

</label>

<input
    type="number"
    step="0.01"
    name="insured_value"
    class="form-control"
    required
>

</div>

</div>

<div class="box-footer">

<button
    type="submit"
    class="btn btn-success"
>

Save Insurance

</button>

<a
    href=""
    class="btn btn-default"
>

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</section>

@endsection
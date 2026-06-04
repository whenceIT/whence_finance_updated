@extends('layouts.master')

@section('content')

<section class="content-header">

<h1>Vehicle Inspection</h1>

</section>

<section class="content">

<div class="row">

<div class="col-md-8 col-md-offset-2">

<div class="box box-warning">

<form
    method="POST"
    enctype="multipart/form-data"
    action="{{ url('vehicles/'.$vehicle->id.'/inspections/store') }}"
>

@csrf

<div class="box-body">

<div class="alert alert-info">

    {{ $vehicle->make }}
    {{ $vehicle->model }}

    ({{ $vehicle->registration_number }})

</div>

<div class="form-group">

<label>Inspection Date</label>

<input
    type="date"
    name="inspection_date"
    class="form-control"
    required>

</div>

<div class="form-group">

<label>Inspector</label>

<input
    type="text"
    name="inspector"
    class="form-control"
    required>

</div>

<div class="form-group">

<label>Mileage</label>

<input
    type="number"
    name="mileage"
    class="form-control">

</div>

<div class="form-group">

<label>Condition Rating</label>

<select
    name="condition_rating"
    class="form-control">

<option>Excellent</option>
<option selected>Good</option>
<option>Fair</option>
<option>Poor</option>

</select>

</div>

<div class="form-group">

<label>Result</label>

<select
    name="result"
    class="form-control">

<option>Passed</option>
<option>Failed</option>
<option>Pending</option>

</select>

</div>

<div class="form-group">

<label>Notes</label>

<textarea
    name="notes"
    rows="5"
    class="form-control"></textarea>

</div>

<div class="form-group">

<label>Inspection Report</label>

<input
    type="file"
    name="report_file"
    class="form-control">

</div>

</div>

<div class="box-footer">

<button
    class="btn btn-warning">

    Save Inspection

</button>

</div>

</form>

</div>

</div>

</div>

</section>

@endsection
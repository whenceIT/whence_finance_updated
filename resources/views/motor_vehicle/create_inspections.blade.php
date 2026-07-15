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

<label>Inspection Type</label>

<select
    name="inspection_type"
    class="form-control"
    required>

    <option value="receipt">Vehicle Receipt</option>
    <option value="release">Vehicle Release</option>
    <option value="routine">Routine Inspection</option>

</select>

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

<label>Fuel Level</label>

<select
    name="fuel_level"
    class="form-control">

    <option value="">Select Fuel Level</option>
    <option>Empty</option>
    <option>1/4 Tank</option>
    <option>1/2 Tank</option>
    <option>3/4 Tank</option>
    <option>Full Tank</option>

</select>

</div>

<div class="form-group">

<label>Condition Rating</label>

<select
    name="condition_rating"
    class="form-control">
    
<option></option>
<option>Excellent</option>
<option>Good</option>
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

<label>Inspection Report</label>

<input
    type="file"
    name="report_file"
    class="form-control">

</div>


<div class="form-group">

<label>General Remarks</label>

<textarea
    name="notes"
    rows="5"
    class="form-control"></textarea>

</div>


<div class="form-group">

<label>Supporting Photographs</label>

<input
    type="file"
    name="photos[]"
    multiple
    accept="image/*"
    class="form-control">

<p class="help-block">

You may upload multiple images showing damages, mileage, fuel gauge, accessories, engine bay, or any other inspection evidence.

</p>

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
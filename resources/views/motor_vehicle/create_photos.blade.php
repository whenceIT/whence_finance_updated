@extends('layouts.master')

@section('content')

<section class="content-header">

    <h1>Upload Vehicle Photo</h1>

</section>

<section class="content">

<div class="row">

<div class="col-md-8 col-md-offset-2">

<div class="box box-primary">

<form
    method="POST"
    enctype="multipart/form-data"
    action="{{ url('vehicles/'.$vehicle->id.'/photos/store') }}"
>

@csrf

<div class="box-body">

<div class="alert alert-info">

    {{ $vehicle->make }}
    {{ $vehicle->model }}
    ({{ $vehicle->registration_number }})

</div>

<div class="form-group">

<label>Photo Type</label>

<select
    name="photo_type"
    class="form-control"
    required
>

<option value="">Select</option>

<option>Front View</option>
<option>Rear View</option>
<option>Left Side</option>
<option>Right Side</option>
<option>Interior</option>
<option>Dashboard</option>
<option>Engine</option>
<option>Odometer</option>
<option>Damage</option>
<option>Other</option>

</select>

</div>

<div class="form-group">

<label>Caption</label>

<input
    type="text"
    name="caption"
    class="form-control">

</div>

<div class="form-group">

<label>Select Photo</label>

<input
    type="file"
    name="photo"
    class="form-control"
    required>

</div>

</div>

<div class="box-footer">

<button
    class="btn btn-primary">

    Upload Photo

</button>

</div>

</form>

</div>

</div>

</div>

</section>

@endsection
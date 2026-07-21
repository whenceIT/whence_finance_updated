@extends('layouts.master')

@section('content')

<section class="content-header">

<h1>

Receive Vehicle Into Custody

</h1>

</section>

<section class="content">

<div class="box box-danger">

<form method="POST"
action="{{ url('vehicles/'.$vehicle->id.'/custody') }}">

{{ csrf_field() }}

<div class="box-body">

<div class="row">

<div class="col-md-6">

<div class="form-group">

<label>Date & Time Received</label>

<input
type="datetime-local"
name="received_at"
class="form-control"
required>

</div>

</div>

<div class="col-md-6">

<div class="form-group">

<label>Receiving Officer</label>

<select
name="received_by"
class="form-control">
 <option></option>
@foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach

</select>

</div>

</div>

</div>

<div class="box box-default">

<div class="box-header with-border">

<h4 class="box-title">

Garage / Storage Facility Details

</h4>

</div>

<div class="box-body">

<div class="form-group">

<label>Garage / Storage Facility Name</label>

<input
    type="text"
    name="garage_name"
    class="form-control"
    placeholder="e.g. Whence Main Yard"
    required>

</div>

<div class="form-group">

<label>Physical Location</label>

<textarea
    name="garage_location"
    class="form-control"
    rows="2"
    placeholder="Enter the physical address or description"></textarea>

</div>

<div class="row">

<div class="col-md-6">

<div class="form-group">

<label>GPS Coordinates (Optional)</label>

<input
    type="text"
    name="garage_gps"
    class="form-control"
    placeholder="-15.3875, 28.3228">

</div>

</div>

<div class="col-md-6">

<div class="form-group">

<label>Parking Bay / Slot (Optional)</label>

<input
    type="text"
    name="parking_bay"
    class="form-control"
    placeholder="e.g. Bay A12">

</div>

</div>

</div>

<div class="row">

<div class="col-md-6">

<div class="form-group">

<label>Contact Person</label>

<input
    type="text"
    name="garage_contact_person"
    class="form-control">

</div>

</div>

<div class="col-md-6">

<div class="form-group">

<label>Contact Number</label>

<input
    type="text"
    name="garage_contact_phone"
    class="form-control">

</div>

</div>

</div>

</div>

</div>

<div class="row">

<div class="col-md-6">

<div class="form-group">

<label>Keys Received</label>

<input
type="number"
name="keys_received"
class="form-control">

</div>

</div>

<div class="col-md-6">

<div class="form-group">

<label>Key Tag Numbers</label>

<input
type="text"
name="key_tag_numbers"
class="form-control">

</div>

</div>

</div>

<div class="form-group">

<label>Remarks</label>

<textarea
name="remarks"
class="form-control"></textarea>

</div>

</div>

<div class="box-footer">

<button
class="btn btn-danger">

Receive Vehicle

</button>

</div>

</form>

</div>

</section>

@endsection
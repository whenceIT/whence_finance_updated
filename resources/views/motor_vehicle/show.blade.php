@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Details</h1>
</section>

<section class="content">

<div class="box">

<div class="box-body">

<div class="row">

<div class="col-md-4">

<div class="box box-primary">

<div class="box-header with-border">
    <h3 class="box-title">Vehicle Summary</h3>
</div>

<div class="box-body">

<p>
<strong>Vehicle Code:</strong>
{{ $vehicle->vehicle_code }}
</p>

<p>
<strong>Owner:</strong>
{{ optional($vehicle->client)->first_name }}
{{ optional($vehicle->client)->last_name }}
</p>

<p>
<strong>Registration:</strong>
{{ $vehicle->registration_number }}
</p>

<p>
<strong>Market Value:</strong>
K{{ number_format($vehicle->market_value,2) }}
</p>

<p>
<strong>Forced Sale Value:</strong>
K{{ number_format($vehicle->forced_sale_value,2) }}
</p>

</div>

</div>

<div class="box box-success">

<div class="box-header">

<h3 class="box-title">
Insurance
</h3>

<a
    href="{{ url('vehicles/'.$vehicle->id.'/insurance/create') }}"
    class="btn btn-success btn-xs pull-right"
>

Add Insurance

</a>

</div>

<div class="box-body">

@if($vehicle->insurancePolicies->count())

    @foreach($vehicle->insurancePolicies as $insurance)

        <p>

            {{ $insurance->insurer_name }}

            <br>

            Expires:
            {{ $insurance->expiry_date }}

        </p>

        <hr>

    @endforeach

@else

    <div class="alert alert-warning">

        No insurance added.

    </div>

@endif

</div>

</div>



<div class="box box-danger">

<div class="box-header with-border">

<h3 class="box-title">

Vehicle Custody

</h3>

<a
href="{{ url('vehicles/'.$vehicle->id.'/custody/create') }}"
class="btn btn-danger btn-xs pull-right">

Receive Vehicle

</a>

</div>

<div class="box-body">

@if($vehicle->custody)

<p>

<strong>Status:</strong>

<span class="label label-success">

{{ ucfirst($vehicle->custody->status) }}

</span>

</p>

<p>

<strong>Received:</strong>

{{ $vehicle->custody->received_at }}

</p>

<p>

<strong>Received By:</strong>

{{ optional($vehicle->custody->receiver)->name }}

</p>

<p>

<strong>Keys Received:</strong>

{{ $vehicle->custody->keys_received }}

</p>

<p>

<strong>Key Tags:</strong>

{{ $vehicle->custody->key_tag_numbers }}

</p>

<p>

<strong>Garage:</strong>

{{ optional($vehicle->custody->garage)->garage_name }}

</p>

<a
href="{{ url('vehicles/'.$vehicle->id.'/custody') }}"
class="btn btn-primary btn-block">

View Custody Details

</a>

@else

<div class="alert alert-warning">

Vehicle has not yet been received into custody.

</div>

@endif

</div>

</div>



<div class="box box-info">

<div class="box-header with-border">

<h3 class="box-title">

Garage / Storage Facility

</h3>

</div>

<div class="box-body">

@if(optional($vehicle->custody)->garage)

<p>

<strong>Garage Name</strong><br>

{{ $vehicle->custody->garage->garage_name }}

</p>

<p>

<strong>Location</strong><br>

{{ $vehicle->custody->garage->physical_location }}

</p>

<p>

<strong>GPS Coordinates</strong><br>

{{ $vehicle->custody->garage->gps_coordinates }}

</p>

<p>

<strong>Contact Person</strong><br>

{{ $vehicle->custody->garage->contact_person }}

</p>

<p>

<strong>Phone</strong><br>

{{ $vehicle->custody->garage->contact_phone }}

</p>

@else

<div class="alert alert-info">

No garage assigned.

</div>

@endif

</div>

</div>

</div>


<!-- RIGHT SIDE -->

<div class="col-md-8">

<div class="box">

<div class="box-header">

<h3 class="box-title">
Documents
</h3>

<a
    href="{{ url('vehicles/'.$vehicle->id.'/documents/create') }}"
    class="btn btn-primary btn-xs pull-right">   

    Upload Document

</a>

</div>

<div class="box-body">

<table class="table table-bordered">

<thead>

<tr>

<th>Type</th>
<th>File</th>

</tr>

</thead>

<tbody>

@forelse($vehicle->documents as $document)

<tr>

<td>
{{ $document->document_type }}
</td>

<td>

<a href="{{ $document->document_file }}"
   target="_blank"
   class="btn btn-xs btn-primary">

    View

</a>

<a href="{{ $document->document_file }}"
   target="_blank"
   class="btn btn-xs btn-success">

    Download

</a>

</td>

</tr>

@empty

<tr>

<td colspan="2">

No documents uploaded.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>



<div class="box">

<div class="box-header with-border">

<h3 class="box-title">

Vehicle Photos

</h3>

<a
    href="{{ url('vehicles/'.$vehicle->id.'/photos/create') }}"
    class="btn btn-primary btn-xs pull-right">   

    Upload Photos

</a>

</div>

<div class="box-body">

<div class="row">

@forelse($vehicle->photos as $photo)

<div class="col-md-3">

<div class="thumbnail">

<img
    src="{{ $photo->photo_url }}"
    class="img-responsive">

<div class="caption">

<strong>

{{ $photo->photo_type }}

</strong>

<br>

{{ $photo->caption }}

</div>

</div>

</div>

@empty

<div class="col-md-12">

<div class="alert alert-warning">

No photos uploaded.

</div>

</div>

@endforelse

</div>

</div>

</div>





<div class="box box-warning">

<div class="box-header">

<h3 class="box-title">

Inspection History

</h3>

<a
    href="{{ url('vehicles/'.$vehicle->id.'/inspections/create') }}"
    class="btn btn-warning btn-xs pull-right">

    Add Inspection

</a>

</div>

<div class="box-body">

<table class="table table-bordered">

<thead>

<tr>

<th>Date</th>
<th>Inspector</th>
<th>Result</th>
<th>Report</th>

</tr>

</thead>

<tbody>

@forelse($vehicle->inspections as $inspection)

<tr>

<td>
{{ $inspection->inspection_date }}
</td>

<td>
{{ $inspection->inspector }}
</td>

<td>
{{ ucfirst($inspection->result) }}
</td>

<td>
    @if($inspection->report_url)

        <a
            href="{{ $inspection->report_url }}"
            target="_blank"
            class="btn btn-xs btn-primary">

            Report

        </a>

    @endif
</td>

</tr>

@empty

<tr>

<td colspan="3">

No inspections recorded.

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
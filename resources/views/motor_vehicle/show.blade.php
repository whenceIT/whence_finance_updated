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
        <h3 class="box-title">
            <i class="fa fa-car"></i> Vehicle Information
        </h3>
    </div>

    <div class="box-body no-padding">
        <table class="table table-striped">
            <tr>
                <th width="35%">Vehicle Code</th>
                <td>{{ $vehicle->vehicle_code }}</td>

                <th width="20%">Registration</th>
                <td>
                    <span class="label label-primary">
                        {{ $vehicle->registration_number }}
                    </span>
                </td>
            </tr>

            <tr>
                <th>Owner</th>
                <td>
                    {{ optional($vehicle->client)->first_name }}
                    {{ optional($vehicle->client)->last_name }}
                </td>

                <th>Market Value</th>
                <td>
                    <strong class="text-green">
                        K{{ number_format($vehicle->market_value,2) }}
                    </strong>
                </td>
            </tr>

            <tr>
                <th>Engine Number</th>
                <td >
                    <strong class="text-red">
                        {{ $vehicle->engine_number }}
                    </strong>
                </td>

                <th>Chassis Number</th>
                <td>
                    <strong class="text-blue">
                        {{ $vehicle->engine_number }}
                    </strong>
                </td>

            </tr>

        
                
      

        </table>
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

<table class="table table-hover">

<thead>
<tr>
    <th>Insurer</th>
    <th>Value</th>
    <th>Policy Number</th>
    <th>Expiry</th>
</tr>
</thead>

<tbody>

@forelse($vehicle->insurancePolicies as $insurance)

<tr>
    <td>{{ $insurance->insurer_name }}</td>
    <td>{{ $insurance->insured_value }}</td>
    <td>{{ $insurance->policy_number }}</td>
    <td>
        <span class="label label-success">
            {{ $insurance->expiry_date }}
        </span>
    </td>
</tr>

@empty

<tr>
    <td colspan="2" class="text-center text-muted">
        No insurance found
    </td>
</tr>

@endforelse

</tbody>

</table>

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

<table class="table table-bordered">

<tr>
    <th>Status</th>

    <td>
        <span class="label label-success">
            {{ ucfirst($vehicle->custody->status) }}
        </span>
    </td>
</tr>

<tr>
    <th>Received</th>
    <td>{{ $vehicle->custody->received_at }}</td>
</tr>

<tr>
    <th>Received By</th>
    <td>{{ optional($vehicle->custody->receiver)->first_name }} {{ optional($vehicle->custody->receiver)->last_name }}</td>
</tr>

<tr>
    <th>Key Received</th>
    <td>{{ $vehicle->custody->keys_received }}</td>
</tr>

<tr>
    <th>Key Tag Numbers</th>
    <td>{{ $vehicle->custody->key_tag_numbers }}</td>
</tr>

<tr>
    <th>Remarks</th>
    <td>{{ $vehicle->custody->remarks }}</td>
</tr>

</table>

<!-- <a class="btn btn-primary btn-block" href="{{ url('vehicles/'.$vehicle->id.'/custody') }}">
    <i class="fa fa-eye"></i>
    View Custody
</a> -->

</div>



<div class="box box-info">

<div class="box-header with-border">

<h3 class="box-title">

Garage / Storage Facility

</h3>

</div>

<div class="box-body">

@if($vehicle->custody->garage_name)

<p>

<strong>Garage Name</strong><br>

{{ $vehicle->custody->garage_name }}

</p>

<p>

<strong>Location</strong><br>

{{ $vehicle->custody->garage_location }}

</p>

<p>
    <strong>GPS Coordinates</strong><br>

    @if($vehicle->custody->garage_gps)
        <a href="{{ $vehicle->custody->garage_gps }}"
           target="_blank"
           class="btn btn-primary btn-sm">
            <i class="fa fa-map-marker"></i>
            Open in Google Maps
        </a>
    @else
        <span class="text-muted">Not available</span>
    @endif
</p>

<p>

<strong>Contact Person</strong><br>

{{ $vehicle->custody->garage_contact_person }}

</p>

<p>

<strong>Phone</strong><br>

{{ $vehicle->custody->garage_contact_phone }}

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
<th>Mileage</th>
<th>Fuel Level</th>
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
{{ $inspection->mileage }}
</td>

<td>
{{ $inspection->fuel }}
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
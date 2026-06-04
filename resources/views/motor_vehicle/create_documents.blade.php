@extends('layouts.master')

@section('content')

<section class="content-header">

    <h1>Upload Vehicle Document</h1>

</section>

<section class="content">

<div class="row">

<div class="col-md-8 col-md-offset-2">

<div class="box box-primary">

<div class="box-header with-border">

<h3 class="box-title">

Vehicle Document

</h3>

</div>

<form
    method="POST"
    enctype="multipart/form-data"
    action="{{ url('vehicles/'.$vehicle->id.'/documents/store') }}"
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

Document Type

</label>

<select
    name="document_type"
    class="form-control"
    required
>

<option value="">

Select Document Type

</option>

<option value="Whitebook">

Whitebook

</option>

<option value="Insurance Certificate">

Insurance Certificate

</option>

<option value="Valuation Report">

Valuation Report

</option>

<option value="Purchase Invoice">

Purchase Invoice

</option>

<option value="Road Tax">

Road Tax

</option>

<option value="Import Papers">

Import Papers

</option>

<option value="Inspection Report">

Inspection Report

</option>

<option value="Other">

Other

</option>

</select>

</div>

<div class="form-group">

<label>

Document Name

</label>

<input
    type="text"
    name="document_name"
    class="form-control"
>

</div>

<div class="form-group">

<label>

Select File

</label>

<input
    type="file"
    name="document_file"
    class="form-control"
    required
>

</div>

</div>

<div class="box-footer">

<button
    type="submit"
    class="btn btn-primary"
>

Upload Document

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
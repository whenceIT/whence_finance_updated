@extends('layouts.master')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<section class="content-header">
    <h1>Add Vehicle</h1>
</section>

<section class="content">

<div class="box">

<form method="POST" action="{{ url('vehicles/store') }}">

@csrf

<div class="box-body">

<div class="form-group">
    <label>Client</label>

    <select name="client_id"
            id="client_id"
            class="form-control"
            required>
    </select>
</div>

<div class="form-group">
    <label>Make</label>
    <input type="text"
           name="make"
           class="form-control">
</div>

<div class="form-group">
    <label>Model</label>
    <input type="text"
           name="model"
           class="form-control">
</div>

<div class="form-group">
    <label>Year</label>
    <input type="number"
           name="year"
           class="form-control">
</div>

<div class="form-group">
    <label>Registration Number</label>
    <input type="text"
           name="registration_number"
           class="form-control">
</div>

<div class="form-group">
    <label>Market Value</label>
    <input type="number"
           step="0.01"
           name="market_value"
           class="form-control">
</div>

<div class="form-group">
    <label>Forced Sale Value</label>
    <input type="number"
           step="0.01"
           name="forced_sale_value"
           class="form-control">
</div>

<button type="submit" class="btn btn-success">
    Save Vehicle
</button>

</div>

</form>

</div>

</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    $('#client_id').select2({
        placeholder: 'Type client name...',
        minimumInputLength: 2,
        width: '100%',
        ajax: {
            url: '{{ url("vehicles/search-clients") }}',
            dataType: 'json',
            delay: 250,
            cache: true,

            data: function(params) {
                return {
                    search: params.term
                };
            },

            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

});
</script>

@endsection
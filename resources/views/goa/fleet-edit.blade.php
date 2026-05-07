@extends('layouts.master')
@section('title')
    GOA Manager - Edit Fleet Vehicle
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p class="lead">Edit vehicle details.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <strong>There were some issues with your submission.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Vehicle: {{ $fleet->vehicle_id }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fleets.update', $fleet->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_type">Type</label>
                                    <select class="form-control" id="vehicle_type" name="vehicle_type">
                                        <option value="">-- Select Type --</option>
                                        <option value="Sedan" {{ $fleet->vehicle_type == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                        <option value="SUV" {{ $fleet->vehicle_type == 'SUV' ? 'selected' : '' }}>SUV</option>
                                        <option value="Truck" {{ $fleet->vehicle_type == 'Truck' ? 'selected' : '' }}>Truck</option>
                                        <option value="Van" {{ $fleet->vehicle_type == 'Van' ? 'selected' : '' }}>Van</option>
                                        <option value="Bus" {{ $fleet->vehicle_type == 'Bus' ? 'selected' : '' }}>Bus</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_model">Model</label>
                                    <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" value="{{ $fleet->vehicle_model }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="assigned_to">Assigned To</label>
                                    <select class="form-control" id="assigned_to" name="assigned_to">
                                        <option value="">-- Select User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $fleet->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="office_id">Office</label>
                                    <select class="form-control" id="office_id" name="office_id">
                                        <option value="">-- Select Office --</option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->id }}" {{ $fleet->office_id == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color">Color</label>
                                    <input type="text" class="form-control" id="color" name="color" value="{{ $fleet->color }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="current_value">Current Value</label>
                                    <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" value="{{ $fleet->current_value }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="white_book">White Book</label>
                                    <select class="form-control" id="white_book" name="white_book">
                                        <option value="available" {{ $fleet->white_book == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="none" {{ $fleet->white_book == 'none' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_status">Status</label>
                                    <select class="form-control" id="vehicle_status" name="vehicle_status">
                                        <option value="Active" {{ $fleet->vehicle_status == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Maintenance" {{ $fleet->vehicle_status == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="Out of Service" {{ $fleet->vehicle_status == 'Out of Service' ? 'selected' : '' }}>Out of Service</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Vehicle</button>
                            <a href="{{ route('goa.fleet-management') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
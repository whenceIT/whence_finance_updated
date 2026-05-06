@extends('layouts.master')
@section('title')
    GOA Manager - Fleet Vehicle Details
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p class="lead">Vehicle details.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Vehicle: {{ $fleet->vehicle_id }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Vehicle ID</dt>
                                <dd class="col-sm-8">{{ $fleet->vehicle_id }}</dd>

                                <dt class="col-sm-4">Type</dt>
                                <dd class="col-sm-8">{{ $fleet->vehicle_type ?: 'N/A' }}</dd>

                                <dt class="col-sm-4">Model</dt>
                                <dd class="col-sm-8">{{ $fleet->vehicle_model ?: 'N/A' }}</dd>

                                <dt class="col-sm-4">Assigned To</dt>
                                <dd class="col-sm-8">{{ $fleet->user ? $fleet->user->first_name . ' ' . $fleet->user->last_name : 'N/A' }}</dd>

                                <dt class="col-sm-4">Office</dt>
                                <dd class="col-sm-8">{{ $fleet->office ? $fleet->office->name : 'N/A' }}</dd>

                                <dt class="col-sm-4">Color</dt>
                                <dd class="col-sm-8">{{ $fleet->color ?: 'N/A' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Date Purchased</dt>
                                <dd class="col-sm-8">{{ $fleet->date_purchased ? $fleet->date_purchased->format('Y-m-d') : 'N/A' }}</dd>

                                <dt class="col-sm-4">Insurance Expire</dt>
                                <dd class="col-sm-8">{{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('Y-m-d') : 'N/A' }}</dd>

                                <dt class="col-sm-4">Current Value</dt>
                                <dd class="col-sm-8">{{ $fleet->current_value ? '$' . number_format($fleet->current_value, 2) : 'N/A' }}</dd>

                                <dt class="col-sm-4">White Book</dt>
                                <dd class="col-sm-8">{{ ucfirst($fleet->white_book) }}</dd>

                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    @if($fleet->vehicle_status == 'Active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($fleet->vehicle_status == 'Maintenance')
                                        <span class="badge badge-warning">Maintenance</span>
                                    @else
                                        <span class="badge badge-danger">{{ $fleet->vehicle_status }}</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Last Maintenance</dt>
                                <dd class="col-sm-8">{{ $fleet->last_maintenance ? $fleet->last_maintenance->format('Y-m-d') : 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('fleets.edit', $fleet->id) }}" class="btn btn-primary">Edit Vehicle</a>
                        <a href="{{ route('goa.fleet-management') }}" class="btn btn-secondary">Back to Fleet Management</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
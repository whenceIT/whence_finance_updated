@extends('layouts.master')
@section('title')
    Vehicle Movement History
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Movement History</h3>
                    <div class="box-tools">
                        <a href="{{ url('vehicles/'.$vehicle->id) }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover table-striped" id="movements-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Previous Location</th>
                                <th>New Location</th>
                                <th>Authorized By</th>
                                <th>Moved By</th>
                                <th>Reason</th>
                                <th>Condition</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $movement)
                                <tr>
                                    <td>{{ date('jS M, Y', strtotime($movement->movement_date)) }}</td>
                                    <td>{{ $movement->previous_location }}</td>
                                    <td>{{ $movement->new_location }}</td>
                                    <td>{{ $movement->authorizedBy->name ?? '' }}</td>
                                    <td>{{ $movement->movedBy->name ?? '' }}</td>
                                    <td>{{ $movement->reason }}</td>
                                    <td>{{ $movement->condition }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="box-footer clearfix">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Add New Movement</h3>
                </div>
                <form action="{{ route('vehicles.movements.store', $vehicle->id) }}" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="previous_location">Previous Location</label>
                                    <input type="text" class="form-control" id="previous_location" name="previous_location" value="{{ old('previous_location') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_location">New Location</label>
                                    <input type="text" class="form-control" id="new_location" name="new_location" value="{{ old('new_location') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="movement_date">Movement Date</label>
                                    <input type="date" class="form-control" id="movement_date" name="movement_date" value="{{ old('movement_date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="authorized_by">Authorized By</label>
                                    <select class="form-control" id="authorized_by" name="authorized_by">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('authorized_by') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="moved_by">Moved By</label>
                                    <select class="form-control" id="moved_by" name="moved_by">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('moved_by') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="condition">Condition</label>
                            <textarea class="form-control" id="condition" name="condition" rows="3" required>{{ old('condition') }}</textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save Movement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.master')
@section('title')
    Edit District
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit District</h3>
        </div>
        <form method="POST" action="{{ url('districts/'.$district->id) }}">
            @csrf
            @method('PUT')
            <div class="box-body">
                <div class="form-group">
                    <label for="province_id">Province</label>
                    <select class="form-control" id="province_id" name="province_id" required>
                        <option value="">Select Province</option>
                        @foreach(\App\Models\Province::all() as $province)
                            <option value="{{ $province->id }}" {{ $district->province_id == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">District Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $district->name }}" required>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update District</button>
                <a href="{{ url('districts') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@endsection
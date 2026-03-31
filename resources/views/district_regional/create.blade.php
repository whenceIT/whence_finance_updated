@extends('layouts.master')
@section('title')
    Create District Regional
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Create District Regional</h3>
        </div>
        <form method="POST" action="{{ url('district-regionals') }}">
            @csrf
            <div class="box-body">
                <div class="form-group">
                    <label for="name">District Regional Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="district_id">District</label>
                    <select class="form-control" id="district_id" name="district_id" required>
                        <option value="">Select District</option>
                        @foreach(\App\Models\District::with('province')->get() as $district)
                            <option value="{{ $district->id }}">{{ $district->name }} ({{ $district->province->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="province_id">Province</label>
                    <select class="form-control" id="province_id" name="province_id" required>
                        <option value="">Select Province</option>
                        @foreach(\App\Models\Province::all() as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Create District Regional</button>
                <a href="{{ url('district-regionals') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@endsection
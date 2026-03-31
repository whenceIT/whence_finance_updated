@extends('layouts.master')
@section('title')
    Create District
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Create District</h3>
        </div>
        <form method="POST" action="{{ url('districts') }}">
            @csrf
            <div class="box-body">
                <div class="form-group">
                    <label for="name">District Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
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
                <button type="submit" class="btn btn-primary">Create District</button>
                <a href="{{ url('districts') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@endsection
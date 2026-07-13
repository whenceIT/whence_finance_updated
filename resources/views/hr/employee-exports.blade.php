@extends('layouts.master')

@section('title', 'Employee Exports')

@section('content')
<section class="content-header">
    <h1>
        Employee Exports
        <small>Export employee records to CSV</small>
    </h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Export Filters</h3>
        </div>
        
        <div class="box-body">
            <form method="POST" action="{{ route('hr.employee-exports.download') }}" class="form-inline">
                @csrf
                
                <div class="form-group" style="margin-right: 10px;">
                    <label for="office_id" style="margin-right: 5px;">Office:</label>
                    <select name="office_id" id="office_id" class="form-control">
                        <option value="">All Offices</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-right: 10px;">
                    <label for="position_id" style="margin-right: 5px;">Position:</label>
                    <select name="position_id" id="position_id" class="form-control">
                        <option value="">All Positions</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-right: 10px;">
                    <label for="status" style="margin-right: 5px;">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fa fa-download"></i> Export to CSV
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
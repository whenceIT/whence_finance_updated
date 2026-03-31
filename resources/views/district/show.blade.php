@extends('layouts.master')
@section('title')
    District Details
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">District Details</h3>
            <div class="box-tools pull-right">
                <a href="{{ url('districts') }}" class="btn btn-default btn-sm">Back to List</a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th>District Name</th>
                    <td>{{ $district->name }}</td>
                </tr>
                <tr>
                    <th>Province</th>
                    <td>{{ $district->province->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $district->created_at ? $district->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $district->updated_at ? $district->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="box-footer">
            @if(Sentinel::hasAccess('districts.update'))
                <a href="{{ url('districts/'.$district->id.'/edit') }}" class="btn btn-primary">Edit District</a>
            @endif
        </div>
    </div>
@endsection
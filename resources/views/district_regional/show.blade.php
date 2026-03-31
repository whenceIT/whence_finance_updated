@extends('layouts.master')
@section('title')
    District Regional Details
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">District Regional Details</h3>
            <div class="box-tools pull-right">
                <a href="{{ url('district-regionals') }}" class="btn btn-default btn-sm">Back to List</a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th>District Regional Name</th>
                    <td>{{ $districtRegional->name }}</td>
                </tr>
                <tr>
                    <th>District</th>
                    <td>{{ $districtRegional->district->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Province</th>
                    <td>{{ $districtRegional->province->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $districtRegional->created_at ? $districtRegional->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $districtRegional->updated_at ? $districtRegional->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <div class="box-footer">
            @if(Sentinel::hasAccess('district-regionals.update'))
                <a href="{{ url('district-regionals/'.$districtRegional->id.'/edit') }}" class="btn btn-primary">Edit District Regional</a>
            @endif
        </div>
    </div>
@endsection
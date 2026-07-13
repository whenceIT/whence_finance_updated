@extends('layouts.master')

@section('title', 'Employee Exports')

@section('content')
<section class="content-header">
    <h1>
        Employee Exports
        <small>Export employee records</small>
    </h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Export All Employees</h3>
        </div>
        
        <div class="box-body">
            <div class="form-inline">
                <a href="{{ route('hr.employee-exports.excel') }}" class="btn btn-primary">
                    <i class="fa fa-file-excel-o"></i> Export to Excel
                </a>
            </div>
        </div>
    </div>
</section>

<section>
    <!-- here -->
</section>
@endsection
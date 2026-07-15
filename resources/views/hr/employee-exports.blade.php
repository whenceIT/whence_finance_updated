@extends('layouts.master')

@section('title', 'Employee Exports')

@section('content')
<section class="content-header">
    <small>Export employee records</small>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Export Employee Data</h3>
        </div>
        
        <div class="box-body">
            <form id="exportForm" class="form-inline" style="margin-bottom: 15px;">
                <div class="form-group" style="margin-right: 10px;">
                    <label for="period" style="margin-right: 5px;">Period:</label>
                    <select name="period" id="period" class="form-control">
                        <option value="all">All</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                        <option value="last_year">Last Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="form-group custom-dates" style="display:none; margin-right: 10px;">
                    <input type="date" name="start_date" id="start_date" class="form-control" placeholder="Start Date">
                </div>
                <div class="form-group custom-dates" style="display:none; margin-right: 10px;">
                    <input type="date" name="end_date" id="end_date" class="form-control" placeholder="End Date">
                </div>
            </form>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="list-group">
                        <a href="{{ route('hr.employee-exports.excel') }}" class="list-group-item export-link">
                            <i class="fa fa-file-excel-o"></i> Export All Employee Data
                        </a>
                        <a href="{{ route('hr.employee-exports.inactive') }}" class="list-group-item export-link">
                            <i class="fa fa-file-excel-o"></i> Export All Inactive Employees
                        </a>
                        <a href="{{ route('hr.employee-exports.active') }}" class="list-group-item export-link">
                            <i class="fa fa-file-excel-o"></i> Export All Active Employees
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="list-group">
                        <a href="{{ route('hr.employee-exports.erp') }}" class="list-group-item export-link">
                            <i class="fa fa-file-excel-o"></i> Export Employee ERP Format
                        </a>
                        <a href="{{ route('hr.employee-exports.napsa') }}" class="list-group-item export-link">
                            <i class="fa fa-file-excel-o"></i> Export Employee NAPSA
                        </a>
                        <a href="{{ route('hr.employee-exports.nhima') }}" class="list-group-item export-link">
                            <i class="fa fa-file-excel-o"></i> Export Employee NHIMA
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$('#period').on('change', function() {
    var period = $(this).val();
    if (period === 'custom') {
        $('.custom-dates').show();
    } else {
        $('.custom-dates').hide();
    }
});

$('.export-link').on('click', function(e) {
    e.preventDefault();
    var baseUrl = $(this).attr('href');
    var period = $('#period').val();
    var start = $('#start_date').val();
    var end = $('#end_date').val();
    
    var url = baseUrl;
    
    if (period && period !== 'all') {
        url += (url.indexOf('?') !== -1 ? '&' : '?') + 'period=' + period;
    }
    if (start) {
        url += (url.indexOf('?') !== -1 ? '&' : '?') + 'start_date=' + start;
    }
    if (end) {
        url += (url.indexOf('?') !== -1 ? '&' : '?') + 'end_date=' + end;
    }
    
    window.location.href = url;
});
</script>
@endsection
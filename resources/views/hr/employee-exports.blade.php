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


<!-- Add a section here -->
<div class="box box-primary" style="margin-top: 20px;">
    <div class="box-header with-border">
        <h3 class="box-title">Prompt User Profile</h3>
    </div>
    <div class="box-body">
        <form action="{{ url('user/prompt-user-profile') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="office_id" style="font-weight: 600; color: #000041;">Select Office</label>
                <select name="office_id" id="office_id" class="form-control" required>
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Save</button>
        </form>
    </div>
    <!-- Here display a list of Office where pscan = 1 order by updated_at desc -->
    @if(isset($scanedoffices) && $scanedoffices->count() > 0)
        <div class="box box-primary" style="margin-top: 20px;">
            <div class="box-header with-border">
                <h3 class="box-title">Offices with Prompt User Profile Enabled</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Office Name</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scanedoffices as $office)
                            <tr>
                                <td>{{ $office->name }}</td>
                                <td>{{ $office->updated_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($office->pscan == 1 && $office->cscan == 0)
                                        <i class="fa fa-check-circle" style="color:#27ae60;font-size:18px;"></i>
                                    @else
                                        <i class="fa fa-location" style="color:#c0392b;font-size:18px;"></i>&nbsp;Current
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>


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
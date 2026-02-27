@extends('layouts.master')

@section('title')
   Audit Logs
@endsection

@section('content')

<div class="box box-primary">

    <div class="box-header with-border">
        <h3 class="box-title">Audit Logs</h3>
    </div>

    {{-- Date Filter Section --}}
    <div class="box-body" style="border-bottom:1px solid #f4f4f4;">
        <form method="GET" action="{{ url()->current() }}">
            <div class="row">

                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control"
                        value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control"
                        value="{{ request('to_date') }}">
                </div>

                <div class="col-md-3" style="margin-top:25px;">
                    <button type="submit" class="btn btn-primary">
                        Filter
                    </button>

                    <a href="{{ url()->current() }}" class="btn btn-default">
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="box-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Action</th>
                        <th>Done By</th>
                        <th>Details</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->action }}</td>
                            <td>
                            
                            </td>
                            <td>{{ $log->details ?? $log->notes }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@section('footer-scripts')

<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#data-table').DataTable({
        dom: 'frti',
        paging: true,
        pageLength: 25,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: true,
        order: [[4, "desc"]],
        responsive: false
    });
});
</script>

@endsection

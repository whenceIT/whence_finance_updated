@extends('layouts.master')

@section('title', 'Deposit Logs')

@section('content')
<div class="content-wrapper">

    {{-- PAGE HEADER --}}
    <section class="content-header">
        <h1>
            Deposit Logs
            <small class="text-muted">View, filter and track all deposit transactions</small>
        </h1>
    </section>

    <section class="content">

        {{-- FILTERS --}}
        <div class="box box-solid box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-filter"></i> Filters
                </h3>
            </div>

            <div class="box-body">
                <div class="row">

                    <div class="col-md-4">
                        <label>Branch</label>
                        <select id="branchFilter" class="form-control">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Date</label>
                        <input type="date" id="dateFilter" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button id="resetFilters" class="btn btn-default btn-block">
                            <i class="fa fa-refresh"></i> Reset Filters
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="box box-solid box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-database"></i> Deposit Records
                </h3>
            </div>

            <div class="box-body table-responsive">
                <table id="depositLogsTable" class="table table-bordered table-hover">
                    <thead class="bg-gray">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Office</th>
                            <th>User</th>
                            <th class="text-right">Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>
@endsection


@section('footer-scripts')
<script>
$(document).ready(function () {

    // Initialize DataTable
    var table = $('#depositLogsTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 15,
        order: [[7, 'desc']],
        autoWidth: false,
        columnDefs: [
            { targets: 4, className: 'text-right' }
        ]
    });

    function showLoading() {
        table.clear().draw();
        $('#depositLogsTable tbody').html(`
            <tr>
                <td colspan="8" class="text-center text-muted">
                    <i class="fa fa-spinner fa-spin"></i> Loading deposit logs...
                </td>
            </tr>
        `);
    }

 function loadDepositLogs() {
    showLoading();

    var branch = $('#branchFilter').val();
    var date   = $('#dateFilter').val();

    $.get('https://lms2backend.whencefinancesystem.com/deposit-logs', {
        branch: branch,
        date: date
    }, function (res) {

        console.log('Deposit Logs Response:', res); // <-- added console log

        table.clear();

 res.forEach(function (r) {
    // Format created_date as YYYY-MM-DD
    let formattedDate = new Date(r.created_date).toISOString().split('T')[0];

    table.row.add([
        r.id,
        r.deposit_type_name,
        r.office_name,
        r.user_name,
        `<strong class="text-success">
            ${parseFloat(r.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
        </strong>`,
        `<span class="label label-info">${r.deposit_method}</span>`,
        r.reference_number ?? '-',
        formattedDate  // <-- formatted as YYYY-MM-DD
    ]);
});

        table.draw();
    });
}

    // Initial load
    loadDepositLogs();

    // Filters
    $('#branchFilter, #dateFilter').change(loadDepositLogs);

    $('#resetFilters').click(function () {
        $('#branchFilter').val('');
        $('#dateFilter').val('');
        loadDepositLogs();
    });

});
</script>
@endsection
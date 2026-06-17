@extends('layouts.master')

@section('title', 'Executive Ledger')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div style="margin-bottom:20px;">
    <h2>Executive Ledger</h2>
<small>Institution-wide cash movement tracking</small>
    </div>


    <div class="row" style="margin-bottom:20px;">

    <div class="col-md-3">
        <label>Start Date</label>
        <input
            type="date"
            id="start_date"
            class="form-control"
            value="{{ date('Y-01-01') }}"
        >
    </div>

    <div class="col-md-3">
        <label>End Date</label>
        <input
            type="date"
            id="end_date"
            class="form-control"
            value="{{ date('Y-m-d') }}"
        >
    </div>

    <div class="col-md-2">
        <label>&nbsp;</label>
        <button
            id="loadLedger"
            class="btn btn-primary btn-block"
        >
            <i class="fa fa-search"></i>
            Load Ledger
        </button>
    </div>

</div>

    <!-- Balance Card -->
    <div class="row">

        <div class="col-md-12">
<div style="
    background:linear-gradient(135deg,#1e3c72,#2a5298);
    border-radius:15px;
    padding:25px;
    color:white;
    margin-bottom:20px;
">

    <div class="row">

        <div class="col-md-8">

         <div style="font-size:14px;opacity:.8;">
    Institution Wallet Balance
</div>

         <h3 id="branchName">
    Loading...
</h3>


<div id="cashBalance" style="
    font-size:48px;
    font-weight:900;
    line-height:1.1;
    margin-top:10px;
    color:#ffffff;
    text-shadow:0 2px 8px rgba(0,0,0,0.25);
">
    ZMW 0.00
</div>

        </div>

        <div class="col-md-4 text-right">

            <i class="fa fa-bank"
               style="
               font-size:70px;
               opacity:.2;
               margin-top:10px;
            "></i>

        </div>

    </div>

</div>
        </div>

    </div>

    <!-- Summary Cards -->

<div class="row">

    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="inner">
                <h3 id="totalCollections">0</h3>
                <p>Total Collections</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 id="totalTransfers">0</h3>
                <p>Total Transfers</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3 id="totalFees">0</h3>
                <p>Total Fees</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
    <div class="small-box bg-blue">
        <div class="inner">
            <h3 id="transactionCount">0</h3>
            <p>Transactions</p>
        </div>
    </div>
</div>


</div>

    <!-- Ledger Table -->

    <div class="box" style="
        margin-top:25px;
        border:none;
        border-radius:12px;
        overflow:hidden;
        box-shadow:0 3px 15px rgba(0,0,0,0.08);
    ">

        <div class="box-header" style="
            background:#f8f9fa;
            border-bottom:1px solid #eee;
            padding:18px;
        ">
            <h3 class="box-title" style="
                font-weight:700;
                color:#2c3e50;
            ">
                Ledger Transactions
            </h3>
        </div>

        <div class="box-body" style="padding:0;">

    <div class="table-responsive">

        <table class="table table-striped table-bordered" style="margin:0; min-width:900px;">

    <thead>

        <tr style="background:#fafafa;">

            <th>Date</th>
            <th>Branch</th>
            <th>Transaction ID</th>
            <th>Description</th>
            <th>Reason</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Fees</th>
            <th>Status</th>

        </tr>

    </thead>

    <tbody id="ledgerBody">

        <tr>
            <td colspan="9" class="text-center">
                Click Load Ledger
            </td>
        </tr>

    </tbody>
         

</table>

   <div id="pagination" class="text-center" style="padding:15px;"></div>
</div>

        </div>
    </div>

</div>

<script>

    let allTransactions = [];
let currentPage = 1;
const pageSize = 30;



function money(amount) {

    return Number(amount).toLocaleString(
        undefined,
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );
}

function cleanTransactionId(id) {

    if (
        id &&
        id.startsWith('PAY') &&
        id.endsWith('_CREDIT')
    ) {
        return id.replace('_CREDIT', '');
    }

    return id;
}

function renderPage(page) {

    currentPage = page;

    let start = (page - 1) * pageSize;
    let end = start + pageSize;

    let pageTransactions = allTransactions.slice(start, end);

    let rows = '';

    pageTransactions.forEach(function (tx) {

        let transactionFees =
            parseFloat(tx.gateway_fee || 0) +
            parseFloat(tx.withinhere_fee || 0);

        let type = tx.transfer_type || 'unknown';
        let status = tx.status || 'pending';

        let amountColor =
            type === 'collection'
                ? '#00a65a'
                : '#dd4b39';

        let sign =
            type === 'collection'
                ? '+'
                : '-';

        let typeLabel =
            type === 'collection'
                ? 'label-success'
                : 'label-danger';

        let statusLabel =
            status === 'completed'
                ? 'label-success'
                : 'label-warning';

        rows += `
            <tr>

                <td>${new Date(tx.created_at).toLocaleString()}</td>
                <td>${tx.branch_name || ''}</td>

                <td>${cleanTransactionId(tx.transaction_id)}</td>

                <td>${tx.description || ''}</td>

                <td>${tx.reason || ''}</td>

                <td>
                    <span class="label ${typeLabel}">
                        ${type.toUpperCase()}
                    </span>
                </td>

                <td style="color:${amountColor};font-weight:bold;">
                    ${sign} ZMW ${money(tx.amount)}
                </td>

                <td>
                    ZMW ${money(transactionFees)}
                </td>

                <td>
                    <span class="label ${statusLabel}">
                        ${status.toUpperCase()}
                    </span>
                </td>

            </tr>
        `;
    });

    $('#ledgerBody').html(rows);

    renderPagination();
}


function renderPagination() {

    let totalPages = Math.ceil(
        allTransactions.length / pageSize
    );

    let html = '';

    if (totalPages <= 1) {
        $('#pagination').html('');
        return;
    }

    html += `
        <ul class="pagination">
    `;

    html += `
        <li class="${currentPage === 1 ? 'disabled' : ''}">
            <a href="#" onclick="renderPage(${currentPage - 1});return false;">
                &laquo;
            </a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {

        html += `
            <li class="${currentPage === i ? 'active' : ''}">
                <a href="#" onclick="renderPage(${i});return false;">
                    ${i}
                </a>
            </li>
        `;
    }

    html += `
        <li class="${currentPage === totalPages ? 'disabled' : ''}">
            <a href="#" onclick="renderPage(${currentPage + 1});return false;">
                &raquo;
            </a>
        </li>
    `;

    html += '</ul>';

    $('#pagination').html(html);
}

$('#loadLedger').click(function () {

    $('#loadLedger').html(
        '<i class="fa fa-spinner fa-spin"></i> Loading...'
    );

    $('#ledgerBody').html(`
        <tr>
            <td colspan="9" class="text-center">
                Loading transactions...
            </td>
        </tr>
    `);

    $.ajax({

     url: "{{ route('executive-ledger.data') }}",

        type: "POST",

  data: {
    _token: "{{ csrf_token() }}",
    start_date: $('#start_date').val(),
    end_date: $('#end_date').val()
},
        success: function (response) {

         console.log('EXECUTIVE LEDGER RESPONSE:', response);

    if (!response.success) {

        $('#ledgerBody').html(`
            <tr>
                <td colspan="9" class="text-center text-danger">
                    No data found
                </td>
            </tr>
        `);

        return;
    }

    $('#branchName').html(
        response.institution.name
    );

    $('#cashBalance').html(
        'ZMW ' + money(response.institution.cash_balance)
    );

    allTransactions = response.transactions || [];

    let collections = 0;
    let transfers = 0;
    let fees = 0;

    allTransactions.forEach(function(tx){

        let amount = parseFloat(tx.amount || 0);

        if(tx.transfer_type === 'collection'){
            collections += amount;
        } else {
            transfers += amount;
        }

        fees +=
            parseFloat(tx.gateway_fee || 0) +
            parseFloat(tx.withinhere_fee || 0);
    });

    renderPage(1);

    $('#totalCollections').html(
        'ZMW ' + money(collections)
    );

    $('#totalTransfers').html(
        'ZMW ' + money(transfers)
    );

    $('#totalFees').html(
        'ZMW ' + money(fees)
    );

    $('#transactionCount').html(
        allTransactions.length
    );
},

        error: function (xhr) {

            console.log(xhr);

            $('#ledgerBody').html(`
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        Failed to load ledger data
                    </td>
                </tr>
            `);
        },

        complete: function () {

            $('#loadLedger').html(
                '<i class="fa fa-search"></i> Load Ledger'
            );
        }

    });

});

// Auto-load when page opens

$(document).ready(function () {

    $('#loadLedger').click();

});

</script>

@endsection
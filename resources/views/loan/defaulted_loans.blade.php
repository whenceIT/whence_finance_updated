@extends('layouts.master')

@section('title', 'Defaulted Loans')

@section('content_header')

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
    ">

        <div>
            <h1 style="margin-bottom:5px;">
                Defaulted Loans
            </h1>

            <small style="color:#777;">
                Defaulted loans and LC accountability
            </small>
        </div>

        <button
            type="button"
            class="btn btn-success"
            onclick="exportLoans()"
        >
            <i class="fa fa-download"></i>
            Export CSV
        </button>

    </div>

@stop


@section('content')

{{-- ========================================================= --}}
{{-- SUMMARY --}}
{{-- ========================================================= --}}

<div class="row">

    <div class="col-md-4">

        <div class="small-box bg-red">

            <div class="inner">

                <h3>
                    {{ number_format($totalDefaulters) }}
                </h3>

                <p>
                    Total Defaulted Loans
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-warning"></i>
            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="small-box bg-orange">

            <div class="inner">

                <h3>
                    {{ number_format($totalLcs) }}
                </h3>

                <p>
                    LCs Involved
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-users"></i>
            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="small-box bg-blue">

            <div class="inner">

                <h3>
                    {{ number_format(
                        collect($defaultedLoans)->sum(function($loan) {
                            return (float)($loan['balance'] ?? 0);
                        }),
                        2
                    ) }}
                </h3>

                <p>
                    Total Outstanding Balance
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-money"></i>
            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- LC RANKING --}}
{{-- ========================================================= --}}

<div class="box box-danger">

    <div class="box-header with-border">

        <h3 class="box-title">
            <i class="fa fa-users"></i>
            LC Defaulted Loan Ranking
        </h3>

        <div class="box-tools pull-right">

            <div class="input-group" style="width:250px;">

                <input
                    type="text"
                    id="lcSearch"
                    class="form-control input-sm"
                    placeholder="Search LC..."
                >

                <span class="input-group-btn">

                    <button class="btn btn-default btn-sm">
                        <i class="fa fa-search"></i>
                    </button>

                </span>

            </div>

        </div>

    </div>


    <div class="box-body">

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover"
                id="lcTable"
            >

             <thead>
    <tr>
        <th style="width:70px;">Rank</th>
        <th>LC</th>
        <th>Office</th>
        <th style="width:180px;">Defaulted Loans</th>
        <th style="width:140px;">Action</th>
    </tr>
</thead>


                <tbody>
@foreach($lcStats as $index => $lc)

    <tr class="lc-row">

        <td>
            <strong>{{ $index + 1 }}</strong>
        </td>

        <td class="lc-name">
            {{ $lc['name'] }}
        </td>

        <td>
            @if(!empty($lc['offices']))

                @foreach($lc['offices'] as $office)

                    <span class="label label-default"
                          style="
                              display:inline-block;
                              margin:2px;
                              font-weight:normal;
                          ">
                        {{ $office }}
                    </span>

                @endforeach

            @else

                <span style="color:#999;">
                    N/A
                </span>

            @endif
        </td>

        <td>
            <span class="label label-danger"
                  style="font-size:13px;">
                {{ number_format($lc['defaulted_loans']) }}
            </span>
        </td>

        <td>
            <button
                type="button"
                class="btn btn-primary btn-sm view-lc-btn"
                data-user-id="{{ $lc['id'] }}"
                data-user-name="{{ $lc['name'] }}"
            >
                <i class="fa fa-eye"></i>
                View Loans
            </button>
        </td>

    </tr>

@endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- SELECTED LC --}}
{{-- ========================================================= --}}

<!-- LC LOANS MODAL -->

<div
    class="modal fade"
    id="lcLoansModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="lcLoansModalLabel"
>

    <div
        class="modal-dialog modal-xl"
        role="document"
        style="width:95%;"
    >

        <div class="modal-content">

            <div class="modal-header">

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4
                    class="modal-title"
                    id="lcLoansModalLabel"
                >
                    <i class="fa fa-user"></i>
                    <span id="selectedLCName"></span>
                </h4>

            </div>


            <div class="modal-body">

                <!-- SUMMARY -->

                <div class="row">

                    <div class="col-md-6">

                        <div style="
                            background:#f4f6f9;
                            padding:15px;
                            border-radius:4px;
                            margin-bottom:15px;
                        ">

                            <small style="color:#777;">
                                Defaulted Loans
                            </small>

                            <h3
                                id="selectedLCCount"
                                style="margin:5px 0 0 0;"
                            >
                                0
                            </h3>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div style="
                            background:#f4f6f9;
                            padding:15px;
                            border-radius:4px;
                            margin-bottom:15px;
                        ">

                            <small style="color:#777;">
                                Outstanding Balance
                            </small>

                            <h3
                                id="selectedLCBalance"
                                style="margin:5px 0 0 0;"
                            >
                                0.00
                            </h3>

                        </div>

                    </div>

                </div>


                <!-- SEARCH -->

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:15px;
                ">

                    <h4 style="margin:0;">
                        Defaulted Loans
                    </h4>

                    <div style="width:300px;">

                        <input
                            type="text"
                            id="loanSearch"
                            class="form-control"
                            placeholder="Search loan, office, officer..."
                        >

                    </div>

                </div>


                <!-- LOANS TABLE -->

                <div
                    class="table-responsive"
                    style="max-height:500px; overflow-y:auto;"
                >

                    <table
                        class="table table-bordered table-striped table-hover"
                        id="selectedLoansTable"
                    >

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Loan ID</th>

                                <th>Office</th>

                                <th>Loan Officer</th>

                                <th>Created</th>

                                <th>First Repayment</th>

                                <th>Principal</th>

                                <th>Balance</th>

                                <th>Vetted By</th>

                                <th>Verified By</th>

                            </tr>

                        </thead>

                        <tbody id="selectedLoansBody"></tbody>

                    </table>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-default"
                    data-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

@stop


@section('footer-scripts')

<script>

$(document).ready(function() {

    const defaultedLoans = @json($defaultedLoans);


    /*
    |--------------------------------------------------------------------------
    | LC Search
    |--------------------------------------------------------------------------
    */

    $('#lcSearch').on('keyup', function() {

        const search = $(this).val().toLowerCase();

        $('.lc-row').each(function() {

            const name = $(this)
                .find('.lc-name')
                .text()
                .toLowerCase();

            $(this).toggle(
                name.includes(search)
            );

        });

    });


    /*
    |--------------------------------------------------------------------------
    | View LC Loans
    |--------------------------------------------------------------------------
    */

    $(document).on('click', '.view-lc-btn', function() {

        const userId = String(
            $(this).data('user-id')
        );

        const userName = $(this).data('user-name');


        /*
        |--------------------------------------------------------------------------
        | Find this LC's defaulted loans
        |--------------------------------------------------------------------------
        */

        const loans = defaultedLoans.filter(function(loan) {

            return String(loan.vetted_by) === userId
                || String(loan.verified_by) === userId;

        });


        /*
        |--------------------------------------------------------------------------
        | LC Name
        |--------------------------------------------------------------------------
        */

        $('#selectedLCName').text(userName);


        /*
        |--------------------------------------------------------------------------
        | Loan Count
        |--------------------------------------------------------------------------
        */

        $('#selectedLCCount').text(
            loans.length.toLocaleString()
        );


        /*
        |--------------------------------------------------------------------------
        | Total Balance
        |--------------------------------------------------------------------------
        */

        const totalBalance = loans.reduce(
            function(total, loan) {

                return total + Number(
                    loan.balance || 0
                );

            },
            0
        );


        $('#selectedLCBalance').text(
            totalBalance.toLocaleString(
                undefined,
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Populate Loans Table
        |--------------------------------------------------------------------------
        */

        const tbody = $('#selectedLoansBody');

        tbody.empty();


        if (loans.length === 0) {

            tbody.append(`

                <tr>

                    <td
                        colspan="10"
                        class="text-center"
                        style="padding:20px;"
                    >
                        No defaulted loans found for this LC.
                    </td>

                </tr>

            `);

        } else {

            loans.forEach(function(loan, index) {

                tbody.append(`

                    <tr>

                        <td>
                            ${index + 1}
                        </td>

                        <td>
                            ${loan.loan_id ?? 'N/A'}
                        </td>

                        <td>
                            ${loan.office_name ?? 'N/A'}
                        </td>

                        <td>
                            ${loan.loan_officer_name ?? 'N/A'}
                        </td>

                        <td>
                            ${loan.created_date ?? 'N/A'}
                        </td>

                        <td>
                            ${loan.first_repayment_date ?? 'N/A'}
                        </td>

                        <td>

                            ${Number(
                                loan.principal || 0
                            ).toLocaleString(
                                undefined,
                                {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }
                            )}

                        </td>

                        <td>

                            <strong>

                                ${Number(
                                    loan.balance || 0
                                ).toLocaleString(
                                    undefined,
                                    {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }
                                )}

                            </strong>

                        </td>

                        <td>
                            ${loan.vetted_by_name ?? 'N/A'}
                        </td>

                        <td>
                            ${loan.verified_by_name ?? 'N/A'}
                        </td>

                    </tr>

                `);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Reset Search
        |--------------------------------------------------------------------------
        */

        $('#loanSearch').val('');

        $('#selectedLoansBody tr').show();


        /*
        |--------------------------------------------------------------------------
        | Open Modal
        |--------------------------------------------------------------------------
        */

        $('#lcLoansModal').modal('show');

    });


    /*
    |--------------------------------------------------------------------------
    | Loan Search
    |--------------------------------------------------------------------------
    */

    $('#loanSearch').on('keyup', function() {

        const search = $(this)
            .val()
            .toLowerCase();


        $('#selectedLoansBody tr').each(function() {

            const rowText = $(this)
                .text()
                .toLowerCase();

            $(this).toggle(
                rowText.includes(search)
            );

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Export CSV
    |--------------------------------------------------------------------------
    */

    window.exportLoans = function() {

        if (!defaultedLoans.length) {

            alert(
                'There are no defaulted loans to export.'
            );

            return;

        }


        const headers = [
            'Loan ID',
            'Office',
            'Loan Officer',
            'Created Date',
            'First Repayment Date',
            'Principal',
            'Balance',
            'Vetted By',
            'Verified By'
        ];


        const rows = defaultedLoans.map(function(loan) {

            return [

                loan.loan_id ?? '',

                loan.office_name ?? '',

                loan.loan_officer_name ?? '',

                loan.created_date ?? '',

                loan.first_repayment_date ?? '',

                loan.principal ?? 0,

                loan.balance ?? 0,

                loan.vetted_by_name ?? '',

                loan.verified_by_name ?? ''

            ];

        });


        let csv = headers.join(',') + '\n';


        rows.forEach(function(row) {

            csv += row.map(function(value) {

                return `"${String(value)
                    .replace(/"/g, '""')}"`;

            }).join(',') + '\n';

        });


        const blob = new Blob(
            [csv],
            {
                type: 'text/csv;charset=utf-8;'
            }
        );


        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');

        link.href = url;

        link.download = 'defaulted-loans.csv';

        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);

        URL.revokeObjectURL(url);

    };

});

</script>

@endsection
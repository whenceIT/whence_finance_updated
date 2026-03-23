
@extends('layouts.master')
@section('title')
    Manager Performance
@endsection
@section('content')
<div>

<div class="box box-primary">

    {{-- BOX HEADER --}}
    <div class="box-header with-border text-center">
        <h3 class="box-title">
             Performance Summary
            {{ date("jS M, Y", strtotime($start)) }}
            to
            {{ date("jS M, Y", strtotime($end)) }}
        </h3>
    </div>

    {{-- BOX BODY --}}
    <div class="box-body">

        {{-- FILTER FORM --}}
        <div style="background:#f9fafc; padding:25px; border-radius:16px; margin-bottom:25px;">
            <form method="GET" action="{{ url('user/manager_performance') }}" class="form-horizontal">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">

                        {{-- Cycle Start --}}
                        <div class="form-group text-center">
                            <label class="control-label">Cycle Start</label>
                            <input type="month" name="start_month" class="form-control"
                                value="{{ substr($start, 0, 7) }}">
                        </div>

                        {{-- Cycle End --}}
                        <div class="form-group text-center">
                            <label class="control-label">Cycle End</label>
                            <input type="month" name="end_month" class="form-control"
                                value="{{ substr($end, 0, 7) }}">
                        </div>

                        {{-- Load Button --}}
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                Load
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <hr>

        <div class="text-center" style="margin-bottom: 20px;">
            <button type="button" class="btn btn-success" id="toggleView">
                <i class="fa fa-book"></i> Ledger
            </button>
        </div>

        {{-- SUMMARY --}}
        <div id="summaryView">

            @if(!$data)
                <p>No data available or failed to fetch.</p>
            @else

                <div class="row" style="margin-bottom: 25px;">

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ number_format($data['total_uncollected']) }}</h3>
                                <p>Cycle Opening Uncollected</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-warning"></i>
                            </div>
                        </div>
                    </div>

                            <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3>{{ number_format($data['uncollected_without_charges']) }}</h3>
                                <p>Cycle Opening Uncollected (without charges)</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-warning"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>{{ number_format($data['total_collected']) }}</h3>
                                <p>Total Collected</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                        <h3>{{ number_format(max(0, $data['still_uncollected'])) }}</h3>
                                <p>Still Uncollected</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>{{ number_format($data['given_out']) }}</h3>
                                <p>Given Out</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>


                        <div class="col-md-4 col-sm-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>{{ number_format($data['carry_over'] ?? 0) }}</h3>
                                <p>Carry Over</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>

        {{-- LEDGER VIEW --}}
        <div id="ledgerView" style="display: none;">

            {{-- TOGGLE SWITCH --}}
            <div class="ledger-toggle text-center">
                <div class="toggle-wrapper">
                    <div class="toggle-slider"></div>

                    <button class="toggle-btn active" data-target="collections">
                        Cycle Opening Uncollected
                    </button>
                    <button class="toggle-btn" data-target="disbursements">
                        Total Cycle Collected
                    </button>
                    <button class="toggle-btn" data-target="adjustments">
                        Total Cycle Given Out
                    </button>
                </div>
            </div>

            {{-- LEDGER SECTIONS --}}

            {{-- Collections --}}
            <div class="ledger-section" id="collections">
                <p class="text-muted text-center">Cycle Opening Uncollected</p>
                    <p class="text-muted text-center" style="margin-top: 8px;">
            <i class="fa fa-info-circle"></i>
These are the balances of all your loans as of  {{ date("jS M, Y", strtotime($start)) }}. Please note that any charges do not increase your uncollected balance, while loans with interest waivers reduce the uncollected amount accordingly.
        </p>

                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-bordered table-striped" id="cycleOpeningTable">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client Name</th>
                                <th>Amount Due</th>
                                <th>Balance</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Disbursements --}}
            <div class="ledger-section" id="disbursements" style="display:none;">
                <p class="text-muted text-center">Total Cycle Collected</p>

                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-bordered table-striped" id="totalCollectedTable">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client Name</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Adjustments --}}
            <div class="ledger-section" id="adjustments" style="display:none;">
                <p class="text-muted text-center">Total Cycle Given Out</p>

                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-bordered table-striped" id="givenOutTable">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client Name</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>

  <div style="margin-bottom:30px; margin-top:30px;">
                    <p
                        style="display: flex;
                                                                                                                                                                                                            align-items: center;
                                                                                                                                                                                                            justify-content: center; font-size:50px;">
                        PDUA%
                    </p>
                    <div
                        style="display: flex;
                                                                                                                                                                                                            align-items: center;
                                                                                                                                                                                                            justify-content: center;">

                        <div class="gauge"
                            style="width: 100%;
                                                                                                                                                                                                          max-width: 250px;
                                                                                                                                                                                                          font-size: 50px;
                                                                                                                                                                                                          color: #004033;">
                            <div class="gauge__body"
                                style=" width: 100%;
                                                                                                                                                                                                          height: 0;
                                                                                                                                                                                                          padding-bottom: 50%;
                                                                                                                                                                                                          background: #b4c0be;
                                                                                                                                                                                                          position: relative;
                                                                                                                                                                                                          border-top-left-radius: 100% 200%;
                                                                                                                                                                                                          border-top-right-radius: 100% 200%;
                                                                                                                                                                                                          overflow: hidden;">

                                @if(($data['pdua']) < 0.75)
                                    <div class="gauge__fill"
                                        style=" position: absolute;
                                                                                                                                                                                                                                                                                          top: 100%;
                                                                                                                                                                                                                                                                                          left: 0;
                                                                                                                                                                                                                                                                                          width: inherit;
                                                                                                                                                                                                                                                                                          height: 100%;
                                                                                                                                                                                                                                                                                          background: red;
                                                                                                                                                                                                                                                                                          transform-origin: center top;
                                                                                                                                                                                                                                                                                          transform: rotate(0.25turn);
                                                                                                                                                                                                                                                                                          transition: transform 0.2s ease-out;">
                                    </div>

                                @elseif(($data['pdua']) >= 0.90)
                                    <div class="gauge__fill"
                                        style=" position: absolute;
                                                                                                                                                                                                                                                                                          top: 100%;
                                                                                                                                                                                                                                                                                          left: 0;
                                                                                                                                                                                                                                                                                          width: inherit;
                                                                                                                                                                                                                                                                                          height: 100%;
                                                                                                                                                                                                                                                                                          background:#d4af37;
                                                                                                                                                                                                                                                                                          transform-origin: center top;
                                                                                                                                                                                                                                                                                          transform: rotate(0.25turn);
                                                                                                                                                                                                                                                                                          transition: transform 0.2s ease-out;">
                                    </div>

                                @else
                                    <div class="gauge__fill"
                                        style=" position: absolute;
                                                                                                                                                                                                                                                                                          top: 100%;
                                                                                                                                                                                                                                                                                          left: 0;
                                                                                                                                                                                                                                                                                          width: inherit;
                                                                                                                                                                                                                                                                                          height: 100%;
                                                                                                                                                                                                                                                                                          background:green;
                                                                                                                                                                                                                                                                                          transform-origin: center top;
                                                                                                                                                                                                                                                                                          transform: rotate(0.25turn);
                                                                                                                                                                                                                                                                                          transition: transform 0.2s ease-out;">
                                    </div>

                                @endif
                                <div class="gauge__cover"
                                    style="width: 75%;
                                                                                                                                                                                                          height: 150%;
                                                                                                                                                                                                          background: #f7f7f7;
                                                                                                                                                                                                          border-radius: 50%;
                                                                                                                                                                                                          position: absolute;
                                                                                                                                                                                                          top: 25%;
                                                                                                                                                                                                          left: 50%;
                                                                                                                                                                                                          transform: translateX(-50%);

                                                                                                                                                                                                          /* Text */
                                                                                                                                                                                                          display: flex;
                                                                                                                                                                                                          align-items: center;
                                                                                                                                                                                                          justify-content: center;
                                                                                                                                                                                                          padding-bottom: 25%;
                                                                                                                                                                                                          box-sizing: border-box;">
                                </div>

                            </div>
                        </div>


                    </div>

                    <div
                        style="display:flex; flex-direction:row; justify-content:space-between;
                                                                                                                                                                                                            align-items: center;
                                                                                                                                                                                                            justify-content: center;">
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div
                                style="background-color: red;  height: 10px;
                                                                                                                                                                                                          width: 20px;">
                            </div>
                            <p style="text-align: center; font-weight:bold;">Poor</p>
                        </div>

                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div
                                style="background-color: green;  height: 10px;
                                                                                                                                                                                                          width: 20px;">
                            </div>
                            <p style="text-align: center; font-weight:bold;">Fair</p>
                        </div>
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div
                                style="background-color: #d4af37;  height: 10px;
                                                                                                                                                                                                          width: 20px;">
                            </div>
                            <p style="text-align: center; font-weight:bold;">Good</p>
                        </div>

                    </div>
                </div>

</div>
@endsection
@section('footer-scripts')
<script>

              $(document).ready(function () {

    var showingLedger = false;

    $('#toggleView').on('click', function () {

        if (!showingLedger) {
            $('#summaryView').hide();
            $('#ledgerView').show();
            $(this).html('<i class="fa fa-bar-chart"></i> Summary');

            // Fetch initial collections table
            fetchCycleOpeningTable();

        } else {
            $('#ledgerView').hide();
            $('#summaryView').show();
            $(this).html('<i class="fa fa-book"></i> Ledger');
        }

        showingLedger = !showingLedger;
    });

    $('.toggle-btn').on('click', function () {

        var target = $(this).data('target');

        // Toggle active button
        $('.toggle-btn').removeClass('active');
        $(this).addClass('active');

        // Move slider
        $('.toggle-wrapper').attr('data-active', target);

        // Show correct section
        $('.ledger-section').hide();
        $('#' + target).fadeIn(200);

        // Fetch data for specific section
        if(target === 'collections') {
            fetchCycleOpeningTable();
        }
        if(target === 'disbursements') {
            fetchTotalCollectedTable();
        }
        if(target === 'adjustments') {
            fetchGivenOutTable();
        }
    });

    // --- FETCH FUNCTIONS ---

   function fetchCycleOpeningTable() {
    var $tableBody = $('#cycleOpeningTable tbody');
    $tableBody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

    $.ajax({
        url: 'https://lms2backend.whencefinancesystem.com/cycle-opening-uncollected-table',
        method: 'GET',
        data: {
            user_id: '{{ $userId }}',
            start_date: '{{ $start }}',
            end_date: '{{ $end }}'
        },
        success: function(response) {
            $tableBody.empty();

            if (!response.loans_uncollected || response.loans_uncollected.length === 0) {
                $tableBody.html('<tr><td colspan="5" class="text-center">No uncollected loans</td></tr>');
                return;
            }

            response.loans_uncollected.forEach(function(loan) {
                $tableBody.append(`
                    <tr>
                        <td>${loan.loan_id}</td>
                        <td>${loan.client_name}</td>
                        <td>${Number(loan.amount_due).toLocaleString()}</td>
                        <td>${Number(loan.balance).toLocaleString()}</td>
                        <td>${loan.due_date ? new Date(loan.due_date).toISOString().slice(0, 10) : '-'}</td>

                    </tr>
                `);
            });
        },
        error: function(err) {
            $tableBody.html('<tr><td colspan="5" class="text-center text-danger">Failed to load data</td></tr>');
            console.error(err);
        }
    });
}

function fetchTotalCollectedTable() {
    var $tableBody = $('#totalCollectedTable tbody');
    $tableBody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

    $.ajax({
        url: 'https://lms2backend.whencefinancesystem.com/total-collected-table',
        method: 'GET',
        data: {
            user_id: '{{ $userId }}',
            start_date: '{{ $start }}',
            end_date: '{{ $end }}'
        },
        success: function(response) {
            $tableBody.empty();

            if (!response.collected_transactions || response.collected_transactions.length === 0) {
                $tableBody.html('<tr><td colspan="4" class="text-center">No collected transactions</td></tr>');
                return;
            }

            response.collected_transactions.forEach(function(tx) {
                $tableBody.append(`
                    <tr>
                        <td>${tx.loan_id}</td>
                        <td>${tx.client_name}</td>
                        <td>${tx.transaction_type}</td>
                        <td>${Number(tx.amount).toLocaleString()}</td>
                    </tr>
                `);
            });
        },
        error: function(err) {
            $tableBody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>');
            console.error(err);
        }
    });
}

function fetchGivenOutTable() {
    var $tableBody = $('#givenOutTable tbody');
    $tableBody.html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

    $.ajax({
        url: 'https://lms2backend.whencefinancesystem.com/given-out-table',
        method: 'GET',
        data: {
            user_id: '{{ $userId }}',
            start_date: '{{ $start }}',
            end_date: '{{ $end }}'
        },
        success: function(response) {
            $tableBody.empty();

            if (!response.given_out_breakdown || response.given_out_breakdown.length === 0) {
                $tableBody.html('<tr><td colspan="4" class="text-center">No given out transactions</td></tr>');
                return;
            }

            response.given_out_breakdown.forEach(function(tx) {
                $tableBody.append(`
                    <tr>
                        <td>${tx.loan_id !== null ? tx.loan_id : '-'}</td>
                        <td>${tx.client_name}</td>
                        <td>${tx.transaction_type}</td>
                        <td>${Number(tx.amount).toLocaleString()}</td>
                    </tr>
                `);
            });
        },
        error: function(err) {
            $tableBody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load data</td></tr>');
            console.error(err);
        }
    });
}


});

   const gaugeElement = document.querySelector(".gauge");

                function setGaugeValue(gauge, value) {
                    if (value < 0 || value > 1) {
                        return;
                    }

                    gauge.querySelector(".gauge__fill").style.transform = `rotate(${value / 2
                        }turn)`;
                    gauge.querySelector(".gauge__cover").textContent = `${Math.round(
                        value * 100
                    )}%`;
                }


    setGaugeValue(gaugeElement, "{{ $data['pdua'] }}");
</script>
@endsection

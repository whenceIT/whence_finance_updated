
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> @yield('title')</title>
    @laravelPWA
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src='https://cdn.plot.ly/plotly-2.24.1.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    </link>


    <style>

       
    .ledger-toggle {
        margin: 20px 0 30px;
    }

    .toggle-wrapper {
        position: relative;
        display: inline-flex;
        background: #f4f6f9;
        border-radius: 30px;
        padding: 4px;
        box-shadow: inset 0 0 0 1px #ddd;
    }

    .toggle-btn {
        position: relative;
        z-index: 2;
        background: none;
        border: none;
        padding: 8px 20px;
        cursor: pointer;
        font-weight: 600;
        color: #555;
        outline: none;
    }

    .toggle-btn.active {
        color: #fff;
    }

    .toggle-slider {
        position: absolute;
        top: 4px;
        left: 4px;
        width: 33.333%;
        height: calc(100% - 8px);
        background: #00a65a;
        border-radius: 25px;
        transition: transform 0.3s ease;
        z-index: 1;
    }

    .toggle-wrapper[data-active="disbursements"] .toggle-slider {
        transform: translateX(100%);
    }

    .toggle-wrapper[data-active="adjustments"] .toggle-slider {
        transform: translateX(200%);
    }

    .ledger-section {
        padding: 30px 0;
    }


        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            transition: opacity 0.75s, visibility 0.75s;
            z-index: 99999;

        }

        #loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes modalFadeIn {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Bottom Sheet Modal Styles */
        .bottom-sheet-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99998;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .bottom-sheet-overlay.active {
            display: flex;
            opacity: 1;
        }

        .bottom-sheet {
            position: fixed;
            bottom: -100%;
            left: 0;
            width: 100%;
            background: white;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -5px 30px rgba(0, 0, 0, 0.3);
            z-index: 99999;
            transition: bottom 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            max-height: 80vh;
            overflow-y: auto;
        }

        .bottom-sheet.active {
            bottom: 0;
        }

        .bottom-sheet-handle {
            width: 50px;
            height: 5px;
            background: #ddd;
            border-radius: 3px;
            margin: 15px auto;
        }

        .bottom-sheet-content {
            padding: 20px 30px 30px 30px;
        }

        .bottom-sheet-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .bottom-sheet-description {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .bottom-sheet-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #00a04a;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .bottom-sheet-btn:hover {
            background: #008a3f;
        }

        .bottom-sheet-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: #999;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .bottom-sheet-close:hover {
            color: #333;
        }
    </style>
    <!-- Theme style -->

    <link href="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/amcharts/plugins/export/export.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/datatables.net/extensions/Buttons/css/buttons.dataTables.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables.net/extensions/Buttons/css/buttons.bootstrap.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/fancybox/jquery.fancybox.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datepicker/bootstrap-datepicker3.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/icheck/square/blue.css') }}" rel="stylesheet" type="text/css" />
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="{{ asset('assets/themes/adminlte/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('assets/themes/adminlte/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/themes/adminlte/css/custom.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery 2.2.3 -->

    
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jqueryui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    {{--Start Page header level scripts--}}
    @yield('page-header-scripts')
    {{--End Page level scripts--}}
</head>
<div class="box box-primary">

    {{-- VERIFICATION HEADER --}}
    <div class="box-header with-border text-center" style="background:#f9fafc;">
        <h3 class="box-title" style="font-weight:600;">
            Loan Consultant Numbers Verification
        </h3>
        <p class="text-muted" style="margin-top:8px; font-size:13px;">
            Please review your performance figures and ledger breakdown below.
            Confirm whether these numbers are accurate before proceeding.
        </p>
    </div>

    {{-- ORIGINAL HEADER --}}
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

   {{-- VERIFICATION NOTICE --}}
<div class="callout callout-info text-center" style="margin-bottom:20px;">
    <i class="fa fa-check-circle"></i>
    <strong>Please verify your numbers carefully.</strong>
    <br>
    Review the summary figures and use the ledger breakdown to confirm accuracy.
    <br><br>
    <i class="fa fa-video-camera"></i>
    <a href="PASTE_VIDEO_LINK_HERE" target="_blank" style="font-weight:600;">
        Watch this short video on how to verify and correct your numbers if they are wrong
    </a>
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
                                <h3>{{ number_format($data['still_uncollected']) }}</h3>
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
                                <h3>{{ number_format($data['carry_over']) }}</h3>
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

            {{-- Collections --}}
            <div class="ledger-section" id="collections">
                <p class="text-muted text-center">Cycle Opening Uncollected</p>
                <p class="text-muted text-center" style="margin-top: 8px;">
                    <i class="fa fa-info-circle"></i>
                    These are the balances of all your loans as of {{ date("jS M, Y", strtotime($start)) }}.
                    Please note that any charges do not increase your uncollected balance, while loans with
                    interest waivers reduce the uncollected amount accordingly.
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

        {{-- VERIFICATION ACTIONS --}}
        <hr>

      <div class="row" style="margin-top:25px;">
    <div class="col-xs-12 text-center">

        <h4 style="margin-bottom:20px; font-weight:600;">
            Are these numbers correct?
        </h4>

        <div class="row">

            <div class="col-xs-12 col-sm-6" style="margin-bottom:15px;">
                <a href="{{ url('user/verify_numbers') }}"
                   class="btn btn-success btn-lg btn-block">
                    <i class="fa fa-check"></i>
                    Yes, my numbers are correct
                </a>
            </div>

            <div class="col-xs-12 col-sm-6" style="margin-bottom:15px;">
                <a href="{{ url('ticket/create_dashboard_ticket') }}"
                   class="btn btn-danger btn-lg btn-block">
                    <i class="fa fa-times"></i>
                    No, my numbers are not correct
                </a>
            </div>

        </div>

    </div>
</div>


    </div>
</div>


 <script>
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

 </script>

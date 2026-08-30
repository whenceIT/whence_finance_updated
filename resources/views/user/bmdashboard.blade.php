@extends('layouts.master')

@section('title')
    Branch Manager Dashboard
@endsection

@section('content')

@php
// ================= MOCK DATA =================

function totalAmount($transactions) {
    return array_sum(array_map(fn($t) => $t['amount'] ?? 0, $transactions));
}


@endphp

@include('components.advance-deduction-banner')

<!-- ================= DAILY PERFORMANCE CARD ================= -->
<div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:0;">
    <div style="padding:14px 18px; border-bottom:1px solid #f1f1f1; background:#fff;">
        <h3 style="margin:0; font-size:18px; font-weight:600;">
            Daily {{ $branch['name'] ?? 'Branch' }} Performance Summary
        </h3>
        <small style="color:#8b929a;">
            Click to view category totals, then click a category to view transactions
        </small>
    </div>

    @php
        $branchTransactions = $branch['transactions'] ?? [];
        $fullPaymentsTotal = totalAmount($branchTransactions['full_payments'] ?? []);
        $reloansTotal = totalAmount($branchTransactions['reloans'] ?? []);
        $partPaymentsTotal = totalAmount($branchTransactions['part_payments'] ?? []);
        $newLoansTotal = totalAmount($branchTransactions['new_loans'] ?? []);
        $expensesTotal = totalAmount($branchTransactions['expenses'] ?? []);
        $advancesTotal = totalAmount($branchTransactions['advances'] ?? []);
        $grandTotal = $fullPaymentsTotal + $reloansTotal + $partPaymentsTotal + $newLoansTotal + $expensesTotal + $advancesTotal;
    @endphp

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr style="background:#f8f9fb; font-size:12px; text-transform:uppercase;">
                    <th style="width:40px;"></th>
                    <th>Branch</th>
                    <th>Collections</th>
                    <th>Loans</th>
                    <th>Cash C/F</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr class="main-row" data-index="0" style="cursor:pointer;">
                    <td style="text-align:center;"><i class="fa fa-plus toggle-icon"></i></td>
                    <td><strong>{{ $branch['name'] ?? 'Branch' }}</strong></td>
                    <td style="color:#28a745;">K{{ number_format($branch['collections'] ?? 0, 2) }}</td>
                    <td style="color:#007bff;">K{{ number_format($branch['new_loans'] ?? 0, 2) }}</td>
                    <td>-</td>
                    <td><strong>K{{ number_format($grandTotal, 2) }}</strong></td>
                </tr>

                <!-- DETAIL ROW -->
                <tr class="detail-row" id="detail-0" style="display:none; background:#f8fafc;">
                    <td colspan="6">
                        <div style="padding:18px;">
                            <div style="background:#fff; border:1px solid #edf1f5; border-radius:10px; overflow:hidden;">
                                <div style="padding:14px 16px; border-bottom:1px solid #f1f3f5;">
                                    <strong>{{ strtoupper($branch['name'] ?? 'Branch') }} TRANSACTION SUMMARY</strong>
                                </div>
                                <div style="padding:16px;">
                                    @php
                                        $categories = [
                                            'full_payments' => ['Full Payments', $fullPaymentsTotal],
                                            'reloans' => ['Reloans', $reloansTotal],
                                            'part_payments' => ['Part Payments', $partPaymentsTotal],
                                            'new_loans' => ['New Loans', $newLoansTotal],
                                            'expenses' => ['Expenses', $expensesTotal],
                                            'advances' => ['Advances', $advancesTotal],
                                        ];
                                    @endphp

                                    @foreach($categories as $key => [$label, $total])
                                        <div class="category-row"
                                             data-target="cat-0-{{ $key }}"
                                             style="cursor:pointer; margin-bottom:10px; padding:12px; border:1px solid #eee; border-radius:8px; display:flex; justify-content:space-between;">
                                            <strong>{{ $label }}</strong>
                                            <div>
                                                <span style="font-weight:700;">K{{ number_format($total, 2) }}</span>
                                                <i class="fas fa-caret-right sub-toggle-icon"></i>
                                            </div>
                                        </div>

                                        <div id="cat-0-{{ $key }}" class="sub-detail" style="display:none; margin-bottom:12px;">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($branch['transactions'][$key] ?? [] as $txn)
                                                        <tr>
                                                            <td>{{ $txn['name'] }}</td>
                                                            <td>{{ $txn['date'] }}</td>
                                                            <td>K{{ number_format($txn['amount'],2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">No {{ strtolower($label) }} found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<!-- ================= FILTER ================= -->

<section class="content">
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Period</h3>
    </div>
    <div class="box-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="form-control">
            </div>
            <div class="col-md-4" style="margin-top:25px;">
                <button class="btn btn-primary">
                    <i class="fa fa-search"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= BRANCH SUMMARY ================= -->
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-building"></i> {{ $branch['office'] ?? ($branch['name'] ?? 'Branch') }} Performance
        </h3>
    </div>
    <div class="box-body">
        <!-- KPI CARDS -->
     <div class="row" style="margin-bottom:20px;">

       <div class="text-center" style="margin-bottom: 20px;">
            <button type="button" class="btn btn-success" id="toggleView">
                <i class="fa fa-book"></i> Ledger
            </button>
        </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#4e73df; color:#fff;">
            <div class="inner">
                <h3>{{ number_format($branch_data['total_uncollected'] ?? 0,2) }}</h3>
                <p>Branch Cycle Opening Uncollected</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#1cc88a; color:#fff;">
            <div class="inner">
                <h3>{{ number_format($branch_data['total_collected'] ?? 0,2) }}</h3>
                <p>Branch Total Cycle Collected</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#f6c23e; color:#fff;">
            <div class="inner">
                <h3>{{ number_format($branch_data['given_out'] ?? 0,2)}}</h3>
                <p>Branch Given Out</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#e74a3b; color:#fff;">
            <div class="inner">
                <h3>{{ number_format($branch_data['still_uncollected'] ?? 0,2) }}</h3>
                <p>Branch Still Uncollected</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#36b9cc; color:#fff;">
            <div class="inner">
                <h3>{{$branch_data['consultants_count'] ?? 0}}</h3>
                <p>Branch Staff</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#858796; color:#fff;">
            <div class="inner">
                <h3>{{$branch_data['targets_met_count'] ?? 0}}</h3>
                <p>Branch Targets Met</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#5a5c69; color:#fff;">
            <div class="inner">
                <h3>{{ number_format(($branch_data['efficiency'] ?? 0)*100,2) }}%</h3>
                <p>Branch Efficiency</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box" style="background:#fd7e14; color:#fff;">
            <div class="inner">
                <h3>{{ number_format(($branch_data['pdua'] ?? 0)*100,2) }}%</h3>
                <p>Branch PDUA</p>
            </div>
        </div>
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
These are the balances of all branch loans as of  {{ date("jS M, Y", strtotime($start_date)) }}. Please note that any charges do not increase branch uncollected balance, while loans with interest waivers reduce the uncollected amount accordingly.
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

        <!-- CONSULTANTS TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead style="background:#f4f4f4;">
                    <tr>
                          <th>Name</th>
                                                <th>Cycle Start Uncollected</th>
                                                <th>Total Cycle Collected</th>
                                                <th>Still Uncollected</th>
                                                <th>Total Cycle Given Out</th>
                                                <th>Carry Over</th>
                                                <th>PDUA</th>
                                                <th>Target Met</th>
                                                <th>Target History</th>
                                                <th>Cycle Ends On</th>
                                                <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultants as $c)
                        <tr>
                            <td><strong>{{ $c['name'] }}</strong></td>
                            <td>{{ number_format($c['total_uncollected'],2) }}</td>
                            <td>{{ number_format($c['total_collected'],2) }}</td>
                            <td>{{ number_format($c['still_uncollected'],2) }}</td>
                            <td>{{ number_format($c['given_out'],2) }}</td>
                            <td>{{ number_format($c['carry_over'],2) }}</td>
                            <td><span class="label label-info">{{ number_format($c['pdua']*100,2) }}%</span></td>
                            <td>
                                @if($c['target_met_current'])
                                    <span class="label label-success">Met</span>
                                @else
                                    <span class="label label-danger">Not Met</span>
                                @endif
                            </td>
                            <td>
                                @foreach($c['target_history'] as $h)
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $h ? '#00a65a' : '#dd4b39' }};margin-right:3px;"></span>
                                @endforeach
                            </td>
                            <td>{{ $c['cycle_end_on'] }}</td>
                 <td>
    <a href="{{ url('user/' . $c['user_id'] . '/staff_info') }}" class="text-primary">
        View
    </a>
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

 <!-- Short cut tools to: Loans, Collateral, Clients -->
@include('components.dashboard-shortcuts')
</section>

@include('components.policy-of-the-day')
@php
    $blockerUser = Sentinel::getUser();
    $debtBlocker = \App\Helpers\BlockerHelper::debt_blocker($blockerUser);
    $deadline = \App\Models\Deadline::first();
    $deadlineName = isset($deadline) ? $deadline->name : 'Building Deposit';
    $deadlineDateValue = isset($deadline) && $deadline->countdown_date ? \Carbon\Carbon::parse($deadline->countdown_date)->format('Y-m-d\TH:i') : '';
@endphp

@include('components.deposit-deadline-modal')

@if($debtBlocker)
    @include('components.setup-debt-reminder')
@endif
@endsection


@if($office && is_null($office->workstations) && is_null($office->recruited))

<div class="modal fade" id="officeSetupModal"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     data-keyboard="false">

    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-building"></i>
                    Branch Information
                </h4>
            </div>

            <div class="modal-body">

                <p>
                    Please provide the following information about your branch.
                </p>

                <div class="form-group">
                    <label>
                        How many workstations are assigned to your branch?
                    </label>

                    <input
                        type="number"
                        id="workstations"
                        class="form-control"
                        min="0"
                        step="1"
                        placeholder="Enter number of workstations"
                    >
                </div>

                <div class="form-group">
                    <label>
                        Has your branch recruited loan consultants?
                    </label>

                    <select id="recruited" class="form-control">
                        <option value="">-- Select --</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div id="officeSetupError"
                     class="alert alert-danger"
                     style="display:none;">
                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-primary"
                    id="saveOfficeInformation">

                    <i class="fa fa-save"></i>
                    Save Information

                </button>

            </div>

        </div>
    </div>
</div>

@endif

@section('footer-scripts')
<style>
.small-box { border-radius: 10px; }
.small-box .inner h3 { font-size: 22px; }
.table td, .table th { vertical-align: middle !important; }
</style>

<script>
$(function () {
    $('.main-row').on('click', function () {
        const detail = $('#detail-0');
        const icon = $(this).find('.toggle-icon');
        if (detail.is(':visible')) {
            detail.hide();
            icon.removeClass('fa-minus').addClass('fa-plus');
        } else {
            detail.show();
            icon.removeClass('fa-plus').addClass('fa-minus');
        }
    });

    $('.category-row').on('click', function () {
        const target = $('#' + $(this).data('target'));
        const icon = $(this).find('.sub-toggle-icon');
        target.toggle();
        if (target.is(':visible')) {
            icon.css('transform', 'rotate(90deg)');
        } else {
            icon.css('transform', 'rotate(0deg)');
        }
    });
});

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
        url: 'https://lms2backend.whencefinancesystem.com/branch-cycle-opening-uncollected-table',
        method: 'GET',
        data: {
            office_id: '{{ $office_id }}',
            start_date: '{{ $start_date }}',
            end_date: '{{ $end_date }}'
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
        url: 'https://lms2backend.whencefinancesystem.com/branch-total-collected-table',
        method: 'GET',
        data: {
            office_id: '{{ $office_id }}',
            start_date: '{{ $start_date }}',
            end_date: '{{ $end_date }}'
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
        url: 'https://lms2backend.whencefinancesystem.com/branch-given-out-table',
        method: 'GET',
        data: {
            office_id: '{{ $office_id }}',
            start_date: '{{ $start_date }}',
            end_date: '{{ $end_date }}'
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


$(document).ready(function () {

    // The modal only exists when both office fields are NULL,
    // so if it exists, show it.
    if ($('#officeSetupModal').length) {
        $('#officeSetupModal').modal('show');
    }


    $('#saveOfficeInformation').on('click', function () {

        const workstations = $('#workstations').val();
        const recruited = $('#recruited').val();

        $('#officeSetupError').hide().text('');


        if (workstations === '') {

            $('#officeSetupError')
                .text('Please enter the number of workstations.')
                .show();

            return;
        }


        if (parseInt(workstations) < 0) {

            $('#officeSetupError')
                .text('The number of workstations cannot be negative.')
                .show();

            return;
        }


        if (recruited === '') {

            $('#officeSetupError')
                .text('Please indicate whether the branch has recruited.')
                .show();

            return;
        }


        const button = $(this);

        button.prop('disabled', true);

        button.html(
            '<i class="fa fa-spinner fa-spin"></i> Saving...'
        );


        $.ajax({

            url: "{{ route('office.setup.update') }}",

            method: "POST",

            data: {
                _token: "{{ csrf_token() }}",
                workstations: workstations,
                recruited: recruited
            },

            success: function (response) {

                $('#officeSetupModal').modal('hide');

                location.reload();

            },

            error: function (xhr) {

                let message =
                    'Unable to save branch information.';

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {
                    message = xhr.responseJSON.message;
                }

                $('#officeSetupError')
                    .text(message)
                    .show();

                button.prop('disabled', false);

                button.html(
                    '<i class="fa fa-save"></i> Save Information'
                );
            }

        });

    });

});

</script>
@endsection
@extends('layouts.master')
@section('title')
    Dashboard
@endsection

@section('content')
<style>
    /* Policy of the Day - Modern Professional Design */
    .policy-of-the-day-container {
        position: fixed;
        top: 400px;
        right: 20px;
        width: 380px;
        z-index: 1050;
        opacity: 1;
        transform: translateX(0);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 10px 25px rgba(0,0,0,0.15));
    }

    .policy-of-the-day-container.hidden {
        opacity: 0;
        transform: translateX(420px);
        pointer-events: none;
    }

    .policy-of-the-day-container:hover {
        transform: translateX(-10px);
    }

    .policy-of-the-day-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .policy-of-the-day-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .policy-of-the-day-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="stars" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.6)"/><circle cx="80" cy="60" r="0.8" fill="rgba(255,255,255,0.4)"/><circle cx="60" cy="90" r="0.6" fill="rgba(255,255,255,0.5)"/></pattern></defs><rect width="100" height="100" fill="url(%23stars)"/></svg>');
        opacity: 0.4;
    }

    .policy-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.9);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #667eea;
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .policy-title-section {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .policy-title-section h5 {
        margin: 0;
        color: white;
        font-size: 16px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .policy-subtitle {
        margin: 2px 0 0 0;
        color: rgba(255,255,255,0.9);
        font-size: 12px;
        font-weight: 400;
    }

    .policy-of-the-day-body {
        padding: 20px;
        color: #333;
    }

    .policy-content h4 {
        margin: 0 0 12px 0;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.3;
        color: #2c3e50;
    }

    .policy-content p {
        margin: 0 0 16px 0;
        font-size: 14px;
        line-height: 1.5;
        color: #555;
    }

    .policy-details {
        margin-top: 16px;
        border-top: 1px solid rgba(255,255,255,0.2);
        padding-top: 16px;
    }

    .policy-details summary {
        cursor: pointer;
        color: #667eea;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s ease;
    }

    .policy-details summary:hover {
        color: #5a67d8;
    }

    .policy-details summary::marker {
        color: #667eea;
    }

    .policy-details p {
        margin: 12px 0 0 0;
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.5;
        border-left: 3px solid #667eea;
        color: #333;
    }

    .policy-actions {
        margin-top: 16px;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .policy-actions a {
        color: #667eea;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 6px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .policy-actions a:hover {
        color: #5a67d8;
        background: #e9ecef;
        border-color: #667eea;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }

    .policy-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(255,255,255,0.9);
        border: 1px solid #e9ecef;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        color: #6c757d;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .policy-close:hover {
        background: #f8f9fa;
        color: #dc3545;
        border-color: #dc3545;
        transform: scale(1.1);
    }

    /* Pulse animation for new policy */
    @keyframes policyPulse {
        0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
        100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
    }

    .policy-new-pulse {
        animation: policyPulse 2s infinite;
    }

    /* Floating animation */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }

    .policy-floating {
        animation: float 3s ease-in-out infinite;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .policy-of-the-day-container {
            width: 320px;
            right: 10px;
            top: 80px;
        }

        .policy-of-the-day-container:hover {
            transform: translateX(-5px);
        }

        .policy-of-the-day-container.hidden {
            transform: translateX(340px);
        }
    }
</style>

    @if(session('msg'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('msg') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    @include('components.policy-of-the-day')

    <!-- What Clients see -->

    @if($role->role_id == '2')
        <div>
            @if(!($clientLoan))
                <div class="row">
                    <div class="col-lg-4 col-xs-12">
                        <p style="text-align: center; font-weight:bold; ">Name: {{$user->first_name}} {{$user->last_name}}</p>
                    </div>
                    <div class="col-lg-4 col-xs-12">
                        <p style="text-align: center; font-weight:bold;">Branch: {{$clientBranch->name}}</p>
                    </div>
                    <div class="col-lg-4 col-xs-12">
                        <p style="text-align: center; font-weight:bold;">Loan Consultant: {{$staff->first_name}}
                            {{$staff->last_name}}
                        </p>
                    </div>

                    <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <p style="font-weight: bold;">Outstanding balance</p>
                                <div class="icon">
                                    <i class="fa fa-usd"></i>
                                </div>
                                <h3>0.00</h3>
                            </div>
                            <div class="small-box-footer">
                                <p></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p style="font-weight: bold;">Due date</p>
                                <div class="icon">
                                    <i class="fa fa-calendar-o"></i>
                                </div>
                                <h3>-</h3>
                            </div>
                            <div class="small-box-footer">
                                <p></p>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <p style="font-weight: bold;">Paid</p>
                                <div class="icon">
                                    <i class="fa fa-usd"></i>
                                </div>
                                <h3>0.00</h3>
                            </div>
                            <div class="small-box-footer">
                                <p></p>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="row">
                    <div class="col-lg-4 col-xs-12">
                        <p style="text-align: center; font-weight:bold; ">Name: {{$user->first_name}} {{$user->last_name}}</p>
                    </div>
                    <div class="col-lg-4 col-xs-12">
                        <p style="text-align: center; font-weight:bold;">Branch: {{$clientBranch->name}}</p>
                    </div>
                    <div class="col-lg-4 col-xs-12">
                        <p style="text-align: center; font-weight:bold;">Loan Consultant: {{$staff->first_name}}
                            {{$staff->last_name}}
                        </p>
                    </div>
                    <?php 
                                                                                                                                                                                                    $balance = 0;
                    ;
                    $in = 0;
                    $out = 0;
                                                                                                                                                                                                    ?>
                    @foreach($clientLoan->transactions as $transaction)
                        <?php 
                                                                                                                                                                                                                                                                            if ($transaction->transaction_type != 'specified_due_date_fee') {
                                $out = $out + $transaction->debit;
                            }

                            $in = $in + $transaction->credit;
                                                                                                                                                                                                                                                                            ?>
                    @endforeach
                    <?php 
                                                                                                                                                                                                    $balance = $out - $in;
                                                                                                                                                                                                    ?>

                    <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <p style="font-weight: bold;">Outstanding balance</p>
                                <div class="icon">
                                    <i class="fa fa-usd"></i>
                                </div>
                                <h3>{{number_format($balance, 2)}}</h3>
                            </div>
                            <div class="small-box-footer">
                                <p></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <p style="font-weight: bold;">Due date</p>
                                <div class="icon">
                                    <i class="fa fa-calendar-o"></i>
                                </div>
                                <h3>{{date("jS M, Y", strtotime($clientLoan->expected_first_repayment_date))}}</h3>
                            </div>
                            <div class="small-box-footer">
                                <p></p>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <p style="font-weight: bold;">Paid</p>
                                <div class="icon">
                                    <i class="fa fa-usd"></i>
                                </div>
                                <h3>{{number_format($in, 2)}}</h3>
                            </div>
                            <div class="small-box-footer">
                                <p></p>
                            </div>
                        </div>
                    </div>

                </div>

            @endif


        </div>
    @endif


    <!-- What Loan Consultants see -->
    @if($role->role_id == '3')



        <!-- Default Dashboard -->
        <div id='mydivon' style="display:block">
            @if($end == 'NCI')
                <div
                    style="display: flex;
                                                                                                                                                                                                    align-items: center;
                                                                                                                                                                                                    justify-content: center;">
                    <a href="{{ url('user/cycle') }}" class="btn btn-info btn-sm">
                        Set your cycle end date
                    </a>
                </div>
            @else

                <!-- first row -->
                <div class="row">
                    <!-- cycle countdown -->
                    <?php
                    $MoneyGivenOut = 0;
                    $MoneyCollected = 0;
                    $reloan_amount = 0;
                    $cycle_opening_uncollected_amount = 0.0001;
                    $full_payments = 0;
                    $part_payments = 0;
                    $reloan_payments = 0;

                                                                                                                                                                                                    ?>

                    <!-- CALCULATING LOAN BALANCES FOR CYCLE OPENING UNCOLLECTED -->
                    <?php
                    $today = date('Y-m-d');
                    $currrent_date = date('Y-m');
                    $cycle_date = $currrent_date . '-' . $cycle_end;
                    $cycle_date = date('Y-m-d', strtotime($cycle_date));
                    $cycle_date = date('Y-m-d', strtotime($cycle_date . ' + 1 day'));
                    if ($today > $cycle_date) {
                        $cycle_date = date('Y-m-d', strtotime($cycle_date . '+ 1 months'));
                    }

                    $cycle_start = date('Y-m-d', strtotime($cycle_date . '- 1 months'));
                    $cycle_opening_uncollected_amounts = [];
                    for ($x = 12; $x > -1; $x--) {
                        $cycle_opening_uncollected_amount = 0;
                        foreach ($myLoans as $loan) {
                            $MoneyCollected = 0;
                            $MoneyGivenOut = 0;
                            $balance = 0;


                            foreach ($loan->transactions as $transaction) {
                                if ($transaction->date <= date('Y-m-d', strtotime($cycle_start . '-' . $x . 'months')) && $transaction->transaction_type != 'specified_due_date_fee') {
                                    $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
                                }

                                if ($transaction->date <= date('Y-m-d', strtotime($cycle_start . '-' . $x . 'months'))) {
                                    $MoneyCollected = $MoneyCollected + $transaction->credit;
                                }



                            }

                            $balance = $MoneyGivenOut - $MoneyCollected;
                            $cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
                        }

                        array_push($cycle_opening_uncollected_amounts, $cycle_opening_uncollected_amount + 0.0001);

                    }
                                                                                                                                                                                                    ?>


                    <!-- CALCULATING 12 MONTHS CYCLE COLLECTED USING TRANSACTIONS -->
                    <?php
                    $collected_amounts = [];
                    $today = date('Y-m-d');
                    $currrent_date = date('Y-m');
                    $cycle_date = $currrent_date . $cycle_end;
                    if ($today > $cycle_date) {
                        $cycle_date = date('Y-m-d', strtotime($cycle_date . '+ 1 months'));
                    }
                    $cycle_start = date('Y-m-d', strtotime($cycle_date . '- 1 months'));
                    $cycle_end_date = date('Y-m-d', strtotime($cycle_start . '+ 1 months'));
                    $use = date('Y-m-');
                    $todaysDate = date('Y-m-d');
                    $targetDate = $use . $cycle_end;
                    $targetDate = date('Y-m-d', strtotime($targetDate));
                    if ($todaysDate > $targetDate) {
                        $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 months'));
                    }
                    $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));

                    for ($x = 12; $x > -1; $x--) {
                        foreach ($myTransactions as $transaction) {
                            if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > date('Y-m-d', strtotime($compareDate . '-' . $x . 'months')) && $transaction->date <= date('Y-m-d', strtotime($targetDate . '-' . $x . 'months'))) {
                                $full_payments = $full_payments + $transaction->credit;
                            }

                            if ($transaction->payment_apply_to == 'part_payment' && $transaction->date > date('Y-m-d', strtotime($compareDate . '-' . $x . 'months')) && $transaction->date <= date('Y-m-d', strtotime($targetDate . '-' . $x . 'months'))) {
                                $part_payments = $part_payments + $transaction->credit;
                            }

                            if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > date('Y-m-d', strtotime($compareDate . '-' . $x . 'months')) && $transaction->date <= date('Y-m-d', strtotime($targetDate . '-' . $x . 'months'))) {

                                $reloan_amount = $transaction->balance_bf;
                                $interest = $transaction->credit / 0.4;
                                $reloan_payments = $reloan_payments + $reloan_amount;

                            }
                        }

                        array_push($collected_amounts, ($full_payments + $part_payments + $reloan_payments));
                        $full_payments = 0;
                        $part_payments = 0;
                        $reloan_payments = 0;


                    }
                                                                                                                                                                                                     ?>


                    <!-- CALCULATING 12 MONTHS GIVEN OUT USING TRANSACTIONS -->
                    <?php
                    $new_loan_total = 0;
                    $reloan_total = 0;
                    $targets = [];
                    $transaction_total = 0;
                    $carry_over = 0;
                    $today = date('Y-m-d');
                    $dates = [];
                    $colors = [];
                    $currrent_date = date('Y-m');
                    $cycle_date = date('Y-m', strtotime($today)) . '-' . str_pad($cycle_end, 2, '0', STR_PAD_LEFT);
                    if (strtotime($today) > strtotime($cycle_date)) {
                        $cycle_date = date('Y-m-d', strtotime($cycle_date . '+ 1 months'));
                    }
                    $cycle_start = date('Y-m-d', strtotime($cycle_date . '- 1 months'));
                    $cycle_end_date = date('Y-m-d', strtotime($cycle_start . '+ 1 months'));

                    $use = date('Y-m-');
                    $todaysDate = date('Y-m-d');
                    $targetDate = $use . $cycle_end;
                    $targetDate = date('Y-m-d', strtotime($targetDate));
                    if ($todaysDate > $targetDate) {
                        $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 months'));
                    }
                    $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));

                    for ($x = 12; $x > -1; $x--) {
                        foreach ($myTransactions as $transaction) {
                            if ($transaction->transaction_type == 'disbursement' && $transaction->date > date('Y-m-d', strtotime($compareDate . '-' . $x . 'months')) && $transaction->date <= date('Y-m-d', strtotime($targetDate . '-' . $x . 'months'))) {
                                $new_loan_total = $transaction->debit;
                            }

                            if ($transaction->transaction_type == 'interest' && $transaction->date > date('Y-m-d', strtotime($compareDate . '-' . $x . 'months')) && $transaction->date <= date('Y-m-d', strtotime($targetDate . '-' . $x . 'months'))) {
                                $principal = $transaction->debit / 0.4;
                                $reloan_total = $principal;
                            }

                            if ($transaction_total + $new_loan_total + $reloan_total >= 40000) {
                                if ($transaction_total == 40000) {
                                    $carry_over = $carry_over + $new_loan_total + $reloan_total;
                                } else {
                                    $carry_over = 40000 - ($transaction_total + $new_loan_total + $reloan_total);
                                    $transaction_total = 40000;
                                }

                            } else {
                                $transaction_total = $transaction_total + $new_loan_total + $reloan_total; //+ $carry_over;
                                $carry_over = 0;
                            }

                            $new_loan_total = 0;
                            $reloan_total = 0;
                        }

                        array_push($targets, $transaction_total);
                        if ($transaction_total < 40000) {
                            array_push($colors, '#ff1c4b');
                        } else {
                            array_push($colors, '#57b7fa');
                        }
                        array_push($dates, date("jS M, Y", strtotime($cycle_date . '-' . $x . 'months')));
                        $transaction_total = 0;
                        $total = $reloan_total + $new_loan_total;
                    }

                                                                                                                                                                                                    ?>

                    <?php 
                                                                                                                                                                                                        $uncollected_amounts = [];
                    for ($x = 0; $x < 12; $x++) {
                        array_push($uncollected_amounts, ($cycle_opening_uncollected_amounts[$x] - $collected_amounts[$x]));
                    }
                                                                                                                                                                                                        ?>
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
            <form method="GET" action="{{ url('dashboard') }}" class="form-horizontal">
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



                    <!-- <div class="col-lg-4 col-xs-12">
                                                                                                                                                                                                        <div class="small-box bg-yellow">
                                                                                                                                                                                                        <div class="inner">
                                                                                                                                                                                                        <p style="font-weight: bold;">Cycle opening uncollected amount (COUA)</p>
                                                                                                                                                                                                        <div class="icon">
                                                                                                                                                                                                        <i class="fa fa-usd"></i>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <h3>{{number_format($cycle_opening_uncollected_amounts[12] ,2)}}</h3>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <div class="small-box-footer">
                                                                                                                                                                                                            <p></p>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        </div> -->


                    <!-- <div class="col-lg-4 col-xs-12">
                                                                                                                                                                                                        <div class="small-box bg-green">
                                                                                                                                                                                                        <div class="inner">
                                                                                                                                                                                                        <p style="font-weight: bold;">Total cycle collected amount (TCC)</p>
                                                                                                                                                                                                        <div class="icon">
                                                                                                                                                                                                        <i class="fa fa-usd"></i>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <h3>{{number_format($collected_amounts[12],2)}}</h3>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <div class="small-box-footer">
                                                                                                                                                                                                            <p></p>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        </div> -->

                    <!-- <div class="col-lg-4 col-xs-12">
                                                                                                                                                                                                        <div class="small-box bg-aqua">
                                                                                                                                                                                                        <div class="inner">
                                                                                                                                                                                                        <p style="font-weight: bold;">Still Uncollected Today</p>
                                                                                                                                                                                                        <div class="icon">
                                                                                                                                                                                                        <i class="fa fa-usd"></i>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <h3>{{number_format($cycle_opening_uncollected_amounts[12] - $collected_amounts[12],2)}}</h3>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <div class="small-box-footer">
                                                                                                                                                                                                            <p></p>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        </div> -->






                </div>
                <!--second row-->
                <?php
                    $tots = ($collected_amounts[12] / $cycle_opening_uncollected_amounts[12])
                                                                                                                                                                                                        ?>

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

                <div style="margin-bottom:30px; margin-top:30px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <h2 class=" text-semibold">{{ trans_choice('general.monthly', 1) }}
                                    {{ trans_choice('general.target', 1) }}
                                </h2>
                            </div>
                            <div class="progress" data-toggle="tooltip"
                                title="You're currently at : {{number_format($targets[12], 2)}}">
                                <div class="progress-bar progress-bar-success progress-bar-striped active"
                                    style="width: {{($targets[12] / 40000) * 100}}% ">
                                    @if($targets[12] > 40000)
                                        <span>You've reached your target congratulations!!!</span>
                                    @else
                                        <span>{{($targets[12] / 40000) * 100}} {{ trans_choice('general.complete', 1) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <canvas id='graph'></canvas>
                <canvas id='target_graph'></canvas>

            @endif

        </div>

        <div id='coua' style="display:none">
            <div class="box box-primary">
                <div id='mydivon_new' style="display:block" class="box-body table-responsive">
                    <div class="box-header with-border">
                        <h3 class="box-title">Loans at end of last cycle<a href="javascript:;"
                                onmousedown="toggleLedger('mydiv');">
                            </a></h3>
                    </div>
                    <table class="table  table-bordered table-hover table-striped" id="data-table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Name</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
            $new_out = 0;
            $use = date('Y-m-');
            $todaysDate = date('Y-m-d');
            $targetDate = $use . $cycle_end;
            $targetDate = date('Y-m-d', strtotime($targetDate));
            if ($todaysDate > $targetDate) {
                $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 months'));
            }
            $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));
                                                                                                                                                    ?>
                            @foreach($myLoans as $loan)


                                            <?php
                                $OutIn = 0;
                                $out = 0;
                                $in = 0;
                                                                                                                                                                                                                                                                                                                                                                                                        ?>
                                            @foreach($loan->transactions as $transaction)
                                                        <?php

                                                if ($transaction->date < $compareDate && $transaction->transaction_type != 'specified_due_date_fee' ) {
                                                    $out = $out + $transaction->debit;
                                                }


                                                // if($transaction->date <= $compareDate && $transaction->transaction_type != 'interest_waiver'){
                                                //     $in = $in + $transaction->credit;
                                                // }

 if ($transaction->date < $compareDate) {
                                                $in = $in + $transaction->credit;
 }

                                                // if($transaction->date <= $compareDate && $transaction->transaction_type == 'specified_due_date_fee'){
                                                //     $newout = $newout + $transaction->debit;
                                                // }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ?>
                                            @endforeach

                                            <?php
                                $OutIn = $out - $in;
                                //$OutIn = $OutIn - $newout;
                                // if($OutIn < 0){
                                //     $OutIn = 0;
                                // }

                                                                                                                                                                                                                                                                                                                                                                                                        ?>

                                            <tr>


                                                <td><a href="{{ url('loan/' . $loan->id . '/show') }}" data-toggle="tooltip"
                                                        title="Click to view">{{$loan->id}}</a></td>
                                                @if(!empty($loan->client->first_name))
                                                    <td>{{$loan->client->first_name}} {{$loan->client->last_name}}
                                                        @if($loan->defaulted == 'yes')
                                                            <span style="color: red;">(Defaulted)</span>
                                                        @endif
                                                    </td>
                                                    @if($OutIn < 0)
                                                        <td style="color: red;">{{number_format($OutIn, 2)}}</td>
                                                    @else
                                                        <td>{{number_format($OutIn, 2)}}</td>
                                                    @endif
                                                @endif
                                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box box-primary">
            </div>

        </div>


        <div id='tcc' style="display:none" class="box-body table-responsive">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Transactions as at start of cycle<a href="javascript:;"
                            onmousedown="toggleLedger('mydiv');">
                        </a></h3>
                </div>
                <table class="table  table-bordered table-hover table-striped" id="data-table">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Name</th>
                            <th>Transaction Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myTransactions as $transaction)
                            @if($transaction->transaction_type != 'interest_waiver' && $transaction->date > $compareDate && $transaction->date <= $targetDate && $transaction->credit)
                                <tr>
                                    <td>{{$transaction->loan_id}} {{$targetDate}} {{$compareDate}}</td>
                                    <td>
                                        @if(isset($transaction->loan->client))
                                            {{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{$transaction->payment_apply_to}}</td>
                                    <td>{{number_format($transaction->credit, 2)}}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>


            </div>
        </div>


        <div id='given_out' style="display:none" class="box-body table-responsive">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Given out as at start of cycle<a href="javascript:;"
                            onmousedown="toggleLedger('mydiv');">
                        </a></h3>
                </div>
                <table class="table  table-bordered table-hover table-striped" id="data-table">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Name</th>
                            <th>Transaction Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myTransactions as $transaction)
                            @if(($transaction->transaction_type == 'disbursement' || $transaction->transaction_type == 'interest') && $transaction->date > $compareDate && $transaction->date <= $targetDate)
                                <tr>
                                    <td>{{$transaction->loan_id}}</td>
                                    <td>
                                        @if(isset($transaction->loan->client))
                                            {{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    @if($transaction->transaction_type == 'disbursement')
                                        <td>New Loan</td>
                                    @elseif($transaction->transaction_type == 'interest')
                                        <td>Reloan</td>
                                    @endif
                                    @if($transaction->transaction_type == 'interest')
                                        <td>{{number_format(($transaction->debit / 0.4), 2)}}</td>
                                    @else
                                        <td>{{number_format($transaction->debit, 2)}}</td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>


            </div>
        </div>

   


        <!-- //GOES HERE -->
        </div>

    @endif

    
    <!--What managers see-->
    @if($role->role_id == '4' || $role->role_id == '12')

        @if ($role->role_id == '12')
            <span>
                <p>This is the District Level Overview</p>
            </span>
        @endif

        <div
            style="display: flex;
                                                                                                                                    align-items: center;
                                                                                                                                    justify-content: center; padding-bottom: 10px; ">


            <a href="{{ url('loan/new_collections') }}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Collections</span>
            </a>

            <a href="javascript:;" onmousedown="toggleDiv('mydiv');" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">COUA and TCC breakdown</span>
                <!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
            </a>

            <a href="javascript:;" onmousedown="toggleMyStaff('mydiv');" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Staff information</span>
                <!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
            </a>

        </div>

        <div id='mydivon' style="display:block">
            <div class="row">
                <?php
            $use = date('Y-m-');
            $todaysDate = date('Y-m-d');
            $newTodaysDate = date('Y-m-d', strtotime($todaysDate . ' + 3 months'));
            $branchtargetDate = $use . '24';
            $branchtargetDate = date('Y-m-d', strtotime($branchtargetDate));
            $cycle_opening_uncollected_amount_debit = 0;
            $cycle_opening_uncollected_amount_credit = 0;
            $disbursed_amount = 0;
            $debit = 0;
            $credit = 0;
            $branch_total_cycle_collected_amount = 0;
            $num = 0;
            $item = 0;
            $count = 0;
            $targetCount = 0;
            $sum = 0;
            $transac = 0;
            $firstAmount = 0;
            $collected_total = 0;
            $secondAmount = 0;

            //BRANCH CYCLE OPENING UNCOLLECTED VARIABLES
            $MoneyGivenOut = 0;
            $MoneyCollected = 0;
            $charges = 0;
            $cycle_opening_uncollected_amount = 0.00001;

            //BRANCH COLLECTED TOTAL CALCULATIONS
            $collected_total = 0;
            $full_payments = 0;
            $part_payments = 0;
            $reloan_payments = 0;
            $reloan_amount = 0;

            // BRANCH COLLECTED TOTAL CALCULATIONS 1 MONTH AGO
            $collected_total_1_months = 0;
            $reloan_amount_1_months = 0;
            $full_payments_1_months = 0;
            $part_payments_1_months = 0;
            $reloan_payments_1_months = 0;

            //BRANCH COLLECTED TOTAL CALCULATIONS 2 MONTHS AGO
            $collected_total_2_months = 0;
            $reloan_amount_2_months = 0;
            $full_payments_2_months = 0;
            $part_payments_2_months = 0;
            $reloan_payments_2_months = 0;

            //STAFF CALCULATIONS
            $staff_count = 0;

            //BRANCH TARGET CALCULATIONS
            $target_monthly = 0;
            $target_reloan = 0;
            $target_total = 0;

            if ($todaysDate > $branchtargetDate) {
                $branchtargetDate = date('Y-m-d', strtotime($branchtargetDate . ' + 1 months'));
            }
            $branchcompareDate = date('Y-m-d', strtotime($branchtargetDate . ' - 1 months'));
            $branchzeroDate = date('Y-m-d', strtotime($branchtargetDate . ' - 3 months'));
            $branchfirstDate = date('Y-m-d', strtotime($branchtargetDate . ' - 2 months'));
            $branchsecondDate = date('Y-m-d', strtotime($branchtargetDate . ' - 1 months'));
            $testcompareDate = date('Y-m-d', strtotime($branchtargetDate . ' - 1 months'));
            $testtargetDate
                                                                                                                                ?>

                <!-- CALCULATION LOAN BALANCES FOR BRANCH CYCLE OPENING UNCOLLECTED -->
                @foreach($newBranchLoans as $loan)
                    <?php
                    $MoneyCollected = 0;
                    $MoneyGivenOut = 0;
                    $charges = 0;
                    $balance = 0;
                                                                                                                                                                                                        ?>
                    @foreach($loan->transactions as $transaction)

                        <?php
                            if ($transaction->date <= $branchcompareDate && $transaction->transaction_type != 'specified_due_date_fee') {
                                $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
                            }

                            if ($transaction->date <= $branchcompareDate) {
                                $MoneyCollected = $MoneyCollected + $transaction->credit;
                            }

                                                                                                                                                                                                                                                                                ?>
                    @endforeach
                    <?php 
                                                                                                                                                                                                        $balance = $MoneyGivenOut - $MoneyCollected;


                    $cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
                    if ($cycle_opening_uncollected_amount == 0) {
                        $cycle_opening_uncollected_amount = 1;
                    }
                                                                                                                                                                                                        ?>
                @endforeach

                <!-- CALCULATING BRANCH CURRENT CYCLE COLLECTED USING TRANSACTIONS -->
                @foreach($branchTransactions as $transaction)
                    <?php 
                                                                                                                                                                                                        if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchcompareDate && $transaction->date <= $branchtargetDate) {
                        $full_payments = $full_payments + $transaction->credit;
                    }

                    if ($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchcompareDate && $transaction->date <= $branchtargetDate) {
                        $part_payments = $part_payments + $transaction->credit;
                    }

                    if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchcompareDate && $transaction->date <= $branchtargetDate) {

                        $reloan_amount = $transaction->balance_bf;
                        $interest = $transaction->credit / 0.4;
                        $reloan_payments = $reloan_payments + $reloan_amount;
                    }


                    //COLLECTIONS 1 MONTH AGO
                    if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchzeroDate && $transaction->date <= $branchfirstDate) {
                        $full_payments_1_months = $full_payments_1_months + $transaction->credit;
                    }

                    if ($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchzeroDate && $transaction->date <= $branchfirstDate) {
                        $part_payments_1_months = $part_payments_1_months + $transaction->credit;
                    }

                    if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchzeroDate && $transaction->date <= $branchfirstDate) {

                        $reloan_amount_1_months = $transaction->credit;
                        +($transaction->credit / 0.4);
                        $interest = $transaction->credit / 0.4;
                        $reloan_payments_1_months = $reloan_payments_1_months + $reloan_amount_1_months + $interest;
                    }

                    //COLLECTIONS 2 MONTHS AGO
                    if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchfirstDate && $transaction->date <= $branchsecondDate) {
                        $full_payments_2_months = $full_payments_2_months + $transaction->credit;
                    }

                    if ($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchfirstDate && $transaction->date <= $branchsecondDate) {
                        $part_payments_2_months = $part_payments_2_months + $transaction->credit;
                    }

                    if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchfirstDate && $transaction->date <= $branchsecondDate) {
                        $reloan_amount_2_months = $transaction->credit;
                        +($transaction->credit / 0.4);
                        $interest = $transaction->credit / 0.4;
                        $reloan_payments_2_months = $reloan_payments_2_months + $reloan_amount_2_months + $interest;
                    }

                                                                                                                                                                                                        ?>
                @endforeach

                <?php
            $collected_total = $reloan_payments + $part_payments + $full_payments;
            $collected_total_1_months = $reloan_payments_1_months + $part_payments_1_months + $full_payments_1_months;
            $collected_total_2_months = $reloan_payments_2_months + $part_payments_2_months + $full_payments_2_months;
                                                                                                                                ?>


                @foreach($branchUsers as $branchUser)
                    <?php
                    $target_total = 0;
                    $target_monthly = 0;
                    $target_reloan = 0;
if($branchUser->role){
      if ($branchUser->role->role_id != 1) {
                        $staff_count = $staff_count + 1;
                    }
}
                  

                    if ($branchUser->cycle_dates != null) {
                        $end = $branchUser->cycle_dates->cycle_end_date;
                    } else {
                        $end = 1;
                    }

                    $branchReferenceTargetDate = $use . '24';
                    $branchReferenceTargetDate = date('Y-m-d', strtotime($branchReferenceTargetDate));
                    $targetDate = $use . $end;
                    $targetDate = date('Y-m-d', strtotime($targetDate));

                    if ($todaysDate > $branchReferenceTargetDate) {
                        $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 months'));
                    }

                    $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));
                                                                                                                                                                                                        ?>

                    @foreach($newBranchLoans as $loan)
                        @foreach($loan->transactions as $transaction)
                            <?php 
                                                                                                                                                                                                                                                                                                                                                //branch transactions
                                    if ($loan->loan_officer_id == $branchUser->id) {
                                        if ($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate && $transaction->loan_id == $loan->id) {
                                            $target_monthly = $target_monthly + $transaction->debit;
                                        }
                                    }

                                    if ($loan->loan_officer_id == $branchUser->id) {
                                        if ($transaction->transaction_type == 'interest' && $transaction->date > $compareDate && $transaction->date <= $targetDate && $transaction->loan_id == $loan->id) {
                                            $principal = $transaction->debit / 0.4;
                                            $target_reloan = $target_reloan + $principal;
                                        }
                                    }
                                                                                                                                                                                                                                                                                                                                                ?>

                        @endforeach
                    @endforeach

                    <?php
                    $target_total = $target_monthly + $target_reloan;
                    if ($target_total > 40000) {
                        $targetCount = $targetCount + 1;
                    }
                                                                                                                                                                                                        ?>
                @endforeach


    

                <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <p style="font-weight: bold;">Branch cycle opening uncollected amount</p>
                            <div class="icon">
                                <i class="fa fa-usd"></i>
                            </div>
                            <h3>{{ number_format($branch_data['total_uncollected']) }}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <p style="font-weight: bold;">Branch total cycle collected amount (TCC)</p>
                            <div class="icon">
                                <i class="fa fa-usd"></i>
                            </div>
                            <h3>{{ number_format($branch_data['total_collected']) }}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>


                          <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <p style="font-weight: bold;">Branch Given Out</p>
                            <div class="icon">
                                <i class="fa fa-usd"></i>
                            </div>
                            <h3 >{{ number_format($branch_data['given_out']) }}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>


                          <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <p style="font-weight: bold;">Branch Still Uncollected</p>
                            <div class="icon">
                                <i class="fa fa-usd"></i>
                            </div>
                            <h3 >{{ number_format(max(0, $branch_data['still_uncollected'])) }}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>


                        <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <p style="font-weight: bold;">Branch Staff</p>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <h3 >{{  $branch_data['consultants_count'] }}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>


                        <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <p style="font-weight: bold;">Branch Targets Met</p>
                            <div class="icon">
                                <i class="fa fa-birthday-cake"></i>
                            </div>
                            <h3 >{{  $branch_data['targets_met_count'] }}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>



                      <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-fuchsia">
                        <div class="inner">
                            <p style="font-weight: bold;">Branch Efficiency</p>
                            <div class="icon">
                                <i class="fa fa-cogs"></i>
                            </div>
                            <h3>{{ rtrim(rtrim(number_format(min($branch_data['efficiency'], 1) * 100, 2), '0'), '.') }}%</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>




            </div>


            <div style="margin-bottom:30px; margin-top:30px;">
                <p
                    style="display: flex;
                                                                                                                                    align-items: center;
                                                                                                                                    justify-content: center; font-size:50px;">
                    PDUA%</p>
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

                            @if(($branch_data['pdua']) < 0.75)
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

                            @elseif(($branch_data['pdua']) >= 0.90)
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


        <div id='mydivstaff' style="display:none">
            @foreach($branchUsers as $branchUser)
                <a href="{{url('user/' . $branchUser->id . '/staff_info')}}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-purple">
                            <span class="info-box-icon"><i class="fa fa-user-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{$branchUser->first_name}} {{$branchUser->last_name}}</span>
                                @if($branchUser->role)
   @if($branchUser->role->role_id == '3')
                                    <p style="font-size: 15px;">Loan Consultant</p>
                                @elseif($branchUser->role->role_id == '4')
                                    <p>Branch Manager</p>
                                @else
                                    <p></p>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>



        <div id='mydivoff' style="display:none">
            <div class="box box-primary">
                <div id='mydivon_new' style="display:block" class="box-body table-responsive">
                    <div class="box-header with-border">
                        <h3 class="box-title">Loans at end of last cycle<a href="javascript:;"
                                onmousedown="toggleLedger('mydiv');">
                                <span style="font-size: 15px; padding-left: 10px;">TCC breakdown</span>
                            </a></h3>
                    </div>
                    <table class="table  table-bordered table-hover table-striped" id="data-table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Name</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newBranchLoans as $loan)
                                            <?php
                                $OutIn = 0;
                                $out = 0;
                                $in = 0;
                                $newout = 0;
                                                                                                                                                                                                                                                                                                                                                                                                                ?>
                                            @foreach($loan->transactions as $transaction)
                                                        <?php

                                                if ($transaction->transaction_type != 'specified_due_date_fee') {
                                                    $out = $out + $transaction->debit;
                                                }

                                                $in = $in + $transaction->credit;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ?>
                                            @endforeach
                                            <?php
                                $OutIn = $out - $in;
                                $OutIn = $OutIn - $newout;
                                                                                                                                                                                                                                                                                                                                                                                                                ?>
                                            <tr>
                                                @if($OutIn != 0)
                                                    <td>{{$loan->id}}</td>
                                                    <td>
                                                        @if(!empty($loan->client->first_name))
                                                            {{$loan->client->first_name}}
                                                        @endif
                                                        @if(!empty($loan->client->last_name))
                                                            {{$loan->client->last_name}}
                                                        @endif
                                                        @if($loan->defaulted == 'yes')
                                                            <span style="color: red;">(Defaulted)</span>
                                                        @endif
                                                    </td>
                                                    @if($OutIn < 0)
                                                        <td style="color: red;">{{number_format($OutIn, 2)}}</td>
                                                    @else
                                                        <td>{{number_format($OutIn, 2)}}</td>
                                                    @endif
                                                @endif
                                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary">
                <div id='mydivoff_new' style="display:none" class="box-body table-responsive">
                    <div class="box-header with-border">
                        <h3 class="box-title">Transactions as at start of cycle<a href="javascript:;"
                                onmousedown="toggleLedger('mydiv');">
                                <span style="font-size: 15px; padding-left: 10px;">COUA breakdown</span>
                            </a></h3>
                    </div>
                    <table class="table  table-bordered table-hover table-striped" id="data-table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Name</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branchTransactions as $transaction)
                                @if($transaction->transaction_type != 'interest_waiver' && $transaction->date > $branchcompareDate && $transaction->date <= $branchtargetDate && $transaction->credit)
                                    <tr>
                                        <td>{{$transaction->loan_id}}</td>
                                        <td>
                                            @if(isset($transaction->loan->client))
                                                {{$transaction->loan->client->first_name}} {{$transaction->loan->client->last_name}}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{$transaction->payment_apply_to}}</td>
                                        <td>{{number_format($transaction->credit, 2)}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php
            $office_id = Sentinel::getUser()->office_id;
        ?>
        <div id="pendingWidget" style="
            position: fixed;
            top: 80%;
            left: 55%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 350px;
            background: #fefefeee;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            overflow: hidden;
            z-index: 9998;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            cursor: move;
            transition: transform 0.2s ease;
        ">
            <div class="slide active" style="display:block; padding:15px; border-left: 6px solid #ff4d4f;">
                <h4 style="margin:0; font-weight:bold; color:#ff4d4f;">Loans Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{\App\Models\Loan::whereIn('status', ['pending', 'approved'])->where('office_id', $office_id)->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #ffa940;">
                <h4 style="margin:0; font-weight:bold; color:#ffa940;">Transactions Pending Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{\App\Models\LoanTransactionUnapproved::where('office_id', $office_id)->count() }}</p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #52c41a;">
                <h4 style="margin:0; font-weight:bold; color:#52c41a;">Reloans Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{\App\Models\LoanTransactionsPending::where('office_id', $office_id)->count() }}</p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #1890ff;">
                <h4 style="margin:0; font-weight:bold; color:#1890ff;">Waivers Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{\App\Models\WaiverTransactionUnapproved::where('status', 'pending')->where('office_id', $office_id)->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #722ed1;">
                <h4 style="margin:0; font-weight:bold; color:#722ed1;">Charges Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{\App\Models\ChargeTransactionUnapproved::where('status', 'pending')->where('office_id', $office_id)->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #fa541c;">
                <h4 style="margin:0; font-weight:bold; color:#fa541c;">Clients Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{\App\Models\Client::where('status', 'pending')->where('office_id', $office_id)->count() }}</p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #13c2c2;">
                <h4 style="margin:0; font-weight:bold; color:#13c2c2;">Advances Pending Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">{{\App\Models\Advance::where('status', 'pending')
                ->where('office_id', $office_id)
                ->count()}}</p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #eb2f96;">
                <h4 style="margin:0; font-weight:bold; color:#eb2f96;">Advance-TopUps Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">{{\App\Models\TopUp::where('status', 'pending')
                ->where('office_id', $office_id)
                ->count()}}</p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #fa8c16;">
                <h4 style="margin:0; font-weight:bold; color:#fa8c16;">Pending Leave Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">{{
                \App\Models\Leave::where('status', 'pending')
                    ->where('office_id', $office_id)
                    ->count()}}</p>
            </div>
        </div>


        @if($HasPendingCarryOvers)
<div class="modal fade" id="managerPendingCarryOverModal"
     tabindex="-1"
     data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h4 class="modal-title">Pending Carry Overs</h4>
            </div>

            <div class="modal-body text-center">
                <p>
                    You have <strong>pending carry over requests</strong> awaiting your action.
                </p>

                <p>
                    Please clear all pending carry overs before continuing to use the system.
                </p>

                <p>
                    <a href="{{ url('user/carry_over_approvals') }}" class="btn btn-primary">
                        View Pending Carry Overs
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endif


 @if($launchNewCarryOver)
    <div class="modal fade" id="broughtForwardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ url('user/create_carry_over') }}">
                @csrf

                <div class="modal-header bg-warning">
                    <h4 class="modal-title">Carry Over</h4>
                </div>

                <div class="modal-body">
                    <p>
                        Please enter your <strong>Carry Over (from last cycle)</strong> amount to continue.
                    </p>

                    

                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="brought_f"
                               class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmCarryOverModal">
                        Save & Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmCarryOverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h4 class="modal-title">Confirm Carry Over</h4>
            </div>

            <div class="modal-body">
                <p>
                    By clicking <strong>Confirm</strong>, you acknowledge that the information you have entered is
                    accurate and correct.
                </p>

                <p>
                    You further understand that if the amount entered affects your target and ultimately your
                    salary negatively  <strong>you and only you will be responsible</strong> for the consequences.
                </p>

                <p class="text-danger">
                    Please ensure the amount entered is correct before proceeding.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>

                <button type="button" class="btn btn-danger" id="confirmSubmitCarryOver">
                    Confirm & Submit
                </button>
            </div>

        </div>
    </div>
</div>

        @endif





    @endif


    <!-- What PMs see -->
    @if($role->role_id == '6')

    @endif


    <!-- What Admins see -->
    @if($role->role_id == '1' || $role->role_id == '10')
        <div style="display: flex;
                                            align-items: center;
                                                justify-content: center; padding-bottom: 10px; ">

            <a target="_blank" href="https://erp.whencefinancesystem.com" style="margin: 10px;">
                <span class="label label-warning" style="font-size: 15px;"><i class="fa fa-external-link"></i> ERP
                    Dashboard</span>
            </a>
            <a href="{{ url('user/detailed_dashboard')}}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Detailed Dashboard</span>
            </a>

            <a href="{{ url('loan/new_collections') }}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Collections</span>
            </a>

            <a href="javascript:;" onmousedown="toggleMyStaff('mydiv');" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Provinces</span>
                <!-- <i class="fa fa-caret-square-o-right" aria-hidden="true"></i> -->
            </a>

            <a href="{{ url('user/daily_figures')}}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Daily figures</span>
            </a>

            <a href="{{ route('performance.institution_metrics') }}" target="_blank" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Institution Metrics</span>
            </a>

        </div>

        <div
            style="display: flex;
                                                                                                                                        align-items: center;
                                                                                                                                        justify-content: center; padding-bottom: 10px; ">
            <p style="font-weight: bold;">Data based on loans created in the last 3 months</p>
        </div>

        <div id='mydivon' style="display:block">
            <div class="row">
                <?php
            $use = date('Y-m-');
            $todaysDate = date('Y-m-d');
            $newTodaysDate = date('Y-m-d', strtotime($todaysDate . ' + 3 months'));
            $branchtargetDate = $use . '24';
            $branchtargetDate = date('Y-m-d', strtotime($branchtargetDate));

            if ($todaysDate > $branchtargetDate) {
                $branchtargetDate = date('Y-m-d', strtotime($branchtargetDate . ' + 1 months'));
            }
            $branchcompareDate = date('Y-m-d', strtotime($branchtargetDate . ' - 1 months'));
            $branchzeroDate = date('Y-m-d', strtotime($branchtargetDate . ' - 3 months'));
            $branchfirstDate = date('Y-m-d', strtotime($branchtargetDate . ' - 2 months'));
            $provincefirstDate = date('Y-m-d', strtotime($branchtargetDate . ' - 2 months'));
            $branchsecondDate = date('Y-m-d', strtotime($branchtargetDate . ' - 1 months'));


            //BRANCH CYCLE OPENING UNCOLLECTED VARIABLES
            $MoneyGivenOut = 0;
            $MoneyCollected = 0;
            $charges = 0;
            $cycle_opening_uncollected_amount = 0.0001;


            //BRANCH COLLECTED TOTAL CALCULATIONS
            $collected_total = 0;
            $full_payments = 0;
            $part_payments = 0;
            $reloan_payments = 0;
            $reloan_amount = 0;

            // BRANCH COLLECTED TOTAL CALCULATIONS 1 MONTH AGO
            $collected_total_1_months = 0;
            $reloan_amount_1_months = 0;
            $full_payments_1_months = 0;
            $part_payments_1_months = 0;
            $reloan_payments_1_months = 0;

            //BRANCH COLLECTED TOTAL CALCULATIONS 2 MONTHS AGO
            $collected_total_2_months = 0;
            $reloan_amount_2_months = 0;
            $full_payments_2_months = 0;
            $part_payments_2_months = 0;
            $reloan_payments_2_months = 0;

            //GIVEN OUT AMOUNTS
            $given_out_total = 0;
            $new_loans_given_out = 0;
            $reloans_given_out = 0;
            $reloans_given_out_not_exp = 0;
            $new_loans_given_out_not_exp = 0;
            $given_out_total_not_exp = 0;

            //COUNTS
            $bar_chart_count = 0;
            $collections_count_fullpayment = 0;
            $collections_count_reloan = 0;
            $collections_count_partpayment = 0;
            $givenout_count_newloan = 0;
            $givenout_count_reloan = 0;
            $given_out_count_total = 0;
            $trans_id = 0;
            $trans_id_int = 0;

            //COLLECTIONS TODAY
            $full_payments_today = 0;
            $part_payments_today = 0;
            $reloan_payments_today = 0;
            $add = 0;

            $trans = []
                // foreach($province_loans as $province_loan){
                //     foreach($province_loan->transactions as $transaction){
                //         array_push($province_transactions, $transaction);
                //     }
                // }
                                                                                                                                    ?>

                <!-- CALCULATION LOAN BALANCES FOR BRANCH CYCLE OPENING UNCOLLECTED -->
                @foreach($allLoans as $loan)
                    <?php
                    $MoneyCollected = 0;
                    $MoneyGivenOut = 0;
                    $charges = 0;
                    $balance = 0;
                                                                                                                                                                                                                                    ?>
                    @foreach($loan->transactions as $transaction)

                        <?php

                            if ($transaction->date <= $branchcompareDate && $transaction->transaction_type != 'specified_due_date_fee') {
                                $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
                            }

                            if ($transaction->date <= $branchcompareDate) {
                                $MoneyCollected = $MoneyCollected + $transaction->credit;
                            }


                            if ($transaction->transaction_type == 'specified_due_date_fee' && $transaction->date <= $branchcompareDate) {
                                $charges = $charges + $transaction->debit;
                            }



                                                                                                                                                                                                                                                                                                                                                ?>
                    @endforeach
                    <?php 
                                                                                                                                                                                                                                    $balance = ($MoneyGivenOut - $MoneyCollected);
                    if ($balance < 0) {
                        $balance = 0;
                    }
                    $cycle_opening_uncollected_amount = $cycle_opening_uncollected_amount + $balance;
                    if ($cycle_opening_uncollected_amount == 0) {
                        $cycle_opening_uncollected_amount = 1;
                    }
                                                                                                                                                                                                                                    ?>
                @endforeach

                <?php 
                                                                                                                                    $collections = [];
            $given_out = [];
            $given_out_not_exp = [];
            $target_dates = [];
            while ($bar_chart_count < 3) {


                $branchtargetDateAlgo = date('Y-m-d', strtotime($branchtargetDate . ' - ' . $bar_chart_count . 'months'));
                $branchcompareDateAlgo = date('Y-m-d', strtotime($branchcompareDate . ' - ' . $bar_chart_count . 'months'));
                $reloan_payments = 0;
                $reloan_amount = 0;
                $full_payments = 0;
                $part_payments = 0;
                $new_loans_given_out = 0;
                $reloans_given_out = 0;
                $interest = 0;
                $disbursement_interest = 0;
                $interest_today = 0;
                $reloan_amount_today = 0;
                $principal = 0;
                $reloans_given_out_not_exp = 0;
                $new_loans_given_out_not_exp = 0;
                $full_payments_today = 0;
                $part_payments_today = 0;
                $reloan_payments_today = 0;
                // {{date("jS M, Y", strtotime($compareDate))}} 
                array_push($target_dates, date("jS M, Y", strtotime($branchtargetDateAlgo)));

                foreach ($allTransactions as $transaction) {

                    if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo) {
                        $full_payments = $full_payments + $transaction->credit;

                        if ($bar_chart_count == 4) {
                            $collections_count_fullpayment = $collections_count_fullpayment + 1;
                        }
                    }

                    if ($transaction->payment_apply_to == 'part_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo) {
                        $part_payments = $part_payments + $transaction->credit;

                        if ($bar_chart_count == 4) {
                            $collections_count_partpayment = $collections_count_partpayment + 1;
                        }
                    }

                    if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo) {

                        $reloan_amount = $transaction->credit;
                        +($transaction->credit / 0.4);
                        $interest = $transaction->credit / 0.4;
                        $reloan_payments = $reloan_payments + $reloan_amount;
                        array_push($trans, $transaction);
                        if ($bar_chart_count == 4) {
                            $collections_count_reloan = $collections_count_reloan + 1;
                        }
                    }

                    if ($transaction->transaction_type == 'disbursement' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo) {
                        $disbursement_interest = $transaction->debit / 0.4;
                        $new_loans_given_out = $new_loans_given_out + $transaction->debit + $disbursement_interest;
                        $new_loans_given_out_not_exp = $new_loans_given_out_not_exp + $transaction->debit;

                        if ($bar_chart_count == 4) {
                            $givenout_count_newloan = $givenout_count_newloan + 1;
                        }
                    }

                    if ($transaction->transaction_type == 'interest' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo) {
                        $principal = $transaction->debit / 0.4;
                        $reloans_given_out = $reloans_given_out + $principal + $transaction->debit;
                        $reloans_given_out_not_exp = $reloans_given_out_not_exp + $principal;
                        if ($bar_chart_count == 4) {
                            $givenout_count_reloan = $givenout_count_reloan + 1;
                        }
                    }


                    if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date == $todaysDate) {
                        $full_payments_today = $full_payments_today + $transaction->credit;
                    }

                    if ($transaction->payment_apply_to == 'part_payment' && $transaction->date == $todaysDate) {
                        $part_payments_today = $part_payments_today + $transaction->credit;
                    }


                    if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date == $todaysDate) {
                        $reloan_amount_today = $transaction->credit;
                        +($transaction->credit / 0.4);
                        $interest_today = $transaction->credit / 0.4;
                        $reloan_payments_today = $reloan_payments_today + $reloan_amount_today + $interest_today;

                    }



                }
                $given_out_total_not_exp = $new_loans_given_out_not_exp + $reloans_given_out_not_exp;
                $collected_total = $reloan_payments + $part_payments + $full_payments;
                $given_out_total = $new_loans_given_out + $reloans_given_out;
                array_push($given_out_not_exp, $given_out_total_not_exp);
                array_push($collections, $collected_total);
                array_push($given_out, $given_out_total);
                $bar_chart_count = $bar_chart_count + 1;

            }
                                                                                                                                    ?>
                <?php 
                                                                                                                                     foreach ($allTransactions as $transaction) {

                if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date == $todaysDate) {
                    $full_payments_today = $full_payments_today + $transaction->credit;
                }

                if ($transaction->payment_apply_to == 'part_payment' && $transaction->date == $todaysDate) {
                    $part_payments_today = $part_payments_today + $transaction->credit;
                }


                if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date == $todaysDate) {
                    $reloan_amount_today = $transaction->credit;
                    +($transaction->credit / 0.4);
                    $interest_today = $transaction->credit / 0.4;
                    $reloan_payments_today = $reloan_payments_today + $reloan_amount_today;
                }
            }


            $pdua = ($collections[0] / $cycle_opening_uncollected_amount)
                                                                                                                                    ?>

                <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <p style="font-weight: bold;">Total collected today</p>
                            <div class="icon">
                                <i class="fa fa-usd"></i>
                            </div>
                            <h3>{{number_format($full_payments_today + $part_payments_today + $reloan_payments_today, 2)}}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <p style="font-weight: bold;">Part and Full payments collected today</p>
                            <div class="icon">
                                <i class="fa fa-usd"></i>
                            </div>
                            <h3>{{number_format($full_payments_today + $part_payments_today, 2)}}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-xs-12">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <p style="font-weight: bold;">Reloans collected today</p>
                            <div class="icon">
                                <i class="fa fa-usd"></i>
                            </div>
                            <h3>{{number_format($reloan_payments_today, 2)}}</h3>
                        </div>
                        <div class="small-box-footer">
                            <p></p>
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

                            @if(($pdua) < 0.75)
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

                            @elseif(($pdua) >= 0.90)
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


            <canvas id='companygraph'></canvas>

            <div class="row" style="padding-top: 20px;">



                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Money collected</h3>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                            <canvas id="myChart"></canvas>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Money given out</h3>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                            <canvas id="myOtherChart"></canvas>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>


            </div>



            <div>

            </div>


        </div>



        <div id='mydivoff' style="display:none">
            @foreach($provinces as $province)
                <a href="{{url('user/' . $province->id . '/province_page')}}">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-purple">
                            <span class="info-box-icon"><i class="fa fa-user-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{$province->name}}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div id='mydivofff' style="display: none;">
            <div class="box box-primary">
                <div id='mydivoff_new' class="box-body table-responsive">
                    <div class="box-header with-border">
                        <h3 class="box-title">Transactions as at start of cycle<a href="javascript:;"
                                onmousedown="toggleLedger('mydiv');">
                                <span style="font-size: 15px; padding-left: 10px;">COUA breakdown</span>
                            </a></h3>
                    </div>
                    <table class="table  table-bordered table-hover table-striped" id="data-table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Name</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allTransactions as $transaction)
                                @if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $branchcompareDateAlgo && $transaction->date <= $branchtargetDateAlgo && $transaction->credit)
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{number_format($transaction->credit, 2)}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>

        <div id="pendingWidget" style="
            position: fixed;
            top: 80%;
            left: 55%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 350px;
            background: #fefefeee;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            overflow: hidden;
            z-index: 9998;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            cursor: move;
            transition: transform 0.2s ease;
        ">
            <div class="slide active" style="display:block; padding:15px; border-left: 6px solid #ff4d4f;">
                <h4 style="margin:0; font-weight:bold; color:#ff4d4f;">Loans Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{ \App\Models\Loan::whereIn('status', ['pending', 'approved'])->count() }}</p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #ffa940;">
                <h4 style="margin:0; font-weight:bold; color:#ffa940;">Transactions Pending Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">{{ \App\Models\LoanTransactionUnapproved::count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #52c41a;">
                <h4 style="margin:0; font-weight:bold; color:#52c41a;">Reloans Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">{{ \App\Models\LoanTransactionsPending::count() }}</p>
            </div>

            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #1890ff;">
                <h4 style="margin:0; font-weight:bold; color:#1890ff;">Waivers Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{ \App\Models\WaiverTransactionUnapproved::where('status', 'pending')->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #722ed1;">
                <h4 style="margin:0; font-weight:bold; color:#722ed1;">Charges Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{ \App\Models\ChargeTransactionUnapproved::where('status', 'pending')->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #fa541c;">
                <h4 style="margin:0; font-weight:bold; color:#fa541c;">Clients Pending Approval</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{ \App\Models\Client::where('status', 'pending')->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #13c2c2;">
                <h4 style="margin:0; font-weight:bold; color:#13c2c2;">Advances Pending Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{ \App\Models\Advance::where('status', 'pending')->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #eb2f96;">
                <h4 style="margin:0; font-weight:bold; color:#eb2f96;">Advance-TopUps Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{ \App\Models\TopUp::where('status', 'pending')->count() }}
                </p>
            </div>
            <div class="slide" style="display:none; padding:15px; border-left: 6px solid #fa8c16;">
                <h4 style="margin:0; font-weight:bold; color:#fa8c16;">Pending Leave Approvals</h4>
                <p style="font-size:20px; margin:5px 0; font-weight:600;">
                    {{ \App\Models\Leave::where('status', 'pending')->count() }}
                </p>
            </div>
        </div>


    @endif

    @if($role->role_id == '1')
     @include('components.client-search-bottom-sheet')
    @endif

@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/serial.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/pie.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/gauge.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/funnel.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/plugins/export/export.min.js') }}" type="text/javascript"></script>
    @if(!Sentinel::inRole('client'))
        <script>


        </script>
    @endif
    @if($role->role_id == '1')
        <script>

            // Auto-slideshow
            const slides = document.querySelectorAll('#pendingWidget .slide');
            let currentSlide = 0;

            function showSlide(index) {
                slides.forEach((slide, i) => slide.style.display = i === index ? 'block' : 'none');
            }

            setInterval(() => {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }, 5000);

            showSlide(currentSlide);

            // Drag functionality (mouse & touch)
            const widget = document.getElementById('pendingWidget');
            let isDragging = false, offsetX = 0, offsetY = 0;

            // Mouse events
            widget.addEventListener('mousedown', e => {
                isDragging = true;
                offsetX = e.clientX - widget.getBoundingClientRect().left;
                offsetY = e.clientY - widget.getBoundingClientRect().top;
                widget.style.transform = 'none';
            });
            document.addEventListener('mousemove', e => {
                if (!isDragging) return;
                widget.style.left = e.clientX - offsetX + 'px';
                widget.style.top = e.clientY - offsetY + 'px';
            });
            document.addEventListener('mouseup', () => { isDragging = false; });

            // Touch events
            widget.addEventListener('touchstart', e => {
                isDragging = true;
                const touch = e.touches[0];
                offsetX = touch.clientX - widget.getBoundingClientRect().left;
                offsetY = touch.clientY - widget.getBoundingClientRect().top;
                widget.style.transform = 'none';
            });
            widget.addEventListener('touchmove', e => {
                if (!isDragging) return;
                e.preventDefault(); // <-- prevent page scroll
                const touch = e.touches[0];
                widget.style.left = touch.clientX - offsetX + 'px';
                widget.style.top = touch.clientY - offsetY + 'px';
            }, { passive: false });
            widget.addEventListener('touchend', () => { isDragging = false; });


            const gaugeElementAdmin = document.querySelector(".gauge");

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

            setGaugeValue(gaugeElementAdmin, '{{($pdua)}}');

            function toggleMyStaff(divid) {
                varon = divid + 'on';
                varoff = divid + 'off';

                if (document.getElementById(varon).style.display == 'block') {
                    document.getElementById(varon).style.display = 'none';
                    document.getElementById(varoff).style.display = 'block';
                }

                else {
                    document.getElementById(varoff).style.display = 'none';
                    document.getElementById(varon).style.display = 'block'
                }
            }

            function toggleCollections(divid) {
                console.log('hello')
                varon = divid + 'on';
                varoff = divid + 'offf';

                if (document.getElementById(varon).style.display == 'block') {
                    document.getElementById(varon).style.display = 'none';
                    document.getElementById(varoff).style.display = 'block';
                }

                else {
                    document.getElementById(varoff).style.display = 'none';
                    document.getElementById(varon).style.display = 'block'
                }
            }


            const companymonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            let companytargetDateName = companymonths[new Date('{{$branchtargetDate}}').getMonth()]
            let companyfirstDateName = companymonths[new Date('{{$branchfirstDate}}').getMonth()];
            let companysecondDateName = companymonths[new Date('{{$branchsecondDate}}').getMonth()]

            var collections =
                <?php    echo json_encode($collections); ?>;
            var given_out =
                <?php    echo json_encode($given_out); ?>;
            console.log(collections.reverse())
            console.log(given_out.reverse())

            var dates =
                <?php    echo json_encode($target_dates); ?>;
            console.log(dates.reverse())

            const allchrt = document.getElementById('companygraph');


            var chartId = new Chart(allchrt, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: "Actual Collections",
                        data: collections,
                        borderWidth: 1,
                    },
                    {
                        label: "Expected Collections",
                        data: given_out,
                        borderWidth: 1,
                    },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            });




            var given_out_total = "{{($collections_count_reloan / (0.001 + $collections_count_partpayment + $collections_count_fullpayment + $collections_count_reloan)) * 100}}"
            console.log(given_out_total)

            var xValues = ["Monthly Full and Part Payments %", "Monthly Reloans %"];
            var yValues = ['{{(($collections_count_partpayment + $collections_count_fullpayment) / (0.001 + $collections_count_partpayment + $collections_count_fullpayment + $collections_count_reloan)) * 100}}', '{{($collections_count_reloan / (0.001 + $collections_count_partpayment + $collections_count_fullpayment + $collections_count_reloan)) * 100}}'];
            var barColors = [
                "#F77FBE",
                "#87CEEB",
            ];
            var otherxValues = ["Monthly New Loans %", "Monthly Reloans %"];
            var otheryValues = ['{{($givenout_count_newloan / (0.001 + $givenout_count_newloan + $givenout_count_reloan)) * 100}}', '{{($givenout_count_reloan / (0.001 + $givenout_count_newloan + $givenout_count_reloan)) * 100}}'];

            new Chart("myChart", {
                type: "pie",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                    }]
                },
                options: {
                    title: {
                        display: true,
                    }
                }
            });

            new Chart("myOtherChart", {
                type: "pie",
                data: {
                    labels: otherxValues,
                    datasets: [{
                        backgroundColor: barColors,
                        data: otheryValues
                    }]
                },
                options: {
                    title: {
                        display: true,
                    }
                }
            });



        </script>

    @endif


    @if($role->role_id == '6')
   
    @endif

    @if($role->role_id == '4')
        <script>

  $('#managerPendingCarryOverModal').modal('show');


    var confirmSubmitCarryOverBtn = document.getElementById('confirmSubmitCarryOver');
                if (confirmSubmitCarryOverBtn) {
                    confirmSubmitCarryOverBtn.addEventListener('click', function () {
                        document.querySelector('#broughtForwardModal form').submit();
                    });
                }

                $(document).ready(function () {
                    $('#broughtForwardModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                });


            const slidesdm = document.querySelectorAll('#pendingWidget .slide');
            let currentSlidedm = 0;

            function showSlidedm(indexdm) {
                slidesdm.forEach((slidedm, idm) => slidedm.style.display = idm === indexdm ? 'block' : 'none');
            }

            setInterval(() => {
                currentSlidedm = (currentSlidedm + 1) % slidesdm.length;
                showSlidedm(currentSlidedm);
            }, 5000);

            showSlidedm(currentSlidedm);

            // Drag functionality (mouse & touch)
            const widgetdm = document.getElementById('pendingWidget');
            let isDraggingdm = false, offsetXdm = 0, offsetYdm = 0;

            // Mouse events
            widgetdm.addEventListener('mousedown', e => {
                isDraggingdm = true;
                offsetXdm = e.clientX - widgetdm.getBoundingClientRect().left;
                offsetYdm = e.clientY - widgetdm.getBoundingClientRect().top;
                widgetdm.style.transform = 'none';
            });
            document.addEventListener('mousemove', e => {
                if (!isDraggingdm) return;
                widgetdm.style.left = e.clientX - offsetXdm + 'px';
                widgetdm.style.top = e.clientY - offsetYdm + 'px';
            });
            document.addEventListener('mouseup', () => { isDraggingdm = false; });

            // Touch events
            widgetdm.addEventListener('touchstart', e => {
                isDraggingdm = true;
                const touchdm = e.touches[0];
                offsetXdm = touchdm.clientX - widgetdm.getBoundingClientRect().left;
                offsetYdm = touchdm.clientY - widgetdm.getBoundingClientRect().top;
                widgetdm.style.transform = 'none';
            });
            widgetdm.addEventListener('touchmove', e => {
                if (!isDraggingdm) return;
                e.preventDefault(); // prevent page scroll
                const touchdm = e.touches[0];
                widgetdm.style.left = touchdm.clientX - offsetXdm + 'px';
                widgetdm.style.top = touchdm.clientY - offsetYdm + 'px';
            }, { passive: false });
            widgetdm.addEventListener('touchend', () => { isDraggingdm = false; });
            function toggleDiv(divid) {

                varon = divid + 'on';
                varoff = divid + 'off';
                varoff2 = divid + 'staff';

                if (document.getElementById(varon).style.display == 'block') {
                    document.getElementById(varon).style.display = 'none';
                    document.getElementById(varoff2).style.display = 'none';
                    document.getElementById(varoff).style.display = 'block';
                }

                else {
                    document.getElementById(varoff).style.display = 'none';
                    document.getElementById(varon).style.display = 'block'
                }
            }


            function toggleMyStaff(divid) {
                varon = divid + 'on';
                varoff = divid + 'staff';
                varoff2 = divid + 'off'

                if (document.getElementById(varon).style.display == 'block') {
                    document.getElementById(varon).style.display = 'none';
                    document.getElementById(varoff2).style.display = 'none';
                    document.getElementById(varoff).style.display = 'block';
                }

                else {
                    document.getElementById(varoff).style.display = 'none';
                    document.getElementById(varon).style.display = 'block'
                }
            }

            function toggleLedger(divid) {

                varon = divid + 'on_new';
                varoff = divid + 'off_new';

                if (document.getElementById(varon).style.display == 'block') {
                    document.getElementById(varon).style.display = 'none';
                    document.getElementById(varoff).style.display = 'block';
                }

                else {
                    document.getElementById(varoff).style.display = 'none';
                    document.getElementById(varon).style.display = 'block'
                }
            }


      

            const gaugeElementBranch = document.querySelector(".gauge");

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

            setGaugeValue(gaugeElementBranch, "{{ $branch_data['pdua'] }}");


            const cty = document.getElementById('branchgraph');

            new Chart(cty, {
                type: 'bar',
                data: {
                    labels: ['24 ' + branchfirstDateName, '24 ' + branchsecondDateName, '24 ' + branchtargetDateName,],
                    datasets: [{
                        label: 'Branch collections as at end of cycle date',
                        data: ['{{$collected_total_1_months}}', '{{$collected_total_2_months}}', '{{$collected_total}}'],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endif

    @if($role->role_id == '3')
        @if($end !== 'NCI')
            <script>
                // console.log('hello')
                //Setting up the cycle count down

                $('#pendingApprovalModal').modal('show');

                var confirmSubmitCarryOverBtn = document.getElementById('confirmSubmitCarryOver');
                if (confirmSubmitCarryOverBtn) {
                    confirmSubmitCarryOverBtn.addEventListener('click', function () {
                        document.querySelector('#broughtForwardModal form').submit();
                    });
                }

                $(document).ready(function () {
                    $('#broughtForwardModal').modal({
                        backdrop: 'static',
                        keyboard: false
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

                var COUA = document.getElementById('coua');
                var TCC = document.getElementById('tcc');
                var given_out = document.getElementById('given_out')
                var mainDiv = document.getElementById('mydivon')

                function toggleCOUA() {
                    if (COUA.style.display == 'none') {
                        TCC.style.display = 'none'
                        mainDiv.style.display = 'none'
                        given_out.style.display = 'none'
                        COUA.style.display = 'block'
                    } else {
                        COUA.style.display = 'none'
                        mainDiv.style.display = 'block'
                        TCC.style.display = 'none'
                        given_out.style.display = 'none'
                    }
                }

                function toggleTCC() {
                    if (TCC.style.display == 'none') {
                        COUA.style.display = 'none'
                        mainDiv.style.display = 'none'
                        given_out.style.display = 'none'
                        TCC.style.display = 'block'

                    } else {
                        COUA.style.display = 'none'
                        mainDiv.style.display = 'block'
                        TCC.style.display = 'none'
                        given_out.style.display = 'none'
                    }

                }


                function toggleGivenOut() {
                    if (given_out.style.display == 'none') {
                        COUA.style.display = 'none'
                        mainDiv.style.display = 'none'
                        TCC.style.display = 'none'
                        given_out.style.display = 'block'
                    } else {
                        COUA.style.display = 'none'
                        mainDiv.style.display = 'block'
                        TCC.style.display = 'none'
                        given_out.style.display = 'none'
                    }

                }


                function toggleDiv(divid) {
                    varon = document.getElementById(divid + 'on');
                    varoff = document.getElementById(divid + 'off');

                    if (varon && varoff) {
                        if (varon.style.display === 'block') {
                            varon.style.display = 'none';
                            varoff.style.display = 'block';
                        } else {
                            varoff.style.display = 'none';
                            varon.style.display = 'block';
                        }
                    }
                }




                function toggleLedger(divid) {

                    varon = divid + 'on_new';
                    varoff = divid + 'off_new';

                    if (document.getElementById(varon).style.display == 'block') {
                        document.getElementById(varon).style.display = 'none';
                        document.getElementById(varoff).style.display = 'block';
                    }

                    else {
                        document.getElementById(varoff).style.display = 'none';
                        document.getElementById(varon).style.display = 'block'
                    }
                }


                const d = new Date();
                let month = d.getMonth() + 1;
                let year = d.getFullYear();
                let day = d.getDate();
                let todayDate = year + '-' + month + '-' + day;
                let countDownEndDate = year + '-' + month + '-' + '{{$end}}'
                if (todayDate >= countDownEndDate) {
                    countDownEndDate = year + '-' + (month + 1) + '-' + '{{$end}}'
                }
                const NewCountDownEndDate = new Date(countDownEndDate);
                var BrandNewCountDownEndDate = new Date(NewCountDownEndDate).getTime();

                var x = setInterval(function () {
                    var now = new Date().getTime();
                    var distance = BrandNewCountDownEndDate - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                 

                });



                // var layout = { width: 400, height: 300, margin: { t: 0, b: 0 },  paper_bgcolor: "transparent", };
                // Plotly.newPlot('pdua', data, layout);
                // var dateTarget = document.getElementById("target").innerHTML = '24 ' + targetDateName;
                const ctx = document.getElementById('graph');
                const ctx_new = document.getElementById('target_graph');
                var collections = <?php        echo json_encode($collected_amounts); ?>;
                var uncollections = <?php        echo json_encode($uncollected_amounts); ?>;
                var dates = <?php        echo json_encode($dates); ?>;
                var coua = <?php        echo json_encode($cycle_opening_uncollected_amounts[11]); ?>;
                for (let i = 0; i < coua.length; i++) {
                    console.log("Index:", i, "Value:", coua[i]);
                }

                console.log('HELLOOOO');


                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: dates,
                        datasets: [
                            {
                                label: 'Your uncollected as at end of cycle',
                                data: uncollections,
                                borderWidth: 1,
                                backgroundColor: '#ff1c4b'

                            },
                            {
                                label: 'Your collected as at end of cycle',
                                data: collections,
                                borderWidth: 1,
                                backgroundColor: '#57b7fa'

                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                var targets = <?php        echo json_encode($targets); ?>;
                var colors = <?php        echo json_encode($colors); ?>;

                new Chart(ctx_new, {
                    type: 'bar',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Cycle given out for last 12 months',
                            data: targets,
                            backgroundColor: colors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
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

                var test1 = <?php        echo json_encode($collected_amounts[12] / $cycle_opening_uncollected_amounts[12]); ?>;
                var test2 = <?php        echo json_encode($collected_amounts[12]); ?>;
                var coua = <?php        echo json_encode($cycle_opening_uncollected_amounts); ?>;
                for (var i = 0; i < coua.length; i++) {
                    console.log(coua[i]);
                }
                console.log(test1)
                console.log(test2)
               setGaugeValue(gaugeElement, "{{ $data['pdua'] }}");

                $('#data-table').DataTable({
                    dom: 'frtip',
                    "paging": true,
                    "lengthChange": true,
                    "displayLength": 15,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": true,
                    "order": [[5, "desc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": [] }
                    ],
                    "language": {
                        "lengthMenu": "{{ trans('general.lengthMenu') }}",
                        "zeroRecords": "{{ trans('general.zeroRecords') }}",
                        "info": "{{ trans('general.info') }}",
                        "infoEmpty": "{{ trans('general.infoEmpty') }}",
                        "search": "{{ trans('general.search') }}",
                        "infoFiltered": "{{ trans('general.infoFiltered') }}",
                        "paginate": {
                            "first": "{{ trans('general.first') }}",
                            "last": "{{ trans('general.last') }}",
                            "next": "{{ trans('general.next') }}",
                            "previous": "{{ trans('general.previous') }}"
                        }
                    },
                    responsive: false
                });

            





            </script>

            <style>
    .province-row > td,
    .branch-row > td {
        vertical-align: middle !important;
    }

    .consultant-container td {
        padding: 10px !important;
    }

    .table > thead > tr > th,
    .table > tbody > tr > td {
        vertical-align: middle;
        font-size: 13px;
    }

    .badge {
        font-size: 12px;
        padding: 5px 8px;
    }

    .label {
        font-size: 11px;
        padding: 5px 7px;
        display: inline-block;
    }

    .toggle-btn,
    .toggle-consultants {
        border-radius: 3px;
    }

    .box-header .box-title {
        font-weight: 600;
    }

    .branch-container-row {
        transition: all 0.2s ease-in-out;
    }
</style>
        @endif
    @endif
@endsection
@extends('layouts.master')

@section('title', 'Cash Health')

@section('content_header')
@stop

@section('content')

@php

    $office = $cashHealth['office'] ?? [];
    $cycle = $cashHealth['cycle'] ?? [];
    $scores = $cashHealth['scores'] ?? [];
    $financials = $cashHealth['financials'] ?? [];
    $loans = $cashHealth['loans'] ?? [];
    $reasons = $cashHealth['reasons'] ?? [];

    $overallScore = (float) ($scores['overall'] ?? 0);
    $disbursementScore = (float) ($scores['disbursement'] ?? 0);
    $collectionScore = (float) ($scores['collection'] ?? 0);
    $residualCashScore = (float) ($scores['residual_cash'] ?? 0);
    $monthsOfCover = (float) ($scores['months_of_cover'] ?? 0);
    $fullPaymentRatio = (float) ($scores['full_payment_ratio'] ?? 0);

    $status = strtolower($scores['status'] ?? 'unknown');

    $statusConfig = match ($status) {

        'green' => [
            'label' => 'Healthy',
            'bg' => '#ecfdf5',
            'border' => '#c9f3df',
            'color' => '#087443',
            'iconBg' => '#d6f7e7',
            'icon' => 'fa-check',
            'description' =>
                'The office is currently within a healthy cash position.'
        ],

        'amber' => [
            'label' => 'Needs attention',
            'bg' => '#fff8e8',
            'border' => '#f6df9f',
            'color' => '#9a6500',
            'iconBg' => '#ffedbf',
            'icon' => 'fa-exclamation',
            'description' =>
                'Some cash health indicators require attention this cycle.'
        ],

        'red' => [
            'label' => 'At risk',
            'bg' => '#fff0f0',
            'border' => '#f4cccc',
            'color' => '#a72828',
            'iconBg' => '#ffdede',
            'icon' => 'fa-exclamation-triangle',
            'description' =>
                'The office is currently showing a significant cash health risk.'
        ],

        default => [
            'label' => 'Unknown',
            'bg' => '#f5f6f8',
            'border' => '#e5e7eb',
            'color' => '#4b5563',
            'iconBg' => '#e5e7eb',
            'icon' => 'fa-question',
            'description' =>
                'Cash health status is currently unavailable.'
        ]
    };

    $scoreColor = function ($score) {

        if ($score >= 80) {
            return '#20a464';
        }

        if ($score >= 60) {
            return '#e3a21a';
        }

        return '#d94b4b';
    };

    $money = function ($value) {
        return 'K' . number_format((float) $value, 0);
    };

@endphp


<div style="
    max-width:1450px;
    margin:0 auto;
    padding:10px 10px 50px;
    font-family:Arial, Helvetica, sans-serif;
">


    {{-- ========================================================= --}}
    {{-- HEADER                                                    --}}
    {{-- ========================================================= --}}

 <div style="
    text-align:right;
    padding:12px 18px;
    border:1px solid #e6e9ef;
    border-radius:12px;
    background:#fff;
">

    <div style="
        font-size:10px;
        font-weight:700;
        letter-spacing:1px;
        color:#8a93a3;
        margin-bottom:7px;
    ">
        CURRENT CYCLE
    </div>


<form
    method="GET"
    action="{{ route('cash_health.show', ['id' => $id]) }}"
    style="
        margin:0;
    "
>

        <select
            name="cycle_start"
            onchange="this.form.submit()"
            style="
                border:1px solid #dfe3e8;
                border-radius:8px;
                padding:8px 35px 8px 10px;
                font-size:13px;
                color:#343b48;
                background:#fff;
                cursor:pointer;
                outline:none;
            "
        >

            @foreach($availableCycles as $availableCycle)

                <option
                    value="{{ $availableCycle['start'] }}"
                    {{ $cycleStart === $availableCycle['start'] ? 'selected' : '' }}
                >
                    {{ \Carbon\Carbon::parse($availableCycle['start'])->format('d M Y') }}
                    →
                    {{ \Carbon\Carbon::parse($availableCycle['end'])->format('d M Y') }}
                </option>

            @endforeach

        </select>

    </form>


    <div style="
        margin-top:7px;
        font-size:12px;
        color:#8a92a0;
    ">

        Selected cycle:

        <strong style="
            color:#343b48;
            font-weight:600;
        ">
            {{ \Carbon\Carbon::parse($cycleStart)->format('d M Y') }}

            →

            {{ \Carbon\Carbon::parse($cycleEnd)->format('d M Y') }}
        </strong>

    </div>

</div>



    {{-- ========================================================= --}}
    {{-- OVERALL HEALTH                                            --}}
    {{-- ========================================================= --}}

    <div style="
        min-height:150px;
        border-radius:18px;
        padding:30px 35px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:38px;
        background:{{ $statusConfig['bg'] }};
        border:1px solid {{ $statusConfig['border'] }};
        color:{{ $statusConfig['color'] }};
    ">

        <div style="
            display:flex;
            align-items:center;
            gap:20px;
        ">

            <div style="
                width:60px;
                height:60px;
                border-radius:16px;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:22px;
                background:{{ $statusConfig['iconBg'] }};
                color:{{ $statusConfig['color'] }};
            ">
                <i class="fas {{ $statusConfig['icon'] }}"></i>
            </div>


            <div>

                <div style="
                    font-size:11px;
                    font-weight:700;
                    letter-spacing:1.3px;
                    opacity:.75;
                ">
                    OVERALL CASH HEALTH
                </div>

                <div style="
                    font-size:27px;
                    font-weight:700;
                    margin-top:3px;
                ">
                    {{ $statusConfig['label'] }}
                </div>

                <div style="
                    margin-top:5px;
                    font-size:13px;
                    opacity:.75;
                ">
                    {{ $statusConfig['description'] }}
                </div>

            </div>

        </div>


        <div style="
            display:flex;
            align-items:baseline;
            line-height:1;
        ">

            <div style="
                font-size:68px;
                font-weight:750;
                letter-spacing:-4px;
            ">
                {{ number_format($overallScore, 0) }}
            </div>

            <div style="
                font-size:16px;
                opacity:.55;
                margin-left:5px;
            ">
                / 100
            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- SECTION HEADING                                           --}}
    {{-- ========================================================= --}}

    <div style="
        margin-bottom:17px;
    ">

        <h2 style="
            font-size:18px;
            font-weight:700;
            margin:0;
            color:#202633;
        ">
            Cash Health Score
        </h2>

        <p style="
            margin:5px 0 0;
            color:#7c8492;
            font-size:13px;
        ">
            Your overall score is made up of three key areas.
        </p>

    </div>



    {{-- ========================================================= --}}
    {{-- SCORE CARDS                                               --}}
    {{-- ========================================================= --}}

    <div style="
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:18px;
        margin-bottom:40px;
    ">


        {{-- DISBURSEMENT --}}

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:16px;
            padding:23px;
            box-shadow:0 2px 7px rgba(25,35,55,.04);
        ">

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:center;
            ">

                <div style="
                    width:42px;
                    height:42px;
                    border-radius:11px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:16px;
                    background:#eaf2ff;
                    color:#3677e8;
                ">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>

                <div style="
                    padding:5px 9px;
                    border-radius:7px;
                    background:#f5f6f8;
                    color:#7b8493;
                    font-size:11px;
                    font-weight:700;
                ">
                    35%
                </div>

            </div>


            <div style="
                margin-top:22px;
                font-size:14px;
                font-weight:600;
                color:#4a5260;
            ">
                Disbursement
            </div>


            <div style="
                display:flex;
                align-items:baseline;
                margin-top:3px;
            ">

                <span style="
                    font-size:38px;
                    font-weight:750;
                    letter-spacing:-1.5px;
                    color:#1f2633;
                ">
                    {{ number_format($disbursementScore, 0) }}
                </span>

                <small style="
                    font-size:13px;
                    color:#9aa1ad;
                    margin-left:4px;
                ">
                    /100
                </small>

            </div>


            <div style="
                height:7px;
                border-radius:10px;
                background:#eef0f3;
                margin-top:14px;
                overflow:hidden;
            ">

                <div style="
                    height:100%;
                    border-radius:10px;
                    background:{{ $scoreColor($disbursementScore) }};
                    width:{{ min(100,max(0,$disbursementScore)) }}%;
                ">
                </div>

            </div>


            <div style="
                border-top:1px solid #eef0f3;
                margin-top:20px;
                padding-top:17px;
            ">

                <div style="
                    font-size:9px;
                    font-weight:700;
                    letter-spacing:1px;
                    color:#9aa2af;
                ">
                    ACTUAL DISBURSED
                </div>

                <div style="
                    font-size:16px;
                    font-weight:700;
                    color:#303744;
                    margin-top:3px;
                ">
                    {{ $money(
                        $financials['actual_disbursed'] ?? 0
                    ) }}
                </div>

                <div style="
                    font-size:12px;
                    color:#8a92a0;
                    margin-top:4px;
                ">
                    Minimum target:
                    <strong>
                        {{ $money(
                            $financials['minimum_loan_target'] ?? 0
                        ) }}
                    </strong>
                </div>

            </div>

        </div>



        {{-- COLLECTION QUALITY --}}

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:16px;
            padding:23px;
            box-shadow:0 2px 7px rgba(25,35,55,.04);
        ">

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:center;
            ">

                <div style="
                    width:42px;
                    height:42px;
                    border-radius:11px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:16px;
                    background:#f0eaff;
                    color:#7654d6;
                ">
                    <i class="fas fa-chart-line"></i>
                </div>

                <div style="
                    padding:5px 9px;
                    border-radius:7px;
                    background:#f5f6f8;
                    color:#7b8493;
                    font-size:11px;
                    font-weight:700;
                ">
                    35%
                </div>

            </div>


            <div style="
                margin-top:22px;
                font-size:14px;
                font-weight:600;
                color:#4a5260;
            ">
                Collection Quality
            </div>


            <div style="
                display:flex;
                align-items:baseline;
                margin-top:3px;
            ">

                <span style="
                    font-size:38px;
                    font-weight:750;
                    letter-spacing:-1.5px;
                    color:#1f2633;
                ">
                    {{ number_format($collectionScore, 0) }}
                </span>

                <small style="
                    font-size:13px;
                    color:#9aa1ad;
                    margin-left:4px;
                ">
                    /100
                </small>

            </div>


            <div style="
                height:7px;
                border-radius:10px;
                background:#eef0f3;
                margin-top:14px;
                overflow:hidden;
            ">

                <div style="
                    height:100%;
                    border-radius:10px;
                    background:{{ $scoreColor($collectionScore) }};
                    width:{{ min(100,max(0,$collectionScore)) }}%;
                ">
                </div>

            </div>


            <div style="
                border-top:1px solid #eef0f3;
                margin-top:20px;
                padding-top:17px;
                display:flex;
                justify-content:space-between;
            ">

                <div>

                    <div style="
                        font-size:9px;
                        font-weight:700;
                        letter-spacing:1px;
                        color:#9aa2af;
                    ">
                        DEFAULTS
                    </div>

                    <div style="
                        font-size:16px;
                        font-weight:700;
                        color:#303744;
                        margin-top:3px;
                    ">
                        {{ $money(
                            $financials['defaults'] ?? 0
                        ) }}
                    </div>

                </div>


                <div style="
                    text-align:right;
                ">

                    <div style="
                        font-size:9px;
                        font-weight:700;
                        letter-spacing:1px;
                        color:#9aa2af;
                    ">
                        FULL-PAYMENT
                    </div>

                    <div style="
                        font-size:16px;
                        font-weight:700;
                        color:#303744;
                        margin-top:3px;
                    ">
                        {{ number_format(
                            $fullPaymentRatio,
                            1
                        ) }}%
                    </div>

                </div>

            </div>

        </div>



        {{-- RESIDUAL CASH --}}

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:16px;
            padding:23px;
            box-shadow:0 2px 7px rgba(25,35,55,.04);
        ">

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:center;
            ">

                <div style="
                    width:42px;
                    height:42px;
                    border-radius:11px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:16px;
                    background:#e8f8f0;
                    color:#19945c;
                ">
                    <i class="fas fa-wallet"></i>
                </div>

                <div style="
                    padding:5px 9px;
                    border-radius:7px;
                    background:#f5f6f8;
                    color:#7b8493;
                    font-size:11px;
                    font-weight:700;
                ">
                    30%
                </div>

            </div>


            <div style="
                margin-top:22px;
                font-size:14px;
                font-weight:600;
                color:#4a5260;
            ">
                Residual Cash
            </div>


            <div style="
                display:flex;
                align-items:baseline;
                margin-top:3px;
            ">

                <span style="
                    font-size:38px;
                    font-weight:750;
                    letter-spacing:-1.5px;
                    color:#1f2633;
                ">
                    {{ number_format($residualCashScore, 0) }}
                </span>

                <small style="
                    font-size:13px;
                    color:#9aa1ad;
                    margin-left:4px;
                ">
                    /100
                </small>

            </div>


            <div style="
                height:7px;
                border-radius:10px;
                background:#eef0f3;
                margin-top:14px;
                overflow:hidden;
            ">

                <div style="
                    height:100%;
                    border-radius:10px;
                    background:{{ $scoreColor($residualCashScore) }};
                    width:{{ min(100,max(0,$residualCashScore)) }}%;
                ">
                </div>

            </div>


            <div style="
                border-top:1px solid #eef0f3;
                margin-top:20px;
                padding-top:17px;
            ">

                <div style="
                    font-size:9px;
                    font-weight:700;
                    letter-spacing:1px;
                    color:#9aa2af;
                ">
                    RESIDUAL CASH
                </div>

                <div style="
                    font-size:16px;
                    font-weight:700;
                    color:#303744;
                    margin-top:3px;
                ">
                    {{ $money(
                        $financials['residual_cash'] ?? 0
                    ) }}
                </div>

                <div style="
                    font-size:12px;
                    color:#8a92a0;
                    margin-top:4px;
                ">
                    <strong>
                        {{ number_format($monthsOfCover,1) }}
                    </strong>
                    months of cover
                </div>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- WHAT IS DRIVING THE SCORE                                 --}}
    {{-- ========================================================= --}}

    <div style="
        margin-bottom:17px;
    ">

        <h2 style="
            font-size:18px;
            font-weight:700;
            margin:0;
            color:#202633;
        ">
            What is driving the score?
        </h2>

        <p style="
            margin:5px 0 0;
            color:#7c8492;
            font-size:13px;
        ">
            The figures below explain what is affecting this cycle's Cash Health.
        </p>

    </div>


    <div style="
        background:#fff;
        border:1px solid #e7eaf0;
        border-radius:16px;
        padding:0 25px;
        margin-bottom:40px;
    ">


        {{-- DISBURSEMENT REASON --}}

        <div style="
            display:flex;
            gap:17px;
            padding:22px 0;
            border-bottom:1px solid #edf0f3;
        ">

            <div style="
                width:42px;
                height:42px;
                min-width:42px;
                border-radius:11px;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#eaf2ff;
                color:#3677e8;
            ">
                <i class="fas fa-hand-holding-usd"></i>
            </div>


            <div>

                <div style="
                    font-size:13px;
                    font-weight:700;
                    color:#343b48;
                    margin-bottom:5px;
                ">
                    Disbursement
                </div>

                <div style="
                    color:#737c8b;
                    font-size:13px;
                    line-height:1.65;
                ">

                    @if(isset($reasons['disbursement']))

                        {{ $reasons['disbursement'] }}

                    @else

                        Actual disbursement is
                        <strong style="color:#303744;">
                            {{ $money(
                                $financials['actual_disbursed'] ?? 0
                            ) }}
                        </strong>

                        against a minimum loan target of
                        <strong style="color:#303744;">
                            {{ $money(
                                $financials['minimum_loan_target'] ?? 0
                            ) }}
                        </strong>.

                    @endif

                </div>

            </div>

        </div>



        {{-- COLLECTION REASON --}}

        <div style="
            display:flex;
            gap:17px;
            padding:22px 0;
            border-bottom:1px solid #edf0f3;
        ">

            <div style="
                width:42px;
                height:42px;
                min-width:42px;
                border-radius:11px;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#f0eaff;
                color:#7654d6;
            ">
                <i class="fas fa-chart-line"></i>
            </div>


            <div>

                <div style="
                    font-size:13px;
                    font-weight:700;
                    color:#343b48;
                    margin-bottom:5px;
                ">
                    Collection Quality
                </div>

                <div style="
                    color:#737c8b;
                    font-size:13px;
                    line-height:1.65;
                ">

                    @if(isset($reasons['collection']))

                        {{ $reasons['collection'] }}

                    @else

                        Defaults are currently
                        <strong style="color:#303744;">
                            {{ $money(
                                $financials['defaults'] ?? 0
                            ) }}
                        </strong>

                        and the full-payment ratio is
                        <strong style="color:#303744;">
                            {{ number_format(
                                $fullPaymentRatio,
                                1
                            ) }}%
                        </strong>.

                    @endif

                </div>

            </div>

        </div>



        {{-- RESIDUAL CASH REASON --}}

        <div style="
            display:flex;
            gap:17px;
            padding:22px 0;
        ">

            <div style="
                width:42px;
                height:42px;
                min-width:42px;
                border-radius:11px;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#e8f8f0;
                color:#19945c;
            ">
                <i class="fas fa-wallet"></i>
            </div>


            <div>

                <div style="
                    font-size:13px;
                    font-weight:700;
                    color:#343b48;
                    margin-bottom:5px;
                ">
                    Residual Cash
                </div>

                <div style="
                    color:#737c8b;
                    font-size:13px;
                    line-height:1.65;
                ">

                    @if(isset($reasons['residual_cash']))

                        {{ $reasons['residual_cash'] }}

                    @else

                        Residual cash is
                        <strong style="color:#303744;">
                            {{ $money(
                                $financials['residual_cash'] ?? 0
                            ) }}
                        </strong>

                        providing approximately
                        <strong style="color:#303744;">
                            {{ number_format(
                                $monthsOfCover,
                                1
                            ) }}
                            months
                        </strong>
                        of cover.

                    @endif

                </div>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- CASH POSITION                                             --}}
    {{-- ========================================================= --}}

    <div style="
        margin-bottom:17px;
    ">

        <h2 style="
            font-size:18px;
            font-weight:700;
            margin:0;
            color:#202633;
        ">
            Cash Position
        </h2>

        <p style="
            margin:5px 0 0;
            color:#7c8492;
            font-size:13px;
        ">
            The financial position behind the Cash Health assessment.
        </p>

    </div>


    <div style="
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:15px;
        margin-bottom:25px;
    ">


        {{-- GIVEN OUT --}}

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:15px;
            padding:20px;
        ">

            <div style="
                color:#8a94a4;
                font-size:15px;
                margin-bottom:18px;
            ">
                <i class="fas fa-arrow-circle-down"></i>
            </div>

            <div style="
                font-size:9px;
                font-weight:700;
                letter-spacing:1px;
                color:#9aa2af;
            ">
                GIVEN OUT
            </div>

            <div style="
                font-size:22px;
                font-weight:750;
                margin-top:5px;
                color:#252c38;
            ">
                {{ $money($loans['given_out'] ?? 0) }}
            </div>

            <div style="
                font-size:11px;
                color:#929aa7;
                margin-top:5px;
            ">
                Previous cycle
            </div>

        </div>



        {{-- EXPECTED REPAYMENT --}}

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:15px;
            padding:20px;
        ">

            <div style="
                color:#8a94a4;
                font-size:15px;
                margin-bottom:18px;
            ">
                <i class="fas fa-money-bill-wave"></i>
            </div>

            <div style="
                font-size:9px;
                font-weight:700;
                letter-spacing:1px;
                color:#9aa2af;
            ">
                EXPECTED REPAYMENT
            </div>

            <div style="
                font-size:22px;
                font-weight:750;
                margin-top:5px;
                color:#252c38;
            ">
                {{ $money(
                    $financials['maximum_expected_repayment'] ?? 0
                ) }}
            </div>

            <div style="
                font-size:11px;
                color:#929aa7;
                margin-top:5px;
            ">
                Maximum expected repayment
            </div>

        </div>



        {{-- CASH REQUIREMENT --}}

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:15px;
            padding:20px;
        ">

            <div style="
                color:#8a94a4;
                font-size:15px;
                margin-bottom:18px;
            ">
                <i class="fas fa-calculator"></i>
            </div>

            <div style="
                font-size:9px;
                font-weight:700;
                letter-spacing:1px;
                color:#9aa2af;
            ">
                CASH REQUIREMENT
            </div>

            <div style="
                font-size:22px;
                font-weight:750;
                margin-top:5px;
                color:#252c38;
            ">
                {{ $money(
                    $financials['expected_cash_requirement'] ?? 0
                ) }}
            </div>

            <div style="
                font-size:11px;
                color:#929aa7;
                margin-top:5px;
            ">
                Expected requirement
            </div>

        </div>



        {{-- RESIDUAL CASH --}}

        <div style="
            background:#f8fbff;
            border:1px solid #dbe8fa;
            border-radius:15px;
            padding:20px;
        ">

            <div style="
                color:#3677e8;
                font-size:15px;
                margin-bottom:18px;
            ">
                <i class="fas fa-wallet"></i>
            </div>

            <div style="
                font-size:9px;
                font-weight:700;
                letter-spacing:1px;
                color:#9aa2af;
            ">
                RESIDUAL CASH
            </div>

            <div style="
                font-size:22px;
                font-weight:750;
                margin-top:5px;
                color:
                    {{ ($financials['residual_cash'] ?? 0) >= 0
                        ? '#168550'
                        : '#c53d3d' }};
            ">
                {{ $money(
                    $financials['residual_cash'] ?? 0
                ) }}
            </div>

            <div style="
                font-size:11px;
                color:#929aa7;
                margin-top:5px;
            ">
                Cash remaining after requirements
            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- COVER + OFFICE                                            --}}
    {{-- ========================================================= --}}

    <div style="
        display:grid;
        grid-template-columns:2fr 1fr;
        gap:18px;
    ">


        <!-- {{-- COVER --}}  -->
         <!-- REMOVE THIS -->

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:16px;
            padding:24px;
        ">

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
            ">

                <div>

                    <div style="
                        font-size:10px;
                        letter-spacing:1px;
                        font-weight:700;
                        color:#929aa7;
                    ">
                        RESIDUAL CASH COVER
                    </div>

                    <h3 style="
                        font-size:36px;
                        margin:7px 0 0;
                        font-weight:750;
                        letter-spacing:-1.5px;
                        color:#202633;
                    ">
                        {{ number_format(
                            $monthsOfCover,
                            1
                        ) }}

                        <span style="
                            font-size:14px;
                            color:#89919f;
                            font-weight:500;
                            letter-spacing:0;
                        ">
                            months
                        </span>

                    </h3>

                </div>


                <div style="
                    width:43px;
                    height:43px;
                    border-radius:11px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    background:#e8f8f0;
                    color:#19945c;
                ">
                    <i class="fas fa-shield-alt"></i>
                </div>

            </div>


            <div style="
                height:9px;
                border-radius:20px;
                background:#edf0f3;
                margin-top:25px;
                overflow:hidden;
            ">

                <div style="
                    height:100%;
                    border-radius:20px;
                    background:#20a464;
                    width:
                        {{ min(
                            100,
                            max(
                                0,
                                ($monthsOfCover / 2) * 100
                            )
                        ) }}%;
                ">
                </div>

            </div>


            <div style="
                display:flex;
                justify-content:space-between;
                margin-top:12px;
                color:#8b93a0;
                font-size:11px;
            ">

                <span>
                    Average monthly irregular reserve
                </span>

                <strong style="color:#454d59;">
                    {{ $money(
                        $financials[
                            'averageMonthlyIrregularCostReserve'
                        ] ?? 0
                    ) }}
                </strong>

            </div>

        </div>


  <!-- REMOVE THIS -->
        {{-- OFFICE OVERVIEW --}}

        <div style="
            background:#fff;
            border:1px solid #e7eaf0;
            border-radius:16px;
            padding:24px;
        ">

            <div style="
                font-size:14px;
                font-weight:700;
                margin-bottom:13px;
                color:#303744;
            ">
                Office Overview
            </div>


            <div style="
                display:flex;
                justify-content:space-between;
                padding:12px 0;
                border-bottom:1px solid #edf0f3;
                font-size:12px;
            ">

                <span style="color:#8a93a1;">
                    Workstations
                </span>

                <strong style="color:#343b48;">
                    {{ number_format(
                        $office['workstations'] ?? 0
                    ) }}
                </strong>

            </div>


            <div style="
                display:flex;
                justify-content:space-between;
                padding:12px 0;
                border-bottom:1px solid #edf0f3;
                font-size:12px;
            ">

                <span style="color:#8a93a1;">
                    Active loans
                </span>

                <strong style="color:#343b48;">
                    {{ number_format(
                        $loans['number_of_loans'] ?? 0
                    ) }}
                </strong>

            </div>


            <div style="
                display:flex;
                justify-content:space-between;
                padding:12px 0;
                font-size:12px;
            ">

                <span style="color:#8a93a1;">
                    Total collected
                </span>

                <strong style="color:#343b48;">
                    {{ $money(
                        $loans['total_collected'] ?? 0
                    ) }}
                </strong>

            </div>

        </div>

    </div>

</div>

@stop
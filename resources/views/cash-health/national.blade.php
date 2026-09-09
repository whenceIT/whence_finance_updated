@extends('layouts.master')

@section('content')

<style>
    .cash-health-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 18px;
    }

    .cash-health-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cash-health-guide {
        background: #fff;
        border: 1px solid #e4e8ee;
        border-radius: 10px;
        overflow: hidden;
    }

    .cash-health-guide-grid {
        display: grid;
        grid-template-columns: 150px 1.2fr 1fr 1fr 145px;
    }

    .guide-section {
        padding: 14px 16px;
        border-right: 1px solid #edf0f3;
    }

    .guide-section:last-child {
        border-right: none;
    }

    .guide-heading {
        font-size: 15px;
        font-weight: 700;
        color: #202633;
        margin-bottom: 7px;
    }

    .guide-text {
        font-size: 20px;
        color: #697386;
        line-height: 1.5;
    }

    .status-bands {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .status-band {
        padding: 4px 7px;
        border-radius: 5px;
        font-size: 15px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-healthy {
        background: #ecfdf5;
        color: #15803d;
    }

    .status-attention {
        background: #fffbeb;
        color: #b45309;
    }

    .status-risk {
        background: #fef2f2;
        color: #dc2626;
    }

    .score-weights {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 15px;
        color: #697386;
    }


    /* TABLET */
    @media (max-width: 1100px) {

        .cash-health-guide-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .guide-section {
            border-bottom: 1px solid #edf0f3;
        }

        .guide-section:first-child {
            grid-column: 1 / -1;
        }

    }


    /* MOBILE */
    @media (max-width: 768px) {

        .cash-health-top {
            flex-direction: column;
            gap: 15px;
        }

        .cash-health-controls {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .cash-health-controls > * {
            width: 100%;
            box-sizing: border-box;
        }

        .cash-health-guide-grid {
            grid-template-columns: 1fr;
        }

        .guide-section {
            border-right: none;
            border-bottom: 1px solid #edf0f3;
        }

        .guide-section:last-child {
            border-bottom: none;
        }

    }


    /* SMALL PHONES */
    @media (max-width: 480px) {

        .cash-health-title {
            font-size: 22px !important;
        }

        .cash-cycle-selector {
            flex-direction: column;
            align-items: stretch !important;
            padding: 10px !important;
        }

        .cash-cycle-select {
            width: 100%;
            max-width: 100% !important;
        }

    }
</style>

<div style="
    padding:30px;
    background:#f6f8fb;
    min-height:100vh;
">

    @php

        $financials =
            $nationalHealth['financials'] ?? [];

        $scores =
            $nationalHealth['scores'] ?? [];
$status = match (strtolower($scores['status'] ?? 'red')) {
    'red'   => 'At Risk',
    'amber' => 'Needs Attention',
    'green' => 'Healthy',
    default => 'At Risk',
};


        /*
        |--------------------------------------------------------------------------
        | STATUS COLORS
        |--------------------------------------------------------------------------
        */

        $statusColor = match($status) {

            'GREEN' => '#15803d',

            'AMBER' => '#b45309',

            default => '#dc2626'

        };


        $statusBackground = match($status) {

            'GREEN' => '#dcfce7',

            'AMBER' => '#fef3c7',

            default => '#fee2e2'

        };

    @endphp




{{-- HEADER --}}
<div style="margin-bottom:22px;">

{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<div class="cash-health-top">

    {{-- TITLE --}}
    <div>
        <div style="
            font-size:10px;
            font-weight:700;
            letter-spacing:1.5px;
            color:#8a93a3;
            margin-bottom:5px;
        ">
            CASH MANAGEMENT
        </div>

        <h1 class="cash-health-title" style="
            font-size:28px;
            font-weight:700;
            margin:0;
            color:#202633;
            letter-spacing:-.6px;
            line-height:1.2;
        ">
            National Cash Health
        </h1>

        <div style="
            color:#697386;
            margin-top:5px;
            font-size:13px;
        ">
            Organization-wide cash position ||   {{ $nationalHealth['office_count'] ?? 0 }} offices
        </div>
    </div>


    {{-- RIGHT SIDE --}}
    <div class="cash-health-controls">

        {{-- GUIDE BUTTON --}}
        <button
            type="button"
            onclick="openCashHealthGuide()"
            style="
                height:38px;
                border:1px solid #dfe3e8;
                background:#fff;
                color:#343b48;
                border-radius:8px;
                padding:0 13px;
                font-size:12px;
                font-weight:600;
                cursor:pointer;
                white-space:nowrap;
            "
        >
            <i class="fa fa-info-circle" style="margin-right:5px;"></i>
            Cash Health Guide
        </button>


        {{-- CASH CYCLE --}}
        <div
            class="cash-cycle-selector"
            style="
                display:flex;
                align-items:center;
                gap:10px;
                height:38px;
                padding:0 10px 0 13px;
                background:#fff;
                border:1px solid #e1e5eb;
                border-radius:8px;
            "
        >
            <span style="
                font-size:10px;
                font-weight:700;
                letter-spacing:.7px;
                color:#8a93a3;
                white-space:nowrap;
            ">
                CASH CYCLE
            </span>

            <form
                method="GET"
                action="{{ route('cash_health.national') }}"
                style="margin:0;"
            >
                <select
                    name="cycle_start"
                    onchange="this.form.submit()"
                    class="cash-cycle-select"
                    style="
                        border:none;
                        padding:0 20px 0 0;
                        font-size:12px;
                        font-weight:600;
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
        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- INSTITUTION CASH BALANCE --}}
{{-- ========================================================= --}}

<div style="
    margin-top:22px;
    background:#fff;
    border:1px solid #dfe3e8;
    border-radius:12px;
    padding:24px 26px;
    box-shadow:0 2px 8px rgba(20,30,50,.04);
">

    <div style="
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:20px;
        flex-wrap:wrap;
    ">

        {{-- LABEL --}}
        <div>

            <div style="
                font-size:11px;
                font-weight:700;
                letter-spacing:1.2px;
                color:#7b8494;
                text-transform:uppercase;
                margin-bottom:7px;
            ">
                Institution Cash Balance
            </div>

            <div style="
                font-size:12px;
                color:#697386;
            ">
                Total available cash across the institution excluding bank 
            </div>

        </div>


        {{-- BALANCE --}}
        <div style="
            text-align:right;
            margin-left:auto;
        ">

                <div style="
                font-size:40px;
                font-weight:700;
                margin-top:12px;
                color:{{ ($totalBalance ?? 0) < 0 ? '#15803d' : '#15803d' }};
            ">

                K{{ number_format(
                    $totalBalance ?? 0,
                    2
                ) }}

            </div>


        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- QUICK GUIDE --}}
{{-- ========================================================= --}}

<div class="cash-health-guide" style="margin-top:22px;">

<div class="cash-health-guide-grid" style="
    display:grid;
    grid-template-columns:160px 1fr 1fr;
">

    {{-- GUIDE TITLE --}}
    <div
        class="guide-section"
        style="
            background:#f8fafc;
            display:flex;
            align-items:center;
        "
    >
        <div>
            <div style="
                font-size:15px;
                font-weight:700;
                letter-spacing:.8px;
                color:#343b48;
                text-transform:uppercase;
                margin-bottom:5px;
            ">
                Guide
            </div>
        </div>
    </div>


    {{-- OVERALL SCORE --}}
    <div class="guide-section">

        <div class="guide-heading">
            Overall Score
            <span style="
                color:#9aa2af;
                font-weight:400;
            ">
                / 100
            </span>
        </div>

        <div class="status-bands">

            <span class="status-band status-healthy">
                80–100 Healthy
            </span>

            <span class="status-band status-attention">
                60–79 Attention
            </span>

            <span class="status-band status-risk">
                0–59 At Risk
            </span>

        </div>

    </div>


    {{-- SCORE WEIGHT --}}
    <div class="guide-section">

        <div class="guide-heading">
            Score Weight
        </div>

        <div class="score-weights">

            <span>
                <strong>35%</strong>
                Disbursement
            </span>

            <span>
                <strong>35%</strong>
                Collection
            </span>

            <span>
                <strong>30%</strong>
                Residual
            </span>

        </div>

    </div>

</div>

</div>

</div>


{{-- ========================================================= --}}
{{-- SUMMARY CARDS --}}
{{-- ========================================================= --}}

<style>

    .cash-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .cash-summary-card {
        background: #fff;
        border: 1px solid #e6e9ef;
        border-radius: 14px;
        padding: 22px;
    }

    .cash-summary-description {
        margin-top: 10px;
        font-size: 12px;
        line-height: 1.6;
        font-weight: 600;
        color: #4b5563;
    }


    /* Tablet */

    @media (max-width: 900px) {

        .cash-summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }

    }


    /* Mobile */

    @media (max-width: 600px) {

        .cash-summary-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .cash-summary-card {
            padding: 18px;
        }

    }

</style>


<div class="cash-summary-grid">


    {{-- ===================================================== --}}
    {{-- OVERALL SCORE --}}
    {{-- ===================================================== --}}

    <div class="cash-summary-card">

        <div style="
            font-size:11px;
            font-weight:700;
            color:#8a93a3;
            letter-spacing:1px;
        ">
            INSTITUTION CASH HEALTH SCORE
        </div>


        <div style="
            font-size:38px;
            font-weight:700;
            margin-top:8px;
            color:#202633;
        ">

            {{ number_format(
                $scores['overall'] ?? 0,
                0
            ) }}

        </div>


        <span style="
            display:inline-block;
            margin-top:8px;
            padding:5px 10px;
            border-radius:20px;
            background:{{ $statusBackground }};
            color:{{ $statusColor }};
            font-size:10px;
            font-weight:700;
        ">

            {{ $status }}

        </span>


        <div class="cash-summary-description">
            A score from <strong>0–100</strong> showing the
            institution's overall cash health. Higher scores
            indicate better cash health.
        </div>

    </div>



    {{-- ===================================================== --}}
    {{-- RESIDUAL CASH --}}
    {{-- ===================================================== --}}

    <div class="cash-summary-card">

        <div style="
            font-size:11px;
            font-weight:700;
            color:#8a93a3;
            letter-spacing:1px;
        ">
            RESIDUAL CASH
        </div>


        <div style="
            font-size:27px;
            font-weight:700;
            margin-top:12px;
            color:{{ ($financials['residual_cash'] ?? 0) < 0 ? '#dc2626' : '#202633' }};
        ">

            K{{ number_format(
                $financials['residual_cash'] ?? 0,
                2
            ) }}

        </div>


        <div class="cash-summary-description">
            The amount of cash remaining after expected costs,
            reserves and other financial obligations are accounted for.
        </div>

    </div>



    {{-- ===================================================== --}}
    {{-- NET CASH POSITION --}}
    {{-- ===================================================== --}}

    <div class="cash-summary-card">

        <div style="
            font-size:11px;
            font-weight:700;
            color:#8a93a3;
            letter-spacing:1px;
        ">
            NET CASH POSITION
        </div>


        <div style="
            font-size:27px;
            font-weight:700;
            margin-top:12px;
            color:{{ ($financials['net_cash_position'] ?? 0) < 0 ? '#dc2626' : '#202633' }};
        ">

            K{{ number_format(
                $financials['net_cash_position'] ?? 0,
                2
            ) }}

        </div>


        <div class="cash-summary-description">
            The net cash value generated after collections,
            disbursements and operating costs are accounted for.
        </div>

    </div>


</div>


{{-- ========================================================= --}}
{{-- NATIONAL SCORE --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    padding:25px;
    margin-bottom:24px;
">

    <div style="
        font-size:16px;
        font-weight:700;
        color:#202633;
        margin-bottom:20px;
    ">
        National Score
    </div>


    @php

        $scoreRows = [

            [
                'name' => 'Disbursement',
                'value' => $scores['disbursement'] ?? 0,
                'description' => 'A 0–100 score showing how much of the minimum loan target has been disbursed. A higher score means the institution is closer to meeting its lending target.'
            ],

            [
                'name' => 'Collection Quality',
                'value' => $scores['collection'] ?? 0,
                'description' => 'A 0–100 score showing the quality of loan collections. It considers outstanding defaults and how consistently customers make full payments.'
            ],

            [
                'name' => 'Residual Cash',
                'value' => $scores['residual_cash'] ?? 0,
                'description' => 'A 0–100 score showing whether the institution has enough residual cash to cover its expected financial obligations. A higher score means stronger cash coverage.'
            ],

        ];

    @endphp


    @foreach($scoreRows as $score)

        <div style="
            margin-bottom:24px;
        ">


            {{-- SCORE HEADER --}}

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                gap:20px;
                margin-bottom:7px;
            ">


                {{-- NAME + DESCRIPTION --}}

                <div style="
                    flex:1;
                ">

                    <div style="
                        font-size:13px;
                        font-weight:700;
                        color:#202633;
                    ">
                        {{ $score['name'] }}
                    </div>


                    <div style="
                        margin-top:4px;
                        font-size:11px;
                        line-height:1.5;
                        font-weight:600;
                        color:#697386;
                        max-width:850px;
                    ">
                        {{ $score['description'] }}
                    </div>

                </div>


                {{-- SCORE VALUE --}}

                <strong style="
                    font-size:14px;
                    color:#202633;
                    white-space:nowrap;
                ">

                    {{ number_format(
                        $score['value'],
                        0
                    ) }}/100

                </strong>

            </div>


            {{-- PROGRESS BAR --}}

            <div style="
                width:100%;
                height:8px;
                background:#edf0f4;
                border-radius:10px;
                overflow:hidden;
                margin-top:9px;
            ">

                <div style="
                    width:{{ min(100,max(0,$score['value'])) }}%;
                    height:100%;
                    background:{{ $statusColor }};
                    border-radius:10px;
                "></div>

            </div>

        </div>

    @endforeach



    {{-- ===================================================== --}}
    {{-- REASON --}}
    {{-- ===================================================== --}}

    @if(!empty($nationalHealth['reason']))

        <div style="
            margin-top:22px;
            padding:14px 16px;
            border-radius:10px;
            background:#f7f8fa;
            color:#4b5563;
            font-size:13px;
            line-height:1.6;
        ">

            <strong style="
                color:#202633;
            ">
                Why this score?
            </strong>


            <div style="
                margin-top:4px;
            ">

                {{ $nationalHealth['reason'] }}

            </div>

        </div>

    @endif

</div>






    {{-- ========================================================= --}}
    {{-- NATIONAL FINANCIAL POSITION --}}
    {{-- ========================================================= --}}


     <!-- <div style="
        background:#fff;
        border:1px solid #e6e9ef;
        border-radius:14px;
        padding:25px;
        margin-bottom:24px;
    ">


        <div style="
            font-size:16px;
            font-weight:700;
            color:#202633;
            margin-bottom:20px;
        ">
            National Cash Position
        </div>


        <div style="
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:14px;
        ">


            @php

                $nationalMetrics = [

                    'Minimum Loan Target' =>
                        $financials['minimum_loan_target'] ?? 0,

                    'Maximum Expected Repayment' =>
                        $financials['maximum_expected_repayment'] ?? 0,

                    'Mandatory Fixed Cost' =>
                        $financials['mandatory_fixed_cost'] ?? 0,

                    'Salaries' =>
                        $financials['salaries'] ?? 0,

                    'Defaults' =>
                        $financials['defaults'] ?? 0,

                    'Irregular Cost Reserve' =>
                        $financials['irregular_cost_reserve'] ?? 0,

                    'Average Monthly Irregular Reserve' =>
                        $financials['averageMonthlyIrregularCostReserve'] ?? 0,

                    'Salary Advance Reserve' =>
                        $financials['salary_advance_reserve'] ?? 0,

                    'Net Cash Position' =>
                        $financials['net_cash_position'] ?? 0,

                    'Residual Cash' =>
                        $financials['residual_cash'] ?? 0,

                ];

            @endphp


            @foreach($nationalMetrics as $label => $value)

                <div style="
                    border:1px solid #e7eaf0;
                    border-radius:10px;
                    padding:16px;
                ">


                    <div style="
                        font-size:10px;
                        color:#8a93a3;
                        margin-bottom:7px;
                    ">
                        {{ $label }}
                    </div>


                    <div style="
                        font-size:17px;
                        font-weight:700;
                        color:{{ $value < 0 ? '#dc2626' : '#202633' }};
                    ">

                        K{{ number_format(
                            $value,
                            2
                        ) }}

                    </div>

                </div>

            @endforeach

        </div>

    </div> -->


    {{-- ========================================================= --}}
    {{-- RESERVE BREAKDOWN --}}
    {{-- ========================================================= --}}

     <!-- <div style="
        background:#fff;
        border:1px solid #e6e9ef;
        border-radius:14px;
        padding:25px;
        margin-bottom:24px;
    ">


        <div style="
            font-size:16px;
            font-weight:700;
            color:#202633;
            margin-bottom:20px;
        ">
            Reserve Breakdown
        </div>


        <div style="
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:14px;
        ">


            @foreach(($nationalHealth['reserve_breakdown'] ?? []) as $label => $value)

                <div style="
                    border:1px solid #e7eaf0;
                    border-radius:10px;
                    padding:15px;
                ">


                    <div style="
                        font-size:11px;
                        color:#8a93a3;
                        margin-bottom:6px;
                        text-transform:capitalize;
                    ">

                        {{ str_replace(
                            '_',
                            ' ',
                            $label
                        ) }}

                    </div>


                    <div style="
                        font-size:16px;
                        font-weight:700;
                        color:#202633;
                    ">

                        K{{ number_format(
                            $value,
                            2
                        ) }}

                    </div>

                </div>

            @endforeach

        </div>

    </div> -->



{{-- ========================================================= --}}
{{-- OFFICE LIST --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    overflow:hidden;
">

    {{-- HEADER --}}

    <div style="
        padding:20px 22px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        border-bottom:1px solid #edf0f4;
    ">

        <div>

            <div style="
                font-size:16px;
                font-weight:700;
                color:#202633;
            ">
                National Cash Health
            </div>

            <div style="
                font-size:12px;
                color:#4b5563;
                margin-top:5px;
                line-height:1.5;
                font-weight:500;
            ">
                Review the cash health of the institution by province,
                district and office.
            </div>

        </div>

        <div style="
            font-size:12px;
            color:#4b5563;
            font-weight:600;
        ">

            {{ count($nationalHealth['provinces'] ?? []) }}

            provinces

        </div>

    </div>


    {{-- TABLE GUIDE --}}

    <div style="
        padding:16px 22px;
        background:#f3f5f8;
        border-bottom:1px solid #dfe3e8;
        font-size:13px;
        color:#374151;
        line-height:1.7;
    ">

        <strong style="
            color:#202633;
            font-weight:700;
        ">
            Guide:
        </strong>

        The
        <strong style="
            color:#202633;
            font-weight:700;
        ">
            Cash Health Score
        </strong>
        is a score from
        <strong style="
            color:#202633;
            font-weight:700;
        ">
            0–100
        </strong>
        that shows how healthy the cash position is.
        It is a score, not a money amount.

        <span style="
            margin:0 10px;
            color:#6b7280;
            font-weight:700;
        ">
            •
        </span>

        <strong style="
            color:#202633;
            font-weight:700;
        ">
            Residual Cash
        </strong>
        is a money amount in Kwacha showing what remains after
        expected cash obligations and reserves.

        <span style="
            margin:0 10px;
            color:#6b7280;
            font-weight:700;
        ">
            •
        </span>

        <strong style="
            color:#202633;
            font-weight:700;
        ">
            Status
        </strong>
        gives a simple indication of whether the cash position is
        healthy, needs attention, or is at risk.

    </div>


    {{-- TABLE --}}

    <table style="
        width:100%;
        border-collapse:collapse;
    ">

        <thead>

            <tr style="
                background:#f5f7fa;
            ">

                <th style="
                    width:45px;
                "></th>


                {{-- PROVINCE --}}

                <th style="
                    text-align:left;
                    padding:13px 15px;
                    font-size:11px;
                    color:#374151;
                    letter-spacing:.8px;
                    font-weight:700;
                ">

                    <div>
                        PROVINCE
                    </div>

                </th>


                {{-- CASH HEALTH SCORE --}}

                <th style="
                    text-align:right;
                    padding:13px 15px;
                    font-size:11px;
                    color:#374151;
                    letter-spacing:.8px;
                    font-weight:700;
                ">

                    <div>
                        CASH HEALTH SCORE
                    </div>

                    <div style="
                        font-size:10px;
                        font-weight:500;
                        letter-spacing:0;
                        text-transform:none;
                        margin-top:4px;
                        color:#6b7280;
                    ">
                        0–100 score; higher is better
                    </div>

                </th>


                {{-- RESIDUAL CASH --}}

                <th style="
                    text-align:right;
                    padding:13px 15px;
                    font-size:11px;
                    color:#374151;
                    letter-spacing:.8px;
                    font-weight:700;
                ">

                    <div>
                        RESIDUAL CASH
                    </div>

                    <div style="
                        font-size:10px;
                        font-weight:500;
                        letter-spacing:0;
                        text-transform:none;
                        margin-top:4px;
                        color:#6b7280;
                    ">
                        Money remaining after obligations
                    </div>

                </th>


                {{-- STATUS --}}

                <th style="
                    text-align:center;
                    padding:13px 15px;
                    font-size:11px;
                    color:#374151;
                    letter-spacing:.8px;
                    font-weight:700;
                ">

                    <div>
                        STATUS
                    </div>

                    <div style="
                        font-size:10px;
                        font-weight:500;
                        letter-spacing:0;
                        text-transform:none;
                        margin-top:4px;
                        color:#6b7280;
                    ">
                        Overall condition
                    </div>

                </th>


                {{-- DETAILS --}}

                <th style="
                    text-align:center;
                    padding:13px 15px;
                    font-size:11px;
                    color:#374151;
                    letter-spacing:.8px;
                    font-weight:700;
                ">

                    <div>
                        DETAILS
                    </div>

                    <div style="
                        font-size:10px;
                        font-weight:500;
                        letter-spacing:0;
                        text-transform:none;
                        margin-top:4px;
                        color:#6b7280;
                    ">
                        Reason for cash health score
                    </div>

                </th>

            </tr>

        </thead>
<tbody>

@foreach(($nationalHealth['provinces'] ?? []) as $province)

    @php

        $provinceId =
            $province['province_id'] ?? 0;

         $scoreDetails = $province['reason'];

        $provinceKey =
            'national_province_' . $provinceId;

        $provinceScores =
            $province['scores'] ?? [];

        $provinceFinancials =
            $province['financials'] ?? [];

     $provinceStatus = match (strtolower($provinceScores['status'] ?? 'red')) {
    'red'   => 'At Risk',
    'amber' => 'Needs Attention',
    'green' => 'Healthy',
    default => 'At Risk',
};

        $provinceStatusColor = match($provinceScores['status']) {

            'GREEN' => '#15803d',

            'AMBER' => '#b45309',

            default => '#dc2626'

        };

        $provinceStatusBackground = match($provinceScores['status']) {

            'GREEN' => '#dcfce7',

            'AMBER' => '#fef3c7',

            default => '#fee2e2'

        };



    @endphp


    {{-- ================================================= --}}
    {{-- PROVINCE --}}
    {{-- ================================================= --}}

    <tr
        onclick="toggleNationalOffice('{{ $provinceKey }}')"
        style="
            border-top:1px solid #dfe3e8;
            cursor:pointer;
            background:#f5f7fa;
        "
    >

        <td style="
            padding:16px;
            text-align:center;
        ">

            <span
                id="{{ $provinceKey }}_arrow"
                style="
                    display:inline-block;
                    color:#8a93a3;
                    transition:.2s;
                "
            >
                ▶
            </span>

        </td>


        <td style="
    padding:16px;
    font-weight:700;
    font-size:13px;
    color:#202633;
">


        {{ $province['province_name'] ?? 'Unknown Province' }}

    <span style="
        margin-top:4px;
        font-size:10px;
        color:#8a93a3;
        font-weight:500;
    ">
       {{ $province['office_count'] ?? 0 }} offices
</span>

</td>



        <td style="
            padding:16px;
            text-align:right;
            font-weight:700;
            color:#202633;
        ">

            {{ number_format(
                $provinceScores['overall'] ?? 0,
                0
            ) }}

        </td>


        <td style="
            padding:16px;
            text-align:right;
            color:{{ ($provinceFinancials['residual_cash'] ?? 0) < 0 ? '#dc2626' : '#343b48' }};
        ">

            K{{ number_format(
                $provinceFinancials['residual_cash'] ?? 0,
                2
            ) }}

        </td>


        <td style="
            padding:16px;
            text-align:center;
        ">

            <span style="
                display:inline-block;
                padding:5px 10px;
                border-radius:20px;
                background:{{ $provinceStatusBackground }};
                color:{{ $provinceStatusColor }};
                font-size:10px;
                font-weight:700;
            ">

                {{ $provinceStatus }}

            </span>

        </td>


        <td style="
            padding:16px;
            font-size:12px;
            text-align:center;
        ">

           {{$scoreDetails}}

        </td>

    </tr>


    {{-- ================================================= --}}
    {{-- PROVINCE EXPANDED --}}
    {{-- ================================================= --}}

    <tr
        id="{{ $provinceKey }}"
        style="display:none;"
    >

        <td
            colspan="6"
            style="
                padding:0;
                background:#fafbfc;
            "
        >

            <div style="
                padding:10px 30px 20px 55px;
            ">


                @foreach(($province['districts'] ?? []) as $district)

                    @php

                        $districtId =
                            $district['district_id'] ?? 0;

                             $scoreDetails = $district['reason'];

                        $districtKey =
                            $provinceKey .
                            '_district_' .
                            $districtId;

                        $districtScores =
                            $district['scores'] ?? [];

                        $districtFinancials =
                            $district['financials'] ?? [];
$districtStatus = match (strtolower($districtScores['status'] ?? 'red')) {
    'red'   => 'At Risk',
    'amber' => 'Needs Attention',
    'green' => 'Healthy',
    default => 'At Risk',
};

                        $districtStatusColor = match($districtScores['status'] ) {

                            'GREEN' => '#15803d',

                            'AMBER' => '#b45309',

                            default => '#dc2626'

                        };

                        $districtStatusBackground = match($districtScores['status']) {

                            'GREEN' => '#dcfce7',

                            'AMBER' => '#fef3c7',

                            default => '#fee2e2'

                        };

                    @endphp


                    {{-- ================================= --}}
                    {{-- DISTRICT --}}
                    {{-- ================================= --}}

                    <div style="
                        margin-top:10px;
                        border:1px solid #e7eaf0;
                        border-radius:10px;
                        background:#fff;
                        overflow:hidden;
                    ">

                      <div
    onclick="toggleNationalOffice('{{ $districtKey }}')"
    style="
        display:grid;
        grid-template-columns:45px minmax(180px, 1fr) 100px 160px 100px minmax(180px, 1.5fr);
        align-items:center;
        cursor:pointer;
        background:#fff;
        border-bottom:1px solid #edf0f4;
    "
>

{{-- ARROW --}}
<div style="
    padding:14px;
    text-align:center;
">
    <span
        id="{{ $districtKey }}_arrow"
        style="
            display:inline-block;
            color:#8a93a3;
            transition:.2s;
        "
    >
        ▶
    </span>
</div>


{{-- DISTRICT --}}
<div style="
    padding:14px;
    font-weight:600;
    font-size:12px;
    color:#202633;
    min-width:0;
">

    {{ $district['district_name'] ?? 'Unknown District' }}

    <span style="
        margin-left:8px;
        font-size:10px;
        color:#8a93a3;
        font-weight:500;
    ">
        {{ $district['office_count'] ?? 0 }} offices
    </span>

</div>


{{-- OVERALL SCORE --}}
<div style="
    padding:14px;
    text-align:right;
    font-weight:700;
    font-size:12px;
">

    {{ number_format(
        $districtScores['overall'] ?? 0,
        0
    ) }}

</div>


{{-- RESIDUAL --}}
<div style="
    padding:14px;
    text-align:right;
    font-size:12px;
    color:{{ ($districtFinancials['residual_cash'] ?? 0) < 0 ? '#dc2626' : '#343b48' }};
">

    K{{ number_format(
        $districtFinancials['residual_cash'] ?? 0,
        2
    ) }}

</div>


{{-- STATUS --}}
<div style="
    padding:14px;
    text-align:center;
">

    <span style="
        display:inline-block;
        padding:4px 8px;
        border-radius:20px;
        background:{{ $districtStatusBackground }};
        color:{{ $districtStatusColor }};
        font-size:9px;
        font-weight:700;
        white-space:nowrap;
    ">

        {{ $districtStatus }}

    </span>

</div>


{{-- REASON --}}
<div style="
    padding:14px 16px;
    font-size:11px;
    line-height:1.4;
    text-align:left;
    color:#697386;
    min-width:0;
    overflow-wrap:anywhere;
">

    {{ $scoreDetails }}

</div>


</div>


                        {{-- ================================= --}}
                        {{-- DISTRICT OFFICES --}}
                        {{-- ================================= --}}

                        <div
                            id="{{ $districtKey }}"
                            style="display:none;"
                        >

                            @foreach(($district['offices'] ?? []) as $office)

                                @php

                                    $officeScores =
                                        $office['scores'] ?? [];

                                    $officeFinancials =
                                        $office['financials'] ?? [];

                                 $officeStatus = match (strtolower($officeScores['status'] ?? 'red')) {
    'red'   => 'At Risk',
    'amber' => 'Needs Attention',
    'green' => 'Healthy',
    default => 'At Risk',
};
                                    $officeStatusColor = match($officeScores['status'] ) {

                                        'GREEN' => '#15803d',

                                        'AMBER' => '#b45309',

                                        default => '#dc2626'

                                    };

                                    $officeStatusBackground = match($officeScores['status'] ) {

                                        'GREEN' => '#dcfce7',

                                        'AMBER' => '#fef3c7',

                                        default => '#fee2e2'

                                    };

                                    $officeKey =
                                        'national_office_' .
                                        ($office['office_id'] ?? 0);

                                @endphp


                                {{-- ============================= --}}
                                {{-- OFFICE --}}
                                {{-- ============================= --}}

                                <div
                                    onclick="toggleNationalOffice('{{ $officeKey }}')"
                                    style="
                                        display:grid;
                                        grid-template-columns:45px 1fr 150px 180px 100px 100px;
                                        align-items:center;
                                        border-top:1px solid #f0f2f5;
                                        cursor:pointer;
                                        background:#fafbfc;
                                    "
                                >

                                    <div style="
                                        padding:14px;
                                        text-align:center;
                                    ">

                                        <span
                                            id="{{ $officeKey }}_arrow"
                                            style="
                                                display:inline-block;
                                                color:#a0a7b2;
                                                transition:.2s;
                                            "
                                        >
                                            ▶
                                        </span>

                                    </div>


                                    <div style="
                                        padding:14px;
                                        font-weight:600;
                                        font-size:12px;
                                        color:#343b48;
                                    ">

                                        {{ $offices[$office['office_id']]->name ?? 'Unknown Office' }}

                                    </div>


                                    <div style="
                                        padding:14px;
                                        text-align:right;
                                        font-weight:700;
                                        font-size:12px;
                                    ">

                                        {{ number_format(
                                            $officeScores['overall'] ?? 0,
                                            0
                                        ) }}

                                    </div>


                                    <div style="
                                        padding:14px;
                                        text-align:right;
                                        font-size:12px;
                                        color:{{ ($officeFinancials['residual_cash'] ?? 0) < 0 ? '#dc2626' : '#343b48' }};
                                    ">

                                        K{{ number_format(
                                            $officeFinancials['residual_cash'] ?? 0,
                                            2
                                        ) }}

                                    </div>


                                    <div style="
                                        padding:14px;
                                        text-align:center;
                                    ">

                                        <span style="
                                            display:inline-block;
                                            padding:4px 8px;
                                            border-radius:20px;
                                            background:{{ $officeStatusBackground }};
                                            color:{{ $officeStatusColor }};
                                            font-size:9px;
                                            font-weight:700;
                                        ">

                                            {{ $officeStatus }}

                                        </span>

                                    </div>


                                    <div style="
                                        padding:14px;
                                        text-align:center;
                                    ">

                                        <a
                                            href="{{ route('cash_health.show', ['id' => $office['office_id']]) }}"
                                            onclick="event.stopPropagation();"
                                            style="
                                                display:inline-block;
                                                padding:5px 10px;
                                                background:#202633;
                                                color:#fff;
                                                border-radius:6px;
                                                text-decoration:none;
                                                font-size:10px;
                                                font-weight:600;
                                            "
                                        >
                                            View
                                        </a>

                                    </div>

                                </div>


                                {{-- ================================= --}}
                                {{-- OFFICE DETAILS --}}
                                {{-- ================================= --}}

                                <div
                                    id="{{ $officeKey }}"
                                    style="display:none;"
                                >

                                    <div style="
                                        padding:20px 30px 20px 75px;
                                        background:#fff;
                                        border-top:1px solid #edf0f4;
                                    ">


                                        {{-- REASON --}}

                                        @if(!empty($office['reason']))

                                            <div style="
                                                padding:13px 15px;
                                                background:#fff;
                                                border:1px solid #e7eaf0;
                                                border-radius:10px;
                                                margin-bottom:20px;
                                                font-size:13px;
                                                color:#4b5563;
                                                line-height:1.6;
                                            ">

                                                <strong style="
                                                    color:#202633;
                                                ">
                                                    Why this score?
                                                </strong>

                                                <div style="
                                                    margin-top:4px;
                                                ">

                                                    {{ $office['reason'] }}

                                                </div>

                                            </div>

                                        @endif


                                        {{-- OFFICE FINANCIALS --}}

                                        <div style="
                                            display:grid;
                                            grid-template-columns:repeat(4,1fr);
                                            gap:12px;
                                            margin-bottom:20px;
                                        ">

                                          @php

    $officeMetrics = [

        [
            'label' => 'Minimum Loan Target',
            'description' => 'Minimum amount expected to be disbursed.',
            'value' => $officeFinancials['minimum_loan_target'] ?? 0
        ],

        [
            'label' => 'Maximum Repayment',
            'description' => 'Expected maximum amount to be collected.',
            'value' => $officeFinancials['maximum_expected_repayment'] ?? 0
        ],

        [
            'label' => 'Fixed Costs',
            'description' => 'Essential operating costs that must be paid.',
            'value' => $officeFinancials['mandatory_fixed_cost'] ?? 0
        ],

        [
            'label' => 'Salaries',
            'description' => 'Expected salary costs for the period.',
            'value' => $officeFinancials['salaries'] ?? 0
        ],

        [
            'label' => 'Defaults',
            'description' => 'Money that is currently overdue or uncollected.',
            'value' => $officeFinancials['defaults'] ?? 0
        ],

        [
            'label' => 'Irregular Reserve',
            'description' => 'Money set aside for irregular costs.',
            'value' => $officeFinancials['irregular_cost_reserve'] ?? 0
        ],

        [
            'label' => 'Salary Advances',
            'description' => 'Money advanced to staff.',
            'value' => $officeFinancials['salary_advance_reserve'] ?? 0
        ],

        [
            'label' => 'Net Cash',
            'description' => 'Collections minus disbursements and operating costs.',
            'value' => $officeFinancials['net_cash_position'] ?? 0
        ],

        [
            'label' => 'Residual Cash',
            'description' => 'Cash remaining after expected obligations and reserves.',
            'value' => $officeFinancials['residual_cash'] ?? 0
        ],

    ];

@endphp


@foreach($officeMetrics as $metric)

    <div style="
        background:#fff;
        border:1px solid #e7eaf0;
        border-radius:10px;
        padding:14px;
    ">

        <div style="
            font-size:10px;
            color:#697386;
            font-weight:700;
            margin-bottom:4px;
        ">
            {{ $metric['label'] }}
        </div>

        <div style="
            font-size:10px;
            color:#9aa2af;
            line-height:1.4;
            margin-bottom:8px;
        ">
            {{ $metric['description'] }}
        </div>

        <div style="
            font-size:15px;
            font-weight:700;
            color:{{ $metric['value'] < 0 ? '#dc2626' : '#202633' }};
        ">

            K{{ number_format(
                $metric['value'],
                2
            ) }}

        </div>

    </div>

@endforeach
                                        </div>


                                        {{-- OFFICE SCORES --}}

                                        <div style="
                                            display:flex;
                                            gap:12px;
                                        ">

                                            <div style="
                                                flex:1;
                                                background:#fff;
                                                border:1px solid #e7eaf0;
                                                border-radius:10px;
                                                padding:14px;
                                            ">

                                                <div style="
                                                    font-size:10px;
                                                    color:#8a93a3;
                                                ">
                                                    DISBURSEMENT
                                                </div>

                                                <strong style="
                                                    font-size:18px;
                                                    color:#202633;
                                                ">

                                                    {{ $officeScores['disbursement'] ?? 0 }}

                                                </strong>

                                            </div>


                                            <div style="
                                                flex:1;
                                                background:#fff;
                                                border:1px solid #e7eaf0;
                                                border-radius:10px;
                                                padding:14px;
                                            ">

                                                <div style="
                                                    font-size:10px;
                                                    color:#8a93a3;
                                                ">
                                                    COLLECTION QUALITY
                                                </div>

                                                <strong style="
                                                    font-size:18px;
                                                    color:#202633;
                                                ">

                                                    {{ $officeScores['collection'] ?? 0 }}

                                                </strong>

                                            </div>


                                            <div style="
                                                flex:1;
                                                background:#fff;
                                                border:1px solid #e7eaf0;
                                                border-radius:10px;
                                                padding:14px;
                                            ">

                                                <div style="
                                                    font-size:10px;
                                                    color:#8a93a3;
                                                ">
                                                    RESIDUAL CASH
                                                </div>

                                                <strong style="
                                                    font-size:18px;
                                                    color:#202633;
                                                ">

                                                    {{ $officeScores['residual_cash'] ?? 0 }}

                                                </strong>

                                            </div>

                                        </div>


                                        {{-- DETAILS --}}

                                        @if(!empty($office['details']))

                                            <div style="
                                                margin-top:20px;
                                                padding:15px;
                                                background:#fff;
                                                border:1px solid #e7eaf0;
                                                border-radius:10px;
                                            ">

                                                <div style="
                                                    font-size:12px;
                                                    font-weight:700;
                                                    color:#202633;
                                                    margin-bottom:10px;
                                                ">
                                                    Additional Details
                                                </div>


                                                @if(isset(
                                                    $office['details']['salaries']['total_salary']
                                                ))

                                                    <div style="
                                                        font-size:12px;
                                                        color:#697386;
                                                    ">

                                                        Predicted salaries:

                                                        <strong style="
                                                            color:#202633;
                                                        ">

                                                            K{{ number_format(
                                                                $office['details']['salaries']['total_salary'],
                                                                2
                                                            ) }}

                                                        </strong>

                                                    </div>

                                                @endif


                                                @if(isset(
                                                    $office['details']['defaults']['still_uncollected']
                                                ))

                                                    <div style="
                                                        margin-top:7px;
                                                        font-size:12px;
                                                        color:#697386;
                                                    ">

                                                        Still uncollected:

                                                        <strong style="
                                                            color:#dc2626;
                                                        ">

                                                            K{{ number_format(
                                                                $office['details']['defaults']['still_uncollected'],
                                                                2
                                                            ) }}

                                                        </strong>

                                                    </div>

                                                @endif


                                                @if(isset(
                                                    $office['details']['salary_advances']['advance_count']
                                                ))

                                                    <div style="
                                                        margin-top:7px;
                                                        font-size:12px;
                                                        color:#697386;
                                                    ">

                                                        Salary advances:

                                                        <strong style="
                                                            color:#202633;
                                                        ">

                                                            {{ $office['details']['salary_advances']['advance_count'] }}

                                                        </strong>

                                                    </div>

                                                @endif

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </div>

        </td>

    </tr>

@endforeach

</tbody>



       

        </table>

    </div>


{{-- ========================================================= --}}
{{-- NATIONAL CONTRIBUTION HISTORY --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    padding:25px;
    margin-bottom:24px;
">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:20px;
    ">

        <div>

            <div style="
                font-size:16px;
                font-weight:700;
                color:#202633;
            ">
                National Contribution History
            </div>

            <div style="
                font-size:12px;
                color:#8a93a3;
                margin-top:4px;
            ">
                Contribution performance across all branches for the last 13 cash cycles
            </div>

        </div>


        {{-- GRAPH FILTER --}}

        <div>

            <select
                id="contributionLevel"
                onchange="updateContributionGraph()"
                style="
                    border:1px solid #dfe3e8;
                    border-radius:8px;
                    padding:8px 30px 8px 10px;
                    font-size:12px;
                    color:#343b48;
                    background:#fff;
                    cursor:pointer;
                    outline:none;
                "
            >

                <option value="province">
                    Provinces
                </option>

                <option value="district">
                    Districts
                </option>

                <option value="office">
                    Branches
                </option>

            </select>

        </div>

    </div>


{{-- GRAPH --}}

<div id="contributionLoading" style="
    text-align:center;
    padding:30px;
    color:#666;
">
    <i class="fa fa-spinner fa-spin" style="font-size:24px;"></i>

    <div style="margin-top:10px;">
        Gathering contribution history...
    </div>

    <small>
        This may take a little while.
    </small>
</div>

<div id="contributionGraphContainer" style="
    display:none;
    position:relative;
    width:100%;
    height:420px;
">

    <canvas id="nationalContributionChart"></canvas>

</div>

</div>



</div>

{{-- ========================================================= --}}
{{-- CASH HEALTH GUIDE                                         --}}
{{-- ========================================================= --}}

<div
    id="cashHealthGuideOverlay"
    onclick="closeCashHealthGuide(event)"
    style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(15,23,42,.35);
        z-index:9998;
        transition:opacity .2s ease;
    "
>

    {{-- ===================================================== --}}
    {{-- GUIDE DRAWER                                         --}}
    {{-- ===================================================== --}}

    <div
        id="cashHealthGuide"
        onclick="event.stopPropagation()"
        style="
            position:absolute;
            top:0;
            right:0;
            width:460px;
            max-width:92%;
            height:100%;
            background:#fff;
            box-shadow:-8px 0 30px rgba(15,23,42,.12);
            overflow-y:auto;
            transform:translateX(100%);
            transition:transform .25s ease;
        "
    >

        {{-- ================================================= --}}
        {{-- GUIDE HEADER                                      --}}
        {{-- ================================================= --}}

        <div style="
            position:sticky;
            top:0;
            z-index:2;
            background:#fff;
            border-bottom:1px solid #e8ebf0;
            padding:22px 25px 18px;
        ">

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                gap:15px;
            ">

                <div>

                    <div style="
                        font-size:10px;
                        font-weight:700;
                        letter-spacing:1.2px;
                        color:#8a93a3;
                        margin-bottom:5px;
                    ">
                        CASH MANAGEMENT
                    </div>

                    <div style="
                        font-size:21px;
                        font-weight:700;
                        color:#202633;
                    ">
                        Cash Health Guide
                    </div>

                    <div style="
                        margin-top:5px;
                        color:#737c8b;
                        font-size:12px;
                        line-height:1.5;
                    ">
                        Understand your scores and financial indicators.
                    </div>

                </div>


                {{-- CLOSE BUTTON --}}

                <button
                    type="button"
                    onclick="closeCashHealthGuide()"
                    style="
                        width:34px;
                        height:34px;
                        border:none;
                        border-radius:8px;
                        background:#f5f6f8;
                        color:#697386;
                        cursor:pointer;
                        font-size:16px;
                        flex-shrink:0;
                    "
                >
                    <i class="fa-times"></i>
                </button>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- GUIDE CONTENT                                     --}}
        {{-- ================================================= --}}

        <div style="
            padding:24px 25px 40px;
        ">


            {{-- ================================================= --}}
            {{-- QUICK REFERENCE                                  --}}
            {{-- ================================================= --}}

            <div style="
                background:#f8fafc;
                border:1px solid #e8ebf0;
                border-radius:12px;
                padding:16px;
                margin-bottom:25px;
            ">

                <div style="
                    font-size:11px;
                    font-weight:700;
                    letter-spacing:.8px;
                    color:#7b8494;
                    margin-bottom:12px;
                ">
                    HOW TO READ THE NUMBERS
                </div>


                <div style="
                    display:flex;
                    gap:10px;
                    margin-bottom:9px;
                    align-items:center;
                ">

                    <div style="
                        width:30px;
                        height:30px;
                        border-radius:7px;
                        background:#eaf2ff;
                        color:#3677e8;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:12px;
                    ">
                        <i class="fa fa-bar-chart"></i>
                    </div>

                    <div style="font-size:12px;color:#4b5563;">
                        <strong style="color:#202633;">
                            Score / 100
                        </strong>
                        — performance or health score
                    </div>

                </div>


                <div style="
                    display:flex;
                    gap:10px;
                    margin-bottom:9px;
                    align-items:center;
                ">

                    <div style="
                        width:30px;
                        height:30px;
                        border-radius:7px;
                        background:#e8f8f0;
                        color:#168550;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:12px;
                    ">
                        <i class="fa fa-percent"></i>
                    </div>

                    <div style="font-size:12px;color:#4b5563;">
                        <strong style="color:#202633;">
                            %
                        </strong>
                        — percentage or ratio
                    </div>

                </div>


                <div style="
                    display:flex;
                    gap:10px;
                    align-items:center;
                ">

                    <div style="
                        width:30px;
                        height:30px;
                        border-radius:7px;
                        background:#fef3c7;
                        color:#b45309;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:12px;
                    ">
                        <strong>K</strong>
                    </div>

                    <div style="font-size:12px;color:#4b5563;">
                        <strong style="color:#202633;">
                            K (Kwacha)
                        </strong>
                        — financial amount
                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- OVERALL CASH HEALTH                               --}}
            {{-- ================================================= --}}

            <div style="
                margin-bottom:27px;
            ">

                <div style="
                    font-size:14px;
                    font-weight:700;
                    color:#202633;
                    margin-bottom:7px;
                ">
                    Overall Cash Health
                </div>

                <div style="
                    font-size:12px;
                    color:#737c8b;
                    line-height:1.7;
                    margin-bottom:14px;
                ">

                    The Overall Cash Health is a
                    <strong style="color:#343b48;">
                        score out of 100
                    </strong>,
                    not a money value and not a percentage.

                    It combines three areas of Cash Health:

                </div>


                <div style="
                    display:grid;
                    grid-template-columns:1fr 1fr 1fr;
                    gap:7px;
                    margin-bottom:15px;
                ">

                    <div style="
                        background:#f7f8fa;
                        border-radius:8px;
                        padding:10px;
                        text-align:center;
                    ">
                        <div style="
                            font-size:16px;
                            font-weight:700;
                            color:#202633;
                        ">
                            35%
                        </div>

                        <div style="
                            font-size:10px;
                            color:#737c8b;
                            margin-top:2px;
                        ">
                            Disbursement
                        </div>
                    </div>


                    <div style="
                        background:#f7f8fa;
                        border-radius:8px;
                        padding:10px;
                        text-align:center;
                    ">
                        <div style="
                            font-size:16px;
                            font-weight:700;
                            color:#202633;
                        ">
                            35%
                        </div>

                        <div style="
                            font-size:10px;
                            color:#737c8b;
                            margin-top:2px;
                        ">
                            Collection
                        </div>
                    </div>


                    <div style="
                        background:#f7f8fa;
                        border-radius:8px;
                        padding:10px;
                        text-align:center;
                    ">
                        <div style="
                            font-size:16px;
                            font-weight:700;
                            color:#202633;
                        ">
                            30%
                        </div>

                        <div style="
                            font-size:10px;
                            color:#737c8b;
                            margin-top:2px;
                        ">
                            Residual Cash
                        </div>
                    </div>

                </div>


                {{-- STATUS BANDS --}}

                <div style="
                    border:1px solid #edf0f3;
                    border-radius:10px;
                    overflow:hidden;
                ">

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        padding:10px 12px;
                        background:#ecfdf5;
                        font-size:11px;
                    ">
                        <strong style="color:#15803d;">
                            🟢 Healthy
                        </strong>

                        <span style="color:#166534;">
                            80–100
                        </span>
                    </div>


                    <div style="
                        display:flex;
                        justify-content:space-between;
                        padding:10px 12px;
                        background:#fffbeb;
                        font-size:11px;
                    ">
                        <strong style="color:#b45309;">
                            🟡 Needs attention
                        </strong>

                        <span style="color:#92400e;">
                            60–79
                        </span>
                    </div>


                    <div style="
                        display:flex;
                        justify-content:space-between;
                        padding:10px 12px;
                        background:#fef2f2;
                        font-size:11px;
                    ">
                        <strong style="color:#dc2626;">
                            🔴 At risk
                        </strong>

                        <span style="color:#991b1b;">
                            0–59
                        </span>
                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- DISBURSEMENT                                     --}}
            {{-- ================================================= --}}

            <div style="
                padding-top:22px;
                border-top:1px solid #edf0f3;
                margin-bottom:25px;
            ">

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:7px;
                ">

                    <div style="
                        font-size:14px;
                        font-weight:700;
                        color:#202633;
                    ">
                        Disbursement
                    </div>

                    <span style="
                        padding:4px 7px;
                        border-radius:6px;
                        background:#eaf2ff;
                        color:#3677e8;
                        font-size:9px;
                        font-weight:700;
                    ">
                        SCORE / 100
                    </span>

                </div>


                <div style="
                    font-size:12px;
                    color:#737c8b;
                    line-height:1.7;
                ">

                    Measures how much was actually disbursed compared
                    with the minimum loan target for the selected cycle.

                    <div style="
                        margin-top:10px;
                        padding:10px 12px;
                        background:#f8fafc;
                        border-radius:8px;
                        font-size:11px;
                        color:#4b5563;
                    ">

                        <strong style="color:#202633;">
                            Calculation:
                        </strong>

                        Actual Disbursed ÷ Minimum Loan Target × 100

                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- COLLECTION QUALITY                               --}}
            {{-- ================================================= --}}

            <div style="
                padding-top:22px;
                border-top:1px solid #edf0f3;
                margin-bottom:25px;
            ">

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:7px;
                ">

                    <div style="
                        font-size:14px;
                        font-weight:700;
                        color:#202633;
                    ">
                        Collection Quality
                    </div>

                    <span style="
                        padding:4px 7px;
                        border-radius:6px;
                        background:#eaf2ff;
                        color:#3677e8;
                        font-size:9px;
                        font-weight:700;
                    ">
                        SCORE / 100
                    </span>

                </div>


                <div style="
                    font-size:12px;
                    color:#737c8b;
                    line-height:1.7;
                ">

                    Measures the quality of loan collections during
                    the selected cycle.

                    The score considers collection performance,
                    including defaults and full-payment performance.

                </div>


                <div style="
                    margin-top:10px;
                    padding:10px 12px;
                    background:#f8fafc;
                    border-radius:8px;
                    font-size:11px;
                    color:#4b5563;
                ">

                    <strong style="color:#202633;">
                        Full-Payment Ratio:
                    </strong>

                    This is a
                    <strong>percentage (%)</strong>
                    showing the proportion of relevant repayments
                    that were fully paid.

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- RESIDUAL CASH                                    --}}
            {{-- ================================================= --}}

            <div style="
                padding-top:22px;
                border-top:1px solid #edf0f3;
                margin-bottom:25px;
            ">

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:7px;
                ">

                    <div style="
                        font-size:14px;
                        font-weight:700;
                        color:#202633;
                    ">
                        Residual Cash
                    </div>

                    <span style="
                        padding:4px 7px;
                        border-radius:6px;
                        background:#fff7ed;
                        color:#c2410c;
                        font-size:9px;
                        font-weight:700;
                    ">
                        MONEY VALUE
                    </span>

                </div>


                <div style="
                    font-size:12px;
                    color:#737c8b;
                    line-height:1.7;
                ">

                    Residual Cash is the amount of cash remaining
                    after the expected financial requirements have
                    been accounted for.

                    It is a
                    <strong style="color:#343b48;">
                        money value in Kwacha (K)
                    </strong>,
                    not a score.

                </div>


                <div style="
                    margin-top:10px;
                    padding:10px 12px;
                    background:#f8fafc;
                    border-radius:8px;
                    font-size:11px;
                    color:#4b5563;
                ">

                    <strong style="color:#202633;">
                        Positive:
                    </strong>
                    cash remains after requirements.

                    <br>

                    <strong style="color:#dc2626;">
                        Negative:
                    </strong>
                    expected obligations exceed available cash.

                </div>


                <div style="
                    margin-top:9px;
                    font-size:11px;
                    color:#737c8b;
                ">

                    <strong style="color:#202633;">
                        Important:
                    </strong>

                    The Residual Cash Score is different.
                    It is a
                    <strong>score out of 100</strong>
                    based on months of cash cover.

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- VALUE ADDED / NET CONTRIBUTION                    --}}
            {{-- ================================================= --}}

            <div style="
                padding-top:22px;
                border-top:1px solid #edf0f3;
                margin-bottom:25px;
            ">

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:7px;
                ">

                    <div style="
                        font-size:14px;
                        font-weight:700;
                        color:#202633;
                    ">
                        Value Added / Net Contribution
                    </div>

                    <span style="
                        padding:4px 7px;
                        border-radius:6px;
                        background:#ecfdf5;
                        color:#15803d;
                        font-size:9px;
                        font-weight:700;
                    ">
                        MONEY VALUE
                    </span>

                </div>


                <div style="
                    font-size:12px;
                    color:#737c8b;
                    line-height:1.7;
                ">

                    Shows the net cash value contributed by the
                    institution, province, district, or branch
                    during the selected period.

                </div>


                <div style="
                    margin-top:10px;
                    padding:12px;
                    background:#f8fafc;
                    border-radius:8px;
                    text-align:center;
                    font-size:12px;
                    color:#202633;
                    font-weight:700;
                ">

                    Collections
                    <span style="color:#9aa2af;">−</span>
                    Disbursements
                    <span style="color:#9aa2af;">−</span>
                    Operating Costs

                </div>


                <div style="
                    margin-top:10px;
                    font-size:11px;
                    color:#737c8b;
                    line-height:1.6;
                ">

                    The result is a
                    <strong style="color:#343b48;">
                        Kwacha (K) amount
                    </strong>.

                    A positive value means the operation generated
                    more cash than it consumed during the period.

                    A negative value means it consumed more cash
                    than it generated.

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- FINANCIAL FIGURES                                 --}}
            {{-- ================================================= --}}

            <div style="
                padding-top:22px;
                border-top:1px solid #edf0f3;
                margin-bottom:25px;
            ">

                <div style="
                    font-size:14px;
                    font-weight:700;
                    color:#202633;
                    margin-bottom:8px;
                ">
                    Financial Figures
                </div>


                <div style="
                    font-size:12px;
                    color:#737c8b;
                    line-height:1.7;
                    margin-bottom:12px;
                ">

                    Figures such as the following are
                    <strong style="color:#343b48;">
                        money values in Kwacha (K)
                    </strong>:

                </div>


                <div style="
                    display:flex;
                    flex-wrap:wrap;
                    gap:7px;
                ">

                    @foreach([
                        'Actual Disbursed',
                        'Collections',
                        'Defaults',
                        'Salaries',
                        'Operating Costs',
                        'Irregular Cost Reserve',
                        'Residual Cash',
                        'Net Contribution'
                    ] as $item)

                        <span style="
                            padding:6px 8px;
                            background:#f7f8fa;
                            border:1px solid #edf0f3;
                            border-radius:6px;
                            font-size:10px;
                            color:#596273;
                        ">
                            {{ $item }}
                        </span>

                    @endforeach

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- CASH CYCLE                                        --}}
            {{-- ================================================= --}}

            <div style="
                padding-top:22px;
                border-top:1px solid #edf0f3;
                margin-bottom:25px;
            ">

                <div style="
                    font-size:14px;
                    font-weight:700;
                    color:#202633;
                    margin-bottom:8px;
                ">
                    Cash Cycle
                </div>


                <div style="
                    font-size:12px;
                    color:#737c8b;
                    line-height:1.7;
                ">

                    Cash Health is measured using a monthly cycle
                    running from the

                    <strong style="color:#202633;">
                        25th of one month
                    </strong>

                    to the

                    <strong style="color:#202633;">
                        24th of the following month.
                    </strong>

                </div>


                <div style="
                    margin-top:10px;
                    padding:11px 12px;
                    background:#f8fafc;
                    border-radius:8px;
                    text-align:center;
                    font-size:12px;
                    font-weight:700;
                    color:#343b48;
                ">

                    25 Aug 2026
                    <span style="color:#9aa2af;margin:0 5px;">
                        →
                    </span>
                    24 Sep 2026

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- IMPORTANT REMINDER                               --}}
            {{-- ================================================= --}}

            <div style="
                background:#f8fafc;
                border:1px solid #e4e8ee;
                border-radius:12px;
                padding:15px;
            ">

                <div style="
                    display:flex;
                    gap:10px;
                    align-items:flex-start;
                ">

                    <div style="
                        width:30px;
                        height:30px;
                        min-width:30px;
                        border-radius:8px;
                        background:#eaf2ff;
                        color:#3677e8;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:12px;
                    ">
                        <i class="fa fa-lightbulb"></i>
                    </div>


                    <div>

                        <div style="
                            font-size:12px;
                            font-weight:700;
                            color:#202633;
                            margin-bottom:4px;
                        ">
                            Remember
                        </div>

                        <div style="
                            font-size:11px;
                            color:#737c8b;
                            line-height:1.6;
                        ">

                            Review trends across multiple cycles,
                            not just one good or bad period.

                            A single cycle provides a snapshot;
                            several cycles show the underlying trend.

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- GUIDE JAVASCRIPT                                          --}}
{{-- ========================================================= --}}

<script>

function openCashHealthGuide() {

    const overlay =
        document.getElementById('cashHealthGuideOverlay');

    const guide =
        document.getElementById('cashHealthGuide');


    overlay.style.display = 'block';


    // Small delay allows the transition to animate

    setTimeout(function () {

        guide.style.transform =
            'translateX(0)';

    }, 10);

    document.body.style.overflow = 'hidden';

}


function closeCashHealthGuide(event) {

    // If called by clicking the overlay,
    // only close when the overlay itself was clicked.

    if (
        event &&
        event.target !==
        document.getElementById(
            'cashHealthGuideOverlay'
        )
    ) {

        return;

    }


    const overlay =
        document.getElementById(
            'cashHealthGuideOverlay'
        );

    const guide =
        document.getElementById(
            'cashHealthGuide'
        );


    guide.style.transform =
        'translateX(100%)';


    setTimeout(function () {

        overlay.style.display =
            'none';

    }, 250);


    document.body.style.overflow = '';

}


// Close guide with ESC key

document.addEventListener(
    'keydown',
    function(event) {

        if (event.key === 'Escape') {

            const overlay =
                document.getElementById(
                    'cashHealthGuideOverlay'
                );

            if (
                overlay &&
                overlay.style.display !== 'none'
            ) {

                closeCashHealthGuide();

            }

        }

    }
);

</script>

@endsection


{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}


@section('footer-scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>


/* ============================================================
   NATIONAL CONTRIBUTION GRAPH
   ============================================================ */
let nationalContributionData = [];

let nationalContributionChart = null;


/*
|--------------------------------------------------------------------------
| FORMAT MONEY
|--------------------------------------------------------------------------
*/

function formatContribution(value)
{
    return new Intl.NumberFormat(
        'en-ZM',
        {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }
    ).format(value);
}


/*
|--------------------------------------------------------------------------
| GET UNIQUE GROUPS
|--------------------------------------------------------------------------
*/

function getContributionGroups(level)
{
    const groups = {};

    nationalContributionData.forEach(row => {

        let id;
        let name;

        if (level === 'province') {

            id =
                row.province_id;

            name =
                row.province_name;

        }

        else if (level === 'district') {

            id =
                row.district_id;

            name =
                row.district_name;

        }

        else {

            id =
                row.office_id;

            name =
                row.office_name;

        }


        if (!groups[id]) {

            groups[id] = {

                id: id,

                name: name,

                cycles: {}

            };

        }


        groups[id].cycles[
            row.cycle_start
        ] =
            Number(row.contribution) || 0;

    });


    return Object.values(groups);
}


/*
|--------------------------------------------------------------------------
| UPDATE GRAPH
|--------------------------------------------------------------------------
*/

function updateContributionGraph()
{
    const level =
        document.getElementById(
            'contributionLevel'
        ).value;


    const groups =
        getContributionGroups(level);


    /*
    |--------------------------------------------------------------------------
    | GET ALL CYCLES
    |--------------------------------------------------------------------------
    */

    const cycleMap = {};


    nationalContributionData.forEach(row => {

        cycleMap[
            row.cycle_start
        ] = {

            start:
                row.cycle_start,

            end:
                row.cycle_end

        };

    });


    const cycles =
        Object.values(cycleMap)
            .sort(
                (a, b) =>
                    new Date(a.start) -
                    new Date(b.start)
            );


    /*
    |--------------------------------------------------------------------------
    | LABELS
    |--------------------------------------------------------------------------
    */

    const labels =
        cycles.map(cycle => {

            const start =
                new Date(
                    cycle.start + 'T00:00:00'
                );

            return start.toLocaleDateString(
                'en-GB',
                {
                    day:'2-digit',
                    month:'short',
                    year:'numeric'
                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | DATASETS
    |--------------------------------------------------------------------------
    */

    const datasets =
        groups.map(group => {

            return {

                label:
                    group.name,

                data:
                    cycles.map(
                        cycle =>
                            group.cycles[
                                cycle.start
                            ] || 0
                    ),

              borderWidth:1.5,

pointRadius:2,

pointHoverRadius:6,

                tension:0.3,

                fill:false

            };

        });


    /*
    |--------------------------------------------------------------------------
    | DESTROY OLD GRAPH
    |--------------------------------------------------------------------------
    */

    if (nationalContributionChart) {

        nationalContributionChart.destroy();

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE GRAPH
    |--------------------------------------------------------------------------
    */

    const ctx =
        document
            .getElementById(
                'nationalContributionChart'
            )
            .getContext('2d');


            const zeroLinePlugin = {

    id: 'zeroLine',

    afterDraw(chart) {

        const yScale = chart.scales.y;

        if (!yScale) {
            return;
        }

        const zeroY = yScale.getPixelForValue(0);

        if (
            zeroY < yScale.top ||
            zeroY > yScale.bottom
        ) {
            return;
        }

        const ctx = chart.ctx;

        ctx.save();

        ctx.beginPath();

        ctx.moveTo(
            chart.chartArea.left,
            zeroY
        );

        ctx.lineTo(
            chart.chartArea.right,
            zeroY
        );

        ctx.lineWidth = 3;

        ctx.strokeStyle = '#202633';

        ctx.setLineDash([]);

        ctx.stroke();

        ctx.restore();

    }

};


    nationalContributionChart =
        new Chart(
            ctx,
            {
                 plugins: [
                zeroLinePlugin
            ],

                type:'line',

                data: {

                    labels: labels,

                    datasets: datasets

                },

                options: {

                    responsive:true,

                    maintainAspectRatio:false,


                 interaction: {

    mode: 'nearest',

    intersect: true

},


                    plugins: {

                        legend: {

                            display:true,

                            position:'bottom',

                            labels: {

                                usePointStyle:true,

                                padding:15,

                                font: {

                                    size:11

                                }

                            }

                        },


                        tooltip: {

                            callbacks: {

                                label:function(context)
                                {

                                    return (
                                        context.dataset.label +
                                        ': K' +
                                        formatContribution(
                                            context.parsed.y
                                        )
                                    );

                                }

                            }

                        }

                    },


                    scales: {

                        x: {

                            grid: {

                                display:false

                            },

                            ticks: {

                                font: {

                                    size:10

                                },

                                maxRotation:0,

                                autoSkip:true,

                                maxTicksLimit:13

                            }

                        },


                        y: {

                            beginAtZero:false,

                            ticks: {

                                font: {

                                    size:10

                                },

                                callback:function(value)
                                {

                                    return 'K' +
                                        formatContribution(
                                            value
                                        );

                                }

                            }

                        }

                    }

                }

            }
        );
}

async function loadNationalContribution() {

    const loading = document.getElementById('contributionLoading');
    const graphContainer = document.getElementById('contributionGraphContainer');

    if (loading) {
        loading.style.display = 'block';
    }

    if (graphContainer) {
        graphContainer.style.display = 'none';
    }

    try {

        const response = await fetch(
            'https://lms2backend.whencefinancesystem.com/cash-health/national/contributions'
        );

        if (!response.ok) {
            throw new Error('Unable to retrieve contribution history');
        }

        const data = await response.json();

        nationalContributionData = data.graph || [];

        updateContributionGraph();

        if (loading) {
            loading.style.display = 'none';
        }

        if (graphContainer) {
            graphContainer.style.display = 'block';
        }

    } catch (error) {

        console.error(
            'National contribution loading error:',
            error
        );

        if (loading) {
            loading.innerHTML = `
                <i class="fa fa-exclamation-triangle"
                   style="font-size:24px;"></i>

                <div style="margin-top:10px;">
                    Unable to load contribution history.
                </div>

                <small>
                    Please refresh the page and try again.
                </small>
            `;
        }

    }
}


/*
|--------------------------------------------------------------------------
| LOAD GRAPH WHEN PAGE IS READY
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function()
    {
        loadNationalContribution();
    }
);
    

function toggleNationalOffice(id)
{
    const row =
        document.getElementById(id);

    const arrow =
        document.getElementById(
            id + '_arrow'
        );


    if (
        row.style.display === 'none' ||
        row.style.display === ''
    ) {

        row.style.display =
            'table-row';

        arrow.style.transform =
            'rotate(90deg)';

    } else {

        row.style.display =
            'none';

        arrow.style.transform =
            'rotate(0deg)';
    }
}

</script>
@endsection
@extends('layouts.master')

@section('content')
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

        $status =
            strtoupper(
                $scores['status'] ?? 'RED'
            );


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



    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        margin-bottom:28px;
    ">


        {{-- TITLE --}}

        <div>

            <div style="
                font-size:11px;
                font-weight:700;
                letter-spacing:1.5px;
                color:#7b8494;
                margin-bottom:5px;
            ">
                CASH MANAGEMENT
            </div>


            <h1 style="
                font-size:30px;
                font-weight:700;
                margin:0;
                color:#202633;
                letter-spacing:-0.7px;
            ">
                National Cash Health
            </h1>


            <div style="
                color:#697386;
                margin-top:5px;
                font-size:14px;
            ">
                Organization-wide cash position
            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- CYCLE SELECTOR --}}
        {{-- ===================================================== --}}

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
                CASH CYCLE
            </div>


            <form
                method="GET"
                action="{{ route('cash_health.national') }}"
                style="margin:0;"
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
                margin-top:6px;
                font-size:11px;
                color:#8a93a3;
            ">

                {{ \Carbon\Carbon::parse($cycleStart)->format('d M Y') }}

                →

                {{ \Carbon\Carbon::parse($cycleEnd)->format('d M Y') }}

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- SUMMARY CARDS --}}
    {{-- ========================================================= --}}

      <div style="
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:18px;
        margin-bottom:24px;
    ">


        {{-- OVERALL SCORE --}}

        <div style="
            background:#fff;
            border:1px solid #e6e9ef;
            border-radius:14px;
            padding:22px;
        ">

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

        </div>



        {{-- RESIDUAL CASH --}}

        <div style="
            background:#fff;
            border:1px solid #e6e9ef;
            border-radius:14px;
            padding:22px;
        ">

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

        </div>



        {{-- NET CASH --}}

        <div style="
            background:#fff;
            border:1px solid #e6e9ef;
            border-radius:14px;
            padding:22px;
        ">

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

        </div>



        {{-- OFFICES --}}

        <div style="
            background:#fff;
            border:1px solid #e6e9ef;
            border-radius:14px;
            padding:22px;
        ">

            <div style="
                font-size:11px;
                font-weight:700;
                color:#8a93a3;
                letter-spacing:1px;
            ">
                OFFICES
            </div>


            <div style="
                font-size:28px;
                font-weight:700;
                margin-top:12px;
                color:#202633;
            ">

                {{ $nationalHealth['office_count'] ?? 0 }}

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
                    'value' => $scores['disbursement'] ?? 0
                ],

                [
                    'name' => 'Collection Quality',
                    'value' => $scores['collection'] ?? 0
                ],

                [
                    'name' => 'Residual Cash',
                    'value' => $scores['residual_cash'] ?? 0
                ],

            ];

        @endphp


        @foreach($scoreRows as $score)

            <div style="
                margin-bottom:18px;
            ">


                <div style="
                    display:flex;
                    justify-content:space-between;
                    margin-bottom:7px;
                ">

                    <span style="
                        font-size:13px;
                        color:#4b5563;
                    ">
                        {{ $score['name'] }}
                    </span>


                    <strong style="
                        font-size:13px;
                        color:#202633;
                    ">

                        {{ number_format(
                            $score['value'],
                            0
                        ) }}

                    </strong>

                </div>


                <div style="
                    width:100%;
                    height:8px;
                    background:#edf0f4;
                    border-radius:10px;
                    overflow:hidden;
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



        {{-- REASON --}}

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
                    color:#8a93a3;
                    margin-top:3px;
                ">
                    Review cash health across all provinces, distircts and offices
                </div>

            </div>


            <div style="
                font-size:12px;
                color:#697386;
            ">

                {{ count(
                    $nationalHealth['offices'] ?? []
                ) }}

                offices

            </div>

        </div>



        {{-- TABLE --}}

        <table style="
            width:100%;
            border-collapse:collapse;
        ">


            <thead>

                <tr style="
                    background:#fafbfc;
                ">


                    <th style="
                        width:45px;
                    "></th>


                    <th style="
                        text-align:left;
                        padding:12px 15px;
                        font-size:10px;
                        color:#8a93a3;
                        letter-spacing:.8px;
                    ">
                        PROVINCE
                    </th>


                    <th style="
                        text-align:right;
                        padding:12px 15px;
                        font-size:10px;
                        color:#8a93a3;
                        letter-spacing:.8px;
                    ">
                       CASH HEALTH SCORE
                    </th>


                    <th style="
                        text-align:right;
                        padding:12px 15px;
                        font-size:10px;
                        color:#8a93a3;
                        letter-spacing:.8px;
                    ">
                        RESIDUAL CASH
                    </th>


                    <th style="
                        text-align:center;
                        padding:12px 15px;
                        font-size:10px;
                        color:#8a93a3;
                        letter-spacing:.8px;
                    ">
                        STATUS
                    </th>

                    <th style="
    text-align:center;
    padding:12px 15px;
    font-size:10px;
    color:#8a93a3;
    letter-spacing:.8px;
">
    DETAILS
</th>

                </tr>

            </thead>

<tbody>

@foreach(($nationalHealth['provinces'] ?? []) as $province)

    @php

        $provinceId =
            $province['province_id'] ?? 0;

        $provinceKey =
            'national_province_' . $provinceId;

        $provinceScores =
            $province['scores'] ?? [];

        $provinceFinancials =
            $province['financials'] ?? [];

        $provinceStatus =
            strtoupper(
                $provinceScores['status'] ?? 'RED'
            );

        $provinceStatusColor = match($provinceStatus) {

            'GREEN' => '#15803d',

            'AMBER' => '#b45309',

            default => '#dc2626'

        };

        $provinceStatusBackground = match($provinceStatus) {

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
                margin-left:8px;
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
            text-align:center;
        ">

            —

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

                        $districtKey =
                            $provinceKey .
                            '_district_' .
                            $districtId;

                        $districtScores =
                            $district['scores'] ?? [];

                        $districtFinancials =
                            $district['financials'] ?? [];

                        $districtStatus =
                            strtoupper(
                                $districtScores['status'] ?? 'RED'
                            );

                        $districtStatusColor = match($districtStatus) {

                            'GREEN' => '#15803d',

                            'AMBER' => '#b45309',

                            default => '#dc2626'

                        };

                        $districtStatusBackground = match($districtStatus) {

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
                                grid-template-columns:45px 1fr 150px 180px 100px;
                                align-items:center;
                                cursor:pointer;
                                background:#fff;
                                border-bottom:1px solid #edf0f4;
                            "
                        >

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


                            <div style="
                                padding:14px;
                                font-weight:600;
                                font-size:12px;
                                color:#202633;
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
                                ">

                                    {{ $districtStatus }}

                                </span>

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

                                    $officeStatus =
                                        strtoupper(
                                            $officeScores['status'] ?? 'RED'
                                        );

                                    $officeStatusColor = match($officeStatus) {

                                        'GREEN' => '#15803d',

                                        'AMBER' => '#b45309',

                                        default => '#dc2626'

                                    };

                                    $officeStatusBackground = match($officeStatus) {

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

                                                    'Minimum Loan Target' =>
                                                        $officeFinancials['minimum_loan_target'] ?? 0,

                                                    'Maximum Repayment' =>
                                                        $officeFinancials['maximum_expected_repayment'] ?? 0,

                                                    'Fixed Costs' =>
                                                        $officeFinancials['mandatory_fixed_cost'] ?? 0,

                                                    'Salaries' =>
                                                        $officeFinancials['salaries'] ?? 0,

                                                    'Defaults' =>
                                                        $officeFinancials['defaults'] ?? 0,

                                                    'Irregular Reserve' =>
                                                        $officeFinancials['irregular_cost_reserve'] ?? 0,

                                                    'Salary Advances' =>
                                                        $officeFinancials['salary_advance_reserve'] ?? 0,

                                                    'Net Cash' =>
                                                        $officeFinancials['net_cash_position'] ?? 0,

                                                    'Residual Cash' =>
                                                        $officeFinancials['residual_cash'] ?? 0,

                                                ];

                                            @endphp


                                            @foreach($officeMetrics as $label => $value)

                                                <div style="
                                                    background:#fff;
                                                    border:1px solid #e7eaf0;
                                                    border-radius:10px;
                                                    padding:14px;
                                                ">

                                                    <div style="
                                                        font-size:10px;
                                                        color:#8a93a3;
                                                        margin-bottom:6px;
                                                    ">
                                                        {{ $label }}
                                                    </div>

                                                    <div style="
                                                        font-size:15px;
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

    <div style="
        position:relative;
        width:100%;
        height:420px;
    ">

        <canvas id="nationalContributionChart"></canvas>

    </div>

</div>



</div>
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

const nationalContributionData =
    @json($nationalContribution['graph'] ?? []);

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

                borderWidth:2,

                pointRadius:3,

                pointHoverRadius:5,

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


    nationalContributionChart =
        new Chart(
            ctx,
            {

                type:'line',

                data: {

                    labels: labels,

                    datasets: datasets

                },

                options: {

                    responsive:true,

                    maintainAspectRatio:false,


                    interaction: {

                        mode:'index',

                        intersect:false

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


/*
|--------------------------------------------------------------------------
| LOAD GRAPH WHEN PAGE IS READY
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function()
    {

        updateContributionGraph();

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
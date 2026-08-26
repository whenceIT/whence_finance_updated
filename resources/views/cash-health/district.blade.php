@extends('layouts.master')

@section('content')
<div style="
    padding:30px;
    background:#f6f8fb;
    min-height:100vh;
">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        margin-bottom:28px;
    ">

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
            ">
                District Cash Health
            </h1>

            <div style="
                color:#697386;
                margin-top:5px;
                font-size:14px;
            ">
                {{ $districtHealth['district_name'] ?? 'District' }}
            </div>

        </div>


        {{-- CYCLE SELECTOR --}}

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
                action="{{ url('cash_health/district/' . ($districtHealth['district_id'] ?? request()->route('district'))) }}"
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

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUMMARY CARDS --}}
    {{-- ========================================================= --}}

    @php

        $scores = $districtHealth['scores'] ?? [];
        $financials = $districtHealth['financials'] ?? [];

        $status = strtoupper($scores['status'] ?? 'RED');

        $statusColor = match($status) {
            'GREEN' => '#15803d',
            'AMBER' => '#b45309',
            default => '#dc2626',
        };

        $statusBackground = match($status) {
            'GREEN' => '#dcfce7',
            'AMBER' => '#fef3c7',
            default => '#fee2e2',
        };

    @endphp


    <div style="
        display:grid;
        grid-template-columns:repeat(4, 1fr);
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
                OVERALL SCORE
            </div>

            <div style="
                font-size:38px;
                font-weight:700;
                margin-top:8px;
                color:#202633;
            ">
                {{ number_format($scores['overall'] ?? 0, 0) }}
            </div>

            <span style="
                display:inline-block;
                margin-top:8px;
                padding:5px 9px;
                border-radius:20px;
                background:{{ $statusBackground }};
                color:{{ $statusColor }};
                font-size:11px;
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
                font-size:28px;
                font-weight:700;
                margin-top:12px;
                color:#202633;
            ">
                K{{ number_format($financials['residual_cash'] ?? 0, 2) }}
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
                font-size:28px;
                font-weight:700;
                margin-top:12px;
                color:#202633;
            ">
                K{{ number_format($financials['net_cash_position'] ?? 0, 2) }}
            </div>

        </div>


        {{-- BRANCHES --}}

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
                BRANCHES
            </div>

            <div style="
                font-size:28px;
                font-weight:700;
                margin-top:12px;
                color:#202633;
            ">
                {{ $districtHealth['office_count'] ?? 0 }}
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DISTRICT SCORE PANEL --}}
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
            District Score
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
                ]
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
                        {{ number_format($score['value'], 0) }}
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
                        width:{{ min(100, max(0, $score['value'])) }}%;
                        height:100%;
                        background:{{ $statusColor }};
                        border-radius:10px;
                    "></div>

                </div>

            </div>

        @endforeach


        {{-- REASON --}}

        @if(!empty($districtHealth['reason']))

            <div style="
                margin-top:22px;
                padding:14px 16px;
                border-radius:10px;
                background:#f7f8fa;
                color:#4b5563;
                font-size:13px;
                line-height:1.6;
            ">

                <strong style="color:#202633;">
                    Why this score?
                </strong>

                <div style="margin-top:4px;">
                    {{ $districtHealth['reason'] }}
                </div>

            </div>

        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- BRANCH TABLE --}}
    {{-- ========================================================= --}}

    <div style="
        background:#fff;
        border:1px solid #e6e9ef;
        border-radius:14px;
        overflow:hidden;
    ">

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
                    Branch Cash Health
                </div>

                <div style="
                    font-size:12px;
                    color:#8a93a3;
                    margin-top:3px;
                ">
                    Click a branch to view its financial details
                </div>

            </div>

            <div style="
                font-size:12px;
                color:#697386;
            ">
                {{ count($districtHealth['offices'] ?? []) }} branches
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
                        padding:12px 15px;
                    "></th>

                    <th style="
                        text-align:left;
                        padding:12px 15px;
                        font-size:10px;
                        letter-spacing:.8px;
                        color:#8a93a3;
                    ">
                        BRANCH
                    </th>

                    <th style="
                        text-align:right;
                        padding:12px 15px;
                        font-size:10px;
                        letter-spacing:.8px;
                        color:#8a93a3;
                    ">
                        SCORE
                    </th>

                    <th style="
                        text-align:right;
                        padding:12px 15px;
                        font-size:10px;
                        letter-spacing:.8px;
                        color:#8a93a3;
                    ">
                        RESIDUAL CASH
                    </th>

                    <th style="
                        text-align:center;
                        padding:12px 15px;
                        font-size:10px;
                        letter-spacing:.8px;
                        color:#8a93a3;
                    ">
                        STATUS
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($districtHealth['offices'] ?? [] as $office)

                    @php

                        $officeScores = $office['scores'] ?? [];
                        $officeFinancials = $office['financials'] ?? [];

                        $officeStatus =
                            strtoupper(
                                $officeScores['status'] ?? 'RED'
                            );

                        $officeStatusColor = match($officeStatus) {
                            'GREEN' => '#15803d',
                            'AMBER' => '#b45309',
                            default => '#dc2626',
                        };

                        $officeStatusBackground = match($officeStatus) {
                            'GREEN' => '#dcfce7',
                            'AMBER' => '#fef3c7',
                            default => '#fee2e2',
                        };

                        $officeKey = 'office_' . $office['office_id'];

                    @endphp


                    {{-- MAIN ROW --}}

                    <tr
                        onclick="toggleBranch('{{ $officeKey }}')"
                        style="
                            border-top:1px solid #edf0f4;
                            cursor:pointer;
                        "
                    >

                        <td style="
                            padding:16px 15px;
                            text-align:center;
                        ">

                            <span
                                id="{{ $officeKey }}_arrow"
                                style="
                                    font-size:13px;
                                    color:#8a93a3;
                                    display:inline-block;
                                    transition:.2s;
                                "
                            >
                                ▶
                            </span>

                        </td>


                        <td style="
                            padding:16px 15px;
                        ">

                            <div style="
                                font-weight:600;
                                font-size:13px;
                                color:#202633;
                            ">
                                Office {{ $office['office_id'] }}
                            </div>

                        </td>


                        <td style="
                            padding:16px 15px;
                            text-align:right;
                            font-weight:700;
                            color:#202633;
                        ">

                            {{ number_format($officeScores['overall'] ?? 0, 0) }}

                        </td>


                        <td style="
                            padding:16px 15px;
                            text-align:right;
                            font-size:13px;
                            color:#343b48;
                        ">

                            K{{ number_format(
                                $officeFinancials['residual_cash'] ?? 0,
                                2
                            ) }}

                        </td>


                        <td style="
                            padding:16px 15px;
                            text-align:center;
                        ">

                            <span style="
                                display:inline-block;
                                padding:5px 10px;
                                border-radius:20px;
                                background:{{ $officeStatusBackground }};
                                color:{{ $officeStatusColor }};
                                font-size:10px;
                                font-weight:700;
                            ">
                                {{ $officeStatus }}
                            </span>

                        </td>

                    </tr>


                    {{-- EXPANDED ROW --}}

                    <tr
                        id="{{ $officeKey }}"
                        style="display:none;"
                    >

                        <td
                            colspan="5"
                            style="
                                padding:0;
                                background:#fafbfc;
                            "
                        >

                            <div style="
                                padding:24px 55px;
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
                                    ">

                                        <strong style="color:#202633;">
                                            Branch assessment:
                                        </strong>

                                        {{ $office['reason'] }}

                                    </div>

                                @endif


                                {{-- FINANCIALS --}}

                                <div style="
                                    display:grid;
                                    grid-template-columns:repeat(4, 1fr);
                                    gap:12px;
                                    margin-bottom:20px;
                                ">

                                    @php

                                        $branchMetrics = [
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

                                            'Net Cash Position' =>
                                                $officeFinancials['net_cash_position'] ?? 0,

                                            'Residual Cash' =>
                                                $officeFinancials['residual_cash'] ?? 0,
                                        ];

                                    @endphp


                                    @foreach($branchMetrics as $label => $value)

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
                                                color:#202633;
                                            ">
                                                K{{ number_format($value, 2) }}
                                            </div>

                                        </div>

                                    @endforeach

                                </div>


                                {{-- SCORES --}}

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

                                        <strong>
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
                                            COLLECTION
                                        </div>

                                        <strong>
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

                                        <strong>
                                            {{ $officeScores['residual_cash'] ?? 0 }}
                                        </strong>
                                    </div>

                                </div>

                            </div>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
@endsection


@section('footer-scripts')
<script>

function toggleBranch(id)
{
    const row = document.getElementById(id);
    const arrow = document.getElementById(id + '_arrow');

    if (row.style.display === 'none' || row.style.display === '') {

        row.style.display = 'table-row';

        arrow.style.transform = 'rotate(90deg)';

    } else {

        row.style.display = 'none';

        arrow.style.transform = 'rotate(0deg)';
    }
}

</script>
@endsection
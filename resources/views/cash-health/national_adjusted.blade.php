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




    /* =========================================================
       SIMPLE COMPARISON TABLES
       ========================================================= */
    .cash-simple-table {
        width:100%;
        border-collapse:collapse;
    }

    @media (max-width: 700px) {
        .cash-simple-table { min-width:560px; }
    }

</style>

<div style="
    padding:30px;
    background:#f6f8fb;
    min-height:100vh;
">

@php

    /*
    |--------------------------------------------------------------------------
    | NATIONAL CASH HEALTH DATA
    |--------------------------------------------------------------------------
    */

    $financials =
        $nationalHealth['financials'] ?? [];

    $scores =
        $nationalHealth['scores'] ?? [];

    /*
    |--------------------------------------------------------------------------
    | NEW MANAGEMENT SUMMARY
    |--------------------------------------------------------------------------
    */

    $classification =
        $nationalHealth['classification'] ?? [];

    $issues =
        $nationalHealth['issues'] ?? [];

    $contribution =
        $nationalHealth['contribution'] ?? [];

    $contributors =
        $nationalHealth['contributors'] ?? [];

    $branchIssueAnalysis =
        $nationalHealth['branch_issue_analysis'] ?? [];

    $contributionHistory =
        $nationalHealth['contribution_history'] ?? [];

    $metricDefinitions =
        $nationalHealth['metric_definitions'] ?? [];

    $nationalComparison =
        $nationalHealth['comparison'] ?? [];


    $comparisonStyle = function ($comparison) {
        $assessment = strtoupper($comparison['assessment'] ?? 'NEUTRAL');
        return match ($assessment) {
            'IMPROVED' => ['color' => '#15803d', 'background' => '#ecfdf5', 'label' => 'Improved'],
            'WORSENED' => ['color' => '#dc2626', 'background' => '#fef2f2', 'label' => 'Worsened'],
            default => ['color' => '#697386', 'background' => '#f3f4f6', 'label' => 'No material change']
        };
    };

    $formatComparisonChange = function ($comparison, $money = true) {
        if (empty($comparison)) return 'No previous-cycle data';
        $change = (float)($comparison['change'] ?? 0);
        $prefix = $change > 0 ? '+' : ($change < 0 ? '-' : '');
        $value = number_format(abs($change), $money ? 2 : 0);
        $percent = $comparison['percentage_change'] ?? null;
        return ($money ? 'K' : '') . $prefix . $value .
            ($percent !== null ? ' (' . ($percent > 0 ? '+' : '') . number_format($percent, 1) . '%)' : '');
    };

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $status = match (
        strtolower($scores['status'] ?? 'red')
    ) {
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

        'Healthy' =>
            '#15803d',

        'Needs Attention' =>
            '#b45309',

        default =>
            '#dc2626'
    };

    $statusBackground = match($status) {

        'Healthy' =>
            '#dcfce7',

        'Needs Attention' =>
            '#fef3c7',

        default =>
            '#fee2e2'
    };

    /*
    |--------------------------------------------------------------------------
    | INSTITUTION TYPE
    |--------------------------------------------------------------------------
    */

    $institutionType =
        $classification['label']
        ?? 'Not classified';

    $institutionTypeReason =
        $classification['reason']
        ?? '';

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
            Organization-wide cash position
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
                color:{{ ($totalBalance ?? 0) < 0 ? '#dc2626' : '#15803d' }};
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
{{-- ========================================================= --}}
{{-- CASH HEALTH MANAGEMENT VIEW --}}
{{-- ========================================================= --}}
@php
    $simpleComparison = $nationalComparison ?? [];
    $overallComparison = $simpleComparison['overall'] ?? [];
    $scoreComparisons = $simpleComparison['scores'] ?? [];
    $financialComparisons = $simpleComparison['financials'] ?? [];
    $institutionComparison = $simpleComparison['institution_type'] ?? [];

    $currentOverall = (float)($scores['overall'] ?? 0);
    $previousClosingOverall = (float)($overallComparison['cycle_closing'] ?? ($scoreComparisons['overall']['previous'] ?? 0));
    $currentAverageOverall = (float)($overallComparison['cycle_average_current'] ?? $currentOverall);
    $previousAverageOverall = (float)($overallComparison['cycle_average'] ?? $previousClosingOverall);
    $previousTodayOverall = (float)($overallComparison['today_last_cycle'] ?? $previousClosingOverall);

    $currentInstitutionType = $institutionType ?: 'Not classified';

    /*
     * The API currently supplies closing, average and same-point-last-cycle
     * values. A historical "cycle best" value is not supplied, so we leave
     * that field blank rather than inventing a number.
     */
    $previousCycleBest = null;
    $currentCycleBest = null;

    /*
     * Total amount required to meet the current loan target.
     */
    $currentLoanTarget = (float)($financials['minimum_loan_target'] ?? 0);
    $previousLoanTarget = (float)($financialComparisons['minimum_loan_target']['previous'] ?? 0);
    $previousToDateTarget = (float)($simpleComparison['target']['previous_to_date'] ?? $previousLoanTarget);

    /*
     * Actual disbursement performance used in the lower comparison.
     * These are cumulative cycle figures from the current API.
     */
    $currentDisbursed = (float)($nationalHealth['disbursed'] ?? 0);
    $previousDisbursed = (float)($financialComparisons['disbursed']['previous'] ?? 0);

    $currentDayDisbursement = $currentDisbursed;
    $previousClosingDisbursement = $previousDisbursed;
    $currentCycleAverageDisbursement = $currentLoanTarget > 0
        ? ($currentDisbursed / $currentLoanTarget) * 100
        : 0;
    $previousCycleAverageDisbursement = $previousLoanTarget > 0
        ? ($previousDisbursed / $previousLoanTarget) * 100
        : 0;

    $previousTodayDisbursement = (float)($financialComparisons['disbursed']['previous_to_date'] ?? $previousDisbursed);

    /*
     * Keep the wireframe clean: no extra score cards, financial tables or
     * management panels are placed inside this primary view.
     */
@endphp

<style>
    /* =========================================================
       NATIONAL CASH HEALTH — MANAGEMENT SUMMARY
       Follows the boss's wireframe while using a clean dashboard UI.
       ========================================================= */
    .boss-cash-health {
        margin: 24px 0 30px;
        color: #202633;
    }

    .boss-cash-health * {
        box-sizing: border-box;
    }

    .boss-section {
        max-width: 1080px;
        margin: 0 auto;
    }

    /* Overall score */
    .boss-overall-card {
        width: min(460px, 100%);
        margin: 0 auto;
        background: #fff;
        border: 1px solid #dfe4ea;
        border-radius: 14px;
        box-shadow: 0 5px 18px rgba(30, 45, 65, .06);
        text-align: center;
        overflow: hidden;
    }

    .boss-overall-label {
        padding: 14px 18px 3px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 1.4px;
        color: #697386;
        text-transform: uppercase;
    }

    .boss-overall-score-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 0 18px 16px;
    }

    .boss-overall-score {
        font-size: 40px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -1px;
        color: #202633;
    }

    .boss-overall-status {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    /* Comparison tables */
    .boss-comparison-card {
        width: min(900px, 100%);
        margin: 0 auto;
        background: #fff;
        border: 1px solid #dfe4ea;
        border-radius: 0 0 14px 14px;
        box-shadow: 0 5px 18px rgba(30, 45, 65, .04);
        overflow: hidden;
    }

    .boss-comparison-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }

    .boss-comparison-table th {
        padding: 13px 18px;
        background: #f7f9fb;
        color: #343b48;
        font-size: 14px;
        font-weight: 800;
        text-align: center;
        border-bottom: 1px solid #dfe4ea;
    }

    .boss-comparison-table th + th,
    .boss-comparison-table td + td {
        border-left: 1px solid #e5e9ee;
    }

    .boss-comparison-table td {
        padding: 18px 22px;
        vertical-align: top;
        font-size: 14px;
    }

    .boss-comparison-table td:first-child {
        border-radius: 0 0 0 13px;
    }

    .boss-comparison-table td:last-child {
        border-radius: 0 0 13px 0;
    }

    .boss-metric-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 18px;
        padding: 8px 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .boss-metric-row:last-child {
        border-bottom: none;
    }

    .boss-metric-label {
        color: #697386;
        font-size: 13px;
        line-height: 1.35;
    }

    .boss-metric-value {
        color: #202633;
        font-size: 17px;
        font-weight: 800;
        white-space: nowrap;
    }

    .boss-metric-value.muted {
        color: #9aa2af;
        font-weight: 600;
    }

    /* Space between the two major parts, matching the wireframe hierarchy */
    .boss-middle-stack {
        margin: 48px auto 0;
        text-align: center;
    }

    .boss-institution-card {
        width: min(500px, 100%);
        margin: 0 auto;
        padding: 16px 24px 18px;
        background: #fff;
        border: 1px solid #dfe4ea;
        border-radius: 12px;
        box-shadow: 0 5px 18px rgba(30, 45, 65, .05);
    }

    .boss-institution-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #7b8494;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .boss-institution-value {
        font-size: 23px;
        font-weight: 800;
        color: #202633;
    }

    .boss-target-card {
        width: min(520px, calc(100% - 40px));
        margin: -1px auto 0;
        padding: 13px 22px;
        background: #f8fafc;
        border: 1px solid #dfe4ea;
        border-radius: 10px;
        position: relative;
        z-index: 2;
    }

    .boss-target-label {
        font-size: 13px;
        font-weight: 700;
        color: #697386;
    }

    .boss-target-value {
        margin-left: 5px;
        font-size: 20px;
        font-weight: 800;
        color: #202633;
    }

    .boss-target-comparison {
        width: min(1080px, 100%);
        margin: -1px auto 0;
        background: #fff;
        border: 1px solid #dfe4ea;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 18px rgba(30, 45, 65, .05);
    }

    .boss-target-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }

    .boss-target-table th {
        padding: 13px 18px;
        background: #f7f9fb;
        color: #343b48;
        font-size: 14px;
        font-weight: 800;
        text-align: center;
        border-bottom: 1px solid #dfe4ea;
    }

    .boss-target-table th + th,
    .boss-target-table td + td {
        border-left: 1px solid #e5e9ee;
    }

    .boss-target-table td {
        padding: 18px 22px;
        vertical-align: top;
    }

    .boss-target-table .boss-metric-row {
        min-height: 39px;
    }

    .boss-target-caption {
        padding: 11px 18px;
        background: #fbfcfd;
        border-top: 1px solid #edf0f3;
        color: #8a93a3;
        font-size: 11px;
        text-align: center;
    }

    @media (max-width: 700px) {
        .boss-comparison-table,
        .boss-target-table {
            table-layout: auto;
            min-width: 640px;
        }

        .boss-comparison-card,
        .boss-target-comparison {
            overflow-x: auto;
        }

        .boss-overall-score {
            font-size: 34px;
        }

        .boss-middle-stack {
            margin-top: 36px;
        }
    }
</style>

<div class="boss-cash-health">
    <div class="boss-section">

        {{-- =====================================================
             OVERALL HEALTH
             ===================================================== --}}
        <div class="boss-overall-card">
            <div class="boss-overall-label">Overall Health</div>
            <div class="boss-overall-score-row">
                <div class="boss-overall-score">
                    {{ number_format($currentOverall, 0) }}/100
                </div>
                <span class="boss-overall-status"
                      style="background:{{ $statusBackground }};color:{{ $statusColor }};">
                    {{ $status }}
                </span>
            </div>
        </div>

        {{-- =====================================================
             OVERALL HEALTH COMPARISON
             ===================================================== --}}
        <div class="boss-comparison-card">
            <table class="boss-comparison-table">
                <thead>
                    <tr>
                        <th>Preceding Cycle</th>
                        <th>Current Cycle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Closing</span>
                                <span class="boss-metric-value">{{ number_format($previousClosingOverall, 0) }}</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Average</span>
                                <span class="boss-metric-value">{{ number_format($previousAverageOverall, 0) }}</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Best</span>
                                <span class="boss-metric-value muted">{{ $previousCycleBest !== null ? number_format($previousCycleBest, 0) : '—' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Current Day</span>
                                <span class="boss-metric-value">{{ number_format($currentOverall, 0) }}</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Average Thus Far</span>
                                <span class="boss-metric-value">{{ number_format($currentAverageOverall, 0) }}</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Today vs Last Cycle</span>
                                <span class="boss-metric-value {{ $currentOverall >= $previousTodayOverall ? '' : 'muted' }}">
                                    {{ number_format($previousTodayOverall, 0) }}
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- =====================================================
             INSTITUTION TYPE + TARGET
             ===================================================== --}}
        <div class="boss-middle-stack">
            <div class="boss-institution-card">
                <div class="boss-institution-label">Institution Type</div>
                <div class="boss-institution-value">{{ $currentInstitutionType }}</div>
            </div>

            <div class="boss-target-card">
                <span class="boss-target-label">Total to Meet Target</span>
                <span class="boss-target-value">K{{ number_format($currentLoanTarget, 0) }}</span>
            </div>
        </div>

        {{-- =====================================================
             TARGET / DISBURSEMENT COMPARISON
             ===================================================== --}}
        <div class="boss-target-comparison">
            <table class="boss-target-table">
                <thead>
                    <tr>
                        <th>Preceding Cycle</th>
                        <th>Current Cycle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Closing</span>
                                <span class="boss-metric-value">K{{ number_format($previousDisbursed, 0) }}</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Average</span>
                                <span class="boss-metric-value">{{ number_format($previousCycleAverageDisbursement, 0) }}%</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Best</span>
                                <span class="boss-metric-value muted">—</span>
                            </div>
                        </td>
                        <td>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Current Day</span>
                                <span class="boss-metric-value">K{{ number_format($currentDayDisbursement, 0) }}</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Cycle Average Thus Far</span>
                                <span class="boss-metric-value">{{ number_format($currentCycleAverageDisbursement, 0) }}%</span>
                            </div>
                            <div class="boss-metric-row">
                                <span class="boss-metric-label">Today vs Last Cycle</span>
                                <span class="boss-metric-value">K{{ number_format($previousTodayDisbursement, 0) }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="boss-target-caption">
                Cycle averages show progress against the applicable loan target.
            </div>
        </div>

    </div>
</div>

{{-- QUICK GUIDE --}}
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
                50–79 Attention
            </span>

            <span class="status-band status-risk">
                0–49 At Risk
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
{{-- ISSUES REQUIRING ATTENTION --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    padding:22px 25px;
    margin-bottom:24px;
">

    <div style="
        font-size:16px;
        font-weight:700;
        color:#202633;
        margin-bottom:5px;
    ">
        Issues Requiring Attention
    </div>

    <div style="
        font-size:12px;
        color:#697386;
        margin-bottom:18px;
    ">
        Number of provinces and branches currently falling into each management classification.
    </div>


    <div style="
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:14px;
    ">


        {{-- DISBURSEMENT --}}

        <div style="
            border:1px solid #e7eaf0;
            border-radius:10px;
            padding:16px;
        ">

            <div style="
                font-size:11px;
                font-weight:700;
                color:#697386;
                text-transform:uppercase;
            ">
                Disbursements — Failing to Meet Loans Target
            </div>

            <div style="
                margin-top:9px;
                font-size:22px;
                font-weight:700;
                color:#b45309;
            ">

                {{ $issues['disbursements']['branch_count'] ?? 0 }}

            </div>

            <div style="
                margin-top:3px;
                font-size:11px;
                color:#8a93a3;
            ">

                branches affected

            </div>

            <div style="
                margin-top:10px;
                font-size:11px;
                color:#697386;
            ">

                Across
                <strong>
                    {{ $issues['disbursements']['province_count'] ?? 0 }}
                </strong>
                provinces

            </div>

        </div>


        {{-- COLLECTION --}}

        <div style="
            border:1px solid #e7eaf0;
            border-radius:10px;
            padding:16px;
        ">

            <div style="
                font-size:11px;
                font-weight:700;
                color:#697386;
                text-transform:uppercase;
            ">
                Collecting — Meeting Loans Target
            </div>

            <div style="
                margin-top:9px;
                font-size:22px;
                font-weight:700;
                color:#dc2626;
            ">

                {{ $issues['collecting']['branch_count'] ?? 0 }}

            </div>

            <div style="
                margin-top:3px;
                font-size:11px;
                color:#8a93a3;
            ">

                branches affected

            </div>

            <div style="
                margin-top:10px;
                font-size:11px;
                color:#697386;
            ">

                Across
                <strong>
                    {{ $issues['collecting']['province_count'] ?? 0 }}
                </strong>
                provinces

            </div>

        </div>


        {{-- RESIDUAL CASH --}}

        <div style="
            border:1px solid #e7eaf0;
            border-radius:10px;
            padding:16px;
        ">

            <div style="
                font-size:11px;
                font-weight:700;
                color:#697386;
                text-transform:uppercase;
            ">
                Meeting Loans Target — Failing to Collect
            </div>

            <div style="
                margin-top:9px;
                font-size:22px;
                font-weight:700;
                color:#b45309;
            ">

                {{ $issues['meeting_loans_target']['branch_count'] ?? 0 }}

            </div>

            <div style="
                margin-top:3px;
                font-size:11px;
                color:#8a93a3;
            ">

                branches affected

            </div>

            <div style="
                margin-top:10px;
                font-size:11px;
                color:#697386;
            ">

                Across
                <strong>
                    {{ $issues['meeting_loans_target']['province_count'] ?? 0 }}
                </strong>
                provinces

            </div>

        </div>


    </div>

</div>



{{-- ========================================================= --}}
{{-- NET CONTRIBUTION PERFORMANCE --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    padding:22px 25px;
    margin-bottom:24px;
">

    <div style="
        font-size:16px;
        font-weight:700;
        color:#202633;
        margin-bottom:5px;
    ">
        Net Contribution
    </div>

    <div style="
        font-size:12px;
        color:#697386;
        margin-bottom:18px;
    ">
        Shows whether the institution added or consumed cash
        over different reporting periods.
    </div>


    @php

        $contributionPeriods = [

            'this_month' => 'This Month',

            'last_month' => 'Last Month',

            'this_quarter' => 'This Quarter',

            'this_year' => 'This Year',

            'last_year' => 'Last Year',

        ];

    @endphp


    <div style="
        display:grid;
        grid-template-columns:repeat(5,1fr);
        gap:10px;
    ">

        @foreach($contributionPeriods as $key => $label)

            @php

                $value =
                    $contribution[$key] ?? 0;

                $positive =
                    $value >= 0;

            @endphp


            <div style="
                border:1px solid #e7eaf0;
                border-radius:10px;
                padding:14px;
            ">

                <div style="
                    font-size:10px;
                    color:#8a93a3;
                    font-weight:700;
                    text-transform:uppercase;
                ">
                    {{ $label }}
                </div>


                <div style="
                    margin-top:9px;
                    font-size:17px;
                    font-weight:700;
                    color:{{ $positive ? '#15803d' : '#dc2626' }};
                ">

                    {{ $positive ? '+' : '-' }}
                    K{{ number_format(abs($value), 0) }}

                </div>


                <div style="
                    margin-top:6px;
                    font-size:10px;
                    color:{{ $positive ? '#15803d' : '#dc2626' }};
                ">

                    {{ $positive ? 'Positive contribution' : 'Negative contribution' }}

                </div>

            </div>

        @endforeach

    </div>


    {{-- TREND INTERPRETATION --}}

    @if(!empty($contribution['interpretation']))

        <div style="
            margin-top:16px;
            padding:13px 15px;
            background:#f8fafc;
            border:1px solid #edf0f3;
            border-radius:9px;
            font-size:12px;
            line-height:1.6;
            color:#4b5563;
        ">

            <strong style="color:#202633;">
                Sustainability:
            </strong>

            {{ $contribution['interpretation'] }}

        </div>

    @endif

</div>



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

          
                                         <strong>
    Why is the National Cash Health Score at this level?
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
{{-- ========================================================= --}}
{{-- CONTRIBUTORS --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    padding:22px 25px;
    margin-bottom:24px;
">
    <div style="
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:20px;
        flex-wrap:wrap;
        margin-bottom:18px;
    ">
        <div>
            <div style="font-size:16px;font-weight:700;color:#202633;">
                Value Contributors
            </div>
            <div style="margin-top:5px;font-size:12px;color:#697386;line-height:1.6;">
                Provinces and branches adding or reducing net contribution for each reporting period.
            </div>
        </div>

        <select id="contributorPeriod" style="
            border:1px solid #dfe3e8;
            border-radius:8px;
            padding:8px 30px 8px 10px;
            font-size:12px;
            color:#343b48;
            background:#fff;
            cursor:pointer;
            outline:none;
        ">
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
            <option value="this_quarter">This Quarter</option>
            <option value="this_year">This Year</option>
            <option value="last_year">Last Year</option>
        </select>
    </div>

    <div id="contributorsContent"></div>
</div>

{{-- ========================================================= --}}
{{-- BRANCH ISSUE ANALYSIS --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    padding:22px 25px;
    margin-bottom:24px;
">
    <div style="font-size:16px;font-weight:700;color:#202633;margin-bottom:5px;">
        Branches Requiring Management Attention
    </div>
    <div style="font-size:12px;color:#697386;line-height:1.6;margin-bottom:18px;">
        Individual branches identified by the API as belonging to one of the three management classifications: disbursements, collecting, or meeting the loans target but failing to collect.
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">
        @foreach([
            'disbursements' => 'Disbursements — Failing to Meet Loans Target',
            'collecting' => 'Collecting — Meeting Loans Target',
            'meeting_loans_target' => 'Meeting Loans Target — Failing to Collect'
        ] as $issueKey => $issueLabel)
            @php
                $issueData = $branchIssueAnalysis[$issueKey] ?? [];
                $concerningBranches = $issueData['concerning_branches'] ?? [];
            @endphp

            <div style="border:1px solid #e7eaf0;border-radius:10px;overflow:hidden;">
                <div style="padding:14px;background:#f8fafc;border-bottom:1px solid #edf0f3;">
                    <div style="font-size:11px;font-weight:700;color:#697386;text-transform:uppercase;">
                        {{ $issueLabel }}
                    </div>
                    <div style="margin-top:7px;font-size:22px;font-weight:700;color:#b45309;">
                        {{ $issueData['affected_branch_count'] ?? 0 }}
                    </div>
                    <div style="margin-top:2px;font-size:10px;color:#8a93a3;">
                        affected branches
                    </div>
                    <div style="margin-top:7px;font-size:10px;color:#697386;">
                        Average affected-branch score:
                        <strong>{{ number_format($issueData['average_score_among_affected_branches'] ?? 0, 1) }}/100</strong>
                    </div>
                </div>

                @forelse(array_slice($concerningBranches, 0, 5) as $branch)
                    <div style="padding:11px 14px;border-bottom:1px solid #f0f2f5;">
                        <div style="font-size:11px;font-weight:700;color:#343b48;">
                            {{ $branch['office_name'] ?? 'Unknown Branch' }}
                        </div>
                        <div style="margin-top:3px;font-size:10px;color:#8a93a3;">
                            {{ $branch['province_name'] ?? 'Unknown Province' }}
                            @if(!empty($branch['district_name']))
                                · {{ $branch['district_name'] }}
                            @endif
                        </div>
                        <div style="margin-top:5px;font-size:10px;color:#697386;">
                            Issue score:
                            <strong>{{ number_format($branch['score'] ?? 0, 1) }}/100</strong>
                        </div>
                    </div>
                @empty
                    <div style="padding:15px;font-size:11px;color:#8a93a3;">
                        No concerning branches identified.
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>

{{-- ========================================================= --}}
{{-- VALUE ADDED / SUSTAINABILITY --}}
{{-- ========================================================= --}}

<div style="
    background:#fff;
    border:1px solid #e6e9ef;
    border-radius:14px;
    padding:22px 25px;
    margin-bottom:24px;
">
    <div style="font-size:16px;font-weight:700;color:#202633;margin-bottom:5px;">
        Value Added & Sustainability
    </div>
    <div style="font-size:12px;color:#697386;margin-bottom:18px;">
        Contribution = Actual Collections − Actual Disbursements − Branch Operating Costs.
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        @foreach([
            'trend' => 'Trend',
            'value_added' => 'Value Added',
            'sustainability' => 'Sustainability',
            'current_vs_last_month' => 'Current vs Last Month'
        ] as $key => $label)
            @php
                $value = $contribution[$key] ?? 0;
                $isNumeric = is_numeric($value);
                $positive = $isNumeric ? ((float)$value >= 0) : true;
            @endphp
            <div style="border:1px solid #e7eaf0;border-radius:10px;padding:14px;">
                <div style="font-size:10px;color:#8a93a3;font-weight:700;text-transform:uppercase;">
                    {{ $label }}
                </div>
                <div style="
                    margin-top:8px;
                    font-size:16px;
                    font-weight:700;
                    color:{{ $isNumeric ? ($positive ? '#15803d' : '#dc2626') : '#202633' }};
                ">
                    @if($isNumeric)
                        {{ $positive ? '+' : '-' }}K{{ number_format(abs((float)$value), 0) }}
                    @else
                        {{ $value ?: '—' }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if(!empty($contribution['interpretation']))
        <div style="
            margin-top:15px;
            padding:13px 15px;
            background:#f8fafc;
            border:1px solid #edf0f3;
            border-radius:9px;
            font-size:12px;
            line-height:1.6;
            color:#4b5563;
        ">
            <strong style="color:#202633;">Management interpretation:</strong>
            {{ $contribution['interpretation'] }}
        </div>
    @endif
</div>


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

    <div style="min-width:0;">

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
            max-width:850px;
        ">
            The National Cash Health Score measures the institution's overall
            ability to meet its financial obligations using disbursement,
            collection quality and residual cash performance.
        </div>

    </div>

    <div style="
        font-size:12px;
        color:#4b5563;
        font-weight:600;
        white-space:nowrap;
        margin-left:20px;
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
            Net Contribution
        </strong>
        is a Kwacha value showing Collections − Disbursements −
        Branch Operating Costs for the reporting period.

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


                <th style="
    text-align:right;
    padding:14px 15px;
    font-size:11px;
">
    CASH BALANCE
    <div style="
        font-size:10px;
        font-weight:400;
        color:#9ca3af;
        margin-top:3px;
    ">
        Current available cash
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


                {{-- NET CONTRIBUTION --}}

                <th style="
                    text-align:right;
                    padding:13px 15px;
                    font-size:11px;
                    color:#374151;
                    letter-spacing:.8px;
                    font-weight:700;
                ">

                    <div>
                        NET CONTRIBUTION
                    </div>

                    <div style="
                        font-size:10px;
                        font-weight:500;
                        letter-spacing:0;
                        text-transform:none;
                        margin-top:4px;
                        color:#6b7280;
                    ">
                        Current cycle value added
                    </div>

                </th>


                {{-- TYPE --}}

<th style="
    text-align:center;
    padding:13px 15px;
    font-size:11px;
    color:#374151;
    letter-spacing:.8px;
    font-weight:700;
">

    <div>
        TYPE
    </div>

    <div style="
        font-size:10px;
        font-weight:500;
        letter-spacing:0;
        text-transform:none;
        margin-top:4px;
        color:#6b7280;
    ">
        Main issue
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

        $scoreDetails = $province['reason'] ?? '';

        $provinceClassification =
            $province['classification'] ?? [];

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

        $provinceStatusColor = match(strtoupper($provinceScores['status'] ?? 'RED')) {

            'GREEN' => '#15803d',

            'AMBER' => '#b45309',

            default => '#dc2626'

        };

        $provinceStatusBackground = match(strtoupper($provinceScores['status'] ?? 'RED')) {

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
    padding:14px 15px;
    text-align:right;
    font-weight:600;
    color:#111827;
    white-space:nowrap;
">
   <span
    class="province-cash-balance"
    data-province-id="{{ $province['province_id'] }}"
>
    <i class="fa fa-spinner fa-spin"></i>
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
            @if(!empty($province['comparison']['scores']['overall']))
                <div style="font-size:9px;color:{{ $comparisonStyle($province['comparison']['scores']['overall'])['color'] }};margin-top:3px;">
                    {{ $formatComparisonChange($province['comparison']['scores']['overall'], false) }} vs last cycle
                </div>
            @endif

        </td>


        @php
            $provinceContribution = (float)($province['contribution']['this_month'] ?? 0);
        @endphp

        <td style="
            padding:16px;
            text-align:right;
            font-weight:600;
            color:{{ $provinceContribution < 0 ? '#dc2626' : '#15803d' }};
            white-space:nowrap;
        ">
            {{ $provinceContribution >= 0 ? '+' : '-' }}K{{ number_format(abs($provinceContribution), 0) }}
            @if(!empty($province['comparison']['contribution']))
                <div style="font-size:9px;color:{{ $comparisonStyle($province['comparison']['contribution'])['color'] }};margin-top:3px;">
                    {{ $formatComparisonChange($province['comparison']['contribution'], true) }} vs last cycle
                </div>
            @endif
        </td>

        <td style="
    padding:16px 12px;
    text-align:center;
">

    <span style="
        display:inline-block;
        padding:5px 8px;
        border-radius:7px;
        background:#f8fafc;
        border:1px solid #e7eaf0;
        color:#343b48;
        font-size:9px;
        font-weight:700;
        line-height:1.3;
    ">

        {{ $provinceClassification['label'] ?? 'Not classified' }}

    </span>

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
            colspan="7"
            style="
                padding:0;
                background:#fafbfc;
            "
        >

            <div style="
                padding:10px 30px 20px 55px;
            ">

            {{-- ================================================= --}}
{{-- PROVINCE ISSUE SUMMARY --}}
{{-- ================================================= --}}

@php

    $provinceIssues =
        $province['issues'] ?? [];

@endphp


<div style="
    margin-bottom:14px;
    background:#fff;
    border:1px solid #e7eaf0;
    border-radius:10px;
    padding:15px;
">

    <div style="
        font-size:11px;
        font-weight:700;
        color:#8a93a3;
        text-transform:uppercase;
        margin-bottom:10px;
    ">
        Issues Requiring Attention
    </div>


    <div style="
        display:flex;
        flex-wrap:wrap;
        gap:10px;
    ">


        <div style="
            padding:8px 10px;
            background:#fff7ed;
            border-radius:7px;
            font-size:11px;
            color:#9a3412;
        ">

            Disbursements — Failing to Meet Loans Target:
            <strong>
                {{ $provinceIssues['disbursements']['branch_count'] ?? 0 }}
            </strong>
            branches

        </div>


        <div style="
            padding:8px 10px;
            background:#fef2f2;
            border-radius:7px;
            font-size:11px;
            color:#991b1b;
        ">

            Collecting — Meeting Loans Target:
            <strong>
                {{ $provinceIssues['collecting']['branch_count'] ?? 0 }}
            </strong>
            branches

        </div>


        <div style="
            padding:8px 10px;
            background:#fff7ed;
            border-radius:7px;
            font-size:11px;
            color:#9a3412;
        ">

            Meeting Loans Target — Failing to Collect:
            <strong>
                {{ $provinceIssues['meeting_loans_target']['branch_count'] ?? 0 }}
            </strong>
            branches

        </div>


    </div>

</div>


                @foreach(($province['districts'] ?? []) as $district)

                    @php

                        $districtId =
                            $district['district_id'] ?? 0;

                            $scoreDetails = $district['reason'] ?? '';

                        $districtKey =
                            $provinceKey .
                            '_district_' .
                            $districtId;

                        $districtScores =
                            $district['scores'] ?? [];

                        $districtFinancials =
                            $district['financials'] ?? [];

                        $districtClassification =
                            $district['classification'] ?? [];

$districtStatus = match (strtolower($districtScores['status'] ?? 'red')) {
    'red'   => 'At Risk',
    'amber' => 'Needs Attention',
    'green' => 'Healthy',
    default => 'At Risk',
};

                        $districtStatusColor = match(strtoupper($districtScores['status'] ?? 'RED')) {

                            'GREEN' => '#15803d',

                            'AMBER' => '#b45309',

                            default => '#dc2626'

                        };

                        $districtStatusBackground = match(strtoupper($districtScores['status'] ?? 'RED')) {

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
        grid-template-columns:45px minmax(180px, 1fr) 120px 110px 150px 120px minmax(220px, 1.5fr);
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

    <div>
        {{ $district['district_name'] ?? 'Unknown District' }}
    </div>

    <div style="
        margin-top:4px;
        display:flex;
        gap:6px;
        align-items:center;
        flex-wrap:wrap;
    ">
        <span style="
            font-size:9px;
            color:#8a93a3;
            font-weight:500;
        ">
            {{ $district['office_count'] ?? 0 }} offices
        </span>

        @if(!empty($districtClassification['label']))
            <span style="
                display:inline-block;
                padding:3px 6px;
                border-radius:6px;
                background:#f8fafc;
                border:1px solid #e7eaf0;
                color:#596273;
                font-size:8px;
                font-weight:700;
                line-height:1.2;
            ">
                {{ $districtClassification['short_label'] ?? $districtClassification['label'] }}
            </span>
        @endif
    </div>

</div>



<div style="
    padding:14px 15px;
    text-align:right;
    font-weight:600;
    font-size:12px;
    color:#202633;
    min-width:0;
    white-space:nowrap;
">
    <span
        class="district-cash-balance"
        data-district-id="{{ $district['district_id'] }}"
    >
        <i class="fa fa-spinner fa-spin"></i>
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
    @if(!empty($district['comparison']['scores']['overall']))
        <div style="font-size:9px;color:{{ $comparisonStyle($district['comparison']['scores']['overall'])['color'] }};margin-top:3px;">
            {{ $formatComparisonChange($district['comparison']['scores']['overall'], false) }} vs last cycle
        </div>
    @endif

</div>


{{-- NET CONTRIBUTION --}}
@php
    $districtContribution = (float)($district['contribution']['this_month'] ?? 0);
@endphp

<div style="
    padding:14px;
    text-align:right;
    font-size:12px;
    font-weight:600;
    color:{{ $districtContribution < 0 ? '#dc2626' : '#15803d' }};
    white-space:nowrap;
">
    {{ $districtContribution >= 0 ? '+' : '-' }}K{{ number_format(abs($districtContribution), 0) }}
    @if(!empty($district['comparison']['contribution']))
        <div style="font-size:9px;color:{{ $comparisonStyle($district['comparison']['contribution'])['color'] }};margin-top:3px;">
            {{ $formatComparisonChange($district['comparison']['contribution'], true) }} vs last cycle
        </div>
    @endif
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
    width:100%;
    overflow:hidden;
    overflow-wrap:anywhere;
    word-break:break-word;
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

                                    $officeClassification =
                                        $office['classification'] ?? [];

                                 $officeStatus = match (strtolower($officeScores['status'] ?? 'red')) {
    'red'   => 'At Risk',
    'amber' => 'Needs Attention',
    'green' => 'Healthy',
    default => 'At Risk',
};
                                    $officeStatusColor = match(strtoupper($officeScores['status'] ?? 'RED')) {

                                        'GREEN' => '#15803d',

                                        'AMBER' => '#b45309',

                                        default => '#dc2626'

                                    };

                                    $officeStatusBackground = match(strtoupper($officeScores['status'] ?? 'RED')) {

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
        grid-template-columns:45px minmax(160px,1fr) 120px 120px 150px 120px 100px;
        align-items:center;
        border-top:1px solid #f0f2f5;
        cursor:pointer;
        background:#fafbfc;
        width:100%;
    "
>

    {{-- ARROW --}}
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


    {{-- OFFICE NAME --}}
    <div style="
        padding:14px;
        font-weight:600;
        font-size:12px;
        color:#343b48;
        min-width:0;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    ">

        <div>
            {{ $office['office_name'] ?? ($offices[$office['office_id']]->name ?? 'Unknown Office') }}
        </div>

        @if(!empty($officeClassification['label']))
            <div style="
                margin-top:4px;
                font-size:8px;
                color:#737c8b;
                font-weight:700;
                line-height:1.3;
            ">
                {{ $officeClassification['short_label'] ?? $officeClassification['label'] }}
            </div>
        @endif

    </div>


    {{-- CASH BALANCE --}}
    <div style="
        padding:14px;
        text-align:right;
        font-weight:700;
        font-size:12px;
        color:#202633;
        min-width:0;
        white-space:nowrap;
    ">

        <span
            class="cash-balance"
            data-office-id="{{ $office['office_id'] }}"
        >
            <i class="fa fa-spinner fa-spin"></i>
        </span>

    </div>


    {{-- SCORE --}}
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


    {{-- NET CONTRIBUTION --}}
    @php
        $officeContribution = (float)($office['contribution']['this_month'] ?? 0);
    @endphp

    <div style="
        padding:14px;
        text-align:right;
        font-size:12px;
        font-weight:600;
        color:{{ $officeContribution < 0 ? '#dc2626' : '#15803d' }};
        white-space:nowrap;
    ">
        {{ $officeContribution >= 0 ? '+' : '-' }}K{{ number_format(abs($officeContribution), 0) }}
    </div>


    {{-- STATUS --}}
    <div style="
        padding:14px;
        text-align:center;
        min-width:0;
    ">

        <span style="
            display:inline-block;
            padding:4px 8px;
            border-radius:20px;
            background:{{ $officeStatusBackground }};
            color:{{ $officeStatusColor }};
            font-size:9px;
            font-weight:700;
            white-space:nowrap;
        ">

            {{ $officeStatus }}

        </span>

    </div>


    {{-- VIEW --}}
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

                                         <strong>
    Why is the National Cash Health Score at this level?
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
            'value' => $officeFinancials['minimum_loan_target'] ?? 0,
            'comparison' => $office['comparison']['financials']['minimum_loan_target'] ?? []
        ],

        [
            'label' => 'Maximum Repayment',
            'description' => 'Expected maximum amount to be collected.',
            'value' => $officeFinancials['maximum_expected_repayment'] ?? 0,
            'comparison' => $office['comparison']['financials']['maximum_expected_repayment'] ?? []
        ],

        [
            'label' => 'Fixed Costs',
            'description' => 'Essential operating costs that must be paid.',
            'value' => $officeFinancials['mandatory_fixed_cost'] ?? 0,
            'comparison' => $office['comparison']['financials']['mandatory_fixed_cost'] ?? []
        ],

        [
            'label' => 'Salaries',
            'description' => 'Expected salary costs for the period.',
            'value' => $officeFinancials['salaries'] ?? 0,
            'comparison' => $office['comparison']['financials']['salaries'] ?? []
        ],

        [
            'label' => 'Defaults',
            'description' => 'Money that is currently overdue or uncollected.',
            'value' => $officeFinancials['defaults'] ?? 0,
            'comparison' => $office['comparison']['financials']['defaults'] ?? []
        ],

        [
            'label' => 'Irregular Reserve',
            'description' => 'Money set aside for irregular costs.',
            'value' => $officeFinancials['irregular_cost_reserve'] ?? 0,
            'comparison' => $office['comparison']['financials']['irregular_cost_reserve'] ?? []
        ],

        [
            'label' => 'Salary Advances',
            'description' => 'Money advanced to staff.',
            'value' => $officeFinancials['salary_advance_reserve'] ?? 0,
            'comparison' => $office['comparison']['financials']['salary_advance_reserve'] ?? []
        ],

        [
            'label' => 'Net Cash',
            'description' => 'Collections minus disbursements and operating costs.',
            'value' => $officeFinancials['net_cash_position'] ?? 0,
            'comparison' => $office['comparison']['financials']['net_cash_position'] ?? []
        ],

        [
            'label' => 'Residual Cash',
            'description' => 'Cash remaining after expected obligations and reserves.',
            'value' => $officeFinancials['residual_cash'] ?? 0,
            'comparison' => $office['comparison']['financials']['residual_cash'] ?? []
        ],

        [
            'label' => 'Net Contribution',
            'description' => 'Collections minus disbursements and branch operating costs.',
            'value' => $office['contribution']['this_month'] ?? 0,
            'comparison' => $office['comparison']['contribution'] ?? []
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

        @if(!empty($metric['comparison']))
            @php
                $metricComparison = $metric['comparison'];
                $metricStyle = $comparisonStyle($metricComparison);
                $metricChange = $formatComparisonChange($metricComparison, true);
            @endphp
            <div style="margin-top:8px;font-size:9px;line-height:1.5;">
                <span style="color:{{ $metricStyle['color'] }};font-weight:700;">{{ $metricChange }}</span>
                <span style="color:#8a93a3;">vs previous cycle</span>
            </div>
            <div style="margin-top:3px;font-size:9px;color:{{ $metricStyle['color'] }};font-weight:600;">{{ $metricStyle['label'] }}</div>
        @endif

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
    <div style="margin-bottom:18px;">
        <div style="font-size:16px;font-weight:700;color:#202633;">
            National Contribution History
        </div>
        <div style="font-size:12px;color:#8a93a3;margin-top:4px;">
            Contribution trend returned by the National Cash Health API.
        </div>
    </div>

    <div style="
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:12px;
        margin-bottom:20px;
    ">
        @foreach(array_slice($contributionHistory, 0, 4) as $cycleHistory)
            @php
                $cycleContribution = (float)($cycleHistory['contribution'] ?? 0);
            @endphp
            <div style="border:1px solid #e7eaf0;border-radius:10px;padding:13px;">
                <div style="font-size:10px;font-weight:700;color:#8a93a3;">
                    {{ !empty($cycleHistory['cycle']['start_date']) ? \Carbon\Carbon::parse($cycleHistory['cycle']['start_date'])->format('d M Y') : '—' }}
                    →
                    {{ !empty($cycleHistory['cycle']['end_date']) ? \Carbon\Carbon::parse($cycleHistory['cycle']['end_date'])->format('d M Y') : '—' }}
                </div>
                <div style="
                    margin-top:8px;
                    font-size:16px;
                    font-weight:700;
                    color:{{ $cycleContribution >= 0 ? '#15803d' : '#dc2626' }};
                ">
                    {{ $cycleContribution >= 0 ? '+' : '-' }}K{{ number_format(abs($cycleContribution), 0) }}
                </div>
                <div style="margin-top:4px;font-size:10px;color:#8a93a3;">
                    Net contribution
                </div>
            </div>
        @endforeach
    </div>

    <div style="position:relative;width:100%;height:380px;">
        <canvas id="nationalContributionChart"></canvas>
    </div>
</div>


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
   NATIONAL TABLE TOGGLE
   ============================================================ */

function toggleNationalOffice(id)
{
    const element =
        document.getElementById(id);

    const arrow =
        document.getElementById(
            id + '_arrow'
        );

    if (!element) {

        console.warn(
            'Element not found:',
            id
        );

        return;
    }

    const isHidden =
        element.style.display === 'none' ||
        element.style.display === '';

    if (isHidden) {

        element.style.display =
            element.tagName === 'TR'
                ? 'table-row'
                : 'block';

    } else {

        element.style.display = 'none';
    }

    if (arrow) {

        arrow.style.transform =
            isHidden
                ? 'rotate(90deg)'
                : 'rotate(0deg)';
    }
}


/* ============================================================
   NATIONAL CASH BALANCES
   ============================================================ */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        console.log(
            'Starting national cash balance loading...'
        );

        const balancesUrl =
            '{{ route('cash_health.national.balances') }}';

        const officeBalanceUrl =
            '{{ url('cash_health/national/balance') }}';


        /* ---------------------------------------------------------
           NUMBER FORMAT
           --------------------------------------------------------- */

        function formatMoney(amount)
        {
            return 'K ' + Number(amount).toLocaleString(
                'en-US',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );
        }


        /* ---------------------------------------------------------
           MARK OFFICE UNAVAILABLE
           --------------------------------------------------------- */

        function markOfficeUnavailable(officeId)
        {
            const element =
                document.querySelector(
                    '.cash-balance[data-office-id="' +
                    officeId +
                    '"]'
                );

            if (element) {

                element.innerHTML =
                    '<span style="color:#999;">' +
                    'Unavailable' +
                    '</span>';
            }
        }


        /* ---------------------------------------------------------
           LOAD OFFICE BALANCE
           --------------------------------------------------------- */

        function loadOfficeBalance(
            office,
            districtTotals,
            provinceTotals
        ) {

            return fetch(
                officeBalanceUrl +
                '/' +
                office.office_id
            )

            .then(function (response) {

                return response.json()

                    .then(function (data) {

                        if (!response.ok) {

                            throw new Error(
                                data.message ||
                                'HTTP ' +
                                response.status
                            );
                        }

                        return data;
                    });
            })

            .then(function (data) {

                console.log(
                    'Office balance:',
                    office.office_id,
                    data
                );


                if (
                    !data.success ||
                    data.balance === null ||
                    data.balance === undefined
                ) {

                    markOfficeUnavailable(
                        office.office_id
                    );

                    return null;
                }


                const amount =
                    Number(data.balance);


                if (!Number.isFinite(amount)) {

                    markOfficeUnavailable(
                        office.office_id
                    );

                    return null;
                }


                /* -------------------------------------------------
                   OFFICE
                   ------------------------------------------------- */

                const officeElement =
                    document.querySelector(
                        '.cash-balance[data-office-id="' +
                        office.office_id +
                        '"]'
                    );


                if (officeElement) {

                    officeElement.innerHTML =
                        formatMoney(amount);
                }


                /* -------------------------------------------------
                   DISTRICT
                   ------------------------------------------------- */

                const districtId =
                    String(
                        office.district_id
                    );


                districtTotals[districtId] =
                    (
                        districtTotals[districtId] ||
                        0
                    ) + amount;


                /* -------------------------------------------------
                   PROVINCE
                   ------------------------------------------------- */

                const provinceId =
                    String(
                        office.province_id
                    );


                provinceTotals[provinceId] =
                    (
                        provinceTotals[provinceId] ||
                        0
                    ) + amount;


                return amount;
            })

            .catch(function (error) {

                console.error(
                    'Office balance failed:',
                    office.office_id,
                    error
                );


                markOfficeUnavailable(
                    office.office_id
                );


                return null;
            });
        }


        /* ---------------------------------------------------------
           UPDATE DISTRICT TOTALS
           --------------------------------------------------------- */

        function updateDistrictTotals(
            districtTotals
        ) {

            document
                .querySelectorAll(
                    '.district-cash-balance'
                )
                .forEach(function (element) {

                    const districtId =
                        String(
                            element.dataset.districtId
                        );


                    const total =
                        districtTotals[districtId] || 0;


                    element.innerHTML =
                        formatMoney(total);
                });
        }


        /* ---------------------------------------------------------
           UPDATE PROVINCE TOTALS
           --------------------------------------------------------- */

        function updateProvinceTotals(
            provinceTotals
        ) {

            document
                .querySelectorAll(
                    '.province-cash-balance'
                )
                .forEach(function (element) {

                    const provinceId =
                        String(
                            element.dataset.provinceId
                        );


                    const total =
                        provinceTotals[provinceId] || 0;


                    element.innerHTML =
                        formatMoney(total);
                });
        }


        /* ---------------------------------------------------------
           LOAD OFFICE LIST
           --------------------------------------------------------- */

        fetch(balancesUrl)

            .then(function (response) {

                return response.json()

                    .then(function (data) {

                        if (!response.ok) {

                            throw new Error(
                                data.message ||
                                'HTTP ' +
                                response.status
                            );
                        }

                        return data;
                    });
            })


            .then(function (data) {

                console.log(
                    'Office list response:',
                    data
                );


                if (!data.success) {

                    throw new Error(
                        data.message ||
                        'Unable to load offices.'
                    );
                }


                const offices =
                    data.offices || [];


                console.log(
                    'Offices to process:',
                    offices.length
                );


                /* -------------------------------------------------
                   TOTALS
                   ------------------------------------------------- */

                const districtTotals = {};

                const provinceTotals = {};

                let nationalTotal = 0;


                /* -------------------------------------------------
                   PROCESS OFFICES ONE AT A TIME
                   ------------------------------------------------- */

                let chain =
                    Promise.resolve();


                offices.forEach(
                    function (office) {

                        chain =
                            chain

                                .then(function () {

                                    return loadOfficeBalance(
                                        office,
                                        districtTotals,
                                        provinceTotals
                                    );
                                })

                                .then(function (amount) {

                                    if (
                                        amount !== null &&
                                        amount !== undefined
                                    ) {

                                        nationalTotal +=
                                            amount;
                                    }


                                    /* ---------------------------------
                                       UPDATE DISTRICT TOTALS
                                       --------------------------------- */

                                    updateDistrictTotals(
                                        districtTotals
                                    );


                                    /* ---------------------------------
                                       UPDATE PROVINCE TOTALS
                                       --------------------------------- */

                                    updateProvinceTotals(
                                        provinceTotals
                                    );


                                    /* ---------------------------------
                                       UPDATE NATIONAL TOTAL
                                       --------------------------------- */

                                    const nationalElement =
                                        document.getElementById(
                                            'nationalCashBalance'
                                        );


                                    if (nationalElement) {

                                        nationalElement.innerHTML =
                                            formatMoney(
                                                nationalTotal
                                            );
                                    }
                                });
                    }
                );


                return chain;
            })


            .then(function () {

                console.log(
                    'National cash balance loading complete.'
                );
            })


            .catch(function (error) {

                console.error(
                    'National cash balance error:',
                    error
                );


                /* ---------------------------------------------
                   NATIONAL BALANCE
                   --------------------------------------------- */

                const nationalElement =
                    document.getElementById(
                        'nationalCashBalance'
                    );


                if (nationalElement) {

                    nationalElement.innerHTML =
                        '<span style="color:#999;">' +
                        'Unavailable' +
                        '</span>';
                }
            });

    }
);


/* ============================================================
   NATIONAL CONTRIBUTION HISTORY
   ============================================================ */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const contributionHistory =
            @json($contributionHistory);

        const canvas =
            document.getElementById(
                'nationalContributionChart'
            );

        if (
            canvas &&
            Array.isArray(contributionHistory) &&
            contributionHistory.length > 0
        ) {

            const ordered =
                contributionHistory
                    .slice()
                    .reverse();

            const labels =
                ordered.map(function (row) {

                    const start =
                        row.cycle?.start_date;

                    if (!start) {
                        return '';
                    }

                    return new Date(
                        start + 'T00:00:00'
                    ).toLocaleDateString(
                        'en-GB',
                        {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }
                    );
                });

            const values =
                ordered.map(function (row) {

                    return Number(
                        row.contribution || 0
                    );
                });

            new Chart(
                canvas.getContext('2d'),
                {
                    type: 'line',

                    data: {
                        labels: labels,

                        datasets: [{
                            label: 'National Net Contribution',

                            data: values,

                            borderWidth: 2,

                            pointRadius: 3,

                            pointHoverRadius: 6,

                            tension: 0.3,

                            fill: false
                        }]
                    },

                    options: {
                        responsive: true,

                        maintainAspectRatio: false,

                        interaction: {
                            mode: 'nearest',
                            intersect: true
                        },

                        plugins: {

                            legend: {
                                display: false
                            },

                            tooltip: {

                                callbacks: {

                                    label:
                                        function (context) {

                                            const value =
                                                Number(
                                                    context.parsed.y || 0
                                                );

                                            return (
                                                'Contribution: K' +
                                                value.toLocaleString(
                                                    'en-ZM',
                                                    {
                                                        minimumFractionDigits: 0,
                                                        maximumFractionDigits: 0
                                                    }
                                                )
                                            );
                                        }
                                }
                            }
                        },

                        scales: {

                            x: {

                                grid: {
                                    display: false
                                },

                                ticks: {

                                    font: {
                                        size: 10
                                    },

                                    maxRotation: 0,

                                    autoSkip: true,

                                    maxTicksLimit: 13
                                }
                            },

                            y: {

                                beginAtZero: false,

                                ticks: {

                                    font: {
                                        size: 10
                                    },

                                    callback:
                                        function (value) {

                                            return (
                                                'K' +
                                                Number(value)
                                                    .toLocaleString(
                                                        'en-ZM',
                                                        {
                                                            maximumFractionDigits: 0
                                                        }
                                                    )
                                            );
                                        }
                                }
                            }
                        }
                    }
                }
            );
        }


        /* ========================================================
           CONTRIBUTORS BY PERIOD
           ======================================================== */

        const contributors =
            @json($contributors);

        const periodSelect =
            document.getElementById(
                'contributorPeriod'
            );

        const contributorsContent =
            document.getElementById(
                'contributorsContent'
            );

        function renderContributors(period)
        {
            if (!contributorsContent) {
                return;
            }

            const data =
                contributors[period] || {
                    positive: [],
                    negative: []
                };

            function renderList(items, positive)
            {
                if (!Array.isArray(items) || items.length === 0) {

                    return `
                        <div style="
                            padding:18px;
                            font-size:12px;
                            color:#8a93a3;
                        ">
                            No ${positive ? 'positive' : 'negative'} contributors for this period.
                        </div>
                    `;
                }

                return items.map(function (item) {

                    const amount =
                        Math.abs(
                            Number(item.contribution || 0)
                        );

                    const locationParts = [];

                    if (item.province_name) {
                        locationParts.push(
                            item.province_name
                        );
                    }

                    if (item.district_name) {
                        locationParts.push(
                            item.district_name
                        );
                    }

                    return `
                        <div style="
                            display:flex;
                            justify-content:space-between;
                            align-items:flex-start;
                            gap:15px;
                            padding:12px 18px;
                            border-bottom:1px solid #f0f2f5;
                        ">
                            <div>
                                <div style="
                                    font-size:12px;
                                    font-weight:700;
                                    color:#343b48;
                                ">
                                    ${escapeHtml(item.name || 'Unknown')}
                                </div>

                                <div style="
                                    margin-top:3px;
                                    font-size:10px;
                                    color:#8a93a3;
                                ">
                                    ${escapeHtml(
                                        item.type
                                            ? item.type.charAt(0).toUpperCase() + item.type.slice(1)
                                            : 'Unit'
                                    )}
                                    ${
                                        locationParts.length
                                            ? ' · ' + escapeHtml(locationParts.join(' · '))
                                            : ''
                                    }
                                </div>
                            </div>

                            <strong style="
                                color:${positive ? '#15803d' : '#dc2626'};
                                font-size:12px;
                                white-space:nowrap;
                            ">
                                ${positive ? '+' : '-'}K${amount.toLocaleString('en-ZM', {
                                    maximumFractionDigits: 0
                                })}
                            </strong>
                        </div>
                    `;

                }).join('');
            }

            contributorsContent.innerHTML = `
                <div style="
                    display:grid;
                    grid-template-columns:1fr 1fr;
                    gap:18px;
                ">
                    <div style="
                        border:1px solid #e7eaf0;
                        border-radius:10px;
                        overflow:hidden;
                    ">
                        <div style="
                            padding:15px 18px;
                            background:#f8fafc;
                            border-bottom:1px solid #edf0f3;
                        ">
                            <div style="font-size:12px;font-weight:700;color:#202633;">
                                Positive Contributors
                            </div>
                            <div style="margin-top:4px;font-size:11px;color:#697386;">
                                Units with positive net contribution.
                            </div>
                        </div>

                        ${renderList(data.positive, true)}
                    </div>

                    <div style="
                        border:1px solid #e7eaf0;
                        border-radius:10px;
                        overflow:hidden;
                    ">
                        <div style="
                            padding:15px 18px;
                            background:#f8fafc;
                            border-bottom:1px solid #edf0f3;
                        ">
                            <div style="font-size:12px;font-weight:700;color:#202633;">
                                Negative Contributors
                            </div>
                            <div style="margin-top:4px;font-size:11px;color:#697386;">
                                Units with negative net contribution.
                            </div>
                        </div>

                        ${renderList(data.negative, false)}
                    </div>
                </div>
            `;
        }

        function escapeHtml(value)
        {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        if (periodSelect) {

            periodSelect.addEventListener(
                'change',
                function () {
                    renderContributors(
                        periodSelect.value
                    );
                }
            );

            renderContributors(
                periodSelect.value
            );
        }

    }
);

</script>

@endsection


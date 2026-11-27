
@extends('layouts.master')

@section('title')
    Performance Leaderboard
@endsection

@section('content')

@php
    $metricLabels = [
        'pdua' => 'PDUA',
        'efficiency' => 'Efficiency',
        'collections' => 'Collections'
    ];

    $selectedMetric = $metricLabels[$metric] ?? 'PDUA';

    $formatPercentage = function ($value) {
        return is_numeric($value)
            ? number_format((float)$value * 100, 2) . '%'
            : 'N/A';
    };

    $formatAmount = function ($value) {
        return is_numeric($value)
            ? number_format((float)$value, 2)
            : 'N/A';
    };

    $getRankClass = function ($rank) {
        if ($rank == 1) return 'rank-first';
        if ($rank == 2) return 'rank-second';
        if ($rank == 3) return 'rank-third';

        return '';
    };
@endphp

<section class="content-header">
    <h1>
        <i class="fa fa-trophy"></i>
        Performance Leaderboard
        <small>
            Comparing province, branch and consultant performance
        </small>
    </h1>
</section>

<section class="content">

    {{-- FILTERS --}}

    <div class="box box-primary">

        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-filter"></i> Leaderboard Filters
            </h3>
        </div>

        <div class="box-body">

            <form method="GET" action="{{ route('performance.leaderboard') }}">

                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input
                                type="date"
                                name="start_date"
                                class="form-control"
                                value="{{ $start_date }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input
                                type="date"
                                name="end_date"
                                class="form-control"
                                value="{{ $end_date }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Rank By</label>

                            <select name="metric" class="form-control">

                                <option value="pdua"
                                    {{ $metric == 'pdua' ? 'selected' : '' }}>
                                    PDUA
                                </option>

                                <option value="efficiency"
                                    {{ $metric == 'efficiency' ? 'selected' : '' }}>
                                    Efficiency
                                </option>

                                <option value="collections"
                                    {{ $metric == 'collections' ? 'selected' : '' }}>
                                    Collections
                                </option>

                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" style="margin-top:25px;">

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-refresh"></i>
                                Apply Filters
                            </button>

                        </div>
                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- RANKING INFORMATION --}}

    <div class="alert alert-info">

        <i class="fa fa-info-circle"></i>

        Currently ranking by:

        <strong>{{ $selectedMetric }}</strong>

        <br>

        The highest performing province, branch or consultant
        appears first in each leaderboard.

        Period: <strong>{{ $start_date }}</strong>
        to <strong>{{ $end_date }}</strong>

    </div>


    {{-- ================================================= --}}
    {{-- PROVINCE LEADERBOARD --}}
    {{-- ================================================= --}}

    <div class="box box-success">

        <div class="box-header with-border">

            <h3 class="box-title">
                <i class="fa fa-map-marker"></i>
                Provincial Leaderboard
            </h3>

            <div class="box-tools pull-right">
                <span class="label label-success">
                    {{ count($provinceLeaderboard) }} Provinces
                </span>
            </div>

        </div>

        <div class="box-body table-responsive no-padding">

            <table class="table table-bordered table-hover">

                <thead style="background:#f4f4f4;">
                    <tr>
                        <th width="60">Rank</th>
                        <th>Province</th>
                        <th>Efficiency</th>
                        <th>PDUA</th>
                        <th>Total Collections</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($provinceLeaderboard as $index => $province)

                        <tr>

                            <td class="text-center {{ $getRankClass($index + 1) }}">
                                <strong>#{{ $index + 1 }}</strong>
                            </td>

                            <td>
                                <strong>
                                    {{ $province['province'] ?? 'N/A' }}
                                </strong>
                            </td>

                            <td>
                                {{ $formatPercentage(data_get($province, 'efficiency')) }}
                            </td>

                            <td>
                                <span class="label label-info">
                                    {{ $formatPercentage(data_get($province, 'pdua')) }}
                                </span>
                            </td>

                            <td>
                                <strong>
                                    {{ $formatAmount(data_get($province, 'total_collected')) }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No provincial records found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- BRANCH LEADERBOARD --}}
    {{-- ================================================= --}}

    <div class="box box-warning">

        <div class="box-header with-border">

            <h3 class="box-title">
                <i class="fa fa-building"></i>
                Branch Leaderboard
            </h3>

            <div class="box-tools pull-right">
                <span class="label label-warning">
                    {{ count($branchLeaderboard) }} Branches
                </span>
            </div>

        </div>

        <div class="box-body table-responsive no-padding">

            <table class="table table-bordered table-hover">

                <thead style="background:#f4f4f4;">
                    <tr>
                        <th width="60">Rank</th>
                        <th>Branch</th>
                        <th>Efficiency</th>
                        <th>PDUA</th>
                        <th>Total Collections</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($branchLeaderboard as $index => $branch)

                        <tr>

                            <td class="text-center {{ $getRankClass($index + 1) }}">
                                <strong>#{{ $index + 1 }}</strong>
                            </td>

                            <td>
                                <strong>
                                    {{ $branch['office'] ?? 'N/A' }}
                                </strong>
                            </td>

                            <td>
                                {{ $formatPercentage(data_get($branch, 'efficiency')) }}
                            </td>

                            <td>
                                <span class="label label-info">
                                    {{ $formatPercentage(data_get($branch, 'pdua')) }}
                                </span>
                            </td>

                            <td>
                                <strong>
                                    {{ $formatAmount(data_get($branch, 'total_collected')) }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No branch records found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- CONSULTANT LEADERBOARD --}}
    {{-- ================================================= --}}

    <div class="box box-primary">

        <div class="box-header with-border">

            <h3 class="box-title">
                <i class="fa fa-users"></i>
                Loan Consultant Leaderboard
            </h3>

            <div class="box-tools pull-right">
                <span class="label label-primary">
                    {{ count($consultantLeaderboard) }} Consultants
                </span>
            </div>

        </div>

        <div class="box-body table-responsive no-padding">

            <table class="table table-bordered table-hover">

                <thead style="background:#f4f4f4;">
                    <tr>
                        <th width="60">Rank</th>
                        <th>Consultant</th>
                        <th>Branch</th>
                        <th>Efficiency</th>
                        <th>PDUA</th>
                        <th>Total Collections</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($consultantLeaderboard as $index => $consultant)

                        <tr>

                            <td class="text-center {{ $getRankClass($index + 1) }}">
                                <strong>#{{ $index + 1 }}</strong>
                            </td>

                            <td>
                                <strong>
                                    {{ $consultant['name'] ?? 'N/A' }}
                                </strong>
                            </td>

                            <td>
                                {{ $consultant['office_name'] ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $formatPercentage(data_get($consultant, 'efficiency')) }}
                            </td>

                            <td>
                                <span class="label label-info">
                                    {{ $formatPercentage(data_get($consultant, 'pdua')) }}
                                </span>
                            </td>

                            <td>
                                <strong>
                                    {{ $formatAmount(data_get($consultant, 'total_collected')) }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No consultant records found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</section>

@endsection


@section('footer-scripts')

<style>

    .table > thead > tr > th,
    .table > tbody > tr > td {
        vertical-align: middle !important;
        padding: 10px;
        font-size: 13px;
    }

    .rank-first {
        background: #dff0d8 !important;
        color: #3c763d;
    }

    .rank-second {
        background: #f5f5f5 !important;
        color: #555;
    }

    .rank-third {
        background: #fcf8e3 !important;
        color: #8a6d3b;
    }

    .box-title {
        font-weight: 600;
    }

    .label {
        font-size: 11px;
        padding: 5px 8px;
    }

</style>

@endsection

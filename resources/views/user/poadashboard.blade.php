@extends('layouts.master')

@section('title')
    Performance Operations Administrator Dashboard
@endsection

@section('content')
<section class="content-header">
    <h1>
    
        <small>Province, Branch and Consultant Performance Breakdown from {{$start_date}} to {{$end_date}} </small>
    </h1>
</section>

<section class="content">

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-filter"></i> Filter Period
            </h3>
        </div>
        <div class="box-body">
            <form method="GET" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $start_date }}" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $end_date }}" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


        <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-check-circle"></i>Targets met today
            </h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <thead style="background: #f4f4f4;">
                    <tr>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Target Type</th>
                        <th>Cycle Start</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($targets_met as $target)
                        <tr>
                            <td><strong>{{ $target->user_name }}</strong></td>
                            <td>{{ $target->office_name }}</td>
                           <td>
@php
    $level = $target->target_level;
    $label = '';
    $color = '';

    if($level == 40000){
        $label = 'Single Target';
        $color = 'green';
    } elseif($level == 50000){
        $label = '50 Band';
        $color = 'blue';
    } elseif($level == 80000){
        $label = 'Double Target';
        $color = 'orange';
    } elseif($level == 120000){
        $label = 'Triple Target';
        $color = 'red';
    } else {
        $label = number_format($level);
        $color = 'black';
    }
@endphp

<span style="color: {{$color}}; font-weight:bold;">
    {{$label}}
</span>
</td>
                            <td>{{ $target->cycle_start }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No targets today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-bar-chart"></i> Provincial Performance
            </h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <thead style="background: #f4f4f4;">
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Province</th>
                        <th>Cycle Opening Uncollected</th>
                        <th>Total Cycle Collected</th>
                        <th>Still Uncollected</th>
                        <th>Given Out</th>
                        <th>PDUA%</th>
                        <th>Consultants</th>
                        <th>Targets Met</th>
                        <th>Efficiency</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provinces as $province)
                        @php
                            $provinceId = $province['province_id'] ?? '';
                            $provinceEfficiency = (float) data_get($province, 'efficiency', 0) * 100;
                            $provinceEfficiencyClass = $provinceEfficiency >= 80 ? 'label-success' : ($provinceEfficiency >= 50 ? 'label-warning' : 'label-danger');
                        @endphp

                        <tr class="province-row bg-light-blue-gradient" data-id="{{ $provinceId }}">
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-primary toggle-btn" title="Expand branches">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                            <td><strong>{{ $province['province'] ?? '0' }}</strong></td>
                            <td>{{ number_format((float) data_get($province, 'total_uncollected', 0), 2) }}</td>
                            <td>{{ number_format((float) data_get($province, 'total_collected', 0), 2) }}</td>
                            <td>{{ number_format((float) data_get($province, 'still_uncollected', 0), 2) }}</td>
                            <td>{{ number_format((float) data_get($province, 'given_out', 0), 2) }}</td>
                            <td>
                                <span class="label label-info">
                                    {{ number_format((float) data_get($province, 'pdua', 0) * 100, 2) }}%
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-blue">
                                    {{ data_get($province, 'consultants_count', 0) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-green">
                                    {{ data_get($province, 'targets_met_count', 0) }}
                                </span>
                            </td>
                            <td>
                                <span class="label {{ $provinceEfficiencyClass }}">
                                    {{ number_format($provinceEfficiency, 2) }}%
                                </span>
                            </td>
                        </tr>

                        <tr class="branch-container-row" id="branches-{{ $provinceId }}" style="display:none; background:#fcfcfc;">
                            <td colspan="10" style="padding: 0;">
                                <div style="padding: 10px 15px 15px 15px; border-top: 1px solid #eee;">
                                    <div class="callout callout-info" style="margin-bottom: 10px;">
                                        <h4 style="margin-top: 0; margin-bottom: 5px;">
                                            <i class="fa fa-sitemap"></i> Branches under {{ $province['province'] ?? 'Province' }}
                                        </h4>
                                        <p style="margin: 0;">Click the yellow button to load loan consultants.</p>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead style="background: #fafafa;">
                                                <tr>
                                                    <th style="width: 50px;"></th>
                                                    <th>Branch</th>
                                                    <th>Cycle Opening Uncollected</th>
                                                    <th>Total Cycle Collected</th>
                                                    <th>Still Uncollected</th>
                                                    <th>Given Out</th>
                                                    <th>PDUA%</th>
                                                    <th>Consultants</th>
                                                    <th>Targets Met</th>
                                                    <th>Efficiency</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $hasBranches = false; @endphp

                                                @foreach($branches as $branch)
                                                    @if(isset($branch['office_id']) && ($branch['province_id'] ?? null) == $provinceId)
                                                        @php
                                                            $hasBranches = true;
                                                            $officeId = $branch['office_id'];
                                                            $branchEfficiency = (float) data_get($branch, 'efficiency', 0) * 100;
                                                            $branchEfficiencyClass = $branchEfficiency >= 80 ? 'label-success' : ($branchEfficiency >= 50 ? 'label-warning' : 'label-danger');
                                                        @endphp

                                                        <tr class="branch-row" data-id="{{ $officeId }}">
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-xs btn-warning toggle-consultants" title="Expand consultants">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </td>
                                                            <td><strong>{{ $branch['office'] ?? 'N/A' }}</strong></td>
                                                            <td>{{ number_format((float) data_get($branch, 'total_uncollected', 0), 2) }}</td>
                                                            <td>{{ number_format((float) data_get($branch, 'total_collected', 0), 2) }}</td>
                                                            <td>{{ number_format((float) data_get($branch, 'still_uncollected', 0), 2) }}</td>
                                                            <td>{{ number_format((float) data_get($branch, 'given_out', 0), 2) }}</td>
                                                            <td>
                                                                <span class="label label-info">
                                                                    {{ number_format((float) data_get($branch, 'pdua', 0) * 100, 2) }}%
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-blue">
                                                                    {{ data_get($branch, 'consultants_count', 0) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-green">
                                                                    {{ data_get($branch, 'targets_met_count', 0) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="label {{ $branchEfficiencyClass }}">
                                                                    {{ number_format($branchEfficiency, 2) }}%
                                                                </span>
                                                            </td>
                                                        </tr>

                                                        <tr class="consultant-container" id="consultants-{{ $officeId }}" style="display:none; background:#fffdf7;">
                                                            <td colspan="10">
                                                                <div class="consultants-list">
                                                                    <div class="text-muted">
                                                                        <i class="fa fa-info-circle"></i> Consultants will load here.
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach

                                                @if(!$hasBranches)
                                                    <tr>
                                                        <td colspan="10" class="text-center text-muted">
                                                            No branch records found for this province.
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No province records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('footer-scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let row = this.closest('tr');
            let id = row.dataset.id;
            let container = document.getElementById('branches-' + id);
            let icon = this.querySelector('i');

            if (!container) return;

            const isHidden = container.style.display === 'none';

            if (isHidden) {
                container.style.display = 'table-row';
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            } else {
                container.style.display = 'none';
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        });
    });

    document.querySelectorAll('.toggle-consultants').forEach(btn => {
        btn.addEventListener('click', async function() {
            let row = this.closest('tr');
            let officeId = row.dataset.id;
            let container = document.getElementById('consultants-' + officeId);
            let icon = this.querySelector('i');

            if (!container) return;

            const isHidden = container.style.display === 'none';

            if (isHidden) {
                let div = container.querySelector('.consultants-list');

                if (div.innerHTML.trim() === '' || div.innerHTML.includes('Consultants will load here')) {
                    div.innerHTML = `
                        <div class="text-center text-primary" style="padding: 15px;">
                            <i class="fa fa-spinner fa-spin"></i> Loading consultants...
                        </div>
                    `;

                    try {
                        const startDate = document.querySelector('input[name="start_date"]')?.value || '';
                        const endDate = document.querySelector('input[name="end_date"]')?.value || '';

                        const endpoint = `https://lms2backend.whencefinancesystem.com/consultants-performance-by-office?office_id=${officeId}&start_date=${startDate}&end_date=${endDate}`;

                        let res = await fetch(endpoint);
                        let data = await res.json();

                        let html = `
                            <div class="box box-warning" style="margin-bottom:0;">
                                <div class="box-header with-border">
                                    <h3 class="box-title">
                                        <i class="fa fa-users"></i> Loan Consultants
                                    </h3>
                                </div>
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-bordered table-striped">
                                        <thead>
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
                        `;

                   if (data.data && data.data.length > 0) {

    const bands = [
        {
            label: 'Below 10,000',
            min: 0,
            max: 9999.99
        },
        {
            label: '10,000',
            min: 10000,
            max: 19999.99
        },
        {
            label: '20,000',
            min: 20000,
            max: 29999.99
        },
        {
            label: '30,000',
            min: 30000,
            max: 39999.99
        },
        {
            label: '40,000+',
            min: 40000,
            max: Number.MAX_VALUE
        }
    ];

    bands.forEach(band => {

        const consultants = data.data.filter(c => {
            const givenOut = parseFloat(c.given_out ?? 0);

            return (
                givenOut >= band.min &&
                givenOut <= band.max
            );
        });

        if (consultants.length === 0) {
            return;
        }

        html += `
            <tr style="background:#f4f4f4;">
                <td colspan="11">
                    <strong>${band.label}</strong>
                    <span class="badge bg-blue">
                        ${consultants.length}
                    </span>
                </td>
            </tr>
        `;

        consultants.forEach(c => {

            const pdua = ((parseFloat(c.pdua ?? 0)) * 100).toFixed(2);

            const totalUncollected = parseFloat(
                c.total_uncollected ?? 0
            ).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const totalCollected = parseFloat(
                c.total_collected ?? 0
            ).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const stillUncollected = parseFloat(
                c.still_uncollected ?? 0
            ).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const givenOut = parseFloat(
                c.given_out ?? 0
            ).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const carryOver = parseFloat(
                c.carry_over ?? 0
            ).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const cycle_end = c.cycle_end_on;
            const id = c.user_id;

            const targetMetCurrent = Number(
                c.target_met_current ?? 0
            );

            const targetMetBadge =
                targetMetCurrent === 1
                    ? `<span class="label label-success">Met</span>`
                    : `<span class="label label-default">Not Met</span>`;

            const historyArray = Array.isArray(
                c.target_history
            )
                ? c.target_history
                : [];

            let historyCircles =
                `<div style="white-space: nowrap;">`;

            historyArray.forEach(v => {

                let bgColor = '#d2d6de';

                if (Number(v) === 1) {
                    bgColor = '#00a65a';
                } else if (Number(v) === 0) {
                    bgColor = '#dd4b39';
                }

                historyCircles += `
                    <span style="
                        display:inline-block;
                        width:12px;
                        height:12px;
                        border-radius:50%;
                        background:${bgColor};
                        margin-right:4px;
                        vertical-align:middle;
                    "></span>
                `;
            });

            if (historyArray.length < 5) {
                for (
                    let i = historyArray.length;
                    i < 5;
                    i++
                ) {
                    historyCircles += `
                        <span style="
                            display:inline-block;
                            width:12px;
                            height:12px;
                            border-radius:50%;
                            background:#d2d6de;
                            margin-right:4px;
                            vertical-align:middle;
                        "></span>
                    `;
                }
            }

            historyCircles += `</div>`;

            html += `
                <tr>
                    <td>
                        <strong>${c.name ?? ''}</strong>
                    </td>
                    <td>${totalUncollected}</td>
                    <td>${totalCollected}</td>
                    <td>${stillUncollected}</td>
                    <td>${givenOut}</td>
                    <td>${carryOver}</td>
                    <td>
                        <span class="label label-info">
                            ${pdua}%
                        </span>
                    </td>
                    <td>${targetMetBadge}</td>
                    <td>${historyCircles}</td>
                    <td>${cycle_end}</td>
                    <td>
                        <a href="/user/${id}/staff_info"
                           class="text-primary">
                            View
                        </a>
                    </td>
                </tr>
            `;
        });

    });

} else {

    html += `
        <tr>
            <td colspan="11"
                class="text-center text-muted">
                No consultant records found.
            </td>
        </tr>
    `;
}

                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;

                        div.innerHTML = html;

                    } catch (error) {
                        div.innerHTML = `
                            <div class="alert alert-danger" style="margin-bottom:0;">
                                <i class="fa fa-warning"></i> Failed to load consultant data.
                            </div>
                        `;
                        console.error(error);
                    }
                }

                container.style.display = 'table-row';
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');

            } else {
                container.style.display = 'none';
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        });
    });

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
@endsection
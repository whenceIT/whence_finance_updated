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
        $fullPaymentsTotal = totalAmount($branch['transactions']['full_payments'] ?? []);
        $reloansTotal = totalAmount($branch['transactions']['reloans'] ?? []);
        $partPaymentsTotal = totalAmount($branch['transactions']['part_payments'] ?? []);
        $newLoansTotal = totalAmount($branch['transactions']['new_loans'] ?? []);
        $expensesTotal = totalAmount($branch['transactions']['expenses'] ?? []);
        $advancesTotal = totalAmount($branch['transactions']['advances'] ?? []);
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
            <i class="fa fa-building"></i> {{ $branch['office'] ?? $branch['name'] }} Performance
        </h3>
    </div>
    <div class="box-body">
        <!-- KPI CARDS -->
     <div class="row" style="margin-bottom:20px;">

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

</section>

@include('components.policy-of-the-day')

@endsection

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
</script>
@endsection
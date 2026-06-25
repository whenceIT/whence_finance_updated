@extends('layouts.master')

@section('title')
    Provincial Manager Dashboard
@endsection

@section('content')

@php
function totalAmount($items) {
    return collect($items)->sum('amount');
}
@endphp

<div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:0;">
    <div style="padding:14px 18px; border-bottom:1px solid #f1f1f1; background:#fff;">
        <h3 style="margin:0; font-size:18px; font-weight:600;">Daily Province Branches Performance Summary</h3>
        <small style="color:#8b929a;">Click a branch to view category totals, then click a category to view transactions</small>
    </div>

    <div class="table-responsive">
        <table class="table mb-0" style="margin:0;">
            <thead>
                <tr style="background:#f8f9fb; color:#6c757d; font-size:12px; text-transform:uppercase;">
                    <th style="width:40px;"></th>
                    <th>Branch</th>
                    <th>Collections</th>
                    <th>Loans</th>
                    <th>Cash C/F</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($province_data['branches'] as $i => $b)
                    @php
                        $fullPaymentsTotal = totalAmount($b['transactions']['full_payments'] ?? []);
                        $reloansTotal = totalAmount($b['transactions']['reloans'] ?? []);
                        $partPaymentsTotal = totalAmount($b['transactions']['part_payments'] ?? []);
                        $newLoansTotal = totalAmount($b['transactions']['new_loans'] ?? []);
                        $expensesTotal = totalAmount($b['transactions']['expenses'] ?? []);
                        $advancesTotal = totalAmount($b['transactions']['advances'] ?? []);

                        $grandTotal = $fullPaymentsTotal + $reloansTotal + $partPaymentsTotal + $newLoansTotal + $expensesTotal + $advancesTotal;
                    @endphp

                    <tr class="main-row" data-index="{{ $i }}" style="cursor:pointer; transition:0.2s;">
                        <td style="text-align:center; vertical-align:middle;">
                          <i class="fa fa-plus" aria-hidden="true"></i>
                        </td>

                        <td style="font-weight:600; color:#212529; vertical-align:middle;">
                            {{ $b['name'] ?? '' }}
                        </td>

                        <td style="font-weight:700; color:#28a745; vertical-align:middle;">
                            K{{ number_format($b['collections'] ?? 0, 2) }}
                        </td>

                        <td style="font-weight:700; color:#007bff; vertical-align:middle;">
                            K{{ number_format($b['new_loans'] ?? 0, 2) }}
                        </td>

                        <td style="vertical-align:middle;">
                            -
                            <!-- K{{ number_format($b['cash_balance'] ?? 0, 2) }} -->
                        </td>

                        <td style="vertical-align:middle; font-weight:600;">
                            K{{ number_format($grandTotal, 2) }}
                        </td>

                    </tr>

                    <tr class="detail-row" id="detail-{{ $i }}" style="display:none; background:#f8fafc;">
                        <td colspan="7" style="padding:0; border:0;">
                            <div style="padding:18px;">
                                <div style="background:#fff; border:1px solid #edf1f5; border-radius:10px; overflow:hidden;">
                                    
                                    <div style="padding:14px 16px; border-bottom:1px solid #f1f3f5; background:#f9fafb;">
                                        <div style="font-size:16px; font-weight:700; color:#212529;">
                                            {{ strtoupper($b['name'] ?? '') }} TRANSACTION SUMMARY
                                        </div>
                                    </div>

                                    <div style="padding:16px;">

                                        <div class="category-row" data-target="cat-{{ $i }}-full-payments"
                                             style="cursor:pointer; background:#f7fbff; border:1px solid #dbeafe; border-radius:8px; padding:12px 14px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                                            <div style="font-weight:600;">Full Payments</div>
                                            <div>
                                                <span style="font-weight:700; margin-right:10px;">K{{ number_format($fullPaymentsTotal, 2) }}</span>
                                                <i class="fas fa-caret-right sub-toggle-icon" style="transition:0.25s;"></i>
                                            </div>
                                        </div>
                                        <div id="cat-{{ $i }}-full-payments" class="sub-detail" style="display:none; margin:-2px 0 12px 0;">
                                            <div style="border:1px solid #edf1f5; border-radius:8px; overflow:hidden;">
                                                <table class="table mb-0" style="margin:0;">
                                                    <thead>
                                                        <tr style="background:#fcfcfd;">
                                                            <th>Name</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($b['transactions']['full_payments'] ?? [] as $txn)
                                                            <tr>
                                                                <td>{{ $txn['name'] ?? '' }}</td>
                                                                <td>{{ $txn['date'] ?? '' }}</td>
                                                                <td>K{{ number_format($txn['amount'] ?? 0, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">No full payments found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="category-row" data-target="cat-{{ $i }}-reloans"
                                             style="cursor:pointer; background:#f7fbff; border:1px solid #dbeafe; border-radius:8px; padding:12px 14px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                                            <div style="font-weight:600;">Reloans</div>
                                            <div>
                                                <span style="font-weight:700; margin-right:10px;">K{{ number_format($reloansTotal, 2) }}</span>
                                                <i class="fas fa-caret-right sub-toggle-icon" style="transition:0.25s;"></i>
                                            </div>
                                        </div>
                                        <div id="cat-{{ $i }}-reloans" class="sub-detail" style="display:none; margin:-2px 0 12px 0;">
                                            <div style="border:1px solid #edf1f5; border-radius:8px; overflow:hidden;">
                                                <table class="table mb-0" style="margin:0;">
                                                    <thead>
                                                        <tr style="background:#fcfcfd;">
                                                            <th>Name</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($b['transactions']['reloans'] ?? [] as $txn)
                                                            <tr>
                                                                <td>{{ $txn['name'] ?? '' }}</td>
                                                                <td>{{ $txn['date'] ?? '' }}</td>
                                                                <td>K{{ number_format($txn['amount'] ?? 0, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">No reloans found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="category-row" data-target="cat-{{ $i }}-part-payments"
                                             style="cursor:pointer; background:#f7fbff; border:1px solid #dbeafe; border-radius:8px; padding:12px 14px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                                            <div style="font-weight:600;">Part Payments</div>
                                            <div>
                                                <span style="font-weight:700; margin-right:10px;">K{{ number_format($partPaymentsTotal, 2) }}</span>
                                                <i class="fas fa-caret-right sub-toggle-icon" style="transition:0.25s;"></i>
                                            </div>
                                        </div>
                                        <div id="cat-{{ $i }}-part-payments" class="sub-detail" style="display:none; margin:-2px 0 12px 0;">
                                            <div style="border:1px solid #edf1f5; border-radius:8px; overflow:hidden;">
                                                <table class="table mb-0" style="margin:0;">
                                                    <thead>
                                                        <tr style="background:#fcfcfd;">
                                                            <th>Name</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($b['transactions']['part_payments'] ?? [] as $txn)
                                                            <tr>
                                                                <td>{{ $txn['name'] ?? '' }}</td>
                                                                <td>{{ $txn['date'] ?? '' }}</td>
                                                                <td>K{{ number_format($txn['amount'] ?? 0, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">No part payments found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="category-row" data-target="cat-{{ $i }}-new-loans"
                                             style="cursor:pointer; background:#f7fbff; border:1px solid #dbeafe; border-radius:8px; padding:12px 14px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                                            <div style="font-weight:600;">New Loans</div>
                                            <div>
                                                <span style="font-weight:700; margin-right:10px;">K{{ number_format($newLoansTotal, 2) }}</span>
                                                <i class="fas fa-caret-right sub-toggle-icon" style="transition:0.25s;"></i>
                                            </div>
                                        </div>
                                        <div id="cat-{{ $i }}-new-loans" class="sub-detail" style="display:none; margin:-2px 0 12px 0;">
                                            <div style="border:1px solid #edf1f5; border-radius:8px; overflow:hidden;">
                                                <table class="table mb-0" style="margin:0;">
                                                    <thead>
                                                        <tr style="background:#fcfcfd;">
                                                            <th>Name</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($b['transactions']['new_loans'] ?? [] as $txn)
                                                            <tr>
                                                                <td>{{ $txn['name'] ?? '' }}</td>
                                                                <td>{{ $txn['date'] ?? '' }}</td>
                                                                <td>K{{ number_format($txn['amount'] ?? 0, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">No new loans found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="category-row" data-target="cat-{{ $i }}-expenses"
                                             style="cursor:pointer; background:#fffdf7; border:1px solid #f6e7b8; border-radius:8px; padding:12px 14px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                                            <div style="font-weight:600;">Expenses</div>
                                            <div>
                                                <span style="font-weight:700; margin-right:10px;">K{{ number_format($expensesTotal, 2) }}</span>
                                                <i class="fas fa-caret-right sub-toggle-icon" style="transition:0.25s;"></i>
                                            </div>
                                        </div>
                                        <div id="cat-{{ $i }}-expenses" class="sub-detail" style="display:none; margin:-2px 0 12px 0;">
                                            <div style="border:1px solid #edf1f5; border-radius:8px; overflow:hidden;">
                                                <table class="table mb-0" style="margin:0;">
                                                    <thead>
                                                        <tr style="background:#fcfcfd;">
                                                            <th>Name</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($b['transactions']['expenses'] ?? [] as $txn)
                                                            <tr>
                                                                <td>{{ $txn['name'] ?? '' }}</td>
                                                                <td>{{ $txn['date'] ?? '' }}</td>
                                                                <td>K{{ number_format($txn['amount'] ?? 0, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">No expenses found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="category-row" data-target="cat-{{ $i }}-advances"
                                             style="cursor:pointer; background:#fff7fb; border:1px solid #f3d9e8; border-radius:8px; padding:12px 14px; margin-bottom:0; display:flex; justify-content:space-between; align-items:center;">
                                            <div style="font-weight:600;">Advances</div>
                                            <div>
                                                <span style="font-weight:700; margin-right:10px;">K{{ number_format($advancesTotal, 2) }}</span>
                                                <i class="fas fa-caret-right sub-toggle-icon" style="transition:0.25s;"></i>
                                            </div>
                                        </div>
                                        <div id="cat-{{ $i }}-advances" class="sub-detail" style="display:none; margin:10px 0 0 0;">
                                            <div style="border:1px solid #edf1f5; border-radius:8px; overflow:hidden;">
                                                <table class="table mb-0" style="margin:0;">
                                                    <thead>
                                                        <tr style="background:#fcfcfd;">
                                                            <th>Name</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($b['transactions']['advances'] ?? [] as $txn)
                                                            <tr>
                                                                <td>{{ $txn['name'] ?? '' }}</td>
                                                                <td>{{ $txn['date'] ?? '' }}</td>
                                                                <td>K{{ number_format($txn['amount'] ?? 0, 2) }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">No advances found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

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

$(function () {
    $('.main-row').on('click', function () {
        const index = $(this).data('index');
        const detailRow = $('#detail-' + index);
        const icon = $(this).find('.toggle-icon');
        const isOpen = detailRow.is(':visible');

        $('.detail-row').not(detailRow).hide();
        $('.main-row').not(this).css('background', '');
        $('.toggle-icon').not(icon).css('transform', 'rotate(0deg)');

        if (isOpen) {
            detailRow.hide();
            $(this).css('background', '');
            icon.css('transform', 'rotate(0deg)');
        } else {
            detailRow.show();
            $(this).css('background', '#f4f8ff');
            icon.css('transform', 'rotate(90deg)');
        }
    });

    $('.category-row').on('click', function (e) {
        e.stopPropagation();

        const target = $('#' + $(this).data('target'));
        const icon = $(this).find('.sub-toggle-icon');
        const isOpen = target.is(':visible');

        if (isOpen) {
            target.hide();
            icon.css('transform', 'rotate(0deg)');
        } else {
            target.show();
            icon.css('transform', 'rotate(90deg)');
        }
    });
});

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
                            data.data.forEach(c => {
                                const pdua = ((parseFloat(c.pdua ?? 0)) * 100).toFixed(2);
                                const totalUncollected = parseFloat(c.total_uncollected ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                const totalCollected = parseFloat(c.total_collected ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                const stillUncollected = parseFloat(c.still_uncollected ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                const givenOut = parseFloat(c.given_out ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                const carryOver = parseFloat(c.carry_over ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                const cycle_end = c.cycle_end_on;
                                const id = c.user_id;

                                const targetMetCurrent = Number(c.target_met_current ?? 0);

                                const targetMetBadge = targetMetCurrent === 1
                                    ? `<span class="label label-success">Met</span>`
                                    : `<span class="label label-default">Not Met</span>`;

                                const historyArray = Array.isArray(c.target_history) ? c.target_history : [];

                                let historyCircles = `<div style="white-space: nowrap;">`;

                                historyArray.forEach(v => {
                                    let bgColor = '#d2d6de'; // grey default
                                    if (Number(v) === 1) {
                                        bgColor = '#00a65a'; // green
                                    } else if (Number(v) === 0) {
                                        bgColor = '#dd4b39'; // red
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
                                    for (let i = historyArray.length; i < 5; i++) {
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
                                        <td><strong>${c.name ?? ''}</strong></td>
                                        <td>${totalUncollected}</td>
                                        <td>${totalCollected}</td>
                                        <td>${stillUncollected}</td>
                                        <td>${givenOut}</td>
                                        <td>${carryOver}</td>
                                        <td><span class="label label-info">${pdua}%</span></td>
                                        <td>${targetMetBadge}</td>
                                        <td>${historyCircles}</td>
                                      
                                         <td>${cycle_end}</td>
                                             
  <td>
    <a href="{{ url('user/${id}/staff_info') }}" class="text-primary">
        View
    </a>
</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No consultant records found.</td>
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
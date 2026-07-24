@extends('layouts.master')

@section('content')
<style>
    :root {
        --wd-primary: #5b4fdb;
        --wd-primary-dark: #4338ca;
        --wd-ink: #1a1d29;
        --wd-muted: #6b7280;
        --wd-border: #eef0f4;
        --wd-bg: #f7f8fb;
        --wd-success: #16a34a;
        --wd-warning: #d97706;
        --wd-danger: #dc2626;
        --wd-info: #2563eb;
    }

    .wd-page { background: var(--wd-bg); }

    /* Hero */
    .wd-hero {
        position: relative;
        overflow: hidden;
        border: none;
        border-radius: 16px;
        background: linear-gradient(120deg, #5b4fdb 0%, #7c3aed 55%, #a855f7 100%);
        color: #fff;
        box-shadow: 0 10px 30px rgba(91, 79, 219, 0.25);
    }
    .wd-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 260px; height: 260px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .wd-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; right: 120px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .wd-hero-body { position: relative; z-index: 1; padding: 28px 32px; }
    .wd-hero-title { margin: 0; font-size: 26px; font-weight: 700; letter-spacing: -0.02em; }
    .wd-hero-meta { margin: 10px 0 0; opacity: 0.92; font-size: 14px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .wd-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;
        background: rgba(255,255,255,0.18); backdrop-filter: blur(4px);
    }
    .wd-chip-success { background: rgba(34,197,94,0.25); }
    .wd-chip-warn { background: rgba(251,191,36,0.28); }

    /* Stat cards */
    .wd-stat-card {
        border: 1px solid var(--wd-border);
        border-radius: 14px;
        background: #fff;
        transition: transform .15s ease, box-shadow .15s ease;
        height: 100%;
    }
    .wd-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(17,24,39,0.06); }
    .wd-stat-body { padding: 20px 22px; display: flex; align-items: flex-start; gap: 14px; }
    .wd-icon {
        flex: 0 0 auto;
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
    }
    .wd-icon svg { width: 20px; height: 20px; }
    .wd-icon-blue { background: #eaf1ff; color: var(--wd-info); }
    .wd-icon-green { background: #e9f9ee; color: var(--wd-success); }
    .wd-icon-amber { background: #fef4e6; color: var(--wd-warning); }
    .wd-icon-red { background: #fdecec; color: var(--wd-danger); }
    .wd-icon-grey { background: #f0f1f4; color: var(--wd-muted); }

    .wd-stat-label { color: var(--wd-muted); font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 600; margin-bottom: 4px; }
    .wd-stat-value { font-size: 24px; font-weight: 700; color: var(--wd-ink); line-height: 1.2; }
    .wd-stat-sub { color: var(--wd-muted); font-size: 12.5px; margin-top: 2px; display: block; }

    /* Section cards */
    .wd-card {
        border: 1px solid var(--wd-border);
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
    }
    .wd-card-header {
        background: #fff;
        border-bottom: 1px solid var(--wd-border);
        padding: 16px 20px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .wd-card-header h5 { margin: 0; font-weight: 700; font-size: 15px; color: var(--wd-ink); }

    /* Distribution */
    .wd-dist-item { text-align: center; padding: 10px 4px; border-radius: 10px; }
    .wd-dist-value { font-size: 22px; font-weight: 700; }
    .wd-dist-label { color: var(--wd-muted); font-size: 12px; margin-top: 2px; }

    /* Table */
    .wd-table thead th {
        font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px;
        color: var(--wd-muted); font-weight: 700; border-top: none;
        border-bottom: 1px solid var(--wd-border);
        background: #fafbfc;
    }
    .wd-table tbody td { vertical-align: middle; font-size: 13.5px; color: var(--wd-ink); }
    .wd-table tbody tr:hover { background: #fafbff; }
    .wd-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px; border-radius: 999px; font-size: 11.5px; font-weight: 700;
    }
    .wd-badge-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .wd-badge-active { background: #e9f9ee; color: var(--wd-success); }
    .wd-badge-active .wd-badge-dot { background: var(--wd-success); }
    .wd-badge-inactive { background: #f1f2f5; color: var(--wd-muted); }
    .wd-badge-inactive .wd-badge-dot { background: #9ca3af; }

    /* Skeleton loading */
    .wd-skel {
        display: inline-block;
        background: linear-gradient(90deg, #eef0f4 25%, #f6f7f9 37%, #eef0f4 63%);
        background-size: 400% 100%;
        animation: wd-shimmer 1.4s ease infinite;
        border-radius: 6px;
    }
    @keyframes wd-shimmer { 0% { background-position: 100% 50%; } 100% { background-position: 0 50%; } }

    /* Empty / error state */
    .wd-empty { padding: 40px 20px; text-align: center; color: var(--wd-muted); }
    .wd-empty svg { width: 40px; height: 40px; color: #cbd0d8; margin-bottom: 10px; }
    .wd-error-banner {
        background: #fdecec; border: 1px solid #f8c9c9; color: #9b1c1c;
        border-radius: 10px; padding: 12px 16px; font-size: 13.5px;
        display: flex; align-items: center; gap: 10px;
    }

    @media (max-width: 767px) {
        .wd-hero-body { padding: 22px 20px; }
        .wd-hero-title { font-size: 21px; }
        .wd-stat-value { font-size: 20px; }
    }
</style>

<div class="container-fluid wd-page">

    <!-- Hero -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-12">
            <div class="card wd-hero">
                <div class="wd-hero-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="wd-hero-title" id="entity-name">
                                <span class="wd-skel" style="display:inline-block;width:220px;height:26px;"></span>
                            </h1>
                            <p>{{ Sentinel::getUser()->first_name.' '.Sentinel::getUser()->last_name }}</p>
                            <div class="wd-hero-meta" id="entity-meta"></div>
                        </div>
                        <div class="col-md-4 text-md-right" id="entity-badges" style="margin-top: 10px;"></div>
                    </div>
                    <div class="row" style="margin-top: 16px;">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success btn-lg" onclick="openTransactionModal('deposit')" style="width: 100%; font-size: 16px; font-weight: 600; border-radius: 10px; padding: 12px;">
                                <i class="fa fa-arrow-down"></i> Deposit
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger btn-lg" onclick="openTransactionModal('withdrawal')" style="width: 100%; font-size: 16px; font-weight: 600; border-radius: 10px; padding: 12px;">
                                <i class="fa fa-arrow-up"></i> Withdrawal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error banner (hidden by default) -->
    <div class="row" id="error-row" style="display:none; margin-bottom: 20px;">
        <div class="col-12">
            <div class="wd-error-banner">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span id="error-message">Unable to load dashboard data. Please try again later.</span>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
            <div class="wd-stat-card">
                <div class="wd-stat-body">
                    <div class="wd-icon wd-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>
                    </div>
                    <div style="min-width:0;">
                        <div class="wd-stat-label">Total Balance</div>
                        <div class="wd-stat-value" id="total-balance"><span class="wd-skel" style="width:80px;height:22px;"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
            <div class="wd-stat-card">
                <div class="wd-stat-body">
                    <div class="wd-icon wd-icon-amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l3 8 4-16 3 8h4"/></svg>
                    </div>
                    <div style="min-width:0;">
                        <div class="wd-stat-label">Transactions (30d)</div>
                        <div class="wd-stat-value" id="tx-count-30d"><span class="wd-skel" style="width:50px;height:22px;"></span></div>
                        <small class="wd-stat-sub" id="tx-volume-30d">&nbsp;</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
            <div class="wd-stat-card">
                <div class="wd-stat-body">
                    <div class="wd-icon wd-icon-red">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                    </div>
                    <div style="min-width:0;">
                        <div class="wd-stat-label">Pending Payments</div>
                        <div class="wd-stat-value" id="pending-count"><span class="wd-skel" style="width:40px;height:22px;"></span></div>
                        <small class="wd-stat-sub" id="pending-total">&nbsp;</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
            <div class="wd-stat-card">
                <div class="wd-stat-body">
                    <div class="wd-icon wd-icon-grey">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div style="min-width:0;">
                        <div class="wd-stat-label">Total Staff Members</div>
                        <div class="wd-stat-value" id="user-count" style="font-size:20px;"><span class="wd-skel" style="width:40px;height:20px;"></span></div>
                        <small class="wd-stat-sub" id="branch-count">&nbsp;</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leadership & Management -->
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-lg-4 col-md-12" style="margin-bottom: 16px;">
            <div class="wd-card" style="height:100%;">
                <div class="wd-card-header">
                    <h5>User Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="wd-dist-item">
                                <div class="wd-dist-value" style="color:#9b59b6;" id="leadership-count">
                                    <span class="wd-skel" style="width:30px;height:20px;"></span>
                                </div>
                                <div class="wd-dist-label">Leadership</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="wd-dist-item">
                                <div class="wd-dist-value" style="color:var(--wd-warning);" id="supervisor-count">
                                    <span class="wd-skel" style="width:30px;height:20px;"></span>
                                </div>
                                <div class="wd-dist-label">Supervisors</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="wd-dist-item">
                                <div class="wd-dist-value" style="color:var(--wd-info);" id="management-count">
                                    <span class="wd-skel" style="width:30px;height:20px;"></span>
                                </div>
                                <div class="wd-dist-label">Management</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12" style="margin-bottom: 16px;">
            <div class="wd-card" style="height:100%;">
                <div class="wd-card-header">
                    <h5>My Branches</h5>
                    <span class="wd-stat-sub" id="branch-summary-count">&nbsp;</span>
                </div>
                <div class="card-body" style="max-height: 320px; overflow-y: auto; padding: 0;">
                    <div class="table-responsive">
                        <table class="table table-sm wd-table" style="margin-bottom:0;">
                            <thead>
                                <tr>
                                    <th style="padding-left:20px;">Branch</th>
                                    <th>Code</th>
                                    <th>Location</th>
                                    <th style="padding-right:20px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="branches-table-body">
                                <tr>
                                    <td style="padding-left:20px;"><span class="wd-skel" style="width:120px;height:16px;"></span></td>
                                    <td><span class="wd-skel" style="width:60px;height:16px;"></span></td>
                                    <td><span class="wd-skel" style="width:100px;height:16px;"></span></td>
                                    <td><span class="wd-skel" style="width:60px;height:16px;"></span></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:20px;"><span class="wd-skel" style="width:120px;height:16px;"></span></td>
                                    <td><span class="wd-skel" style="width:60px;height:16px;"></span></td>
                                    <td><span class="wd-skel" style="width:100px;height:16px;"></span></td>
                                    <td><span class="wd-skel" style="width:60px;height:16px;"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var userEmail = 'nawanawa85@gmail.com';
    // var userEmail = '{{ $userEmail }}';

    function fmtMoney(v) {
        return 'K' + (v ? Number(v).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
    }

    function showError(message) {
        $('#error-row').show();
        $('#error-message').text(message || 'Unable to load dashboard data. Please try again later.');
        $('#entity-name').text('Unable to load');
        $('#entity-meta').html('<span>Dashboard data could not be retrieved.</span>');
    }

    function loadDashboardData() {
        $.ajax({
            url: 'https://withinheremobileapi.com/api/v1/business-dashboard/company/CMP-35230338/summary',
            type: 'GET',
            headers: {
                'x-user-email': userEmail
            },
            success: function(response) {
                if (response.success && response.data) {
                    var data = response.data;
                    var summary = data.summary || {};
                    var branches = data.branches || [];

                    $('#entity-name').text(data.entity_name || 'N/A');

                    var metaHtml = '';
                    if (data.entity_type) {
                        metaHtml += '<span>' + data.entity_type.charAt(0).toUpperCase() + data.entity_type.slice(1) + '</span>';
                    }
                    if (data.entity_id) {
                        metaHtml += '<span style="opacity:0.6;">&middot;</span><span>' + data.entity_id + '</span>';
                    }
                    if (data.registration_status) {
                        metaHtml += '<span class="wd-chip wd-chip-success">' + data.registration_status.charAt(0).toUpperCase() + data.registration_status.slice(1) + '</span>';
                    }
                    if (data.is_whence_partner) {
                        metaHtml += '<span class="wd-chip">Partner</span>';
                    }
                    $('#entity-meta').html(metaHtml);

                    $('#entity-badges').html(data.is_supervisor ? '<span class="wd-chip wd-chip-warn" style="font-size:13px;">Supervisor Access</span>' : '');

                    $('#users-wallet').text(fmtMoney(summary.users_wallet_balance));
                    $('#tx-count-30d').text(summary.transaction_count_30d ? Number(summary.transaction_count_30d).toLocaleString() : '0');
                    $('#tx-volume-30d').text('Volume: ' + fmtMoney(summary.transaction_volume_30d));
                    $('#pending-count').text(summary.pending_payments_count ? Number(summary.pending_payments_count).toLocaleString() : '0');
                    $('#pending-total').text('Total: ' + fmtMoney(summary.pending_payments_total));

                    $('#main-wallet').text(fmtMoney(summary.main_wallet_balance));
                    $('#branch-wallet').text(fmtMoney(summary.branch_wallet_balance));
                    $('#total-balance').text(fmtMoney(summary.role_wallets_balance));
                    $('#user-count').text(summary.user_count ? Number(summary.user_count).toLocaleString() : '0');
                    $('#branch-count').text('Across ' + (summary.branch_count ? Number(summary.branch_count).toLocaleString() : '0') + ' branches');
                    $('#branch-summary-count').text(branches.length + ' total');

                    $('#leadership-count').text(summary.leadership_count ? Number(summary.leadership_count).toLocaleString() : '0');
                    $('#supervisor-count').text(summary.supervisor_count ? Number(summary.supervisor_count).toLocaleString() : '0');
                    $('#management-count').text(summary.management_count ? Number(summary.management_count).toLocaleString() : '0');

                    var branchesHtml = '';
                    if (branches.length > 0) {
                        $.each(branches, function(index, branch) {
                            var statusBadge = branch.is_active
                                ? '<span class="wd-badge wd-badge-active"><span class="wd-badge-dot"></span>Active</span>'
                                : '<span class="wd-badge wd-badge-inactive"><span class="wd-badge-dot"></span>Inactive</span>';
                            branchesHtml += '<tr>' +
                                '<td style="padding-left:20px;"><strong>' + (branch.branch_name || 'N/A') + '</strong></td>' +
                                '<td>' + (branch.branch_id || 'N/A') + '</td>' +
                                '<td>' + (branch.location || 'N/A') + '</td>' +
                                '<td style="padding-right:20px;">' + statusBadge + '</td>' +
                                '</tr>';
                        });
                    } else {
                        branchesHtml = '<tr><td colspan="4"><div class="wd-empty">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>' +
                            '<div>No branches found</div></div></td></tr>';
                    }
                    $('#branches-table-body').html(branchesHtml);
                } else {
                    showError('Dashboard data was returned in an unexpected format.');
                }
            },
            error: function() {
                showError('Failed to load dashboard data. Please try again later.');
                if (window.KiloAlert) {
                    window.KiloAlert.error('Failed to load dashboard data. Please try again later.');
                }
            }
        });
    }

    loadDashboardData();
});
</script>

@include('provincial-ledger._partials.transaction-modal')

@endsection
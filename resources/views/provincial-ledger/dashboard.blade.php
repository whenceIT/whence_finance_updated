@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none;">
                <div class="card-body" style="padding: 24px;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 style="margin: 0; font-size: 28px; font-weight: 700;" id="entity-name">Loading...</h1>
                            <p style="margin: 8px 0 0; opacity: 0.9;" id="entity-meta"></p>
                        </div>
                        <div class="col-md-4 text-right" id="entity-badges"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #3498db;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Total Balance</div>
                    <div style="font-size: 26px; font-weight: 700; color: #2c3e50;" id="total-balance">--</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #2ecc71;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Users Wallet</div>
                    <div style="font-size: 26px; font-weight: 700; color: #2c3e50;" id="users-wallet">--</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #f39c12;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Transactions (30d)</div>
                    <div style="font-size: 26px; font-weight: 700; color: #2c3e50;" id="tx-count-30d">--</div>
                    <small style="color: #777;" id="tx-volume-30d"></small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #e74c3c;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Pending Payments</div>
                    <div style="font-size: 26px; font-weight: 700; color: #2c3e50;" id="pending-count">--</div>
                    <small style="color: #777;" id="pending-total"></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats -->
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #9b59b6;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Main Wallet</div>
                    <div style="font-size: 22px; font-weight: 700; color: #2c3e50;" id="main-wallet">--</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #1abc9c;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Branch Wallet</div>
                    <div style="font-size: 22px; font-weight: 700; color: #2c3e50;" id="branch-wallet">--</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #34495e;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Role Wallets</div>
                    <div style="font-size: 22px; font-weight: 700; color: #2c3e50;" id="role-wallets">--</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card" style="border-left: 4px solid #95a5a6;">
                <div class="card-body">
                    <div style="color: #777; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">User Count</div>
                    <div style="font-size: 22px; font-weight: 700; color: #2c3e50;" id="user-count">--</div>
                    <small style="color: #777;" id="branch-count"></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Leadership & Management -->
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header" style="background: #f8f9fa; border-bottom: 1px solid #eee;">
                    <h5 style="margin: 0; font-weight: 600;">User Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 text-center">
                            <div style="font-size: 24px; font-weight: 700; color: #9b59b6;" id="leadership-count">--</div>
                            <small style="color: #777;">Leadership</small>
                        </div>
                        <div class="col-4 text-center">
                            <div style="font-size: 24px; font-weight: 700; color: #f39c12;" id="supervisor-count">--</div>
                            <small style="color: #777;">Supervisors</small>
                        </div>
                        <div class="col-4 text-center">
                            <div style="font-size: 24px; font-weight: 700; color: #3498db;" id="management-count">--</div>
                            <small style="color: #777;">Management</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" style="background: #f8f9fa; border-bottom: 1px solid #eee;">
                    <h5 style="margin: 0; font-weight: 600;">Branches</h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Code</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="branches-table-body">
                                <tr><td colspan="4" class="text-center">Loading...</td></tr>
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
    var userEmail = '{{ $userEmail }}';
    
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
                    $('#entity-meta').html(
                        (data.entity_type ? data.entity_type.charAt(0).toUpperCase() + data.entity_type.slice(1) + ' | ' : '') +
                        (data.entity_id || '') +
                        (data.registration_status ? ' <span class="badge badge-success">' + data.registration_status.charAt(0).toUpperCase() + data.registration_status.slice(1) + '</span>' : '') +
                        (data.is_whence_partner ? ' <span class="badge badge-info">Partner</span>' : '')
                    );
                    $('#entity-badges').html(data.is_supervisor ? '<span class="badge badge-warning" style="font-size: 14px;">Supervisor Access</span>' : '');
                    
                    $('#total-balance').text('K' + (summary.total_balance ? summary.total_balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'));
                    $('#users-wallet').text('K' + (summary.users_wallet_balance ? summary.users_wallet_balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'));
                    $('#tx-count-30d').text(summary.transaction_count_30d ? summary.transaction_count_30d.toLocaleString() : '0');
                    $('#tx-volume-30d').text('Volume: K' + (summary.transaction_volume_30d ? summary.transaction_volume_30d.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'));
                    $('#pending-count').text(summary.pending_payments_count ? summary.pending_payments_count.toLocaleString() : '0');
                    $('#pending-total').text('Total: K' + (summary.pending_payments_total ? summary.pending_payments_total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'));
                    
                    $('#main-wallet').text('K' + (summary.main_wallet_balance ? summary.main_wallet_balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'));
                    $('#branch-wallet').text('K' + (summary.branch_wallet_balance ? summary.branch_wallet_balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'));
                    $('#role-wallets').text('K' + (summary.role_wallets_balance ? summary.role_wallets_balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'));
                    $('#user-count').text(summary.user_count ? summary.user_count.toLocaleString() : '0');
                    $('#branch-count').text('Branches: ' + (summary.branch_count ? summary.branch_count.toLocaleString() : '0'));
                    
                    $('#leadership-count').text(summary.leadership_count ? summary.leadership_count.toLocaleString() : '0');
                    $('#supervisor-count').text(summary.supervisor_count ? summary.supervisor_count.toLocaleString() : '0');
                    $('#management-count').text(summary.management_count ? summary.management_count.toLocaleString() : '0');
                    
                    var branchesHtml = '';
                    if (branches.length > 0) {
                        $.each(branches, function(index, branch) {
                            branchesHtml += '<tr>' +
                                '<td><strong>' + (branch.branch_name || 'N/A') + '</strong></td>' +
                                '<td>' + (branch.branch_id || 'N/A') + '</td>' +
                                '<td>' + (branch.location || 'N/A') + '</td>' +
                                '<td>' + (branch.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>') + '</td>' +
                                '</tr>';
                        });
                    } else {
                        branchesHtml = '<tr><td colspan="4" class="text-center">No branches found</td></tr>';
                    }
                    $('#branches-table-body').html(branchesHtml);
                }
            },
            error: function() {
                $('#entity-name').text('Error');
                $('#entity-meta').text('Unable to load external dashboard data.');
                window.KiloAlert.error('Failed to load dashboard data. Please try again later.');
            }
        });
    }
    
    loadDashboardData();
});
</script>
@endsection
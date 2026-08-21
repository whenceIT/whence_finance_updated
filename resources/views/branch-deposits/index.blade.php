@extends('layouts.master')

@section('title')
    Branch Deposits
@endsection

@section('content')

@php
    $blockerUser = Sentinel::getUser();
    $debtBlocker = \App\Helpers\BlockerHelper::debt_blocker($blockerUser);
    
@endphp
<x-kilo-alert/>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Branch Deposits Management</h3>
                <small>{{$debtBlocker}}</small>
            </div>
            <section class="content-header">
                <div class="deposit-header-box">
                    <h2 style="margin-top:0; font-weight: 700; font-size: 28px;" id="monthlyDepositsTitle">Monthly Deposits for {{ $selectedMonthForInput ?? $selectedMonth }}</h2>
                    <p class="text-muted" style="margin-bottom:15px;">
                        Enter deposit for the allowed monthly deposits required for your branch. Please ensure the total amount covers the full or atleast K5,000 minimum.
                    </p>

                    <p style="color: rgba(255, 17, 41, 0.84); padding:5px; font-weight: 500; background-color:rgba(255, 245, 246, 0.57); border-radius:4px;">
                        <i class="fa fa-info-circle"></i>
                        For the savings, salaries, and housing sections to unlock make sure to make and record full payments on the mandatory deposit which are the setup debt, building deposit, and administration deposit. Once these are fully paid, the other sections will be available for deposits.
                    </p>
                    <hr style="border-top:1px solid #eee; margin:20px 0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:10px;">
                        <div style="max-width:300px;">
                            <label class="deposit-label">Select Month</label>
                            <input type="month" id="monthFilter" class="form-control" value="{{ $selectedMonthForInput ?? $selectedMonth }}" max="{{ date('Y-m') }}">
                        </div>

                        <!-- Add a button here  -->
                        <button type="button" id="viewBankDepositsBtn" class="btn btn-primary">
                            <i class="fa fa-money"></i> View Overal History
                        </button>
                    </div>
                </div>
            </section>
            <div class="box-body" id="depositsContainer">
                
                <!-- Payment A -->
                @include('branch-deposits._partials.debt-setup', ['selectedMonth' => $selectedMonth])
                
                <br>
                <hr>
                <!-- Payment B -->
                @if(!$debtBlocker && isset($status[0]) && ($status[0]['status'] === 'unpaid' || $status[0]['status'] === 'partially paid')
                || in_array($blockerUser->office_id, [49, 46]) )
                    @include('branch-deposits._partials.building', ['selectedMonth' => $selectedMonth])
                @else
                    @include('branch-deposits._partials.building', ['selectedMonth' => $selectedMonth, 'disabled'=>true] )
                @endif
                <br>
                <hr>
                <!-- Payment C -->
                @if(isset($status[0]) && isset($status[1]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] != 'fully paid'
                || in_array($blockerUser->office_id, [49, 46]))
                    @include('branch-deposits._partials.administration', ['selectedMonth' => $selectedMonth])
                @else
                    @include('branch-deposits._partials.administration', ['selectedMonth' => $selectedMonth, 'disabled'=>true])
                @endif
                <br>
                <hr>
                <!-- Payment D -->
                @if(isset($status[0]) && isset($status[1]) && isset($status[2]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid' && $status[2]['status'] != 'fully paid'
                || in_array($blockerUser->office_id, [49, 46]))
                    @include('branch-deposits._partials.statutory', ['selectedMonth' => $selectedMonth])
                @else
                    @include('branch-deposits._partials.statutory', ['selectedMonth' => $selectedMonth, 'disabled'=>true])
                @endif
                <br>
                <hr>
                @if(isset($status[0]) && isset($status[1]) && isset($status[2]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid' && $status[2]['status'] == 'fully paid' || in_array($blockerUser->office_id, [62, 68, 79, 80]))
                    @include('branch-deposits._partials.salaries', ['selectedMonth' => $selectedMonth])
                @else
                    @include('branch-deposits._partials.salaries', ['selectedMonth' => $selectedMonth, 'disabled'=>true])
                @endif
                <br>
                <hr>
                @if(isset($status[0]) && isset($status[1]) && isset($status[2]) && $status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid'  && $status[2]['status'] === 'fully paid'
                || in_array($blockerUser->office_id, [62, 68, 79, 80]))
                    @include('branch-deposits._partials.savings', ['selectedMonth' => $selectedMonth])
                @else
                    @include('branch-deposits._partials.savings', ['selectedMonth' => $selectedMonth, 'disabled'=>true])
                @endif
                <br>
                <hr>
                @if($status[0]['status'] === 'fully paid' && $status[1]['status'] === 'fully paid' && $status[2]['status'] === 'fully paid' 
                || in_array($blockerUser->office_id, [62, 68, 79, 80]))
                    @include('branch-deposits._partials.housing', ['selectedMonth' => $selectedMonth])
                @else
                    @include('branch-deposits._partials.housing', ['selectedMonth' => $selectedMonth, 'disabled'=>true])
                @endif 
            </div>
        </div>
    </div>
</div>

<!-- Overall History Modal -->
<div class="modal fade" id="overallHistoryModal" tabindex="-1" role="dialog" aria-labelledby="overallHistoryModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%; max-width: 1400px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 6px 6px 0 0;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="overallHistoryModalLabel" style="font-weight: 600;">
                    <i class="fa fa-history"></i> Overall Deposit History
                </h4>
            </div>
            <div class="modal-body" id="overallHistoryContent" style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                <!-- Shimmer Loading Effect -->
                <div id="shimmerLoading">
                    <div class="shimmer-wrapper" style="margin-bottom: 20px;">
                        <div class="shimmer-header"></div>
                        <div class="shimmer-line"></div>
                        <div class="shimmer-line"></div>
                        <div class="shimmer-line"></div>
                    </div>
                    <div class="shimmer-wrapper" style="margin-bottom: 20px;">
                        <div class="shimmer-header"></div>
                        <div class="shimmer-line"></div>
                        <div class="shimmer-line"></div>
                        <div class="shimmer-line"></div>
                    </div>
                    <div class="shimmer-wrapper">
                        <div class="shimmer-header"></div>
                        <div class="shimmer-line"></div>
                        <div class="shimmer-line"></div>
                        <div class="shimmer-line"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f9f9f9;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Shimmer Loading Effect */
    .shimmer-wrapper {
        animation: shimmer-animation 1.5s infinite;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        border-radius: 8px;
        padding: 20px;
    }
    
    @keyframes shimmer-animation {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .shimmer-header {
        height: 40px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 4px;
        margin-bottom: 15px;
    }
    
    .shimmer-line {
        height: 20px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 4px;
        margin-bottom: 10px;
    }
    
    /* Accordion Styles */
    .history-accordion {
        margin-bottom: 15px;
    }
    
    .accordion-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 18px 20px;
        cursor: pointer;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .accordion-header:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    
    .accordion-header.collapsed {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        color: #333;
    }
    
    .accordion-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .accordion-total {
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .accordion-icon {
        transition: transform 0.3s ease;
        font-size: 20px;
    }
    
    .accordion-header.collapsed .accordion-icon {
        transform: rotate(-90deg);
    }
    
    .accordion-body {
        padding: 0;
        border: 1px solid #e0e0e0;
        border-top: none;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
    }
    
    .deposits-table {
        margin: 0;
        background: white;
    }
    
    .deposits-table thead {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .deposits-table thead th {
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #667eea;
        padding: 12px;
    }
    
    .deposits-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .deposits-table tbody tr:hover {
        background-color: #f8f9ff;
    }
    
    .deposits-table tbody td {
        padding: 12px;
        vertical-align: middle;
    }
    
    .deposit-type-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .amount-cell {
        font-weight: 600;
        color: #00a65a;
        font-size: 14px;
    }
</style>

<script>
$(document).ready(function() {

   console.log('{{!!$debtBlocker!!}}');
   console.log('{{!!$status!!}}');
    // Handle month filter change
    $('#monthFilter').on('change', function() {
        var selectedMonth = $(this).val();
        if (selectedMonth) {
            // Reload page with selected month parameter
            window.location.href = '/user/branch_deposits?month=' + selectedMonth;
        }
    });
    
    // View Overall History button
    $('#viewBankDepositsBtn').on('click', function() {
        $('#overallHistoryModal').modal('show');
        loadOverallHistory();
    });
    
    function loadOverallHistory() {
        $.ajax({
            url: '{{ route("branch-deposits.overall-history") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayOverallHistory(response.data);
                } else {
                    $('#overallHistoryContent').html(
                        '<div class="alert alert-danger">' +
                        '<i class="fa fa-exclamation-triangle"></i> Failed to load deposit history.' +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                $('#overallHistoryContent').html(
                    '<div class="alert alert-danger">' +
                    '<i class="fa fa-exclamation-triangle"></i> Error loading deposit history: ' + error +
                    '</div>'
                );
            }
        });
    }
    
    function displayOverallHistory(data) {
        if (Object.keys(data).length === 0) {
            $('#overallHistoryContent').html(
                '<div class="alert alert-info" style="text-align: center; padding: 30px;">' +
                '<i class="fa fa-info-circle fa-3x" style="margin-bottom: 15px;"></i>' +
                '<h4>No deposit history found for this office.</h4>' +
                '</div>'
            );
            return;
        }
        
        var html = '<div class="panel-group" id="depositAccordion">';
        var accordionIndex = 0;
        
        // Loop through each month-year group
        $.each(data, function(monthYear, monthData) {
            var collapseId = 'collapse' + accordionIndex;
            var isFirst = accordionIndex === 0;
            
            html += '<div class="history-accordion">';
            
            // Accordion Header
            html += '<div class="accordion-header' + (isFirst ? '' : ' collapsed') + '" data-toggle="collapse" data-target="#' + collapseId + '" aria-expanded="' + (isFirst ? 'true' : 'false') + '">';
            html += '<div class="accordion-title">';
            html += '<i class="fa fa-chevron-down accordion-icon"></i>';
            html += '<i class="fa fa-calendar"></i>';
            html += '<span>' + monthYear + '</span>';
            html += '<span class="badge" style="background-color: rgba(255,255,255,0.3); font-size: 14px; margin-left: 10px;">' + monthData.deposits.length + ' deposits</span>';
            html += '</div>';
            html += '</div>';
            
            // Accordion Body
            html += '<div id="' + collapseId + '" class="collapse' + (isFirst ? ' in' : '') + '" data-parent="#depositAccordion">';
            html += '<div class="accordion-body">';
            html += '<div class="table-responsive">';
            html += '<table class="table table-hover deposits-table">';
            html += '<thead>';
            html += '<tr>';
            html += '<th><i class="fa fa-tag"></i> Deposit Type</th>';
            html += '<th><i class="fa fa-money"></i> Amount</th>';
            html += '<th><i class="fa fa-credit-card"></i> Method</th>';
            html += '<th><i class="fa fa-hashtag"></i> Reference</th>';
            html += '<th><i class="fa fa-user"></i> Recorded By</th>';
            html += '<th><i class="fa fa-clock-o"></i> Status</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            
            // Loop through deposits in this month
            $.each(monthData.deposits, function(index, deposit) {
                html += '<tr>';
                html += '<td><span class="deposit-type-badge">' + deposit.deposit_type_name + '</span></td>';
                html += '<td class="amount-cell">K' + parseFloat(deposit.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                html += '<td><span style="color: #666;">' + deposit.deposit_method + '</span></td>';
                html += '<td><code style="background-color: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + deposit.reference_number + '</code></td>';
                html += '<td><i class="fa fa-user-circle" style="color: #667eea;"></i> ' + deposit.user_name + '</td>';
                html += '<td>' + deposit.status + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            accordionIndex++;
        });
        
        html += '</div>';
        
        $('#overallHistoryContent').html(html);
        
        // Add click handlers for accordion headers
        $('.accordion-header').on('click', function() {
            $(this).toggleClass('collapsed');
        });
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        var date = new Date(dateString);
        var options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
});
</script>
@endsection

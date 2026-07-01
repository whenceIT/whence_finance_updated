@extends('layouts.master')

@section('title')
    Branch Deposits
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Branch Deposits Management</h3>
            </div>

            <section class="content-header">
                <div class="deposit-header-box">
                    <h2 style="margin-top:0; font-weight: 700; font-size: 28px;" id="monthlyDepositsTitle">Monthly Deposits</h2>
                    <p class="text-muted" style="margin-bottom:15px;">
                        Enter deposit for the allowed monthly deposits required for your branch. Please ensure the total amount covers the full or atleast K5,000 partial minimum required deposit for the month. Once you click "Save Deposit", it will be recorded and cannot be reversed. If you are unsure about the required amount, click "This Month Deposit" to view your current month's deposit status.
                    </p>

                    <p style="color: rgba(255, 17, 41, 0.84); padding:5px; font-weight: 500; background-color:rgba(255, 245, 246, 0.57); border-radius:4px;">
                        <i class="fa fa-info-circle"></i>
                        For the savings, salaries, and housing sections to unlock make sure to make and record full payments on the mandatory deposit
                    </p>
                    <hr style="border-top:1px solid #eee; margin:20px 0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:10px;">
                        <div style="max-width:300px;">
                            <label class="deposit-label">Select Month</label>
                            <input type="month" id="monthFilter" class="form-control" value="{{ $selectedMonth }}" max="{{ date('Y-m') }}">
                        </div>

                        <!-- Add a button here  -->
                        <button type="button" id="viewBankDepositsBtn" class="btn btn-primary">
                            <i class="fa fa-money"></i> View Overal History
                        </button>
                    </div>
                </div>
            </section>
            <div class="box-body" id="depositsContainer">
                @include('branch-deposits._partials.building', ['selectedMonth' => $selectedMonth])
                <br>
                <hr>
                @include('branch-deposits._partials.administration', ['selectedMonth' => $selectedMonth])
                <br>
                <hr>
                @include('branch-deposits._partials.statutory', ['selectedMonth' => $selectedMonth])
                <br>
                <hr>
                @include('branch-deposits._partials.salaries', ['selectedMonth' => $selectedMonth])
                <br>
                <hr>
                @include('branch-deposits._partials.savings', ['selectedMonth' => $selectedMonth])
                <br>
                <hr>
                @include('branch-deposits._partials.housing', ['selectedMonth' => $selectedMonth]) 
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle month filter change
    $('#monthFilter').on('change', function() {
        var selectedMonth = $(this).val();
        if (selectedMonth) {
            // Reload page with selected month parameter
            window.location.href = '/user/branch-deposits?month=' + selectedMonth;
        }
    });
    
    // View Overall History button
    $('#viewBankDepositsBtn').on('click', function() {
        // TODO: Implement overall history view
        alert('Overall history view coming soon!');
    });
});
</script>
@endsection

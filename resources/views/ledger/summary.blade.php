<style>
    .box {
        border-radius: 8px; /* Rounding the corners */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    }

    .box-title {
        text-align: center; /* Center title text */
    }

    /* Flex container for aligning form and buttons */
    .filter-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filter-container form {
        display: flex;
        align-items: center;
    }

    .filter-container .btn {
        height: 100%; /* Make buttons fill the available height */
    }

    /* Style for the report section */
    .report-section {
        display: flex;
        align-items: center;
    }

    .report-section span {
        margin-right: 10px; /* Add spacing between label and button */
    }
</style>

@extends('layouts.master')

@section('title')
    Total Ledger Summary 
@endsection

@section('content')
<div class="container">

    <div class="row mb-3 filter-container">
        <div class="col-md-8 ">
            <form action="{{ route('ledger.summary') }}" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <label for="start_date" class="mr-2">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') ?: date('Y-m-4') }}" min="2025-01-04">
                </div>
                <div class="form-group mr-2">
                    <label for="end_date" class="mr-2">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') ?: date('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-primary">Apply Filter</button>
            </form>
        </div>

        <div class="col-md-4">
            <div class="report-section">
                <span>Generate Report:</span>
                <form action="{{ route('ledger.all_report_excel') }}" method="GET">
                    <input type="hidden" name="start_date" value="{{ request('start_date') ?: date('Y-m-25') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') ?: date('Y-m-d') }}">
                    <button type="submit" class="btn btn-primary">Excel</button>
                </form>
            </div>
        </div>
    </div>

  
    <div style="margin-bottom: 25px;"></div>
        <div class="col-md-11">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        
                        @if(Sentinel::hasAccess('groups.create'))
                    
                        <tr class="bg-danger">
                            <td style="font-size: 16px;">Total Cash Balance</td>
                            <td style="font-size: 16px;">{{ number_format($totalCashBalance) }}.00</td>
                        </tr>
                        <tr class="bg-success">
                            <td style="font-size: 16px;">Total Income</td>
                            <td style="font-size: 16px;">{{ number_format($totalIncome, 2) }}</td>
                        </tr>
                        <tr class="bg-warning">
                            <td style="font-size: 16px;">Total Advances</td>
                            <td style="font-size: 16px;">{{ number_format($totalAdvances) }}.00</td>
                        </tr>
                        <tr class="bg-info">
                            <td style="font-size: 16px;">Advance Installments Paid </td>
                            <td style="font-size: 16px;">{{ number_format($totalAdvancesPaid, 2) }}</td>
                        </tr>
                        <tr class="bg-primary">
                            <td style="font-size: 16px;">Total Expenses</td>
                            <td style="font-size: 16px;">{{ number_format($totalExpenses) }}.00</td>
                        </tr>
                        <tr class="bg-info">
                            <td style="font-size: 16px;">New Loans</td>
                            <td style="font-size: 16px;">{{ number_format($totalNewLoans) }}.00</td>
                        </tr>
                        
                        <tr class="bg-secondary">
                            <td style="font-size: 16px;">Total Full Payments</td>
                            <td style="font-size: 16px;">{{ number_format($totalFullPayments) }}.00</td>
                        </tr>
                        <tr class="bg-danger">
                            <td style="font-size: 16px;">Total Reloaned Amount</td>
                            <td style="font-size: 16px;">{{ number_format($totalReloanedAmount) }}.00</td>
                        </tr>
                        <tr class="bg-warning">
                            <td style="font-size: 16px;">Total Part Payments</td>
                            <td style="font-size: 16px;">{{ number_format($totalPartPayment) }}.00</td>
                        </tr>
                        
                        <h5>View 'Branch Ledgers' tab for a detailed breakdown of transactions</h5>
                    
                        
                        @endif
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<!---here--->
        
        <div class="box box-primary" style="max-width: 400px; margin: 30;">
            <div class="box-header with-border">
                <h3 class="box-title">Add income</h3>
            </div>
            <div class="box-body">
                <button id="showFormButton" class="btn btn-primary">Add Income</button>
                <form id="closeIncomeForm" action="{{ route('ledger.income_store') }}" method="POST" style="display: none;">
                    @csrf
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" name="source" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" id="hideFormButton" class="btn btn-secondary">Cancel</button>
                </form>
            </div>
        </div>

</div>

@section('footer-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
          
            var showFormButton = document.getElementById('showFormButton');
            var closeIncomeForm = document.getElementById('closeIncomeForm');
            var hideFormButton = document.getElementById('hideFormButton');

           
            showFormButton.addEventListener('click', function () {
                closeIncomeForm.style.display = 'block';
                showFormButton.style.display = 'none';
            });

          
            hideFormButton.addEventListener('click', function () {
                closeIncomeForm.style.display = 'none';
                showFormButton.style.display = 'block';
            });
        });
    </script>
@endsection


@endsection



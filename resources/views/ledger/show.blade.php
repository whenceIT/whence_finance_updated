@extends('layouts.master')

@section('title')
    LEDGER - {{ $officeName }}
@endsection

@section('styles')
    
@endsection

@section('content')
<div class="container">
    <div class="row mb-3">
     <div class="col-md-8">
    <form action="{{ route('ledger.show', ['officeName' => $officeName]) }}" method="GET" class="form-inline">
        
        <!-- Fixed Start Date (Hidden Input) -->
        <div class="form-group mr-2">
            <label class="mr-2">Start Date:</label>
            <input type="hidden" name="start_date" value="2025-01-04">
            <input type="text" class="form-control" value="2025-01-04" readonly>
        </div>

        <div class="form-group mr-2">
            <label for="end_date" class="mr-2">End Date:</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') ?: date('Y-m-d') }}">
        </div>

        <button type="submit" class="btn btn-primary mr-2">Apply Filter</button>
    </form>
</div>


<div class="row mb-3">
    <div class="col-md-12" style="margin-bottom: 10px;">
        <form action="{{ route('ledger.ledger_report_pdf') }}" method="GET" style="display:flex; align-items:center; gap:10px; flex-wrap: wrap;">
            <input type="hidden" name="office_id" value="{{ $office->id }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') ?: date('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') ?: date('Y-m-d') }}">

            <!-- Instructions -->
            <span style="width: 100%; font-size: 12px; margin-bottom: 5px;">
                Select the sections you want to expand into detailed transactions in the PDF report. 
                If a section is not selected, only the summary totals will appear.
            </span>

            <!-- Checkboxes -->
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="advances"> Advances</label>
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="advances_paid"> Advances Paid</label>
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="expenses"> Expenses</label>
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="deposits"> Deposits</label>
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="full_payments"> Full Payments</label>
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="reloan"> Reloan Payments</label>
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="part_payments"> Part Payments</label>
            <label style="white-space: nowrap;"><input type="checkbox" name="sections[]" value="new_loans"> New Loans</label>

            <button type="submit" class="btn btn-primary btn-sm" style="background-color: #007bff; border-color: blue;">PDF</button>
        </form>

         <!-- <form action="{{ route('ledger.ledger_report_excel') }}" method="GET">
            <input type="hidden" name="office_id" value="{{ $office->id }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') ?: date('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') ?: date('Y-m-d') }}">
            <button type="submit" class="btn btn-primary btn-sm" style="background-color: #007bff; border-color: blue;">Excel</button>
        </form> -->
    </div>
</div>



    </div>

  
    <div style="margin-bottom: 25px;"></div>

    
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr class="bg-danger">
                    <td style="font-size: 16px;">Cash Balance at {{$endDate}}</td>
                    <td style="font-size: 16px;">{{ number_format($closingBalance, 2) }}</td>
                </tr>
                   <tr class="bg-info">
                    <td style="font-size: 16px;">Cash Brought Forward</td>
                    <td style="font-size: 16px;">{{ number_format($openingBalance, 2) }}</td>
                </tr>
                <tr class="bg-success">
                    <td style="font-size: 16px;">Total Income</td>
                    <td style="font-size: 16px;">{{ number_format($totalIncome, 2) }}</td>
                </tr>

                <tr class="bg-warning">
                    <td style="font-size: 16px;">Total Advances</td>
		        <td style="font-size: 16px;">{{ number_format($advances, 2) }}</td>

	            <tr class="bg-info">
                    <td style="font-size: 16px;">Advance Installments Paid </td>
                    <td style="font-size: 16px;">{{ number_format($advancesPaid, 2) }}</td>
                </tr>
                </tr>
                <tr class="bg-primary">
                    <td style="font-size: 16px;">Total Expenses</td>
                    <td style="font-size: 16px;">{{ number_format($expenses, 2) }}</td>
                </tr>
                <tr class="bg-secondary">
                    <td style="font-size: 16px;">Total Full Payments</td>
                    <td style="font-size: 16px;">{{ number_format($fullPayments, 2) }}</td>
                </tr>
                <tr class="bg-warning">
                     <td style="font-size: 16px;">Total Deposits</td>
                    <td style="font-size: 16px;">{{ number_format($deposits, 2) }}</td>
                </tr>
                <tr class="bg-danger">
                    <td style="font-size: 16px;">Total Reloan Payments</td>
                    <td style="font-size: 16px;">{{ number_format($reloanedAmount, 2) }}</td>
                </tr>
                <tr class="bg-info">
                    <td style="font-size: 16px;">Total Part Payments</td>
                    <td style="font-size: 16px;">{{ number_format($partPayment, 2) }}</td>
                </tr>
                <tr class="bg-warning">
                    <td style="font-size: 16px;">Total New Loans</td>
                    <td style="font-size: 16px;">{{ number_format($newLoans, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
<div class="row mb-4">
        <div class="col-md-12">
            <form action="{{ route('ledger.store', ['officeName' => $officeName]) }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label for="income_amount" class="col-sm-2 col-form-label">Income Amount:</label>
                    <div class="col-sm-3">
                        <input type="number" name="income_amount" id="income_amount" class="form-control" step="0.01" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_received" class="col-sm-2 col-form-label">Date Received:</label>
                    <div class="col-sm-3">
                        <input type="date" name="date_received" id="date_received" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Add Income</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


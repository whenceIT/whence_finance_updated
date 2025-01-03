@extends('layouts.master')
@section('title', 'Advance Details')
@section('content')


@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Advance Details</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40%;">Advance ID</th>
                        <td>{{ $advance->id }}</td>
                    </tr>
                    <tr>
                        <th>User</th>
                        <td>{{ $advance->first_name . ' ' . $advance->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Advance Amount</th>
                        <td>{{ $advance->amount }}</td>
                    </tr>
                    <tr>
                        <th>Installments</th>
                        <td>{{ $advance->installments }}</td>
                    </tr>
                    <tr>
                        <th>Amount per Installment</th>
                        <td>{{ $advance->installment_amount }}</td>
                    </tr>
                    <tr>
                        <th>Date Approved</th>
                        <td>{{ $advance->date_approved }}</td>
                    </tr>  
                    <tr>      
                        <th>Approved by</th>
                        <td>{{ $advance->approved_by_id }}</td>
                    </tr>
                    <tr>
                        <th>Purpose</th>
                        <td>{{ $advance->purpose }}</td>
                    </tr>
                    <tr>
                        <th>Next Repayment Date</th>
                        <td>{{ $advance->expected_repayment_dates }}</td>
                    </tr>
                    <tr>
                        <th>Amount Left</th>
                        <td>{{ $advance->remaining_amount }}</td>
                    </tr>  
                </table>
            </div>
        </div>
        <!-- Close Advance Button -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Close Advance</h3>
            </div>
            <div class="box-body">
                <button id="showFormButton" class="btn btn-primary">Close Advance</button>
                <form id="closeAdvanceForm" action="{{ route('advances.close', $advance->id) }}" method="POST" style="display: none;">
                    @csrf
                    <div class="form-group">
                        <label for="remaining_amount">Balance</label>
                        <input type="number" name="remaining_amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="amount_paid">Total Amount Paid (Full advance amount)</label>
                        <input type="number" name="amount_paid" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" id="hideFormButton" class="btn btn-secondary">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Notes</h3>
            </div>
            <div class="box-body">
                {!! $advance->notes !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
          
            var showFormButton = document.getElementById('showFormButton');
            var closeAdvanceForm = document.getElementById('closeAdvanceForm');
            var hideFormButton = document.getElementById('hideFormButton');

           
            showFormButton.addEventListener('click', function () {
                closeAdvanceForm.style.display = 'block';
                showFormButton.style.display = 'none';
            });

          
            hideFormButton.addEventListener('click', function () {
                closeAdvanceForm.style.display = 'none';
                showFormButton.style.display = 'block';
            });
        });
    </script>
@endsection



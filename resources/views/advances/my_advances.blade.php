@extends('layouts.master')

@section('title')
    My Advances
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">My Advances</h3>
    </div>    
    <div class="box-body table-responsive">
        @if ($advances->isEmpty())
            <p>No advances found.</p>
        @else
            <table class="table table-bordered table-hover table-striped" id="data-table">     
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Amount Requested</th>
                        <th scope="col">Installment Amount</th>
                        <th scope="col">Installments</th>
                        <th scope="col">Next Payment Date</th>
                        <th scope="col">Amount Paid</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Approved On</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($advances as $advance)
                        <tr>
                            <td>{{ $advance->first_name }} {{ $advance->last_name }}</</td>
                            <td>{{ $advance->amount }}</td>
                            <td>{{ $advance->installment_amount }}</td>
                            <td>{{ $advance->installments }}</td>
                            <td>{{ $advance->expected_repayment_dates }}</td>
                            <td>{{ $advance->amount_paid }}</td>
                            <td>{{ $advance->remaining_amount}}</td>
                            <td>{{ $advance->date_approved}}</td>
                            <!----->
                        </tr>
                    @endforeach
                </tbody>
            </table> 
            <div class="alert 
    @if ($advance->status == 'approved') alert-success
    @elseif ($advance->status == 'pending') alert-warning
    @elseif ($advance->status == 'declined') alert-danger
    @endif" role="alert">
    <strong>Status: </strong>
    @if ($advance->status == 'approved')
        Salary Advance Approved
    @elseif ($advance->status == 'pending')
        Waiting for Salary Advance Approval
    @elseif ($advance->status == 'declined')
        Salary Advance has been declined.
    @endif
</div>
        @endif
    </div>
</div>
@endsection
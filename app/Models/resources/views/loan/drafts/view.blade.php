@extends('layouts.master')

@section('title')
    View Loan Termination 
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">Termination for Loan #{{ $draft->loan->id }}</h6>
        </div>

        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th>Client</th>
                    <td>{{ $draft->loan->client->first_name }} {{ $draft->loan->client->last_name }}</td>
                </tr>
                <tr>
                    <th>Outstanding Amount</th>
                    <td>ZMW {{ number_format($draft->amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Contra Account</th>
                    <td>{{ $draft->fund->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Termination Date</th>
                    <td>{{ $draft->termination_date }}</td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td>{{ $draft->withdrawn_notes }}</td>
                </tr>
            </table>

            @if($draft->status == 'draft')
                <form action="{{ url('loan/'.$draft->id.'/withdraw') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve Termination</button>
                </form>

                <a href="{{ route('loan.new_statement', ['loan_id' => $draft->loan->id]) }}" class="btn btn-info">
                  Statement
                </a>
            @else
                <button class="btn btn-secondary" disabled>Already Approved</button>
            @endif

            <a href="{{ route('loan.drafts.index') }}" class="btn btn-default">Back to List</a>
        </div>
    </div>
@endsection

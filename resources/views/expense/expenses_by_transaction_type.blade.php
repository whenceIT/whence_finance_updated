@extends('layouts.master')

@section('title', 'Expenses by Transaction Type')

@section('content')
    <div class="container">
        <h1>Expenses by Transaction Type: {{ $transactionType }}</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Amount</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense->date }}</td>
                        <td>{{ $expense->name }}</td>
                        <td>{{ $expense->amount }}</td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

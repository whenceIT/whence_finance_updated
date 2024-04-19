@extends('layouts.master')

@section('title')
    Input Ledger Data
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="label-text">USER NAME: {{ $user->first_name }}</h3>
                    <h4 class="label-text">OFFICE: {{ $user->office->name }}</h3>
                    @if ($cycle_dates)
                        <h4 class="label-text">CYCLE END DATE: {{ $cycle_dates->cycle_end_date }}</h3>
                    @else
                        <h4 class="label-text">No cycle dates found</h3>
                    @endif

                    <form action="{{ route('ledger.store') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="opening_balance">Opening Balance:</label>
                            <input type="number" name="opening_balance" id="opening_balance" class="form-control" required>
                        </div>

                        <!---div class="form-group">
                            <label for="total_income">Total Income:</label>
                            <input type="number" name="total_income" id="total_income" class="form-control" required>
                        </div-->
                        <div class="form-group">
                            <label for="cycle_opening_uncollected">Cycle Opening Uncollected:</label>
                            <input type="number" name="cycle_opening_uncollected" id="cycle_opening_uncollected" class="form-control" required>
                        </div>

                        

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .label-text {
            font-size: 16px; /* Adjust font size as needed */
            font-family: Arial, sans-serif; /* Specify font family */
            font-weight: normal; /* Adjust font weight as needed */
        }
    </style>
@endpush

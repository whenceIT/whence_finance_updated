@extends('layouts.master')
@section('title')
    Bank Account Expenses
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Bank Account Expenses</h3>
        <div class="box-tools pull-right">
            @if(Sentinel::hasAccess('bank_account_expenses.create'))
                <a href="{{ route('bank_account_expenses.create') }}" class="btn btn-info btn-sm">Add Expense</a>
            @endif
        </div>
    </div>

    <div class="box-body">
        <form method="get" action="{{ route('bank_account_expenses.index') }}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="start_date" class="control-label col-md-2">Start Date</label>
                <div class="col-md-3">
                    <input type="text" name="start_date" class="form-control date-picker" value="{{ $start_date ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label for="end_date" class="control-label col-md-2">End Date</label>
                <div class="col-md-3">
                    <input type="text" name="end_date" class="form-control date-picker" value="{{ $end_date ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-2 col-md-3 text-center">
                    <button type="submit" class="btn btn-success">Search</button>
                    <a href="{{ route('bank_account_expenses.index') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>
    </div>

    @include('bank_account_expenses._index_partial')
</div>
@endsection
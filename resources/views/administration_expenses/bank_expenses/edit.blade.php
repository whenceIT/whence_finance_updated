@extends('layouts.master')
@section('title')
    Edit Bank Account Expense
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Bank Account Expense</h3>
    </div>

    <form method="POST" action="{{ route('bank_account_expenses.update', $expense->id) }}" class="form-horizontal">
        {{ csrf_field() }}
        @method('PUT')
        <div class="box-body">
            <div class="form-group">
                <label for="bank_account_id" class="control-label col-md-2">Bank Account <span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <select name="bank_account_id" class="form-control select2" required>
                        @foreach($bankAccounts as $id => $name)
                            <option value="{{ $id }}" {{ $expense->bank_account_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="category_id" class="control-label col-md-2">Expense Category <span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <select name="category_id" class="form-control select2" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $expense->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="reference_number" class="control-label col-md-2">Reference Number</label>
                <div class="col-md-4">
                    <input type="text" name="reference_number" class="form-control" value="{{ $expense->reference_number }}" placeholder="Enter reference number">
                </div>
            </div>

            <div class="form-group">
                <label for="amount" class="control-label col-md-2">Amount (K) <span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="{{ $expense->amount }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="transaction_date" class="control-label col-md-2">Transaction Date <span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <input type="text" name="transaction_date" class="form-control date-picker" value="{{ $expense->transaction_date }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="comments" class="control-label col-md-2">Comments / Description</label>
                <div class="col-md-6">
                    <textarea name="comments" class="form-control" rows="3" placeholder="Enter description">{{ $expense->comments }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="gl_account_code" class="control-label col-md-2">GL Account Code</label>
                <div class="col-md-4">
                    <input type="text" name="gl_account_code" class="form-control" value="{{ $expense->gl_account_code }}" placeholder="Enter GL account code">
                </div>
            </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('bank_account_expenses.index') }}" class="btn btn-danger">Cancel</a>
        </div>
    </form>
</div>
@endsection
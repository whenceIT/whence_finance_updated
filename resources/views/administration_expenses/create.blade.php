@extends('layouts.master')
@section('title')
    Add Administration Expense
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Add Administration Expense</h3>
    </div>

    <form method="POST" action="{{ route('administration_expenses.store') }}" class="form-horizontal">
        {{ csrf_field() }}
        <div class="box-body">
            <div class="form-group">
                <label for="category_id" class="control-label col-md-2">Expense Category <span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <select name="category_id" class="form-control select2" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="reference_number" class="control-label col-md-2">Reference Number</label>
                <div class="col-md-4">
                    <input type="text" name="reference_number" class="form-control" placeholder="Enter reference number">
                </div>
            </div>

            <div class="form-group">
                <label for="amount" class="control-label col-md-2">Amount (K) <span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                </div>
            </div>

            <div class="form-group">
                <label for="expense_date" class="control-label col-md-2">Expense Date <span class="text-danger">*</span></label>
                <div class="col-md-4">
                    <input type="text" name="expense_date" class="form-control date-picker" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="comments" class="control-label col-md-2">Comments / Description</label>
                <div class="col-md-6">
                    <textarea name="comments" class="form-control" rows="3" placeholder="Enter description"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="gl_account_code" class="control-label col-md-2">GL Account Code</label>
                <div class="col-md-4">
                    <input type="text" name="gl_account_code" class="form-control" placeholder="Enter GL account code">
                </div>
            </div>

            <hr>
            <h4>Bank Charges (Optional)</h4>
            <p class="text-muted">Use this section only for bank-related charges</p>

            <div class="form-group">
                <label for="bank_charge_type" class="control-label col-md-2">Bank Charge Type</label>
                <div class="col-md-4">
                    <select name="bank_charge_type" class="form-control">
                        <option value="">Select Charge Type</option>
                        <option value="Monthly Bank Charges">Monthly Bank Charges</option>
                        <option value="Transaction Fees">Transaction Fees</option>
                        <option value="Transfer Charges">Transfer Charges</option>
                        <option value="SMS Charges">SMS Charges</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('administration_expenses.index') }}" class="btn btn-danger">Cancel</a>
        </div>
    </form>
</div>
@endsection
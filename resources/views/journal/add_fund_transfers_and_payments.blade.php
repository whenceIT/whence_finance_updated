@extends('layouts.master')

@section('title')
    Add Fund Transfer
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Add Fund Transfer</h3>

            <div class="alert alert-info" style="margin-top: 15px; margin-bottom: 0;">
                <strong>Use this page to record outgoing funds and internal bank movements.</strong><br>
                Examples include supplier payments, inter-account transfers, bank charges, and cash withdrawals.
            </div>
        </div>

        <form method="post" action="{{ url('/accounting/store_fund_transfers_and_payments') }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="box-body">

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">1. Movement Information</h4>
                    </div>
                </div>

                <div class="form-group">
                    <label for="movement_type" class="control-label col-md-2">Movement Type</label>
                    <div class="col-md-4">
                        <select name="movement_type" id="movement_type" class="form-control select2" required>
                            <option value="">Select movement type</option>
                            <option value="payment">Payment</option>
                            <option value="transfer">Transfer</option>
                            <option value="bank_charge">Bank Charge</option>
                            <option value="withdrawal">Cash Withdrawal</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>

                    <label for="date" class="control-label col-md-2">Transaction Date</label>
                    <div class="col-md-4">
                        <input type="text" name="date" id="date" class="form-control date-picker" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_id" class="control-label col-md-2">Branch</label>
                    <div class="col-md-4">
                        <select name="office_id" id="office_id" class="form-control select2" required>
                            <option value="">Select branch</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label for="amount" class="control-label col-md-2">Amount</label>
                    <div class="col-md-4">
                        <input type="number" min="0" name="amount" id="amount" class="form-control" placeholder="Enter amount" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reference_no" class="control-label col-md-2">Reference No.</label>
                    <div class="col-md-4">
                        <input type="text" name="reference_no" id="reference_no" class="form-control" placeholder="Bank reference / transaction ID">
                    </div>

                    <label for="payment_method" class="control-label col-md-2">Method</label>
                    <div class="col-md-4">
                        <select name="payment_method" id="payment_method" class="form-control select2">
                            <option value="">Select method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <hr>

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">2. Account Details</h4>
                    </div>
                </div>

                <div class="form-group">
                    <label for="from_account" class="control-label col-md-2">From Account</label>
                    <div class="col-md-4">
                        <select name="from_account" id="from_account" class="form-control select2" required>
                            <option value="">Select source account</option>
                            @foreach($bank_accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label for="to_account" class="control-label col-md-2">To Account</label>
                    <div class="col-md-4">
                        <select name="to_account" id="to_account" class="form-control select2">
                            <option value="">Select destination account</option>
                            @foreach($bank_accounts as $account)
                                <option value="{{ $account->id }}">
                                     {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block" style="margin-bottom: 0;">Use this mainly for transfers between accounts.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="payee_name" class="control-label col-md-2">Payee / Recipient</label>
                    <div class="col-md-4">
                        <input type="text" name="payee_name" id="payee_name" class="form-control" placeholder="Supplier, person, or institution">
                    </div>

                    <label for="expense_category" class="control-label col-md-2">Expense Category</label>
                    <div class="col-md-4">
                        <select name="expense_category" id="expense_category" class="form-control select2">
                            <option value="">Select category</option>
                            <option value="operations">Operations</option>
                            <option value="supplies">Supplies</option>
                            <option value="transport">Transport</option>
                            <option value="utilities">Utilities</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="bank_charges">Bank Charges</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <hr>

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">3. Purpose and Notes</h4>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="control-label col-md-2">Title</label>
                    <div class="col-md-10">
                        <input type="text" name="title" id="title" class="form-control" placeholder="Short title for this fund movement">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="control-label col-md-2">Description</label>
                    <div class="col-md-10">
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Add a clear explanation of why the funds were moved"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="remarks" class="control-label col-md-2">Internal Remarks</label>
                    <div class="col-md-10">
                        <textarea name="remarks" id="remarks" rows="3" class="form-control" placeholder="Optional notes for finance/admin review"></textarea>
                    </div>
                </div>

                <hr>

                <!-- <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">4. Supporting Documents</h4>
                    </div>
                </div>

                <div class="form-group">
                    <label for="attachment" class="control-label col-md-2">Upload File</label>
                    <div class="col-md-4">
                        <input type="file" name="attachment" id="attachment" class="form-control-file">
                    </div>

                    <label for="document_note" class="control-label col-md-2">Document Note</label>
                    <div class="col-md-4">
                        <input type="text" name="document_note" id="document_note" class="form-control" placeholder="e.g. Invoice, transfer slip, receipt">
                    </div>
                </div> -->

                <hr>

                <!-- <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">5. Submission</h4>
                    </div>
                </div> -->

                <!-- <div class="form-group">
                    <label for="status" class="control-label col-md-2">Save As</label>
                    <div class="col-md-4">
                        <select name="status" id="status" class="form-control select2" required>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                        </select>
                    </div>

                    <label for="requires_approval" class="control-label col-md-2">Requires Approval</label>
                    <div class="col-md-4" style="padding-top: 7px;">
                        <label style="font-weight: normal; margin-right: 15px;">
                            <input type="radio" name="requires_approval" value="1" checked> Yes
                        </label>
                        <label style="font-weight: normal;">
                            <input type="radio" name="requires_approval" value="0"> No
                        </label>
                    </div>
                </div> -->

            </div>

            <div class="box-footer">
             
                <button type="submit" class="btn btn-primary pull-right">Save Fund Movement</button>
            </div>
        </form>
    </div>
@endsection

@section('footer-scripts')
    <script>
        $(document).ready(function () {
            $(".form-horizontal").validate();

            $('form').on('submit', function () {
                const $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true).text('Saving...');
            });
        });
    </script>
@endsection
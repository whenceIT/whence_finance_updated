@extends('layouts.master')
@section('title')
    Edit Income
@endsection
@section('content')
    <section class="content-header">
        <h1>Edit Income</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('daily-reports.index') }}"><i class="fa fa-dashboard"></i> Daily Reports</a></li>
            <li class="active">Edit Income</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Income Details</h3>
                    </div>
                    <form action="{{ route('daily-reports.income.update', $income->id) }}" method="post" id="incomeForm">
                        @csrf @method('PUT')
                        <div class="box-body">
                            @if (Session::has('error'))
                                <div class="alert alert-danger">{{ Session::get('error') }}</div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Branch <span class="text-danger">*</span></label>
                                        <select name="branch_id" class="form-control select2" required style="width: 100%;">
                                            <option value="">-- Select Branch --</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ $income->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Income Date <span class="text-danger">*</span></label>
                                        <input type="date" name="income_date" value="{{ $income->income_date->format('Y-m-d') }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Income Category <span class="text-danger">*</span></label>
                                        <select name="income_category" class="form-control select2" required style="width: 100%;" id="incomeCategory">
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $key => $category)
                                                <option value="{{ $key }}" {{ $income->income_category == $key ? 'selected' : '' }}>{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Income Source</label>
                                        <select name="income_source" class="form-control select2" style="width: 100%;" id="incomeSource">
                                            <option value="">-- Select Source --</option>
                                            @foreach($sources as $key => $source)
                                                <option value="{{ $key }}" {{ $income->income_source == $key ? 'selected' : '' }}>{{ $source }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Reference Number</label>
                                        <input type="text" name="reference_number" value="{{ $income->reference_number }}" class="form-control" placeholder="e.g., REC-2026-001">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Amount <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" step="0.01" min="0" value="{{ $income->amount }}" class="form-control" required placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_method" class="form-control select2" required style="width: 100%;">
                                            <option value="Cash" {{ $income->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Bank Transfer" {{ $income->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="Mobile Money" {{ $income->payment_method == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                            <option value="Card" {{ $income->payment_method == 'Card' ? 'selected' : '' }}>Card</option>
                                            <option value="Cheque" {{ $income->payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                            <option value="Other" {{ $income->payment_method == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Received From</label>
                                        <input type="text" name="received_from" value="{{ $income->received_from }}" class="form-control" placeholder="Client/Entity name">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Additional details...">{{ $income->description }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Metadata (JSON)</label>
                                <textarea name="metadata" class="form-control" rows="3" placeholder='{"key": "value"}'>{{ $income->metadata ? json_encode($income->metadata, JSON_PRETTY_PRINT) : '' }}</textarea>
                                <small class="form-hint">Optional JSON metadata for additional tracking</small>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Update Income</button>
                            <a href="{{ route('daily-reports.index') }}" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quick Actions</h3>
                    </div>
                    <div class="box-body">
                        <a href="{{ route('daily-reports.index') }}" class="btn btn-info btn-block margin-bottom">
                            <i class="fa fa-list"></i> View All Records
                        </a>
                        <a href="{{ route('daily-reports.branch-report') }}" class="btn btn-success btn-block margin-bottom">
                            <i class="fa fa-bar-chart"></i> Branch Report
                        </a>
                        <a href="{{ route('daily-reports.reports') }}" class="btn btn-primary btn-block">
                            <i class="fa fa-file-text"></i> Financial Reports
                        </a>
                    </div>
                </div>

                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Record Info</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $income->id }}</td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td>{{ $income->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated</th>
                                <td>{{ $income->updated_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Recorded By</th>
                                <td>{{ $income->recordedBy ? $income->recordedBy->name : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();

    // Format amount input
    $('input[name="amount"]').on('blur', function() {
        var val = parseFloat($(this).val());
        if (!isNaN(val)) {
            $(this).val(val.toFixed(2));
        }
    });
});
</script>
@endsection
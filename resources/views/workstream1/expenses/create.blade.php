@extends('layouts.master')
@section('title', 'Submit Workstream 1 Expense')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border"><h3 class="box-title">Submit Expense for Approval</h3></div>
    <form method="post" action="{{ url('workstream1/expenses') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="box-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <div class="form-group">
                <label>Branch</label>
                <select name="office_id" class="form-control" required>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ (string)old('office_id', $offices->count() === 1 ? $offices->first()->id : '') === (string)$office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="name" class="form-control" maxlength="191" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label>Expense Type</label>
                <select name="expense_type_id" class="form-control">
                    <option value="">Select type (optional)</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ (string)old('expense_type_id') === (string)$type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>GL Account</label>
                <select name="gl_account_id" class="form-control">
                    <option value="">Use default / assign later</option>
                    @foreach($glAccounts as $account)
                        <option value="{{ $account->id }}" {{ (string)old('gl_account_id') === (string)$account->id ? 'selected' : '' }}>{{ $account->gl_code }} | {{ $account->name }}</option>
                    @endforeach
                </select>
                <p class="help-block">The selected account must be validated against the expense type before accounting posting is enabled.</p>
            </div>
            <div class="form-group">
                <label>Amount (ZMW)</label>
                <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required value="{{ old('amount') }}">
            </div>
            <div class="form-group">
                <label>Expense Date</label>
                <input type="date" name="date" class="form-control" required max="{{ date('Y-m-d') }}" value="{{ old('date', date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Narration / Business Purpose</label>
                <textarea name="notes" class="form-control" rows="4" maxlength="10000">{{ old('notes') }}</textarea>
            </div>
            <div class="form-group">
                <label>Supporting documents (invoice, receipt, quotation)</label>
                <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx">
                <p class="help-block">Up to 10 MB per file. Files are stored privately and downloaded through an access-controlled route.</p>
            </div>
            <div class="alert alert-info">Submitting this form records a pending expense only. It does not approve the expense or initiate a payment.</div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit for Approval</button>
            <a href="{{ url('workstream1/expenses') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection

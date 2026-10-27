@extends('layouts.master')
@section('title', 'Workstream 1 Expense Details')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Expense #{{ $expense->id }} — {{ $expense->name }}</h3>
        <div class="box-tools pull-right"><a href="{{ url('workstream1/expenses') }}" class="btn btn-default btn-sm">Back to register</a></div>
    </div>
    <div class="box-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div> @endif
        <table class="table table-bordered">
            <tr><th style="width:25%">Status</th><td>{{ ucfirst($expense->status) }}</td></tr>
            <tr><th>Branch</th><td>{{ optional($expense->office)->name ?: 'N/A' }}</td></tr>
            <tr><th>Expense date</th><td>{{ $expense->date }}</td></tr>
            <tr><th>Type</th><td>{{ optional($expense->type)->name ?: 'N/A' }}</td></tr>
            <tr><th>GL account</th><td>{{ optional($expense->glAccount)->gl_code }} {{ optional($expense->glAccount)->name }}</td></tr>
            <tr><th>Amount</th><td>{{ number_format((float)$expense->amount, 2) }}</td></tr>
            <tr><th>Submitted by</th><td>{{ optional($expense->createdBy)->first_name }} {{ optional($expense->createdBy)->last_name }}</td></tr>
            <tr><th>Narration</th><td>{{ $expense->notes }}</td></tr>
        </table>

        <h4>Supporting Documents</h4>
        @forelse($expense->documents as $document)
            <p><a href="{{ url('workstream1/expenses/'.$expense->id.'/documents/'.$document->id) }}">{{ $document->original_name }}</a>
                <small>uploaded by {{ optional($document->uploadedBy)->first_name }} {{ optional($document->uploadedBy)->last_name }}</small></p>
        @empty
            <p>No supporting documents attached.</p>
        @endforelse

        <h4>Approval History</h4>
        <table class="table table-bordered">
            <thead><tr><th>Action</th><th>By</th><th>When</th><th>Comments</th></tr></thead>
            <tbody>
            @forelse($expense->approvals as $approval)
                <tr>
                    <td>{{ ucfirst($approval->action) }}</td>
                    <td>{{ optional($approval->actor)->first_name }} {{ optional($approval->actor)->last_name }}</td>
                    <td>{{ $approval->acted_at }}</td>
                    <td>{{ $approval->comments }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No approval action recorded.</td></tr>
            @endforelse
            </tbody>
        </table>

        <h4>Payment Records</h4>
        <table class="table table-bordered">
            <thead><tr><th>Date recorded</th><th>Method</th><th>Reference</th><th>Amount</th><th>Status</th><th>Recorded by</th><th>Notes</th></tr></thead>
            <tbody>
            @forelse($expense->payments as $payment)
                <tr>
                    <td>{{ $payment->recorded_at }}</td><td>{{ $payment->payment_method }}</td>
                    <td>{{ $payment->reference_number }}</td><td>{{ number_format((float)$payment->amount, 2) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>{{ optional($payment->recordedBy)->first_name }} {{ optional($payment->recordedBy)->last_name }}</td>
                    <td>{{ $payment->notes }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No payment records.</td></tr>
            @endforelse
            </tbody>
        </table>

        @if($expense->status === 'approved' && \Cartalyst\Sentinel\Laravel\Facades\Sentinel::hasAccess('workstream1.expenses.pay'))
            <hr>
            <h4>Record an Existing Payment Outcome</h4>
            <div class="alert alert-warning">This form records a payment made through another approved process. It does not send money.</div>
            <form method="post" action="{{ url('workstream1/expenses/'.$expense->id.'/payments') }}">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-3 form-group"><label>Payment method</label><select name="payment_method" class="form-control" required><option value="">Select</option><option>Bank transfer</option><option>Mobile money</option><option>Cash</option><option>Other</option></select></div>
                    <div class="col-md-3 form-group"><label>Reference number</label><input name="reference_number" class="form-control" maxlength="191"></div>
                    <div class="col-md-2 form-group"><label>Amount (ZMW)</label><input name="amount" type="number" min="0.01" step="0.01" max="{{ $expense->amount }}" class="form-control" required value="{{ $expense->amount }}"></div>
                    <div class="col-md-2 form-group"><label>Outcome</label><select name="status" class="form-control" required><option value="pending">Pending</option><option value="paid">Paid</option><option value="failed">Failed</option></select></div>
                    <div class="col-md-12 form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                </div>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Record this payment outcome? No transfer will be initiated.')">Record Payment Outcome</button>
            </form>
        @endif
    </div>
</div>
@endsection

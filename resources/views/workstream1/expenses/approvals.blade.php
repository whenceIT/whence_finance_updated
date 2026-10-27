@extends('layouts.master')
@section('title', 'Workstream 1 Expense Approvals')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border"><h3 class="box-title">Pending Expense Approvals</h3></div>
    <div class="box-body">
        @if($errors->any()) <div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div> @endif
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead><tr><th>Date</th><th>Branch</th><th>Description</th><th>Type</th><th>Amount</th><th>Submitted By</th><th>Action</th></tr></thead>
                <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->date }}</td>
                        <td>{{ optional($expense->office)->name ?: 'N/A' }}</td>
                        <td><a href="{{ url('workstream1/expenses/'.$expense->id) }}">{{ $expense->name }}</a></td>
                        <td>{{ optional($expense->type)->name ?: 'N/A' }}</td>
                        <td>{{ number_format((float)$expense->amount, 2) }}</td>
                        <td>{{ optional($expense->createdBy)->first_name }} {{ optional($expense->createdBy)->last_name }}</td>
                        <td>
                            <form method="post" action="{{ url('workstream1/expenses/approvals/'.$expense->id.'/approved') }}" style="display:inline;">
                                {{ csrf_field() }} <button class="btn btn-success btn-xs" type="submit" onclick="return confirm('Approve this expense?')">Approve</button>
                            </form>
                            <form method="post" action="{{ url('workstream1/expenses/approvals/'.$expense->id.'/declined') }}" style="display:inline;">
                                {{ csrf_field() }}
                                <input type="hidden" name="comments" value="Declined from approvals screen">
                                <button class="btn btn-danger btn-xs" type="submit" onclick="return confirm('Decline this expense?')">Decline</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No pending expenses.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-center">{{ $expenses->links() }}</div>
    </div>
</div>
@endsection

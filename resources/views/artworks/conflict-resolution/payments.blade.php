@extends('layouts.master')
@section('title') Payments @endsection
@section('content')
<section class="content">
<div class="box"><div class="box-body">
<button class="btn btn-success" data-toggle="modal" data-target="#paymentModal">Record Payment</button>
<table class="table table-bordered" style="margin-top:15px;">
<tr><th>Ref</th><th>Client</th><th>Branch</th><th>Charge</th><th>Paid</th></tr>
@foreach($cases as $c)
<tr><td>{{ $c->case_reference }}</td><td>{{ $c->client_name }}</td><td>{{ optional(\App\Models\Office::find($c->branch_id))->name }}</td><td>{{ number_format($c->charge_amount,2) }}</td><td>{{ number_format($c->amount_paid,2) }}</td></tr>
@endforeach
</table>
{{ $cases->links() }}
</div></div>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('conflict-resolution.payments.store') }}">
      @csrf
      <div class="modal-header"><h4 class="modal-title">Record Payment</h4></div>
      <div class="modal-body">
        <div class="form-group"><label>Case</label><select name="case_id" class="form-control select2" required><option value="">-- Select --</option>@foreach($allCases as $c)<option value="{{ $c->id }}">{{ $c->case_reference }} - {{ $c->client_name }}</option>@endforeach</select></div>
        <div class="form-group"><label>Amount</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="submit" class="btn btn-success">Record</button></div>
      </form>
    </div>
  </div>
</div>
</section>
@endsection


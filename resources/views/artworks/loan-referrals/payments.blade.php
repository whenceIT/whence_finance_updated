@extends('layouts.master')
@section('title') Payments Received @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">
<button class="btn btn-success" data-toggle="modal" data-target="#paymentModal" style="margin-bottom:10px;">Record Payment</button>
<table class="table table-bordered">
<thead><tr>
<th>Ref</th><th>Client</th><th>NRC</th><th>Contact</th><th>Branch</th><th>Referral Date</th>
<th>Loan Status</th><th>Profit Share</th><th>Amount Paid</th><th>Outstanding</th><th>New Loan Since Referral</th>
</tr></thead>
<tbody>
@foreach($items as $r)
@php($latestLoan = $r->new_loans->first())
<tr>
<td>{{ $r->referral_reference }}</td>
<td>{{ $r->client_name }}</td>
<td>{{ $r->nrc_number ?: 'N/A' }}</td>
<td>{{ $r->contact_number ?: 'N/A' }}</td>
<td>{{ optional(\App\Models\Office::find($r->branch_id))->name ?: 'N/A' }}</td>
<td>{{ optional($r->created_at)->format('Y-m-d') }}</td>
<td>{{ $r->loan_status }}</td>
<td>{{ number_format($r->profit_share,2) }}</td>
<td>{{ number_format($r->amount_paid,2) }}</td>
<td>{{ number_format(max(0, $r->profit_share - $r->amount_paid),2) }}</td>
<td>
    @if($latestLoan)
        Yes ({{ $r->new_loans->count() }})
        <br>Loan #{{ $latestLoan->id }}{{ $latestLoan->account_number ? ' / ' . $latestLoan->account_number : '' }}
        <br>{{ ucfirst($latestLoan->status) }} - {{ number_format((float) ($latestLoan->approved_amount ?? $latestLoan->principal ?? 0),2) }}
        <br>{{ \Illuminate\Support\Carbon::parse($latestLoan->created_at)->format('Y-m-d') }}
    @else
        No
    @endif
</td>
</tr>
@endforeach
</tbody></table>
{{ $items->links() }}
</div></div>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('loan-referrals.payments.store') }}">
      @csrf
      <div class="modal-header"><h4 class="modal-title">Record Payment</h4></div>
      <div class="modal-body">
        <div class="form-group"><label>Referral</label><select name="referral_id" class="form-control select2" required><option value="">--</option>@foreach($all as $r)<option value="{{ $r->id }}">{{ $r->referral_reference }}</option>@endforeach</select></div>
        <div class="form-group"><label>Profit Share</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="submit" class="btn btn-success">Record</button></div>
      </form>
    </div>
  </div>
</div>
</section>
@endsection

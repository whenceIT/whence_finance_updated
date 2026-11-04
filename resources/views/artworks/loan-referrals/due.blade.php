@extends('layouts.master')
@section('title') Profit Share Due @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">
<table class="table table-bordered"><tr><th>Ref</th><th>Client</th><th>Branch</th><th>Profit Share</th><th>Paid</th><th>Outstanding</th></tr>
@foreach($items as $r)
<tr><td>{{ $r->referral_reference }}</td><td>{{ $r->client_name }}</td><td>{{ optional(\App\Models\Office::find($r->branch_id))->name }}</td><td>{{ number_format($r->profit_share,2) }}</td><td>{{ number_format($r->amount_paid,2) }}</td><td>{{ number_format(max(0,$r->profit_share-$r->amount_paid),2) }}</td></tr>
@endforeach
</table>
{{ $items->links() }}
</div></div></section>
@endsection

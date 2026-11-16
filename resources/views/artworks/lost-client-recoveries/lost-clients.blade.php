@extends('layouts.master')
@section('title') Lost Clients @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">

<div class="row"><div class="col-md-6">
    <form method="GET" action="{{ route('lost-client-recoveries.lost-clients') }}" style="display:inline-block;">
        <div class="input-group" style="max-width:320px;">
            <input type="text" name="search" class="form-control" placeholder="Search client name, NRC, phone..." value="{{ $search ?? '' }}">
            <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
        </div>
    </form>
</div></div>

<div class="row"><div class="col-md-12">
    <div class="callout callout-info" style="margin-bottom:15px;">
        <h3 style="font-size:20px;"><strong>{{ $clients->total() }}</strong> Lost Clients</h3>
        <small><strong>Clients whose last loan was 3+ months ago and all loans are closed.</strong></small>
    </div>
</div></div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Client Name</th>
        <th>NRC Number</th>
        <th>Office</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Address</th>
        <th>Working Place</th>
        <th>Total Loans</th>
        <th>Last Loan Date</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @if($clients->isNotEmpty())
        @foreach($clients as $c)
        <tr>
            <td>{{ $c->first_name }} {{ $c->last_name }}</td>
            <td>{{ $c->nrc_number ?? '-' }}</td>
            <td>{{ $c->office_name ?? '-' }}</td>
            <td>{{ $c->phone_primary ?? $c->phone ?? '-' }}</td>
            <td>{{ $c->email_primary ?? $c->email ?? '-' }}</td>
            <td>{{ $c->address_line1 ?? $c->address ?? '-' }}</td>
            <td>{{ $c->employer ?? '-' }}</td>
            <td>{{ $c->total_loans }}</td>
            <td>{{ \Carbon\Carbon::parse($c->last_loan_date)->format('Y-m-d') }}</td>
            <td>
                @if($c->lost_recovery)
                    <span class="label bg-green">Recovered</span>
                @else
                    <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#recoverModal{{ $c->id }}" onclick="setRecoverId({{ $c->id }})"><i class="fa fa-refresh"></i> Recover</button>
                @endif
            </td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="10" class="text-center">No lost clients found.</td></tr>
    @endif
    </tbody>
</table>

<div>{{ $clients->links() }}</div>

@foreach($clients as $c)
<div class="modal fade" id="recoverModal{{ $c->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Mark as Recovered - {{ $c->first_name }} {{ $c->last_name }}</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('lost-client-recoveries.recover', $c->id ) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Recovery Notes</label>
                        <textarea name="lost_recovery_notes" class="form-control" rows="3" placeholder="Enter recovery notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Recover</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function setRecoverId(id) {
    // Modal is per-row, no action needed
}
</script>

</div></div></section>
@endsection

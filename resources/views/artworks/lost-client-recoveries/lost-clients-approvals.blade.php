@extends('layouts.master')
@section('title') Lost Client Recovery Approvals @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">

<div class="row"><div class="col-md-6">
    <form method="GET" action="{{ route('lost-client-recoveries.lost-clients-approvals') }}" style="display:inline-block;">
        <div class="input-group" style="max-width:320px;">
            <input type="text" name="search" class="form-control" placeholder="Search client name, reference..." value="{{ $search ?? '' }}">
            <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
        </div>
    </form>
</div></div>

<div class="row"><div class="col-md-12">
    <div class="callout callout-info" style="margin-bottom:15px;">
        <h3 style="font-size:20px;"><strong>{{ $approvals->total() }}</strong> Pending Approvals</h3>
        <small><strong>Lost client recovery approvals awaiting your decision for your office.</strong></small>
    </div>
</div></div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Client ID</th>
        <th>Client Name</th>
        <th>NRC Number</th>
        <th>Office</th>
        <th>Recovery Notes</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @if($approvals->isNotEmpty())
        @foreach($approvals as $a)
        <tr>
            <td>{{ $a->id }}</td>
            <td>{{ $a->first_name }} {{ $a->last_name }}</td>
            <td>{{ $a->nrc_number ?? '-' }}</td>
            <td>{{ $a->office_name ?? '-' }}</td>
            <td>{{ $a->lost_recovery_notes ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($a->created_at)->format('Y-m-d') }}</td>
            <td>
                <form method="POST" action="{{ route('lost-client-recoveries.approve-lost-recovery', $a->id) }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="action" value="approved">
                    <button type="submit" class="btn btn-xs btn-success" onclick="return confirm('Approve this lost client recovery?')"><i class="fa fa-check"></i> Accept</button>
                </form>
                <form method="POST" action="{{ route('lost-client-recoveries.approve-lost-recovery', $a->id) }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="action" value="lost">
                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Decline this lost client recovery?')"><i class="fa fa-times"></i> Decline</button>
                </form>
            </td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="7" class="text-center">No pending lost client recovery approvals.</td></tr>
    @endif
    </tbody>
</table>

<div>{{ $approvals->links() }}</div>

</div></div></section>
@endsection

@extends('layouts.master')
@section('title', 'Collateral Approval Queue')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Approval Requests</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Collateral</th>
                    <th>Loan</th>
                    <th>Requested By</th>
                    <th>Old Status</th>
                    <th>New Status</th>
                    <th>Reason</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>
                            <a href="{{ route('collateral.show', $request->collateral) }}">
                                {{ $request->collateral->name ?? 'N/A' }}
                            </a>
                        </td>
                        <td>
                            @if($request->collateral && $request->collateral->loan)
                                <a href="#">
                                    Loan #{{ $request->collateral->loan->id }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $request->requested_by->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($request->old_status) }}</td>
                        <td>{{ ucfirst($request->new_status) }}</td>
                        <td>{{ $request->reason }}</td>
                        <td>{{ $request->request_date }}</td>
                        <td>
                            <form method="post" action="{{ route('collateral.approvals.approve', $request) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-xs">Approve</button>
                            </form>
                            <form method="post" action="{{ route('collateral.approvals.reject', $request) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-xs">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No pending approval requests.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
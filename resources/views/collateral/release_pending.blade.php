@extends('layouts.master')
@section('title', 'Release Pending Approvals')
@section('content')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-unlock-alt"></i> Release Pending Approvals
        </h3>
        <div class="box-tools pull-right">
            <span class="badge bg-yellow">{{ $collaterals->total() }} pending</span>
        </div>
    </div>
    <div class="box-body">

        @if(session('flash_notification.message'))
            <div class="alert alert-{{ session('flash_notification.level') }} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('flash_notification.message') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Loan #</th>
                        <th>Client</th>
                        <th>Branch</th>
                        <th>Current Worth</th>
                        <th>Condition</th>
                        <th>Created By</th>
                        <th>Release Requested</th>
                        <th style="min-width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($collaterals as $collateral)
                    <tr>
                        <td>{{ $loop->iteration + ($collaterals->currentPage() - 1) * $collaterals->perPage() }}</td>
                        <td>
                            <a href="{{ route('collateral.show', $collateral) }}">{{ $collateral->name }}</a>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $collateral->category ?? '—')) }}</td>
                        <td>
                            @if($collateral->loan)
                                <a href="{{ url('loan/' . $collateral->loan->id) }}">{{ $collateral->loan->id }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @php
                                $client = optional($collateral->loan)->client;
                            @endphp
                            {{ $client ? trim($client->first_name . ' ' . $client->last_name) : '—' }}
                        </td>
                        <td>{{ optional(optional($collateral->loan)->office)->name ?? '—' }}</td>
                        <td>K {{ number_format($collateral->current_worth, 2) }}</td>
                        <td>{{ ucfirst($collateral->condition ?? '—') }}</td>
                        <td>
                            @php $creator = $collateral->created_by; @endphp
                            {{ $creator ? trim($creator->first_name . ' ' . $creator->last_name) : '—' }}
                        </td>
                        <td>{{ optional($collateral->release_requested_at)->format('d M Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('collateral.show', $collateral) }}"
                               class="btn btn-xs btn-default"
                               title="View">
                                <i class="fa fa-eye"></i> View
                            </a>

                            {{-- Approve: deletes the collateral record --}}
                            <form method="post"
                                  action="{{ route('collateral.release_pending.approve', $collateral) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Approve release? This will permanently delete the collateral record.');">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-success" title="Approve Release">
                                    <i class="fa fa-check"></i> Approve
                                </button>
                            </form>

                            {{-- Decline: sets status back to seized_inventory --}}
                            <form method="post"
                                  action="{{ route('collateral.release_pending.decline', $collateral) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Decline release? Collateral will be moved back to Seized/Inventory.');">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-danger" title="Decline Release">
                                    <i class="fa fa-times"></i> Decline
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted" style="padding: 30px 0;">
                            <i class="fa fa-inbox fa-2x"></i><br>
                            No collateral items pending release.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($collaterals->hasPages())
            <div class="text-center">
                {{ $collaterals->links() }}
            </div>
        @endif

    </div>{{-- /.box-body --}}
</div>{{-- /.box --}}

@endsection

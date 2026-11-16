@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>Payroll Approvals</h1>
</section>

<section class="content">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Summary cards --}}
    <div class="row">

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $summary['pending_administration'] }}</h3>
                    <p>Pending Administration</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>{{ $summary['pending_risk'] }}</h3>
                    <p>Pending Risk</p>
                </div>
                <div class="icon">
                    <i class="fa fa-shield"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $summary['approved'] }}</h3>
                    <p>Fully Approved</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $summary['returned'] }}</h3>
                    <p>Returned</p>
                </div>
                <div class="icon">
                    <i class="fa fa-undo"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Filter Wage Bills</h3>
        </div>

        <div class="box-body">
            <form method="GET" action="{{ route('wage-bills.approvals') }}">
                <div class="row">

                    <div class="col-md-4">
                        <label>Branch</label>
                        <select name="branch_id" class="form-control">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Submission Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Approval Type</label>
                        <select name="approval_type" class="form-control">
                            <option value="">All Approvals</option>
                            <option value="administration" {{ request('approval_type') == 'administration' ? 'selected' : '' }}>Administration</option>
                            <option value="risk" {{ request('approval_type') == 'risk' ? 'selected' : '' }}>Risk</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Wage bill list --}}
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Submitted Wage Bills</h3>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Payroll Cycle</th>
                        <th>Submission No.</th>
                        <th>Submitted By</th>
                        <th>Submitted At</th>
                        <th>Administration</th>
                        <th>Risk</th>
                        <th>Overall Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($submissions as $submission)
                        @php
                            $adminApproval = $submission->approvals->firstWhere('approval_type', 'administration');
                            $riskApproval = $submission->approvals->firstWhere('approval_type', 'risk');
                        @endphp

                        <tr>
                            <td>{{ $submission->branch->name ?? 'Unknown Branch' }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($submission->cycle_start)->format('d M Y') }}
                            </td>

                            <td>#{{ $submission->submission_number }}</td>

                            <td>
                                {{ $submission->submitter->first_name ?? '' }}
                                {{ $submission->submitter->last_name ?? '' }}
                            </td>

                            <td>
                                {{ $submission->submitted_at
                                    ? $submission->submitted_at->format('d M Y H:i')
                                    : '-' }}
                            </td>

                            <td>
                                @if($adminApproval)
                                    <span class="label label-{{ $adminApproval->status == 'approved' ? 'success' : ($adminApproval->status == 'returned' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($adminApproval->status) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if($riskApproval)
                                    <span class="label label-{{ $riskApproval->status == 'approved' ? 'success' : ($riskApproval->status == 'returned' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($riskApproval->status) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <span class="label label-{{
                                    $submission->status == 'approved'
                                        ? 'success'
                                        : ($submission->status == 'returned' ? 'danger' : 'primary')
                                }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('wage-bills.approval-show', $submission->id) }}"
                                   class="btn btn-xs btn-primary">
                                    <i class="fa fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                No wage bill submissions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            {{ $submissions->links() }}
        </div>
    </div>

</section>
@endsection
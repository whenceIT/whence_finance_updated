@extends('layouts.master')

@section('title')
    Department Shares
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-share"></i> Department Shares Summary</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-share"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Recovery Dept Share</span>
                                <span class="info-box-number">K {{ number_format($totalDeptShare, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Unit Share</span>
                                <span class="info-box-number">K {{ number_format($totalUnitShare, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Details</h3>
                    </div>
                    <div class="box-body">
                        <form method="GET" class="form-inline" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin-right: 10px;">
                                <label for="type" style="margin-right: 5px;">Filter:</label>
                                <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <option value="dept_share" {{ request('type') == 'dept_share' ? 'selected' : '' }}>Recoveries Dept Share</option>
                                    <option value="unit_share" {{ request('type') == 'unit_share' ? 'selected' : '' }}>Unit Share</option>
                                </select>
                            </div>
                            @if(request()->hasAny(['type']))
                                <a href="{{ route('dept.shares') }}" class="btn btn-default btn-sm">Clear Filter</a>
                            @endif
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Case</th>
                                        <th>Loan</th>
                                        <th>Office</th>
                                        <th>Client</th>
                                        <th>Staff</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($deptShares) > 0 || count($unitShares) > 0)
                                        @foreach($deptShares as $share)
                                        <tr>
                                            <td>{{ $share->id }}</td>
                                            <td>
                                                @if($share->recoveryCase)
                                                    <a href="{{ url('recovery/case/' . $share->recoveryCase->id . '/show') }}">{{ $share->recoveryCase->case_number ?? '0' }}</a>
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->recoveryCase && $share->recoveryCase->loan)
                                                    {{ $share->recoveryCase->loan->loan_id ?? '0' }}
                                                @else
                                                    '--'
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->recoveryCase && $share->recoveryCase->originBranch)
                                                    {{ $share->recoveryCase->originBranch->name ?? '0' }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->recoveryCase && $share->recoveryCase->client)
                                                    {{ $share->recoveryCase->client->first_name ?? '0' }} {{ $share->recoveryCase->client->last_name ?? '0' }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->recoveryCase && $share->recoveryCase->assignedSpecialist)
                                                    {{ $share->recoveryCase->assignedSpecialist->first_name ?? '0' }} {{ $share->recoveryCase->assignedSpecialist->last_name ?? '0' }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>K {{ number_format($share->dept_share_amount, 2) }}</td>
                                            <td><span class="badge bg-blue">Recovery Dept Share</span></td>
                                        </tr>
                                        @endforeach
                                        @foreach($unitShares as $share)
                                        <tr>
                                            <td>{{ $share->id }}</td>
                                            <td>N/A</td>
                                            <td>
                                                @if($share->loan)
                                                    <a href="{{ url('loan/' . $share->loan->id . '/show') }}">{{ $share->loan->loan_id ?? '0' }}</a>
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if($share->office)
                                                    {{ $share->office->name ?? '0' }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>N/A</td>
                                            <td>
                                                @if($share->user)
                                                    {{ $share->user->first_name ?? '0' }} {{ $share->user->last_name ?? '0' }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>K {{ number_format($share->amount, 2) }}</td>
                                            <td><span class="badge bg-green">Unit Share</span></td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="text-center">No data found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
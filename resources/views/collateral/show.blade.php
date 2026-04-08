@extends('layouts.master')
@section('title', 'Collateral Details')
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Collateral Details</h3>
            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-default btn-sm">Back</button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr><th>Name</th><td>{{ $collateral->name }}</td></tr>
                        <tr><th>Description</th><td>{{ $collateral->description }}</td></tr>
                        <tr><th>Type</th><td>{{ optional($collateral->type)->name }}</td></tr>
                        <tr><th>Loan</th><td>{{ optional($collateral->loan)->id }}</td></tr>
                        <tr><th>Client</th><td>{{ optional($collateral->loan->client)->first_name }} {{ optional($collateral->loan->client)->last_name }}</td></tr>
                        <tr><th>Office</th><td>{{ optional($collateral->loan->office)->name }}</td></tr>
                        <tr><th>Status</th><td>{{ ucfirst($collateral->status) }}</td></tr>
                        <tr><th>Condition</th><td>{{ ucfirst($collateral->condition) }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr><th>Initial Price</th><td>{{ number_format($collateral->initial_price, 2) }}</td></tr>
                        <tr><th>Current Worth</th><td>{{ number_format($collateral->current_worth, 2) }}</td></tr>
                        <tr><th>Date Purchased</th><td>{{ optional($collateral->date_purchased)->format('Y-m-d') }}</td></tr>
                        <tr><th>Date Resold</th><td>{{ optional($collateral->date_resold)->format('Y-m-d') }}</td></tr>
                        <tr><th>Created By</th><td>{{ optional($collateral->created_by)->first_name }} {{ optional($collateral->created_by)->last_name }}</td></tr>
                    </table>
                </div>
            </div>

            @if(Sentinel::hasAccess('collateral.update') && !Sentinel::hasAccess('collateral.approve'))
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Request Status Change</h3>
                    </div>
                    <form method="post" action="{{ route('collateral.request_change', $collateral) }}">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label>New Status</label>
                                <select name="new_status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="sold">Sold</option>
                                    <option value="defaulted">Defaulted</option>
                                    <option value="repossessed">Repossessed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Reason</label>
                                <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            @endif

            @if(Sentinel::hasAccess('collateral.approve'))
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Change Status Directly</h3>
                    </div>
                    <form method="post" action="{{ route('collateral.direct_change', $collateral) }}">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label>New Status</label>
                                <select name="new_status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="sold">Sold</option>
                                    <option value="defaulted">Defaulted</option>
                                    <option value="repossessed">Repossessed</option>
                                </select>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-warning">Change Status</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Audit History</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>IP</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collateral->auditTrail as $audit)
                                <tr>
                                    <td>{{ $audit->action }}</td>
                                    <td>{{ optional($audit->user)->first_name }} {{ optional($audit->user)->last_name }}</td>
                                    <td>{{ $audit->ip_address }}</td>
                                    <td>{{ optional($audit->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Status Change History</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Old Status</th>
                                <th>New Status</th>
                                <th>Reason</th>
                                <th>Requested By</th>
                                <th>Approved By</th>
                                <th>Request Date</th>
                                <th>Approval Date</th>
                                <th>Approval Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collateral->statusChanges as $change)
                                <tr>
                                    <td>{{ ucfirst($change->old_status) }}</td>
                                    <td>{{ ucfirst($change->new_status) }}</td>
                                    <td>{{ $change->reason }}</td>
                                    <td>{{ optional($change->requested_by)->first_name }} {{ optional($change->requested_by)->last_name }}</td>
                                    <td>{{ optional($change->approved_by)->first_name }} {{ optional($change->approved_by)->last_name }}</td>
                                    <td>{{ optional($change->request_date)->format('Y-m-d') }}</td>
                                    <td>{{ optional($change->approval_date)->format('Y-m-d') }}</td>
                                    <td>{{ ucfirst($change->approval_status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.master')
@section('title')
    Motor Vehicle Loans
@endsection

@section('footer-scripts')
<style>
    .onboarding-progress a {
        cursor: pointer;
        transition: transform 0.2s;
        display: inline-block;
    }
    .onboarding-progress a:hover {
        transform: scale(1.2);
    }
    .onboarding-progress a:hover i {
        filter: brightness(0.8);
    }
</style>
@endsection
@section('content')  

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Motor Vehicle Loans Summary</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info" style="margin-bottom: 20px;">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Total MVL Loans:</strong><br>
                                <span class="badge bg-blue">{{ $recentLoans->total() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Total Amount:</strong><br>
                                K{{ number_format($recentLoans->sum('principal'), 2) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Pending:</strong><br>
                                <span class="badge bg-yellow">{{ $recentLoans->where('status', 'pending')->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Approved:</strong><br>
                                <span class="badge bg-green">{{ $recentLoans->where('status', 'approved')->count() }}</span> loans
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">

            <div class="box box-success">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        Motor Vehicle Loans
                    </h3>
                </div>

                <div class="box-body table-responsive">

                <div class="box-body">

<form method="GET">

<div class="row">

<div class="col-md-3">
    <input type="text"
           class="form-control"
           name="search"
           placeholder="Search Loan ID or Client"
           value="{{ request('search') }}">
</div>

<div class="col-md-2">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>

        <option value="pending"
            {{ request('status')=='pending' ? 'selected' : '' }}>
            Pending
        </option>

        <option value="approved"
            {{ request('status')=='approved' ? 'selected' : '' }}>
            Approved
        </option>

        <option value="disbursed"
            {{ request('status')=='disbursed' ? 'selected' : '' }}>
            Disbursed
        </option>

        <option value="closed"
            {{ request('status')=='closed' ? 'selected' : '' }}>
            Closed
        </option>

    </select>
</div>

<div class="col-md-2">
    <input type="date"
           class="form-control"
           name="date"
           value="{{ request('date') }}">
</div>

<div class="col-md-3">

    <select name="office" class="form-control">

        <option value="">All Branches</option>

        @foreach($offices as $office)

            <option value="{{ $office->id }}"
                {{ request('office')==$office->id ? 'selected' : '' }}>
                {{ $office->name }}
            </option>

        @endforeach

    </select>

</div>

<div class="col-md-2">

    <button class="btn btn-success">
        <i class="fa fa-search"></i> Search
    </button>

    <a href="{{ url()->current() }}"
       class="btn btn-default">
       Reset
    </a>

</div>

</div>

</form>

</div>

                    <table class="table table-bordered table-striped">

                        <thead>

                        <tr>

                            <th>ID</th>
                            <th>Client</th>
                            <th>Office</th>
                            <th>Principal</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th>Onboarding Progress</th>

                        </tr>

                        </thead>

                        <tbody>

                        @forelse($recentLoans as $loan)

                            <tr>

                                <td>
                               <a href="{{ url('loan/'.$loan->id.'/show') }}">
                                               {{$loan->id}}</a>
                                </td>

                                <td>
                                    {{ optional($loan->client)->first_name }}
                                    {{ optional($loan->client)->last_name }}
                                </td>

                                    <td>
                                    {{ optional($loan->office)->name }}
                                </td>


                                <td>
                                    K{{ number_format($loan->principal,2) }}
                                </td>

                                   <td>
                                    {{$loan->created_date}}
                                </td>


                                <td>

                                    @if($loan->status == 'disbursed')
                                        <span class="label label-success">
                                            Disbursed
                                        </span>
                                    @elseif($loan->status == 'closed')
                                        <span class="label label-danger">
                                            Closed
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    @php $status = $statuses[$loan->id] ?? ['kyc_completed' => null, 'compliance_screening_completed' => null, 'ownership_completed' => null]; @endphp
                                    <div class="onboarding-progress" style="display: flex; align-items: center; gap: 8px;">
                                        <div style="text-align: center;">
                                            @if($status['kyc_completed'] !== null)
                                                <a href="{{ route('clients.edit-kyc', [$loan->client_id, $loan->id]) }}" style="text-decoration: none;" title="Go to KYC">
                                                    @if($status['kyc_completed'] === true)
                                                        <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                                                    @else
                                                        <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                                                    @endif
                                                </a>
                                            @else
                                                <span style="color: #777; font-size: 12px;">N/A</span>
                                            @endif
                                            <div style="font-size: 10px; color: #666;">KYC</div>
                                        </div>
                                        <div style="width: 1px; height: 25px; background: #ccc;"></div>
                                        <div style="text-align: center;">
                                            @if($status['compliance_screening_completed'] !== null)
                                                <a href="{{ route('motor-vehicle-loans.compliance-screening', $loan->id) }}" style="text-decoration: none;" title="Go to Compliance Screening">
                                                    @if($status['compliance_screening_completed'] === true)
                                                        <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                                                    @else
                                                        <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                                                    @endif
                                                </a>
                                            @else
                                                <span style="color: #777; font-size: 12px;">N/A</span>
                                            @endif
                                            <div style="font-size: 10px; color: #666;">Compliance</div>
                                        </div>
                                        <div style="width: 1px; height: 25px; background: #ccc;"></div>
                                        <div style="text-align: center;">
                                            @if($status['ownership_completed'] !== null)
                                                <a href="{{ route('vehicles.ownership-verification.show', $loan->vehicle_id ?? ($loan->vehicle->id ?? '')) }}" style="text-decoration: none;" title="Go to Vehicle Ownership">
                                                    @if($status['ownership_completed'] === true)
                                                        <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                                                    @else
                                                        <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                                                    @endif
                                                </a>
                                            @else
                                                <span style="color: #777; font-size: 12px;">N/A</span>
                                            @endif
                                            <div style="font-size: 10px; color: #666;">Ownership</div>
                                        </div>
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center">
                                    No vehicle loans found.
                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>
@endsection

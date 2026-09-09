@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Custody Register</h1>
</section>

<section class="content">

<div class="box">

    <div class="box-header">
        <h3 class="box-title">Custody Records</h3>
    </div>

    <div class="box-body">

        <table class="table table-bordered table-striped">

            <thead>
            <tr>
                <th>Vehicle</th>
                <th>Loan Consultant</th>
                <th>Received By</th>
                <th>Inspector</th>
                <th>Valuator</th>
                <th>Custodian</th>
                <th>Garage Name</th>
                <th>Status</th>
                <th>Storage Start Date</th>
                <th>Approved</th>
            </tr>
            </thead>

            <tbody>

            @forelse($custodies as $custody)
            <tr>
                <td>
                    @if($custody->vehicle)
                        <a href="{{ url('vehicles/'.$custody->vehicle->id) }}">{{ $custody->vehicle->registration_number }}</a>
                    @else
                        {{ optional($custody->vehicle)->registration_number ?? 'N/A' }}
                    @endif
                </td>
                <td>
                    @php
                        $loan = optional($custody->vehicle)->loan;
                    @endphp
                    @if(!empty($loan) && !empty($loan->loanConsultant))
                        {{ $loan->loanConsultant->first_name }} {{ $loan->loanConsultant->last_name }}
                        <br><small class="text-muted">ID: {{ $loan->loanConsultant->id }}</small>
                    @endif
                </td>
                <td>
                    @if(!empty($custody->receiver))
                        {{ $custody->receiver->first_name }} {{ $custody->receiver->last_name }}
                    @endif
                </td>
                <td>
                    @php
                        $latestInspection = null;
                        if (!empty($custody->vehicle) && $custody->vehicle->relationLoaded('inspections')) {
                            $latestInspection = $custody->vehicle->inspections->sortByDesc('inspection_date')->first();
                        }
                    @endphp
                    @if(!empty($latestInspection))
                        {{ $latestInspection->inspector }}
                    @endif
                </td>
                <td>
                    @php
                        $latestValuation = null;
                        if (!empty($custody->vehicle) && $custody->vehicle->relationLoaded('valuations')) {
                            $latestValuation = $custody->vehicle->valuations->sortByDesc('valuation_date')->first();
                        }
                    @endphp
                    @if(!empty($latestValuation) && !empty($latestValuation->valuator))
                        {{ $latestValuation->valuator->first_name }} {{ $latestValuation->valuator->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($custody->receiver))
                        {{ $custody->receiver->first_name }} {{ $custody->receiver->last_name }}
                    @endif
                </td>
                <td>{{ $custody->garage_name ?? 'N/A' }}</td>
                <td>{{ ucfirst($custody->status ?? 'N/A') }}</td>
                <td>{{ $custody->received_at ? \Carbon\Carbon::parse($custody->received_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                <td>
                    @if($custody->custody_approved)
                        <span class="label label-success">Yes</span>
                    @else
                        <span class="label label-danger">No</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No custody records found.</td>
            </tr>
            @endforelse

            </tbody>

        </table>

    </div>

</div>

</section>

@endsection

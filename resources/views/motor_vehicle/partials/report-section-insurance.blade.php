<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-shield-alt"></i> Insurance Policies</h3>
        @if($vehicle->insurancePolicies && $vehicle->insurancePolicies->isNotEmpty())
            <div class="report-meta">{{ $vehicle->insurancePolicies->count() }} policy/policies on record</div>
        @endif
    </div>

    @if($vehicle->insurancePolicies && $vehicle->insurancePolicies->isNotEmpty())
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width:15%; text-align:left;">Insurer</th>
                    <th style="width:15%; text-align:left;">Policy #</th>
                    <th style="width:12%; text-align:left;">Start Date</th>
                    <th style="width:12%; text-align:left;">Expiry Date</th>
                    <th style="width:12%; text-align:right;">Insured Value</th>
                    <th style="width:12%; text-align:right;">Premium</th>
                    <th style="width:12%; text-align:left;">Cover Type</th>
                    <th style="width:10%; text-align:left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->insurancePolicies as $policy)
                <tr>
                    <td>{{ $policy->insurer_name ?? 'N/A' }}</td>
                    <td>{{ $policy->policy_number ?? 'N/A' }}</td>
                    <td>{{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $policy->expiry_date ? \Carbon\Carbon::parse($policy->expiry_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td style="text-align:right;">K{{ number_format($policy->insured_value ?? 0, 2) }}</td>
                    <td style="text-align:right;">K{{ number_format($policy->premium ?? 0, 2) }}</td>
                    <td>{{ $policy->cover_type ?? 'N/A' }}</td>
                    <td>
                        @php
                            $expiry = $policy->expiry_date ? \Carbon\Carbon::parse($policy->expiry_date) : null;
                            $now = \Carbon\Carbon::now();
                        @endphp
                        @if(!$expiry)
                            <span class="status-badge" style="background:#e2e8f0; color:#64748b;">N/A</span>
                        @elseif($expiry->isPast())
                            <span class="status-badge status-flagged">Expired</span>
                        @elseif($expiry->diffInDays($now) <= 30)
                            <span class="status-badge status-pending">Expiring {{ $expiry->diffInDays($now) }}d</span>
                        @else
                            <span class="status-badge status-cleared">Active</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No insurance policies recorded for this vehicle.</p>
    @endif
</div>

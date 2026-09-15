<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-calculator"></i> Valuation History</h3>
        @if($vehicle->valuations && $vehicle->valuations->isNotEmpty())
            <div class="report-meta">{{ $vehicle->valuations->count() }} valuation(s) recorded</div>
        @endif
    </div>

    @if($vehicle->valuations && $vehicle->valuations->isNotEmpty())
        <table class="report-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Date</th>
                    <th style="text-align:left;">Company</th>
                    <th style="text-align:left;">Valuator</th>
                    <th style="text-align:right;">Market Value</th>
                    <th style="text-align:right;">Forced Sale</th>
                    <th style="text-align:right;">Valuation Cost</th>
                    <th style="text-align:left;">Expiry</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->valuations as $valuation)
                <tr>
                    <td>{{ $valuation->valuation_date ? \Carbon\Carbon::parse($valuation->valuation_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $valuation->valuation_company ?? 'N/A' }}</td>
                    <td>{{ $valuation->valuator_name ?? optional($valuation->valuator)->first_name ? (optional($valuation->valuator)->first_name . ' ' . optional($valuation->valuator)->last_name) : 'N/A' }}</td>
                    <td style="text-align:right;">K{{ number_format($valuation->market_value ?? 0, 2) }}</td>
                    <td style="text-align:right;">K{{ number_format($valuation->forced_sale_value ?? 0, 2) }}</td>
                    <td style="text-align:right;">K{{ number_format($valuation->valuation_cost ?? 0, 2) }}</td>
                    <td>{{ $valuation->expiry_date ? \Carbon\Carbon::parse($valuation->expiry_date)->format('Y-m-d') : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No valuations recorded.</p>
    @endif
</div>

<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-info-circle"></i> Vehicle Information</h3>
        <div class="report-meta">Vehicle ID: {{ $vehicle->id }} | Vehicle Code: {{ $vehicle->vehicle_code ?? 'N/A' }}</div>
    </div>
    <table class="report-table">
        <tr>
            <th class="label-cell">Make</th>
            <td>{{ $vehicle->make ?? 'N/A' }}</td>
            <th class="label-cell">Model</th>
            <td>{{ $vehicle->model ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Registration Number</th>
            <td>{{ $vehicle->registration_number ?? 'N/A' }}</td>
            <th class="label-cell">Year</th>
            <td>{{ $vehicle->year ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Engine Number</th>
            <td>{{ $vehicle->engine_number ?? 'N/A' }}</td>
            <th class="label-cell">Chassis Number</th>
            <td>{{ $vehicle->chassis_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Color</th>
            <td>{{ $vehicle->color ?? 'N/A' }}</td>
            <th class="label-cell">Mileage</th>
            <td>{{ $vehicle->mileage ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Fuel Type</th>
            <td>{{ $vehicle->fuel_type ?? 'N/A' }}</td>
            <th class="label-cell">Transmission</th>
            <td>{{ $vehicle->transmission ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Market Value</th>
            <td>K{{ number_format($vehicle->market_value ?? 0, 2) }}</td>
            <th class="label-cell">Forced Sale Value</th>
            <td>K{{ number_format($vehicle->forced_sale_value ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th class="label-cell">Ownership Type</th>
            <td>{{ $vehicle->ownership_type ?? 'N/A' }}</td>
            <th class="label-cell">Registered Owner</th>
            <td>{{ $vehicle->registered_owner ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Status</th>
            <td>
                @if($vehicle->status == 'sold')
                    <span class="status-badge status-sold">Sold</span>
                @elseif($vehicle->status == 'in_custody' || $vehicle->custody)
                    <span class="status-badge status-in-custody">In Custody</span>
                @else
                    <span class="status-badge status-available">Available</span>
                @endif
            </td>
            <th class="label-cell">Created</th>
            <td>{{ $vehicle->created_at ? $vehicle->created_at->format('d M Y') : 'N/A' }}</td>
        </tr>
    </table>
</div>

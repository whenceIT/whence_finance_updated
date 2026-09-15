<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-warehouse"></i> Vehicle Custody</h3>
    </div>

    @if($vehicle->custody)
        <table class="report-table">
            <tr>
                <th class="label-cell">Custody Status</th>
                <td>
                    <span class="status-badge" style="background:#ede9fe; color:#5b21b6;">{{ ucfirst($vehicle->custody->status ?? 'N/A') }}</span>
                </td>
                <th class="label-cell">Approved</th>
                <td>
                    @if($vehicle->custody->custody_approved)
                        <span class="status-badge status-cleared">Yes</span>
                    @else
                        <span class="status-badge status-pending">No</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="label-cell">Received Date</th>
                <td>{{ $vehicle->custody->received_at ? $vehicle->custody->received_at->format('d M Y') : 'N/A' }}</td>
                <th class="label-cell">Keys Received</th>
                <td>{{ $vehicle->custody->keys_received ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th class="label-cell">Received By</th>
                <td>
                    {{ optional($vehicle->custody->receiver)->first_name ?? '' }}
                    {{ optional($vehicle->custody->receiver)->last_name ?? '' }}
                </td>
                <th class="label-cell">Key Tag Numbers</th>
                <td>{{ $vehicle->custody->key_tag_numbers ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th class="label-cell">Intake Date</th>
                <td>{{ $vehicle->custody->intake_date ? $vehicle->custody->intake_date->format('d M Y') : 'N/A' }}</td>
                <th class="label-cell">Fuel Level</th>
                <td>{{ $vehicle->custody->fuel_level ?? 'N/A' }}</td>
            </tr>
        </table>
    @else
        <p class="text-muted">This vehicle is not in custody.</p>
    @endif

    <div class="report-header" style="margin-top: 20px;">
        <h3 class="report-title" style="font-size: 16px; color: #334159;">Garage / Storage Facility</h3>
    </div>

    @if($vehicle->custody && $vehicle->custody->garage_name)
        <table class="report-table">
            <tr>
                <th class="label-cell">Garage Name</th>
                <td>{{ $vehicle->custody->garage_name ?? 'N/A' }}</td>
                <th class="label-cell">Location</th>
                <td>{{ $vehicle->custody->garage_location ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th class="label-cell">GPS Coordinates</th>
                <td>{{ $vehicle->custody->garage_gps ?? 'N/A' }}</td>
                <th class="label-cell">Parking Bay</th>
                <td>{{ $vehicle->custody->parking_bay ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th class="label-cell">Contact Person</th>
                <td>{{ $vehicle->custody->garage_contact_person ?? 'N/A' }}</td>
                <th class="label-cell">Phone</th>
                <td>{{ $vehicle->custody->garage_contact_phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th class="label-cell">Storage Period</th>
                <td>
                    From: {{ $vehicle->custody->storage_start_date ? \Carbon\Carbon::parse($vehicle->custody->storage_start_date)->format('d M Y') : 'N/A' }}
                </td>
                <th class="label-cell">To</th>
                <td>
                    {{ $vehicle->custody->storage_end_date ? \Carbon\Carbon::parse($vehicle->custody->storage_end_date)->format('d M Y') : 'N/A' }}
                </td>
            </tr>
            <tr>
                <th class="label-cell">Documents Received</th>
                <td>{{ $vehicle->custody->documents_received ? 'Yes' : 'No' }}</td>
                <th class="label-cell">Accessories Received</th>
                <td>{{ $vehicle->custody->accessories_received ? 'Yes' : 'No' }}</td>
            </tr>
            @if($vehicle->custody->remarks)
            <tr>
                <th class="label-cell">Remarks</th>
                <td colspan="3">{{ $vehicle->custody->remarks }}</td>
            </tr>
            @endif
        </table>
    @else
        <p class="text-muted">No garage/storage facility assigned.</p>
    @endif
</div>

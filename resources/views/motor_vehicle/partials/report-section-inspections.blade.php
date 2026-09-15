<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-clipboard-check"></i> Inspection History</h3>
        @if($vehicle->inspections && $vehicle->inspections->isNotEmpty())
            <div class="report-meta">{{ $vehicle->inspections->count() }} inspection(s) recorded</div>
        @endif
    </div>

    @if($vehicle->inspections && $vehicle->inspections->isNotEmpty())
        @foreach($vehicle->inspections as $inspection)
            <div style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 8px 0; color: #000c3c;">
                    {{ $inspection->inspection_date ? \Carbon\Carbon::parse($inspection->inspection_date)->format('d M Y') : 'N/A' }}
                    &mdash; {{ ucfirst($inspection->inspection_type ?? 'Inspection') }}
                </h4>
                <table class="report-table" style="margin-bottom: 0;">
                    <tr>
                        <th class="label-cell">Inspector</th>
                        <td>{{ $inspection->inspector ?? 'N/A' }}</td>
                        <th class="label-cell">Mileage</th>
                        <td>{{ $inspection->mileage ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="label-cell">Result</th>
                        <td>
                            @if($inspection->result == 'Passed')
                                <span class="status-badge status-cleared">Passed</span>
                            @elseif($inspection->result == 'Failed')
                                <span class="status-badge status-flagged">Failed</span>
                            @else
                                <span class="status-badge status-pending">{{ $inspection->result ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <th class="label-cell">Condition Score</th>
                        <td>{{ $inspection->condition_score ?? 'N/A' }} @if($inspection->condition_score) / 100 @endif</td>
                    </tr>
                    @if($inspection->report_file_path)
                    <tr>
                        <th class="label-cell">Inspection Report</th>
                        <td colspan="3">
                            <a href="{{ $inspection->report_file_path }}" target="_blank" class="btn btn-xs btn-primary">
                                <i class="fa fa-file-pdf-o"></i> View Report
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>

                <div style="margin-top: 8px; font-size: 12px; color: #475569;">
                    @if($inspection->mechanical_condition)
                        <strong>Mechanical:</strong> {{ $inspection->mechanical_condition }}<br>
                    @endif
                    @if($inspection->interior_condition)
                        <strong>Interior:</strong> {{ $inspection->interior_condition }}<br>
                    @endif
                    @if($inspection->exterior_condition)
                        <strong>Exterior:</strong> {{ $inspection->exterior_condition }}<br>
                    @endif
                    @if($inspection->tyres_condition)
                        <strong>Tyres:</strong> {{ $inspection->tyres_condition }}<br>
                    @endif
                    @if($inspection->battery_condition)
                        <strong>Battery:</strong> {{ $inspection->battery_condition }}
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted">No inspections recorded.</p>
    @endif
</div>

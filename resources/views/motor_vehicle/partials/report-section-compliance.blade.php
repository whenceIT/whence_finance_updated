<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-shield-alt"></i> PEP & Sanctions Screening</h3>
        @if($loan->complianceScreenings && $loan->complianceScreenings->isNotEmpty())
            <div class="report-meta">{{ $loan->complianceScreenings->count() }} screening(s) recorded</div>
        @endif
    </div>

    @if($loan->complianceScreenings && $loan->complianceScreenings->isNotEmpty())
        @php $latest = $loan->complianceScreenings->sortByDesc('screening_date')->first(); @endphp
        <table class="report-table">
            <tr>
                <th class="label-cell">PEP Result</th>
                <td>{{ $latest->pep_result ?? 'N/A' }}</td>
                <th class="label-cell">Sanctions Result</th>
                <td>{{ $latest->sanctions_result ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th class="label-cell">Screening Date</th>
                <td>{{ $latest->screening_date ? \Carbon\Carbon::parse($latest->screening_date)->format('d M Y') : 'N/A' }}</td>
                <th class="label-cell">Match Level</th>
                <td>
                    @if($latest->match_level == 'low')
                        <span class="status-badge status-cleared">Low</span>
                    @elseif($latest->match_level == 'medium')
                        <span class="status-badge status-pending">Medium</span>
                    @elseif($latest->match_level == 'high')
                        <span class="status-badge status-flagged">High</span>
                    @else
                        {{ $latest->match_level ?? 'N/A' }}
                    @endif
                </td>
            </tr>
            <tr>
                <th class="label-cell">Overall Status</th>
                <td>
                    @if($latest->status == 'cleared')
                        <span class="status-badge status-cleared">Cleared</span>
                    @elseif($latest->status == 'flagged')
                        <span class="status-badge status-flagged">Flagged</span>
                    @elseif($latest->status == 'pending')
                        <span class="status-badge status-pending">Pending</span>
                    @elseif($latest->status == 'requires_review')
                        <span class="status-badge" style="background:#dbeafe; color:#1e40af;">Requires Review</span>
                    @else
                        {{ $latest->status ?? 'N/A' }}
                    @endif
                </td>
                <th class="label-cell">Screening Officer</th>
                <td>
                    {{ optional($latest->screeningOfficer)->first_name ?? '' }}
                    {{ optional($latest->screeningOfficer)->last_name ?? '' }}
                </td>
            </tr>
            @if($latest->comments)
            <tr>
                <th class="label-cell">Comments</th>
                <td colspan="3">{{ $latest->comments }}</td>
            </tr>
            @endif
            @if($latest->supporting_evidence)
            <tr>
                <th class="label-cell">Supporting Evidence</th>
                <td colspan="3">
                    <a href="{{ $latest->supporting_evidence }}" target="_blank" class="btn btn-xs btn-primary">
                        <i class="fa fa-file-pdf-o"></i> View Document
                    </a>
                </td>
            </tr>
            @endif
        </table>

        @if($loan->complianceScreenings->count() > 1)
            <div style="margin-top: 16px; font-size: 13px; color: #64748b;">
                <strong>History:</strong> {{ $loan->complianceScreenings->count() - 1 }} previous screening(s) on record.
            </div>
        @endif
    @else
        <p class="text-muted">No compliance screening has been recorded.</p>
    @endif
</div>

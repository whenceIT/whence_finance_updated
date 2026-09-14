<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-file-contract"></i> Ownership Verification</h3>
        <div class="report-meta">{{ $vehicle->ownershipRecords->count() ?? 0 }} ownership record(s)</div>
    </div>

    @if($vehicle->ownershipRecords && $vehicle->ownershipRecords->isNotEmpty())
        @foreach($vehicle->ownershipRecords as $record)
            <div style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 12px 0; color: #000c3c; font-size: 16px;">
                    @if($record->ownership_type == 'individual')
                        Individual Ownership
                    @elseif($record->ownership_type == 'letter_of_sale')
                        Letter of Sale
                    @elseif($record->ownership_type == 'company')
                        Company / Corporate
                    @else
                        {{ ucfirst($record->ownership_type ?? 'Ownership') }}
                    @endif
                </h4>

                @if($record->ownership_type == 'individual' || $record->ownership_type == 'letter_of_sale')
                    <table class="report-table">
                        <tr>
                            <th class="label-cell">Registered Owner</th>
                            <td>{{ $record->registered_owner ?? $vehicle->registered_owner ?? 'N/A' }}</td>
                            <th class="label-cell">Ownership Documents</th>
                            <td>
                                @if($record->ownership_documents_path)
                                    <a href="{{ $record->ownership_documents_path }}" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="fa fa-file-pdf-o"></i> View
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @if($record->ownership_type == 'letter_of_sale')
                        <tr>
                            <th class="label-cell">Seller Name</th>
                            <td>{{ $record->seller_name ?? 'N/A' }}</td>
                            <th class="label-cell">Seller NRC</th>
                            <td>{{ $record->seller_nrc ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="label-cell">Seller Phone</th>
                            <td>{{ $record->seller_phone ?? 'N/A' }}</td>
                            <th class="label-cell">Letter of Sale</th>
                            <td>
                                @if($record->ownership_documents_path)
                                    <a href="{{ $record->ownership_documents_path }}" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="fa fa-file-pdf-o"></i> View
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="label-cell">Witness 1</th>
                            <td>{{ $record->witness_1_name ?? 'N/A' }} (NRC: {{ $record->witness_1_nrc ?? 'N/A' }})</td>
                            <th class="label-cell">Witness 2</th>
                            <td>{{ $record->witness_2_name ?? 'N/A' }} (NRC: {{ $record->witness_2_nrc ?? 'N/A' }})</td>
                        </tr>
                        @endif
                        <tr>
                            <th class="label-cell">Verified</th>
                            <td>
                                @if($record->verified)
                                    <span class="status-badge status-cleared">Yes</span>
                                @else
                                    <span class="status-badge status-pending">No</span>
                                @endif
                            </td>
                            <th class="label-cell">Verified By</th>
                            <td>
                                {{ optional($record->verifiedBy)->first_name ?? '' }}
                                {{ optional($record->verifiedBy)->last_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="label-cell">Verified At</th>
                            <td>{{ $record->verified_at ? \Carbon\Carbon::parse($record->verified_at)->format('d M Y') : 'N/A' }}</td>
                            <th class="label-cell">Created</th>
                            <td>{{ $record->created_at ? $record->created_at->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                @endif

                @if($record->ownership_type == 'company')
                    <table class="report-table">
                        <tr>
                            <th class="label-cell">Company Name</th>
                            <td>{{ $record->company_name ?? 'N/A' }}</td>
                            <th class="label-cell">Registration #</th>
                            <td>{{ $record->company_registration ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="label-cell">Directors</th>
                            <td colspan="3">
                                @if($record->directors && is_array($record->directors))
                                    @foreach($record->directors as $director)
                                        <div>{{ $director }}</div>
                                    @endforeach
                                @else
                                    {{ $record->directors ?? 'N/A' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="label-cell">Authorized Representative</th>
                            <td>{{ $record->authorized_representative ?? 'N/A' }}</td>
                            <th class="label-cell">Authorized Rep NRC</th>
                            <td>{{ $record->authorized_representative_nrc ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="label-cell">Ownership Documents</th>
                            <td colspan="3">
                                @if($record->ownership_documents_path)
                                    <a href="{{ $record->ownership_documents_path }}" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="fa fa-file-pdf-o"></i> View
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="label-cell">Verified</th>
                            <td>
                                @if($record->verified)
                                    <span class="status-badge status-cleared">Yes</span>
                                @else
                                    <span class="status-badge status-pending">No</span>
                                @endif
                            </td>
                            <th class="label-cell">Verified By</th>
                            <td>
                                {{ optional($record->verifiedBy)->first_name ?? '' }}
                                {{ optional($record->verifiedBy)->last_name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="label-cell">Verified At</th>
                            <td>{{ $record->verified_at ? \Carbon\Carbon::parse($record->verified_at)->format('d M Y') : 'N/A' }}</td>
                            <th class="label-cell">Created</th>
                            <td>{{ $record->created_at ? $record->created_at->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                @endif
            </div>
        @endforeach
    @else
        <p class="text-muted">No ownership verification records found.</p>
    @endif
</div>

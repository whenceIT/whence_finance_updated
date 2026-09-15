<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-folder"></i> Documents</h3>
        @if($vehicle->documents && $vehicle->documents->isNotEmpty())
            <div class="report-meta">{{ $vehicle->documents->count() }} document(s) on file</div>
        @endif
    </div>

    @if($vehicle->documents && $vehicle->documents->isNotEmpty())
        <table class="report-table">
            <thead>
                <tr>
                    <th style="text-align:left; width:25%;">Type</th>
                    <th style="text-align:left;">File</th>
                    <th style="text-align:left; width:15%;">Uploaded</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->documents as $document)
                <tr>
                    <td>{{ $document->document_type ?? 'N/A' }}</td>
                    <td>
                        @if($document->document_file)
                            <a href="{{ $document->document_file }}" target="_blank" class="btn btn-xs btn-primary">
                                <i class="fa fa-file-pdf-o"></i> View Document
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $document->created_at ? $document->created_at->format('d M Y') : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No documents uploaded.</p>
    @endif
</div>

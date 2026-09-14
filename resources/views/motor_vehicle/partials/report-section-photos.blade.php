<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-image"></i> Vehicle Photos</h3>
        @if($vehicle->photos && $vehicle->photos->isNotEmpty())
            <div class="report-meta">{{ $vehicle->photos->count() }} photo(s) on record</div>
        @endif
    </div>

    @if($vehicle->photos && $vehicle->photos->isNotEmpty())
        <div class="photo-gallery">
            @foreach($vehicle->photos as $photo)
                <div style="text-align: center;">
                    <img src="{{ $photo->photo_url }}" class="photo-thumb" alt="{{ $photo->caption ?? 'Vehicle photo' }}">
                    <div style="font-size: 10px; color: #64748b; margin-top: 4px;">
                        {{ $photo->photo_type ?? 'Photo' }}
                        @if($photo->caption)
                            <br><span style="font-style: italic;">{{ $photo->caption }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No photos uploaded.</p>
    @endif
</div>

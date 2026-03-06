<div style="margin-bottom: 20px; padding: 0;">
    <ol class="breadcrumb" style="background: transparent; margin: 0; padding: 0;">
        @foreach($breadcrumb as $item)
            @if($loop->last)
                <li class="breadcrumb-item active" style="color: var(--text-secondary);">{{ $item['label'] }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}" style="color: var(--primary-color); text-decoration: none;">{{ $item['label'] }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</div>

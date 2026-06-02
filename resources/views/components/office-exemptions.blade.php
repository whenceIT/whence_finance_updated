@if($settings)
<div class="card" style="margin: 20px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div class="card-header" style="cursor: pointer; background: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 12px 16px;" onclick="$(this).next().slideToggle(); $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');">
        <i class="fa fa-building" style="color: #495057;"></i> 
        <span style="font-weight: 600; color: #343a40; margin-left: 8px;">{{ $title ?? 'Office Exemptions' }}</span>
        <i class="fa fa-chevron-down" style="float: right; color: #6c757d;"></i>
    </div>
    <div class="card-body" id="office-exemptions-body" style="padding: 16px; display: none;">
        <div id="office-detail-card">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($exemptions as $exemption)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 8px; background: {{ $exemption['color'] }}15; border-left: 4px solid {{ $exemption['color'] }};">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #343a40; font-size: 14px; margin-bottom: 4px;">{{ $exemption['title'] }}</div>
                            <div style="color: #6c757d; font-size: 13px;">{{ $exemption['description'] }}</div>
                        </div>
                        <div style="text-align: center; min-width: 100px;">
                            <span style="background: {{ $exemption['color'] }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                {{ $exemption['enabled'] ? 'ENABLED' : 'DISABLED' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
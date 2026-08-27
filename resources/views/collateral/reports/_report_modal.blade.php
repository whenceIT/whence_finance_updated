@php
    $reportList = app(\App\Services\CollateralService::class)->getReports();
@endphp

<div id="reportsBackdrop" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1040;"></div>
<div id="reportsModal" style="display:none; position: fixed; left: 0; right: 0; bottom: 0; z-index: 1050; background: #fff; border-top-left-radius: 14px; border-top-right-radius: 14px; box-shadow: 0 -4px 24px rgba(0,0,0,0.2); max-height: 70vh; overflow-y: auto; transform: translateY(100%); transition: transform 0.25s ease; will-change: transform;">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #eef0f4;">
        <div>
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #2c3e50;">Generate Collateral Report</h3>
            <small style="color: #6b7280;">Select a report to configure and generate</small>
        </div>
        <button type="button" class="btn btn-sm btn-default" id="close-reports-modal"><i class="fa fa-times"></i></button>
    </div>
    <div class="row" style="padding: 16px 20px;">
        @foreach($reportList as $key => $r)
            <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 14px;">
                <a href="{{ route('collateral.reports.form', $key) }}" style="display: block; text-decoration: none; background: #f7f9fc; border: 1px solid #e6e9ef; border-radius: 10px; padding: 16px; height: 100%; transition: all 0.2s;">
                    <div style="font-size: 22px; color: #667eea; margin-bottom: 8px;"><i class="fa {{ $r['icon'] }}"></i></div>
                    <h4 style="margin: 0 0 6px; font-size: 14px; font-weight: 700; color: #2c3e50;">{{ $r['label'] }}</h4>
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ $r['description'] }}</p>
                </a>
            </div>
        @endforeach
    </div>
</div>

<script>
    $(document).ready(function() {
        var $modal = $('#reportsModal');
        var $backdrop = $('#reportsBackdrop');
        function openReportsModal() {
            $backdrop.fadeIn(150);
            $modal.css('display', 'block');
            setTimeout(function() {
                $modal.css('transform', 'translateY(0)');
            }, 10);
        }
        function closeReportsModal() {
            $modal.css('transform', 'translateY(100%)');
            $backdrop.fadeOut(200);
            setTimeout(function() { $modal.css('display', 'none'); }, 250);
        }
        $('#open-reports-modal').on('click', openReportsModal);
        $('#close-reports-modal').on('click', closeReportsModal);
        $backdrop.on('click', closeReportsModal);
    });
</script>

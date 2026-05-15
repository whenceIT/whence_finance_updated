{{-- Performance-Linked Learning Pusher Pop-up --}}
{{-- Slides in from bottom-left when there are performance-linked recommendations --}}
@php
    $perfUser = Sentinel::getUser();
    $perfRecommendations = [];
    if ($perfUser) {
        try {
            $perfRecommendations = \App\Services\TrainingHubPerformancePusher::getRecommendationsForUser($perfUser->id);
        } catch (\Exception $e) {
            $perfRecommendations = [];
        }
    }
@endphp

@if(!empty($perfRecommendations))
<style>
    /* ── Performance Pusher Pop-up ── */
    .perf-pusher-container {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 10000;
        max-width: 380px;
        width: calc(100vw - 40px);
        font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Collapsed pill trigger */
    .perf-pusher-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #fff;
        padding: 12px 18px;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: perfPillSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        transform: translateX(-120%);
        user-select: none;
    }

    .perf-pusher-pill:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }

    .perf-pusher-pill .pill-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00a04a, #00c853);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        animation: perfPulse 2s ease-in-out infinite;
    }

    .perf-pusher-pill .pill-text {
        flex: 1;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.3;
    }

    .perf-pusher-pill .pill-text small {
        display: block;
        font-weight: 400;
        opacity: 0.7;
        font-size: 11px;
    }

    .perf-pusher-pill .pill-badge {
        background: #ff4444;
        color: #fff;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .perf-pusher-pill .pill-close {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
        transition: background 0.2s;
    }

    .perf-pusher-pill .pill-close:hover {
        background: rgba(255, 77, 77, 0.5);
    }

    /* Expanded panel */
    .perf-pusher-panel {
        display: none;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 16px 64px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: perfPanelSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        max-height: 70vh;
        overflow-y: auto;
    }

    .perf-pusher-panel.active {
        display: block;
    }

    .perf-panel-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #fff;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .perf-panel-header h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .perf-panel-header h4 i {
        color: #00c853;
    }

    .perf-panel-close {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.2s;
    }

    .perf-panel-close:hover {
        background: rgba(255, 77, 77, 0.5);
    }

    .perf-panel-body {
        padding: 0;
    }

    /* Recommendation group */
    .perf-rec-group {
        border-bottom: 1px solid #f0f0f0;
        padding: 14px 18px;
    }

    .perf-rec-group:last-child {
        border-bottom: none;
    }

    .perf-rec-label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .perf-rec-label .rec-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #fff;
        flex-shrink: 0;
    }

    .perf-rec-label span {
        font-size: 13px;
        font-weight: 700;
        color: #333;
    }

    .perf-rec-message {
        font-size: 12px;
        color: #666;
        line-height: 1.5;
        margin-bottom: 10px;
        padding-left: 36px;
    }

    /* Upload / Topic cards */
    .perf-material-list {
        padding-left: 36px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .perf-material-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 10px;
        text-decoration: none;
        color: #333;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .perf-material-card:hover {
        background: #e8f5e9;
        border-color: #00a04a;
        transform: translateX(3px);
        text-decoration: none;
        color: #333;
    }

    .perf-material-card .mat-thumb {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
    }

    .perf-material-card .mat-thumb img {
        width: 100%;
        height: 100%;
        border-radius: 8px;
        object-fit: cover;
    }

    .perf-material-card .mat-info {
        flex: 1;
        min-width: 0;
    }

    .perf-material-card .mat-info .mat-name {
        font-size: 12px;
        font-weight: 600;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .perf-material-card .mat-info .mat-type {
        font-size: 10px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .perf-material-card .mat-arrow {
        color: #ccc;
        font-size: 12px;
        flex-shrink: 0;
        transition: color 0.2s;
    }

    .perf-material-card:hover .mat-arrow {
        color: #00a04a;
    }

    /* See all link */
    .perf-see-all {
        display: block;
        text-align: center;
        padding: 12px;
        color: #00a04a;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border-top: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .perf-see-all:hover {
        background: #f0faf4;
        text-decoration: none;
        color: #008a3f;
    }

    /* Animations */
    @keyframes perfPillSlideIn {
        0%   { transform: translateX(-120%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }

    @keyframes perfPanelSlideUp {
        0%   { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }

    @keyframes perfPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(0, 200, 83, 0.4); }
        50%      { box-shadow: 0 0 0 8px rgba(0, 200, 83, 0); }
    }

    /* Hide on very small screens if needed */
    @media (max-width: 480px) {
        .perf-pusher-container {
            max-width: calc(100vw - 24px);
            left: 12px;
            bottom: 12px;
        }
    }
</style>

<div class="perf-pusher-container" id="perfPusherContainer">
    {{-- Collapsed pill --}}
    <div class="perf-pusher-pill" id="perfPusherPill">
        <div class="pill-icon">
            <i class="fa fa-graduation-cap"></i>
        </div>
        <div class="pill-text">
            Recommended for You
            <small>Based on your performance</small>
        </div>
        <div class="pill-badge">{{ count($perfRecommendations) }}</div>
        <div class="pill-close" id="perfPusherDismiss" title="Dismiss">
            <i class="fa fa-times"></i>
        </div>
    </div>

    {{-- Expanded panel --}}
    <div class="perf-pusher-panel" id="perfPusherPanel">
        <div class="perf-panel-header">
            <h4><i class="fa fa-graduation-cap"></i> Recommended Training</h4>
            <button class="perf-panel-close" id="perfPanelClose" title="Close">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="perf-panel-body">
            @foreach($perfRecommendations as $rec)
                <div class="perf-rec-group">
                    <div class="perf-rec-label">
                        <div class="rec-icon" style="background: {{ $rec['color'] }};">
                            <i class="fa {{ $rec['icon'] }}"></i>
                        </div>
                        <span>{{ $rec['label'] }}</span>
                    </div>
                    <div class="perf-rec-message">
                        {{ \Illuminate\Support\Str::limit($rec['message'], 150) }}
                    </div>
                    <div class="perf-material-list">
                        @foreach($rec['uploads'] as $upload)
                            <a href="{{ url('/learning/general-uploads/' . $upload->id) }}"
                               class="perf-material-card">
                                <div class="mat-thumb">
                                    @if($upload->poster)
                                        <img src="{{ $upload->poster }}" alt="">
                                    @else
                                        <i class="fa {{ $upload->icon }}"></i>
                                    @endif
                                </div>
                                <div class="mat-info">
                                    <div class="mat-name">{{ $upload->name }}</div>
                                    <div class="mat-type">{{ $upload->type_label ?? $upload->type }}</div>
                                </div>
                                <div class="mat-arrow">
                                    <i class="fa fa-chevron-right"></i>
                                </div>
                            </a>
                        @endforeach
                        @foreach($rec['topics'] as $topic)
                            <a href="{{ url('/learning?category=' . urlencode($topic->name)) }}"
                               class="perf-material-card">
                                <div class="mat-thumb" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                                    @if($topic->poster)
                                        <img src="{{ $topic->poster }}" alt="">
                                    @else
                                        <i class="fa fa-folder-open"></i>
                                    @endif
                                </div>
                                <div class="mat-info">
                                    <div class="mat-name">{{ $topic->name }}</div>
                                    <div class="mat-type">Topic</div>
                                </div>
                                <div class="mat-arrow">
                                    <i class="fa fa-chevron-right"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <a href="{{ url('/learning') }}" class="perf-see-all">
            <i class="fa fa-external-link"></i> Open Training Hub
        </a>
    </div>
</div>

<script>
    $(document).ready(function () {
        var PERF_DISMISS_KEY = 'perfPusherDismissed_{{ date('Y-m-d') }}';
        var $container = $('#perfPusherContainer');
        var $pill      = $('#perfPusherPill');
        var $panel     = $('#perfPusherPanel');

        // Don't show if dismissed today
        if (localStorage.getItem(PERF_DISMISS_KEY)) {
            $container.hide();
            return;
        }

        // Show the pill after a delay
        setTimeout(function () {
            $pill.show();
        }, 5000);

        // Click pill → expand panel
        $pill.on('click', function (e) {
            if ($(e.target).closest('#perfPusherDismiss').length) return;
            $pill.hide();
            $panel.addClass('active');
        });

        // Close panel → show pill again
        $('#perfPanelClose').on('click', function () {
            $panel.removeClass('active');
            $pill.css({ animation: 'none', transform: 'translateX(0)', opacity: 1 }).show();
        });

        // Dismiss entirely (hides for today)
        $('#perfPusherDismiss').on('click', function (e) {
            e.stopPropagation();
            localStorage.setItem(PERF_DISMISS_KEY, '1');
            $container.fadeOut(300);
        });
    });
</script>
@endif

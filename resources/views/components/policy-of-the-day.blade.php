@php
    $policyOfTheDay = \App\Models\PolicyOfTheDay::getTodaysPolicy();
@endphp

@if($policyOfTheDay)
<style>
    .potd-container {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 380px;
        z-index: 1050;
        opacity: 1;
        transform: translateX(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .potd-container.hidden {
        opacity: 0;
        transform: translateX(420px);
        pointer-events: none;
    }

    .potd-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .potd-header {
        background: #1a1a1a;
        color: white;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .potd-icon {
        width: 40px;
        height: 40px;
        background: #00a04a;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .potd-title-section {
        flex: 1;
    }

    .potd-title-section h5 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 1px;
        color: #fff;
    }

    .potd-subtitle {
        margin: 4px 0 0 0;
        font-size: 11px;
        color: #aaa;
        font-weight: 400;
    }

    .potd-body {
        padding: 20px;
    }

    .potd-content h4 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .potd-content p {
        margin: 0 0 16px 0;
        font-size: 13px;
        line-height: 1.5;
        color: #555;
    }

    .potd-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .potd-actions a {
        color: #1a1a1a;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 16px;
        border: 1px solid #e0e0e0;
        background: white;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .potd-actions a:hover {
        background: #f5f5f5;
        border-color: #1a1a1a;
    }

    .potd-close {
        position: absolute;
        top: 8px;
        right: 8px;
        background: white;
        border: none;
        border-radius: 0;
        width: 28px;
        height: 28px;
        color: #1a1a1a;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .potd-close:hover {
        background: #f5f5f5;
    }

    @media (max-width: 768px) {
        .potd-container {
            width: 320px;
            right: 10px;
            top: 80px;
        }

        .potd-container.hidden {
            transform: translateX(280px);
        }
    }
</style>

<div class="potd-container" id="potdContainer">
    <div class="potd-card">
        <button class="potd-close" onclick="hidePotd()" title="Close">
            <i class="fa fa-times"></i>
        </button>

        <div class="potd-header">
            <div class="potd-icon">
                <i class="fa fa-star"></i>
            </div>
            <div class="potd-title-section">
                <h5>POLICY OF THE DAY</h5>
                <div class="potd-subtitle">
                    <i class="fa fa-calendar"></i>
                    {{ now()->format('M d, Y') }}
                    @if($policyOfTheDay->scheduled_date)
                        • Scheduled
                    @else
                        • Random
                    @endif
                </div>
            </div>
        </div>

        <div class="potd-body">
            <div class="potd-content">
                <h4>{{ $policyOfTheDay->title }}</h4>
                <p>{{ \Illuminate\Support\Str::limit($policyOfTheDay->content, 200) }}...</p>
            </div>

            <div class="potd-actions">
                <a href="{{ route('policies.policy-of-the-day.full', ['id' => $policyOfTheDay->id]) }}" target="_blank">
                    <i class="fa fa-book"></i>
                    View
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    let potdHideTimeout;

    function hidePotd() {
        const container = document.getElementById('potdContainer');
        container.classList.add('hidden');
        if (potdHideTimeout) clearTimeout(potdHideTimeout);
    }

    function showPotd() {
        const container = document.getElementById('potdContainer');
        container.classList.remove('hidden');
        resetAutoHide();
    }

    function resetAutoHide() {
        if (potdHideTimeout) clearTimeout(potdHideTimeout);
        potdHideTimeout = setTimeout(() => hidePotd(), 10000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('potdContainer');
        if (container) {
            resetAutoHide();
            container.addEventListener('mouseenter', () => clearTimeout(potdHideTimeout));
            container.addEventListener('mouseleave', resetAutoHide);
        }
    });

    document.addEventListener('mousemove', function(e) {
        const container = document.getElementById('potdContainer');
        if (container && container.classList.contains('hidden') && e.clientX > window.innerWidth - 50) {
            if (potdHideTimeout) clearTimeout(potdHideTimeout);
            potdHideTimeout = setTimeout(showPotd, 300);
        }
    });

    document.addEventListener('keydown', function(e) {
        const container = document.getElementById('potdContainer');
        if (e.key === 'Escape' && container && !container.classList.contains('hidden')) hidePotd();
    });

    document.addEventListener('click', function(e) {
        const container = document.getElementById('potdContainer');
        const card = container ? container.querySelector('.potd-card') : null;
        if (container && !container.classList.contains('hidden') && card && !card.contains(e.target) && e.target !== card) hidePotd();
    });
</script>
@endif
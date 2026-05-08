@php
    $policyOfTheDay = \App\Models\PolicyOfTheDay::getTodaysPolicy();
@endphp

@if($policyOfTheDay)
<style>
    .policy-of-the-day-container {
        position: fixed;
        top: 400px;
        right: 20px;
        width: 380px;
        z-index: 1050;
        opacity: 1;
        transform: translateX(0);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 10px 25px rgba(0,0,0,0.15));
    }

    .policy-of-the-day-container.hidden {
        opacity: 0;
        transform: translateX(420px);
        pointer-events: none;
    }

    .policy-of-the-day-container:hover {
        transform: translateX(-10px);
    }

    .policy-of-the-day-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .policy-of-the-day-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .policy-of-the-day-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="stars" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.6)"/><circle cx="80" cy="60" r="0.8" fill="rgba(255,255,255,0.4)"/><circle cx="60" cy="90" r="0.6" fill="rgba(255,255,255,0.5)"/></pattern></defs><rect width="100" height="100" fill="url(%23stars)"/></svg>');
        opacity: 0.4;
    }

    .policy-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.9);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #667eea;
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .policy-title-section {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .policy-title-section h5 {
        margin: 0;
        color: white;
        font-size: 16px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .policy-subtitle {
        margin: 2px 0 0 0;
        color: rgba(255,255,255,0.9);
        font-size: 12px;
        font-weight: 400;
    }

    .policy-of-the-day-body {
        padding: 20px;
        color: #333;
    }

    .policy-content h4 {
        margin: 0 0 12px 0;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.3;
        color: #2c3e50;
    }

    .policy-content p {
        margin: 0 0 16px 0;
        font-size: 14px;
        line-height: 1.5;
        color: #555;
    }

    .policy-details {
        margin-top: 16px;
        border-top: 1px solid rgba(255,255,255,0.2);
        padding-top: 16px;
    }

    .policy-details summary {
        cursor: pointer;
        color: #667eea;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s ease;
    }

    .policy-details summary:hover {
        color: #5a67d8;
    }

    .policy-details summary::marker {
        color: #667eea;
    }

    .policy-details p {
        margin: 12px 0 0 0;
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.5;
        border-left: 3px solid #667eea;
        color: #333;
    }

    .policy-actions {
        margin-top: 16px;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .policy-actions a {
        color: #667eea;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 6px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .policy-actions a:hover {
        color: #5a67d8;
        background: #e9ecef;
        border-color: #667eea;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }

    .policy-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(255,255,255,0.9);
        border: 1px solid #e9ecef;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        color: #6c757d;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .policy-close:hover {
        background: #f8f9fa;
        color: #dc3545;
        border-color: #dc3545;
        transform: scale(1.1);
    }

    @keyframes policyPulse {
        0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
        100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
    }

    .policy-new-pulse {
        animation: policyPulse 2s infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }

    .policy-floating {
        animation: float 3s ease-in-out infinite;
    }

    @media (max-width: 768px) {
        .policy-of-the-day-container {
            width: 320px;
            right: 10px;
            top: 80px;
        }

        .policy-of-the-day-container:hover {
            transform: translateX(-5px);
        }

        .policy-of-the-day-container.hidden {
            transform: translateX(340px);
        }
    }
</style>

<div class="policy-of-the-day-container" id="policyOfTheDay">
    <div class="policy-of-the-day-card">
        <button class="policy-close" onclick="hidePolicyOfTheDay()" title="Close">
            <i class="fa fa-times"></i>
        </button>

        <div class="policy-of-the-day-header">
            <div class="policy-icon">
                <i class="fa fa-star"></i>
            </div>
            <div class="policy-title-section">
                <h5>Policy of the Day</h5>
                <div class="policy-subtitle">
                    <i class="fa fa-calendar-o"></i>
                    {{ now()->format('M d, Y') }}
                    @if($policyOfTheDay->scheduled_date)
                        • Scheduled
                    @else
                        • Random
                    @endif
                </div>
            </div>
        </div>

        <div class="policy-of-the-day-body">
            <div class="policy-content">
                <h4>{{ $policyOfTheDay->title }}</h4>
                <p>{{ $policyOfTheDay->content }}</p>

                @if($policyOfTheDay->full_content)
                    <details class="policy-details">
                        <summary>
                            <i class="fa fa-chevron-down"></i>
                            Read Full Content
                        </summary>
                        <p>{{ $policyOfTheDay->full_content }}</p>
                    </details>
                @endif
            </div>

            <div class="policy-actions">
                @if($policyOfTheDay->policy)
                    <a href="{{ route('policies.view_policies') }}#policy-{{ $policyOfTheDay->policy->id }}" target="_blank">
                        <i class="fa fa-external-link"></i>
                        View Full Policy
                    </a>
                @endif
                <!-- <a href="{{ route('policies.dashboard') }}" target="_blank">
                    <i class="fa fa-shield"></i>
                    Policy Center
                </a> -->
            </div>
        </div>
    </div>
</div>

<script>
    let policyHideTimeout;

    function hidePolicyOfTheDay() {
        const container = document.getElementById('policyOfTheDay');
        container.classList.add('hidden');

        if (policyHideTimeout) {
            clearTimeout(policyHideTimeout);
        }
    }

    function showPolicyOfTheDay() {
        const container = document.getElementById('policyOfTheDay');
        container.classList.remove('hidden');
        resetAutoHide();
    }

    function resetAutoHide() {
        if (policyHideTimeout) {
            clearTimeout(policyHideTimeout);
        }

        policyHideTimeout = setTimeout(() => {
            hidePolicyOfTheDay();
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('policyOfTheDay');
        if (container) {
            container.classList.add('policy-new-pulse');

            setTimeout(() => {
                container.classList.remove('policy-new-pulse');
            }, 4000);

            resetAutoHide();

            container.addEventListener('mouseenter', function() {
                if (policyHideTimeout) {
                    clearTimeout(policyHideTimeout);
                }
            });

            container.addEventListener('mouseleave', function() {
                resetAutoHide();
            });
        }
    });

    let edgeHoverTimeout;
    document.addEventListener('mousemove', function(e) {
        const container = document.getElementById('policyOfTheDay');

        if (container && container.classList.contains('hidden') && e.clientX > window.innerWidth - 50) {
            if (edgeHoverTimeout) {
                clearTimeout(edgeHoverTimeout);
            }

            edgeHoverTimeout = setTimeout(() => {
                showPolicyOfTheDay();
            }, 300);
        }
    });

    document.addEventListener('keydown', function(e) {
        const container = document.getElementById('policyOfTheDay');
        if (e.key === 'Escape' && container && !container.classList.contains('hidden')) {
            hidePolicyOfTheDay();
        }
    });

    document.addEventListener('click', function(e) {
        const container = document.getElementById('policyOfTheDay');
        const card = container ? container.querySelector('.policy-of-the-day-card') : null;

        if (container && !container.classList.contains('hidden') && card && !card.contains(e.target) && e.target !== card) {
            hidePolicyOfTheDay();
        }
    });
</script>
@endif

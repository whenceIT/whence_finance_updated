@extends('layouts.master')

@section('content')
<div class="container-fluid risk-dashboard">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1>Risk Dashboard</h1>
                <p class="page-subtitle">Live overview of collections, approvals and upcoming deadlines</p>
            </div>

            <!-- Real Time Alerts -->
            <a href="{{ route('risk.fraud-feed') }}" class="ff-stats-bar" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:8px 0 10px;border-bottom:1px solid #eee;margin-bottom:12px;font-size:12px;background:#fff; text-decoration: none; color: inherit;">
                <span class="ff-stat-item" title="Total alerts in window">
                    <i class="fa fa-list" style="color:#555;"></i>&nbsp;
                    <strong style="color:#222;">0</strong>&nbsp;total
                </span>
                <span class="ff-stat-item" title="Critical alerts">
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#c0392b;margin-right:3px;"></span>
                    <strong style="color:#c0392b;">0</strong>&nbsp;critical
                </span>
                <span class="ff-stat-item" title="Warning alerts">
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#f39c12;margin-right:3px;"></span>
                    <strong style="color:#f39c12;">0</strong>&nbsp;warning
                </span>
                <span class="ff-stat-item" title="Info alerts">
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#3498db;margin-right:3px;"></span>
                    <strong style="color:#3498db;">0</strong>&nbsp;info
                </span>
                <span class="ff-stat-item" title="Unread alerts"
                      style="margin-left:auto;color:#888;">
                    <i class="fa fa-envelope-o"></i>&nbsp;
                    <strong>0</strong>&nbsp;unread
                </span>
            </a>
        </div>
    </div>

    <!-- Row Section 1: Collections + Approvals -->
    <div class="row">
        <div class="col-lg-12">
            <div class="bento-grid">

                <div class="bento-card big">
                    <div class="card-top">
                        <div class="icon-wrap"><i class="fa fa-credit-card"></i></div>
                    </div>
                    <div class="card-bottom">
                        <div class="title">Collected Setup Debt Today</div>
                        <div class="value">K{{ number_format($collectedSetupDebtToday ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="bento-card small accent-blue">
                    <div class="card-top">
                        <div class="icon-wrap"><i class="fa fa-building"></i></div>
                    </div>
                    <div class="card-bottom">
                        <div class="title">Collected Building Today</div>
                        <div class="value">K{{ number_format($collectedBuildingToday ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="bento-card small accent-blue">
                    <div class="card-top">
                        <div class="icon-wrap"><i class="fa fa-cogs"></i></div>
                    </div>
                    <div class="card-bottom">
                        <div class="title">Collected Administration Today</div>
                        <div class="value">K{{ number_format($collectedAdminToday ?? 0, 2) }}</div>
                    </div>
                </div>

                <div class="bento-card small accent-blue">
                    <div class="card-top">
                        <div class="icon-wrap"><i class="fa fa-legal"></i></div>
                    </div>
                    <div class="card-bottom">
                        <div class="title">Collected Statutory Today</div>
                        <div class="value">K{{ number_format($collectedStatutoryToday ?? 0, 2) }}</div>
                    </div>
                </div>

                <a href="{{ route('approvals.deposit-approvals') }}" class="bento-card small outline-card"
                     style="{{ ($pendingDepositApprovals ?? 0) > 0 ? 'border-color:#f5a623;background:linear-gradient(135deg,#fff8ec 0%,#fffdf9 100%);' : '' }}; text-decoration: none;">
                    <div class="card-top">
                        <div class="icon-wrap icon-wrap-light"><i class="fa fa-inbox" style="color:#f5a623;"></i></div>
                        @if(($pendingDepositApprovals ?? 0) > 0)
                            <span class="badge-pill">Needs review</span>
                        @endif
                    </div>
                    <div class="card-bottom">
                        <div class="title" style="color:#555;">Pending Deposit Approvals</div>
                        <div class="value" style="color:#222;">{{ $pendingDepositApprovals ?? 0 }}</div>
                    </div>
                </a>

                <!-- <div class="bento-card small outline-card"
                     style="{{ ($pendingExpenseApprovals ?? 0) > 0 ? 'border-color:#f5a623;background:linear-gradient(135deg,#fff8ec 0%,#fffdf9 100%);' : '' }}">
                    <div class="card-top">
                        <div class="icon-wrap icon-wrap-light"><i class="fa fa-file-text-o" style="color:#f5a623;"></i></div>
                        @if(($pendingExpenseApprovals ?? 0) > 0)
                            <span class="badge-pill">Needs review</span>
                        @endif
                    </div>
                    <div class="card-bottom">
                        <div class="title" style="color:#555;">Pending Expenses Approvals</div>
                        <div class="value" style="color:#222;">{{ $pendingExpenseApprovals ?? 0 }}</div>
                    </div>
                </div> -->

            </div>

            <div class="section-divider">
                <span>Upcoming Deadlines</span>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="bento-grid countdown-grid">

                        <div class="bento-card countdown-card" id="building-countdown">
                            <div class="card-top">
                                <div class="icon-wrap icon-wrap-dark"><i class="fa fa-clock-o"></i></div>
                            </div>
                            <div class="card-bottom">
                                <div class="title">Building Fee Deadline</div>
                                <div class="countdown" data-deadline="{{ $buildingDeadline->countdown_date ?? '' }}">
                                    <div class="countdown-unit"><span class="countdown-days">--</span><small>days</small></div>
                                    <div class="countdown-unit"><span class="countdown-hours">--</span><small>hrs</small></div>
                                    <div class="countdown-unit"><span class="countdown-mins">--</span><small>min</small></div>
                                </div>
                            </div>
                        </div>

                        <div class="bento-card countdown-card" id="admin-countdown">
                            <div class="card-top">
                                <div class="icon-wrap icon-wrap-dark"><i class="fa fa-clock-o"></i></div>
                            </div>
                            <div class="card-bottom">
                                <div class="title">Administration Deadline</div>
                                <div class="countdown" data-deadline="{{ $adminDeadline->countdown_date ?? '' }}">
                                    <div class="countdown-unit"><span class="countdown-days">--</span><small>days</small></div>
                                    <div class="countdown-unit"><span class="countdown-hours">--</span><small>hrs</small></div>
                                    <div class="countdown-unit"><span class="countdown-mins">--</span><small>min</small></div>
                                </div>
                            </div>
                        </div>

                        <div class="bento-card countdown-card" id="statutory-countdown">
                            <div class="card-top">
                                <div class="icon-wrap icon-wrap-dark"><i class="fa fa-clock-o"></i></div>
                            </div>
                            <div class="card-bottom">
                                <div class="title">Statutory Deadline</div>
                                <div class="countdown" data-deadline="{{ $statutoryDeadline->countdown_date ?? '' }}">
                                    <div class="countdown-unit"><span class="countdown-days">--</span><small>days</small></div>
                                    <div class="countdown-unit"><span class="countdown-hours">--</span><small>hrs</small></div>
                                    <div class="countdown-unit"><span class="countdown-mins">--</span><small>min</small></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Flip a countdown card into "urgent" styling when under 24 hours remain
        function checkUrgency($card, distance) {
            if (distance > 0 && distance < (1000 * 60 * 60 * 24)) {
                $card.addClass('urgent');
            } else {
                $card.removeClass('urgent');
            }
        }

        $('.countdown').each(function() {
            const deadlineDate = $(this).data('deadline');
            if (!deadlineDate) return;

            const deadlineTimestamp = new Date(deadlineDate).getTime();
            const $el = $(this);
            const $card = $el.closest('.bento-card');

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = deadlineTimestamp - now;

                checkUrgency($card, distance);

                if (distance < 0) {
                    $el.find('.countdown-days').text('0');
                    $el.find('.countdown-hours').text('0');
                    $el.find('.countdown-mins').text('0');
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                $el.find('.countdown-days').text(days);
                $el.find('.countdown-hours').text(hours);
                $el.find('.countdown-mins').text(minutes);
            }

            updateCountdown();
            setInterval(updateCountdown, 60000); // Update every minute
        });
    });
    </script>

    <style>
    .risk-dashboard .page-header {
        margin-bottom: 24px;
    }
    .risk-dashboard .page-header h1 {
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
        color: #1f2430;
    }
    .risk-dashboard .page-subtitle {
        color: #8a8fa3;
        font-size: 14px;
        margin: 0;
    }

    .bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-auto-rows: 1fr;
        gap: 18px;
        height: auto;
    }
    .countdown-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .bento-card {
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 18px;
        padding: 22px;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 8px 20px rgba(31, 36, 48, 0.12);
        border: 1px solid rgba(255,255,255,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .bento-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(31, 36, 48, 0.18);
    }
    .bento-card::after {
        content: "";
        position: absolute;
        top: -40px;
        right: -40px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
        pointer-events: none;
    }

    .bento-card.big {
        grid-row: 1;
        grid-column: 1 / span 3;
        background: linear-gradient(135deg, #0f9b7e 0%, #38ef7d 100%);
        min-height: 160px;
    }
    .bento-card.big .value { font-size: 44px; font-weight: 800; }
    .bento-card.big .title { font-size: 15px; }

    .bento-card.small.accent-blue {
        background: linear-gradient(135deg, #3a78eb 0%, #3892f9 100%);
    }

    .bento-card.outline-card {
        background: #ffffff;
        color: #333;
        box-shadow: 0 4px 14px rgba(31,36,48,0.06);
        border: 1px solid #ececf1;
    }
    .bento-card.outline-card::after { display: none; }

    .card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .icon-wrap {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255,255,255,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .icon-wrap-light { background: #fff6e6; }
    .icon-wrap-dark { background: rgba(255,255,255,0.22); }

    .badge-pill {
        font-size: 11px;
        font-weight: 700;
        color: #b5750a;
        background: #fdecc8;
        padding: 4px 10px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .title {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 6px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .value {
        font-size: 26px;
        font-weight: 700;
        line-height: 1.1;
    }

    .section-divider {
        display: flex;
        align-items: center;
        margin: 30px 0 18px;
        color: #8a8fa3;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }
    .section-divider::before,
    .section-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e6e8f0;
    }
    .section-divider span { padding: 0 14px; white-space: nowrap; }

    .bento-card.countdown-card {
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        min-height: 150px;
    }
    .bento-card.countdown-card.urgent {
        background: linear-gradient(135deg, #d61f3c 0%, #ff5f6d 100%);
        box-shadow: 0 0 0 3px rgba(214,31,60,0.25), 0 8px 20px rgba(31,36,48,0.18);
    }

    .countdown {
        display: flex;
        gap: 14px;
        margin-top: 8px;
    }
    .countdown-unit {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: rgba(255,255,255,0.16);
        border-radius: 10px;
        padding: 6px 10px;
        min-width: 48px;
    }
    .countdown-unit span {
        font-size: 20px;
        font-weight: 800;
        line-height: 1.2;
    }
    .countdown-unit small {
        font-size: 10px;
        text-transform: uppercase;
        opacity: 0.85;
        letter-spacing: 0.4px;
    }

    @media (max-width: 992px) {
        .bento-grid { grid-template-columns: repeat(2, 1fr); }
        .bento-card.big { grid-column: 1 / span 2; }
        .countdown-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .bento-grid, .countdown-grid { grid-template-columns: 1fr; }
        .bento-card.big { grid-column: 1; }
    }
    </style>

</div>
@endsection
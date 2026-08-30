@if(isset($role) && ($role->role_id == 3 || $role->role_id == 4))
<style>
    .stat-tile {
        display: block;
        position: relative;
        border-radius: 14px;
        padding: 24px 22px;
        text-decoration: none;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        color: #fff;
    }
    .stat-tile:hover,
    .stat-tile:focus {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        color: #fff;
        text-decoration: none;
    }
    .stat-tile__icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(255,255,255,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }
    .stat-tile__count {
        font-size: 30px;
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .stat-tile__label {
        font-size: 14px;
        font-weight: 500;
        opacity: 0.88;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .stat-tile__hint {
        font-size: 12px;
        font-weight: 400;
        opacity: 0.75;
        margin-top: 6px;
    }
    .stat-tile__arrow {
        position: absolute;
        top: 22px;
        right: 22px;
        font-size: 14px;
        opacity: 0.6;
        transition: transform 0.25s ease, opacity 0.25s ease;
    }
    .stat-tile:hover .stat-tile__arrow {
        transform: translateX(3px);
        opacity: 1;
    }
    .stat-tile--loans      { background: linear-gradient(135deg, #6366f1, #4338ca); }
    .stat-tile--collateral { background: linear-gradient(135deg, #10b981, #047857); }
    .stat-tile--clients    { background: linear-gradient(135deg, #0ea5e9, #0369a1); }

    @media (max-width: 767px) {
        .stat-tile { margin-bottom: 16px; }
    }
</style>

<div class="row" style="margin-bottom: 24px;">
    <div class="col-lg-4 col-xs-12">
        <a href="{{ url('loan/my_loans') }}" class="stat-tile stat-tile--loans">
            <i class="fa fa-angle-right stat-tile__arrow">> View</i>
            <div class="stat-tile__icon"><i class="fa fa-money"></i></div>
            <div class="stat-tile__count">{{ number_format($loanCount ?? 0) }}</div>
            <div class="stat-tile__label">Loans</div>
            <div class="stat-tile__hint">Review and manage your active/open loans</div>
        </a>
    </div>
    <div class="col-lg-4 col-xs-12">
        <a href="{{ url('collateral') }}" class="stat-tile stat-tile--collateral">
            <i class="fa fa-angle-right stat-tile__arrow">> View</i>
            <div class="stat-tile__icon"><i class="fa fa-briefcase"></i></div>
            <div class="stat-tile__count">{{ number_format($collateralCount ?? 0) }}</div>
            <div class="stat-tile__label">Collateral</div>
            <div class="stat-tile__hint">Track collateral {{ number_format($collateralCount ?? 0) }} pledged.</div>
        </a>
    </div>
    <div class="col-lg-4 col-xs-12">
        <a href="{{ url('client/my_clients') }}" class="stat-tile stat-tile--clients">
            <i class="fa fa-angle-right stat-tile__arrow">> View</i>
            <div class="stat-tile__icon"><i class="fa fa-users"></i></div>
            <div class="stat-tile__count">{{ number_format($clientCount ?? 0) }}</div>
            <div class="stat-tile__label">Clients</div>
            <div class="stat-tile__hint">Browse and manage active client profiles</div>
        </a>
    </div>
</div>
@endif
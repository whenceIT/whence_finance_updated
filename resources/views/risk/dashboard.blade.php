@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1>Risk Dashboard</h1>
            </div>
        </div>
    </div>

    <!-- Row Section 1 -->
    <div class="row">
        <div class="col-lg-12">
            <div class="bento-grid">
                <style>
                .bento-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    grid-template-rows: auto;
                    gap: 15px;
                    height: auto;
                }
                .bento-card {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border-radius: 15px;
                    padding: 20px;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .bento-card.big {
                    grid-row: 1;
                    grid-column: 1 / span 3;
                    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                }
                .bento-card.small {
                    background: linear-gradient(135deg, #3a78eb 0%, #3892f9 100%);
                }
                .bento-card .icon {
                    font-size: 2rem;
                    opacity: 0.8;
                    margin-bottom: 10px;
                }
                .bento-card .title {
                    font-size: 14px;
                    opacity: 0.9;
                    margin-bottom: 10px;
                    font-weight: 600;
                }
                .bento-card .value {
                    font-size: 28px;
                    font-weight: bold;
                }
                .bento-card.big .value {
                    font-size: 42px;
                    font-weight: 800;
                }
                </style>
                
                <div class="bento-card big">
                    <div class="icon"><i class="fa fa-credit-card"></i></div>
                    <div class="title">Collected Setup Debt Today</div>
                    <div class="value">K{{ number_format($collectedSetupDebtToday ?? 0, 2) }}</div>
                </div>
                
                <div class="bento-card small">
                    <div class="icon"><i class="fa fa-building"></i></div>
                    <div class="title">Collected Building Today</div>
                    <div class="value">K{{ number_format($collectedBuildingToday ?? 0, 2) }}</div>
                </div>
                
                <div class="bento-card small">
                    <div class="icon"><i class="fa fa-cogs"></i></div>
                    <div class="title">Collected Administration Today</div>
                    <div class="value">K{{ number_format($collectedAdminToday ?? 0, 2) }}</div>
                </div>
                
                <div class="bento-card small">
                    <div class="icon"><i class="fa fa-legal"></i></div>
                    <div class="title">Collected Statutory Today</div>
                    <div class="value">K{{ number_format($collectedStatutoryToday ?? 0, 2) }}</div>
                </div>
                
                
            </div>

            <br>
            <div class="row">
                <div class="col-lg-12">
                <div class="bento-grid">
                        
                    <!-- Countdown Timer Cards -->
                    <div class="bento-card countdown-card" id="building-countdown">
                        <div class="icon"><i class="fa fa-clock-o"></i></div>
                        <div class="title">Building Fee Deadline</div>
                        <div class="countdown" data-deadline="{{ $buildingDeadline->countdown_date ?? '' }}">
                            <span class="countdown-days">--</span>d 
                            <span class="countdown-hours">--</span>h 
                            <span class="countdown-mins">--</span>m
                        </div>
                    </div>
                    
                    <div class="bento-card countdown-card" id="admin-countdown">
                        <div class="icon"><i class="fa fa-clock-o"></i></div>
                        <div class="title">Administration Deadline</div>
                        <div class="countdown" data-deadline="{{ $adminDeadline->countdown_date ?? '' }}">
                            <span class="countdown-days">--</span>d 
                            <span class="countdown-hours">--</span>h 
                            <span class="countdown-mins">--</span>m
                        </div>
                    </div>
                    
                    <div class="bento-card countdown-card" id="statutory-countdown">
                        <div class="icon"><i class="fa fa-clock-o"></i></div>
                        <div class="title">Statutory Deadline</div>
                        <div class="countdown" data-deadline="{{ $statutoryDeadline->countdown_date ?? '' }}">
                            <span class="countdown-days">--</span>d 
                            <span class="countdown-hours">--</span>h 
                            <span class="countdown-mins">--</span>m
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Update countdowns
        $('.countdown').each(function() {
            const deadlineDate = $(this).data('deadline');
            if (!deadlineDate) return;
            
            const deadlineTimestamp = new Date(deadlineDate).getTime();
            const $el = $(this);
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = deadlineTimestamp - now;
                
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
                
            </div>
        </div>
    </div>

    <style>
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: auto;
        gap: 15px;
        height: auto;
    }
    .bento-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .bento-card.big {
        grid-row: 1;
        grid-column: 1 / span 3;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .bento-card.small, .bento-card.countdown-card {
        background: linear-gradient(135deg, #3a78eb 0%, #3892f9 100%);
    }
    .bento-card.countdown-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .bento-card .icon {
        font-size: 2rem;
        opacity: 0.8;
        margin-bottom: 10px;
    }
    .bento-card .title {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 10px;
        font-weight: 600;
    }
    .bento-card .value {
        font-size: 28px;
        font-weight: bold;
    }
    .bento-card.big .value {
        font-size: 42px;
        font-weight: 800;
    }
    .countdown {
        font-size: 20px;
        font-weight: bold;
        margin-top: 5px;
    }
    .countdown-days, .countdown-hours, .countdown-mins {
        display: inline-block;
    }
    </style>

</div>
@endsection
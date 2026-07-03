<div class="row mb-10">
    <div class="col-lg-12">
        <div class="bento-grid">
            <style>
            .bento-grid {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr;
                grid-template-rows: 1fr 1fr;
                gap: 15px;
                height: 280px;
            }
            .bento-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 15px;
                padding: 10px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            }
            .bento-card.big {
                grid-row: 1 / 4;
                grid-column: 1;
                background: linear-gradient(135deg, #1d12af 0%, #388def 100%);
            }
            .bento-card.small {
                background: linear-gradient(135deg, #3a78eb 0%, #3892f9 100%);
            }
            .bento-card.icon {
                font-size: 2rem;
                opacity: 0.8;
            }
            .bento-card .title {
                font-size: 14px;
                opacity: 0.9;
                margin-bottom: 10px;
            }
            .bento-card .value {
                font-size: 28px;
                font-weight: bold;
            }
            .bento-card .footer {
                font-size: 12px;
                opacity: 0.8;
                margin-top: 10px;
            }
            </style>
            
            <div class="bento-card big">
                <div class="icon"><i class="fa fa-line-chart"></i></div>
                <div class="title" style="font-size: 18px; font-weight: 600; letter-spacing: -0.3px;">Total Overall Recovered</div>
                <div class="value" style="font-size: 52px; font-weight: 800; letter-spacing: -0.5px;">K{{ $overal_tt_recovered + $funds, 2 }}</div>
                <div class="footer">Cumulative recovery to date</div>
            </div>
            
            <div class="bento-card small">
                <div class="icon"><i class="fa fa-folder-open"></i></div>
                <div class="title">Active Cases</div>
                <div class="value">{{ $overal_tt_active_cases }}</div>
            </div>
            
            <div class="bento-card small">
                <div class="icon"><i class="fa fa-check-circle"></i></div>
                <div class="title">Resolved Cases</div>
                <div class="value">{{ $overal_tt_closed_cases }}</div>
            </div>
            
            <div class="bento-card small">
                <div class="icon"><i class="fa fa-folder-open"></i></div>
                <div class="title">Dormant Clients</div>
                <div class="value">{{ $overal_tt_active_cases }}</div>
            </div>

            <div class="bento-card small">
                <div class="icon"><i class="fa fa-users"></i></div>
                <div class="title">Specialists</div>
                <div class="value">{{ $specialists->count() }}</div>
            </div>
            
            <div class="bento-card small">
                <div class="icon"><i class="fa fa-folder-open"></i></div>
                <div class="title">Dept. Attribution</div>
                <div class="value">{{ number_format($overall_tt_debt_attr,2) }}</div>
            </div>

            <div class="bento-card small">
                <div class="icon"><i class="fa fa-money"></i></div>
                <div class="title">Dept. Unit Share</div>
                <div class="value">K{{ number_format($kpis['unitShare'], 2) }}</div>
            </div>
        </div>
    </div>
</div>
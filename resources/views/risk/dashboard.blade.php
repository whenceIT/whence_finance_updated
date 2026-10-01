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

                <div class="bento-card small outline-card late-disbursements-card"
                     style="border-color:#f5a623;background:linear-gradient(135deg,#fff8ec 0%,#fffdf9 100%);cursor:pointer;"
                     data-toggle="modal" data-target="#lateDisbursementsModal">
                    <div class="card-top">
                        <div class="icon-wrap icon-wrap-light"><i class="fa fa-file-text-o" style="color:#f5a623;"></i></div>
                    </div>
                    <div class="card-bottom">
                        <div class="title" style="color:#555;">Late Disbursements This Week</div>
                        <div class="value" style="color:#222;">{{ $lateDisbursementsThisWeek ?? 0 }}</div>
                    </div>
                </div>

                <div class="bento-card small outline-card blocked-card"
                     style="border-color:#e74c3c;background:linear-gradient(135deg,#fdf0ed 0%,#fefaf9 100%);cursor:pointer;"
                     data-toggle="modal" data-target="#blockagesModal">
                    <div class="card-top">
                        <div class="icon-wrap icon-wrap-light"><i class="fa fa-ban" style="color:#e74c3c;"></i></div>
                    </div>
                    <div class="card-bottom">
                        <div class="title" style="color:#555;">Total Blocked</div>
                        <div class="value" style="color:#222;">{{ $totalBlocked ?? 0 }}</div>
                    </div>
                </div>

                <div class="bento-card small outline-card"
                     style="border-color:#8e44ad;background:linear-gradient(135deg,#f5eeff 0%,#fdfaff 100%);cursor:pointer;"
                     data-toggle="modal" data-target="#blockingHistoryModal">
                    <div class="card-top">
                        <div class="icon-wrap icon-wrap-light"><i class="fa fa-bar-chart" style="color:#8e44ad;"></i></div>
                        <span class="badge-pill" style="background:#ede0fa;color:#6c2fa0;">Rate</span>
                    </div>
                    <div class="card-bottom">
                        <div class="title" style="color:#555;">Blocking Rate</div>
                        <div class="value" style="color:#222;">{{ $blockingRate ?? 0 }}%</div>
                        <div style="font-size:11px;color:#aaa;margin-top:2px;">48 ÷ {{ $totalBlockedCount ?? 0 }} × 100</div>
                    </div>
                </div>

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

                        <div class="bento-card countdown-card" id="debt-setup-countdown">
                            <div class="card-top">
                                <div class="icon-wrap icon-wrap-dark"><i class="fa fa-clock-o"></i></div>
                            </div>
                            <div class="card-bottom">
                                <div class="title">Debt Setup Cost</div>
                                <div class="countdown" data-deadline="{{ $debtSetupDeadline->countdown_date ?? '' }}">
                                    <div class="countdown-unit"><span class="countdown-days">--</span><small>days</small></div>
                                    <div class="countdown-unit"><span class="countdown-hours">--</span><small>hrs</small></div>
                                    <div class="countdown-unit"><span class="countdown-mins">--</span><small>min</small></div>
                                </div>
</div>

    </div>
</div>

<!-- Blocking History Modal -->
<div class="modal fade" id="blockingHistoryModal" tabindex="-1" role="dialog" aria-labelledby="blockingHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#8e44ad,#6c2fa0);">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-white" id="blockingHistoryModalLabel">
                    <i class="fa fa-bar-chart"></i>&nbsp;Office Blocking Rate History
                </h4>
            </div>
            <div class="modal-body bh-modal-body">
                <div id="blockingHistoryContent">
                    <div class="text-center text-muted" style="padding:40px 0;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p style="margin-top:10px;">Loading blocking history...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted small" id="blockingHistorySummary"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Blockages Modal -->
<div class="modal fade" id="blockagesModal" tabindex="-1" role="dialog" aria-labelledby="blockagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="blockagesModalLabel">
                    <i class="fa fa-ban me-2"></i>Blocked Offices
                </h4>
            </div>
            <div class="modal-body bd-modal-body">
                <div id="blockagesContent">
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>Loading blocked offices...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted small" id="blockagesSummary"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</div>
    {{-- ─── Branch Cash Balances ─────────────────────────────────────── --}}
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-12">

            <div class="section-divider">
                <span>Branch Cash Balances</span>
                <small style="font-size:11px;color:#8a8fa3;margin-left:10px;">Province ▸ District ▸ Office — click to drill down</small>
            </div>

            {{-- Shimmer skeleton while offices load --}}
            <div id="branchBalancesShimmer">
                <div class="branch-balance-grid">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="bento-card branch-balance-card shimmer-card">
                            <div class="shimmer-line" style="width:60%;height:12px;margin-bottom:10px;"></div>
                            <div class="shimmer-line" style="width:40%;height:22px;"></div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Actual cards injected by JS --}}
            <div id="branchBalancesGrid" class="branch-balance-grid" style="display:none;"></div>

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

        // ── Branch Cash Balances ────────────────────────────────────────
        function formatBalance(val) {
            if (val === null || val === undefined) return '—';
            return 'K ' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function balanceClass(val) {
            if (val === null || val === undefined) return 'branch-balance-card--unknown';
            if (val < 0)   return 'branch-balance-card--negative';
            if (val === 0) return 'branch-balance-card--zero';
            return 'branch-balance-card--positive';
        }

        function buildCard(office, balance, err) {
            var cls   = balanceClass(balance);
            var label = formatBalance(balance);
            var errMsg = err ? '<div class="bb-error">' + err + '</div>' : '';
            return '<div class="bento-card branch-balance-card ' + cls + '">' +
                     '<div class="card-top">' +
                       '<div class="icon-wrap"><i class="fa fa-university"></i></div>' +
                       (office.wallet_id
                         ? '<span class="bb-wallet-chip"><i class="fa fa-link"></i></span>'
                         : '<span class="bb-wallet-chip bb-wallet-chip--none"><i class="fa fa-chain-broken"></i></span>') +
                     '</div>' +
                     '<div class="card-bottom">' +
                       '<div class="title">' + office.office_name + '</div>' +
                       '<div class="value bb-value">' + label + '</div>' +
                       errMsg +
                     '</div>' +
                   '</div>';
        }

        function groupOffices(offices) {
            var tree = {};
            offices.forEach(function(o) {
                var pid = o.province_id || 0;
                var did = o.district_id || 0;
                if (!tree[pid]) {
                    tree[pid] = {
                        id: pid,
                        name: o.province_name || ('Province ' + pid),
                        districts: {},
                        total: 0,
                        valid: 0
                    };
                }
                if (!tree[pid].districts[did]) {
                    tree[pid].districts[did] = {
                        id: did,
                        name: o.district_name || ('District ' + did),
                        offices: [],
                        total: 0,
                        valid: 0
                    };
                }
                tree[pid].districts[did].offices.push(o);
            });
            return tree;
        }

        function districtTotals(district) {
            var total = 0, valid = 0;
            district.offices.forEach(function(o) {
                if (typeof o.balance === 'number' && !isNaN(o.balance)) {
                    total += o.balance;
                    valid++;
                }
            });
            district.total = total;
            district.valid = valid;
        }

        function provinceTotals(province) {
            var total = 0, valid = 0;
            Object.keys(province.districts).forEach(function(did) {
                districtTotals(province.districts[did]);
                total += province.districts[did].total;
                valid += province.districts[did].valid;
            });
            province.total = total;
            province.valid = valid;
        }

        function bbToggleProvince(pid) {
            var $container = $('#bb_province_' + pid);
            var $arrow = $('#bb_arrow_province_' + pid);
            $container.toggle();
            $arrow.toggleClass('open').text($arrow.hasClass('open') ? '▼' : '▶');
        }

        function bbToggleDistrict(pid, did) {
            var $container = $('#bb_district_' + pid + '_' + did);
            var $arrow = $('#bb_arrow_district_' + pid + '_' + did);
            $container.toggle();
            $arrow.toggleClass('open').text($arrow.hasClass('open') ? '▼' : '▶');
        }

        function renderBalancesTree(offices) {
            var tree = groupOffices(offices);

            Object.keys(tree).forEach(function(pid) {
                provinceTotals(tree[pid]);
            });

            var html = '';            Object.keys(tree).sort(function(a, b) {
                var na = tree[a].name.toLowerCase(), nb = tree[b].name.toLowerCase();
                return na < nb ? -1 : na > nb ? 1 : 0;
            }).forEach(function(pid) {
                var p = tree[pid];
                html += '<div class="bento-card branch-balance-card province-card">';
                html += '  <div class="bb-group-header bb-province-header" data-province="' + pid + '">';
                html += '    <div class="bb-group-name">' + p.name + '</div>';
                html += '    <span class="bb-badge">' + p.valid + ' offices</span>';
                html += '    <div class="bb-group-total">' + formatBalance(p.total) + '</div>';
                html += '    <span class="bb-arrow" id="bb_arrow_province_' + pid + '">' + (p.valid ? '▼' : '▶') + '</span>';
                html += '  </div>';
                html += '  <div class="bb-districts" id="bb_province_' + pid + '" style="display:none;">';
                Object.keys(p.districts).sort(function(a, b) {
                    var na = p.districts[a].name.toLowerCase(), nb = p.districts[b].name.toLowerCase();
                    return na < nb ? -1 : na > nb ? 1 : 0;
                }).forEach(function(did) {
                    var d = p.districts[did];
                    html += '<div class="bento-card branch-balance-card district-card">';
                    html += '  <div class="bb-group-header bb-district-header" data-province="' + pid + '" data-district="' + did + '">';
                    html += '    <div class="bb-group-name">' + d.name + '</div>';
                    html += '    <span class="bb-badge">' + d.valid + ' offices</span>';
                    html += '    <div class="bb-group-total">' + formatBalance(d.total) + '</div>';
                    html += '    <span class="bb-arrow" id="bb_arrow_district_' + pid + '_' + did + '">' + (d.valid ? '▼' : '▶') + '</span>';
                    html += '  </div>';
                    html += '  <div class="bb-offices" id="bb_district_' + pid + '_' + did + '" style="display:none;">';
                    d.offices.forEach(function(o) {
                        html += buildCard(o, o.balance, o.error);
                    });
                    html += '  </div>';
                    html += '</div>';
                });
                html += '  </div>';
                html += '</div>';
            });

            $('#branchBalancesShimmer').hide();
            $('#branchBalancesGrid').html(html).show();
        }

        $('#branchBalancesGrid').on('click', '.bb-province-header', function(e) {
            e.preventDefault();
            bbToggleProvince($(this).data('province'));
        });
        $('#branchBalancesGrid').on('click', '.bb-district-header', function(e) {
            e.preventDefault();
            bbToggleDistrict($(this).data('province'), $(this).data('district'));
        });

        $.ajax({
            url: '/cash_health/national/balances',
            type: 'GET',
            success: function(res) {
                if (!res.success || !res.offices || res.offices.length === 0) {
                    $('#branchBalancesShimmer').hide();
                    $('#branchBalancesGrid')
                        .html('<p class="text-muted text-center">No offices found.</p>')
                        .show();
                    return;
                }

                var offices  = res.offices;
                var total    = offices.length;
                var completed = 0;

                offices.forEach(function(office, idx) {
                    if (!office.wallet_id) {
                        office.balance = null;
                        office.error = 'No wallet linked';
                        completed++;
                        maybeRender();
                        return;
                    }

                    $.ajax({
                        url: '/cash_health/national/balance/' + office.office_id,
                        type: 'GET',
                        success: function(data) {
                            office.balance = (data.success && data.balance !== undefined && data.balance !== null)
                                ? Number(data.balance)
                                : null;
                            office.error = (!data.success && data.message) ? data.message : null;
                        },
                        error: function() {
                            office.balance = null;
                            office.error = 'Failed to load';
                        },
                        complete: function() {
                            completed++;
                            maybeRender();
                        }
                    });
                });

                function maybeRender() {
                    if (completed < total) return;
                    renderBalancesTree(offices);
                }
            },
            error: function() {
                $('#branchBalancesShimmer').hide();
                $('#branchBalancesGrid')
                    .html('<p class="text-danger text-center">Could not load branch list.</p>')
                    .show();
            }
        });

        // ── Late Disbursements Modal ───────────────────────────────────────
        let lateDisbursementsLoaded = false;
        $('#lateDisbursementsModal').on('show.bs.modal', function () {
            if (lateDisbursementsLoaded) return;
            lateDisbursementsLoaded = true;

            $.ajax({
                url: '{{ route("risk.dashboard.late-disbursements") }}',
                type: 'GET',
                success: function(res) {
                    if (!res.success || !res.data) {
                        $('#lateDisbursementsContent').html('<div class="ld-empty">No late disbursements found.</div>');
                        $('#lateDisbursementsSummary').text('');
                        return;
                    }
                    renderLateDisbursements(res.data);
                },
                error: function() {
                    $('#lateDisbursementsContent').html('<div class="ld-empty text-danger">Failed to load late disbursements.</div>');
                }
            });
        });

        function formatElapsedMs(ms) {
            const totalMins = Math.floor(ms / 60000);
            const hours = Math.floor(totalMins / 60);
            const mins  = totalMins % 60;
            if (hours > 0) return hours + 'h ' + mins + 'm';
            return mins + 'm';
        }

        function renderLateDisbursements(data) {
            let totalLoans  = 0;
            let totalAmount = 0;
            let html = '';

            // ── Analytics pass ───────────────────────────────────────────
            const allElapsedMs = [];                  // every loan's elapsed ms
            const officeElapsed = {};                 // officeName -> [ms, ms, ...]

            Object.keys(data).forEach(function(provinceName) {
                Object.keys(data[provinceName]).forEach(function(officeName) {
                    data[provinceName][officeName].forEach(function(loan) {
                        if (!loan.created_at) return;
                        const diffMs = Date.now() - new Date(loan.created_at.replace(' ', 'T')).getTime();
                        if (diffMs < 0) return;
                        allElapsedMs.push(diffMs);
                        if (!officeElapsed[officeName]) officeElapsed[officeName] = [];
                        officeElapsed[officeName].push(diffMs);
                    });
                });
            });

            const avgMs = allElapsedMs.length
                ? allElapsedMs.reduce((a, b) => a + b, 0) / allElapsedMs.length
                : 0;

            let mostLateOffice = '—';
            let mostLateMs = 0;
            Object.keys(officeElapsed).forEach(function(name) {
                const avg = officeElapsed[name].reduce((a, b) => a + b, 0) / officeElapsed[name].length;
                if (avg > mostLateMs) { mostLateMs = avg; mostLateOffice = name; }
            });

            // ── Analytics strip ──────────────────────────────────────────
            if (allElapsedMs.length > 0) {
                html += '<div class="ld-analytics-bar">';
                html += '  <div class="ld-analytics-card">';
                html += '    <div class="ld-analytics-label"><i class="fa fa-clock-o"></i>&nbsp;Average Late Time</div>';
                html += '    <div class="ld-analytics-value">' + formatElapsedMs(avgMs) + '</div>';
                html += '  </div>';
                html += '  <div class="ld-analytics-divider"></div>';
                html += '  <div class="ld-analytics-card">';
                html += '    <div class="ld-analytics-label"><i class="fa fa-exclamation-triangle"></i>&nbsp;Most Late Office</div>';
                html += '    <div class="ld-analytics-value">' + mostLateOffice + '</div>';
                html += '    <div class="ld-analytics-sub">' + formatElapsedMs(mostLateMs) + ' avg</div>';
                html += '  </div>';
                html += '</div>';
            }

            Object.keys(data).sort().forEach(function(provinceName) {
                const provinceData = data[provinceName];
                let provinceCount = 0;
                let provinceAmount = 0;

                html += '<div class="ld-province">';
                html += '  <div class="ld-province-header">';
                html += '    <span>' + provinceName + '</span>';

                Object.keys(provinceData).forEach(function(officeName) {
                    const loans = provinceData[officeName];
                    provinceCount += loans.length;
                    loans.forEach(function(l) {
                        provinceAmount += parseFloat(l.principal || 0);
                    });
                });

                html += '    <span class="badge bg-secondary ms-2">' + provinceCount + ' loans</span>';
                html += '    <span class="badge bg-info ms-1">K ' + Number(provinceAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                html += '  </div>';

                Object.keys(provinceData).sort().forEach(function(officeName) {
                    const loans = provinceData[officeName];
                    let officeCount = loans.length;
                    let officeAmount = loans.reduce((sum, l) => sum + parseFloat(l.principal || 0), 0);

                    html += '  <div class="ld-office">';
                    html += '    <div class="ld-office-header">';
                    html += '      <span>' + officeName + '</span>';
                    html += '      <span class="badge bg-light text-dark ms-2">' + officeCount + ' loans</span>';
                    html += '      <span class="badge bg-light text-info ms-1">K ' + Number(officeAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                    html += '    </div>';
                    html += '    <table class="ld-table">';
                    html += '      <thead>';
                    html += '        <tr>';
                    html += '          <th style="width:12%">Amount</th>';
                    html += '          <th style="width:10%">Status</th>';
                    html += '          <th style="width:15%">Created</th>';
                    html += '          <th style="width:15%">Time Elapsed</th>';
                    html += '          <th style="width:18%">Client</th>';
                    html += '          <th style="width:18%">Loan Officer</th>';
                    html += '        </tr>';
                    html += '      </thead>';
                    html += '      <tbody>';

                    loans.forEach(function(loan) {
                        totalLoans++;
                        totalAmount += parseFloat(loan.principal || 0);

                        const statusClass = 'ld-badge-' + (loan.status || 'new').toLowerCase();
                        const client = loan.client || {};
                        const officer = loan.loan_officer || {};

                        // Calculate time elapsed from created_at to now
                        let timeElapsed = '—';
                        if (loan.created_at) {
                            const created = new Date(loan.created_at.replace(' ', 'T'));
                            const now = new Date();
                            const diffMs = now - created;
                            const diffMins = Math.floor(diffMs / 60000);
                            const diffHours = Math.floor(diffMins / 60);
                            const remainingMins = diffMins % 60;
                            if (diffHours > 0) {
                                timeElapsed = diffHours + 'h ' + remainingMins + 'm';
                            } else {
                                timeElapsed = diffMins + 'm';
                            }
                        }

                        html += '        <tr>';
                        html += '          <td class="ld-amount">K ' + Number(loan.principal || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
                        html += '          <td><span class="ld-badge ' + statusClass + '">' + (loan.status || 'new') + '</span></td>';
                        html += '          <td class="ld-date">' + (loan.created_at || '—') + '</td>';
                        html += '          <td class="ld-elapsed">' + timeElapsed + '</td>';
                        html += '          <td>';
                        html += '            <div class="ld-client-name">' + (client.name || '—') + '</div>';
                        html += '            <div class="ld-client-phone"><i class="fa fa-phone me-1"></i>' + (client.phone || '—') + '</div>';
                        html += '          </td>';
                        html += '          <td>';
                        html += '            <div class="ld-officer-name">' + (officer.name || '—') + '</div>';
                        html += '            <div class="ld-officer-phone"><i class="fa fa-phone me-1"></i>' + (officer.phone || '—') + ' <small>(' + (officer.email || '') + ')</small></div>';
                        html += '          </td>';
                        html += '        </tr>';
                    });

                    html += '      </tbody>';
                    html += '    </table>';
                    html += '  </div>';
                });

                html += '</div>';
            });

            if (totalLoans === 0) {
                html = '<div class="ld-empty">No late disbursements found.</div>';
            }

            $('#lateDisbursementsContent').html(html);
            $('#lateDisbursementsSummary').text(totalLoans + ' loans • K ' + Number(totalAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }

        // ── Blocking History Modal ────────────────────────────────────────
        let blockingHistoryLoaded = false;
        $('#blockingHistoryModal').on('show.bs.modal', function () {
            if (blockingHistoryLoaded) return;
            blockingHistoryLoaded = true;

            $.ajax({
                url: '{{ route("risk.dashboard.blocking-history") }}',
                type: 'GET',
                success: function(res) {
                    if (!res.success || !res.data || res.data.length === 0) {
                        $('#blockingHistoryContent').html('<div class="bh-empty">No blocking history found yet.</div>');
                        $('#blockingHistorySummary').text('');
                        return;
                    }
                    renderBlockingHistory(res.data, res.total_blocked_count);
                },
                error: function() {
                    $('#blockingHistoryContent').html('<div class="bh-empty text-danger">Failed to load blocking history.</div>');
                }
            });
        });

        function renderBlockingHistory(data, totalCount) {
            const blockingRate = totalCount > 0 ? ((48 / totalCount) * 100).toFixed(1) : 0;
            const maxCount = data.length > 0 ? data[0].blocked_count : 1;

            let html = '';
            html += '<div class="bh-summary-bar">';
            html += '  <div class="bh-rate-display">';
            html += '    <span class="bh-rate-value">' + blockingRate + '%</span>';
            html += '    <span class="bh-rate-formula">48 ÷ ' + totalCount + ' × 100</span>';
            html += '  </div>';
            html += '  <span class="bh-total-badge">' + data.length + ' offices tracked</span>';
            html += '</div>';

            html += '<table class="bh-table">';
            html += '  <thead>';
            html += '    <tr>';
            html += '      <th style="width:5%">#</th>';
            html += '      <th style="width:30%">Office</th>';
            html += '      <th style="width:35%">Block Frequency</th>';
            html += '      <th style="width:10%;text-align:center;">Count</th>';
            html += '      <th style="width:20%">Last Reason</th>';
            html += '    </tr>';
            html += '  </thead>';
            html += '  <tbody>';

            data.forEach(function(row, idx) {
                const pct = maxCount > 0 ? Math.round((row.blocked_count / maxCount) * 100) : 0;
                const barClass = pct >= 75 ? 'bh-bar--high' : (pct >= 40 ? 'bh-bar--mid' : 'bh-bar--low');
                html += '    <tr>';
                html += '      <td class="bh-rank">' + (idx + 1) + '</td>';
                html += '      <td class="bh-office">' + (row.office?.name || 'Unknown') + '</td>';
                html += '      <td>';
                html += '        <div class="bh-bar-wrap">';
                html += '          <div class="bh-bar ' + barClass + '" style="width:' + pct + '%"></div>';
                html += '        </div>';
                html += '      </td>';
                html += '      <td style="text-align:center;"><span class="bh-count-badge ' + barClass + '">' + row.blocked_count + '</span></td>';
                html += '      <td class="bh-reason">' + (row.reason || '—') + '</td>';
                html += '    </tr>';
            });

            html += '  </tbody>';
            html += '</table>';

            $('#blockingHistoryContent').html(html);
            $('#blockingHistorySummary').text('Total blocks: ' + totalCount + '  •  Rate: ' + blockingRate + '%');
        }

        // ── Blockages Modal ───────────────────────────────────────────────
        let blockagesLoaded = false;
        $('#blockagesModal').on('show.bs.modal', function () {
            if (blockagesLoaded) return;
            blockagesLoaded = true;

            $.ajax({
                url: '{{ route("risk.dashboard.blockages-detail") }}',
                type: 'GET',
                success: function(res) {
                    if (!res.success || !res.data) {
                        $('#blockagesContent').html('<div class="bd-empty">No blocked offices found.</div>');
                        $('#blockagesSummary').text('');
                        return;
                    }
                    renderBlockages(res.data);
                },
                error: function() {
                    $('#blockagesContent').html('<div class="bd-empty text-danger">Failed to load blocked offices.</div>');
                }
            });
        });

        function renderBlockages(data) {
            let totalBlockages = 0;
            let html = '';

            if (!data || data.length === 0) {
                html = '<div class="bd-empty">No blocked offices found.</div>';
            } else {
                html += '<table class="bd-table">';
                html += '  <thead>';
                html += '    <tr>';
                html += '      <th style="width:5%">#</th>';
                html += '      <th style="width:25%">Office</th>';
                html += '      <th style="width:30%">Reason</th>';
                html += '      <th style="width:20%">Blocked At</th>';
                html += '      <th style="width:20%">Unlock Countdown</th>';
                html += '    </tr>';
                html += '  </thead>';
                html += '  <tbody>';

                data.forEach(function(blockage) {
                    totalBlockages++;
                    const unlockTime = blockage.time_to_unlock ? new Date(blockage.time_to_unlock) : null;
                    const now = new Date();
                    let countdownHtml = '';
                    let countdownClass = '';

                    if (unlockTime) {
                        const diffMs = unlockTime - now;
                        if (diffMs <= 0) {
                            countdownHtml = 'Unlocked';
                            countdownClass = 'unlocked';
                        } else {
                            const hours = Math.floor(diffMs / (1000 * 60 * 60));
                            const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);
                            countdownHtml = hours + 'h ' + minutes + 'm ' + seconds + 's';
                            countdownClass = diffMs < 3600000 ? 'warning' : 'locked'; // warning if < 1 hour
                        }
                    } else {
                        countdownHtml = 'No unlock time set';
                        countdownClass = 'locked';
                    }

                    const isUnlocked = blockage.time_to_unlock && now >= new Date(blockage.time_to_unlock);
                    const badgeClass = isUnlocked ? 'bd-badge-expired' : 'bd-badge-active';
                    const badgeText = isUnlocked ? 'UNLOCKED' : 'ACTIVE';

                    html += '    <tr>';
                    html += '      <td>' + blockage.id + '</td>';
                    html += '      <td>';
                    html += '        <div class="bd-office">' + (blockage.office?.name || 'Unknown Office') + '</div>';
                    html += '        <div class="bd-reason">' + (blockage.reason || '—') + '</div>';
                    html += '      </td>';
                    html += '      <td>' + (blockage.reason || '—') + '</td>';
                    html += '      <td class="bd-created">' + (blockage.created_at ? new Date(blockage.created_at).toLocaleString() : '—') + '</td>';
                    html += '      <td><span class="bd-countdown ' + countdownClass + '" data-unlock="' + (blockage.time_to_unlock || '') + '">' + countdownHtml + '</span>';
                    html += '        <span class="bd-badge ' + badgeClass + ' ms-2">' + badgeText + '</span></td>';
                    html += '    </tr>';
                });

                html += '  </tbody>';
                html += '</table>';
            }

            $('#blockagesContent').html(html);
            $('#blockagesSummary').text(totalBlockages + ' blocked office(s)');

            // Start countdown timers
            startBlockageCountdowns();
        }

        function startBlockageCountdowns() {
            $('.bd-countdown[data-unlock]').each(function() {
                const $el = $(this);
                const unlockStr = $el.data('unlock');
                if (!unlockStr) return;

                const unlockTime = new Date(unlockStr);

                function updateCountdown() {
                    const now = new Date();
                    const diffMs = unlockTime - now;

                    if (diffMs <= 0) {
                        $el.text('Unlocked').removeClass('locked warning').addClass('unlocked');
                        $el.next('.bd-badge').text('UNLOCKED').removeClass('bd-badge-active').addClass('bd-badge-expired');
                        return;
                    }

                    const hours = Math.floor(diffMs / (1000 * 60 * 60));
                    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);

                    $el.text(hours + 'h ' + minutes + 'm ' + seconds + 's');

                    if (diffMs < 3600000) { // < 1 hour
                        $el.removeClass('locked').addClass('warning');
                    }
                }

                updateCountdown();
                const interval = setInterval(updateCountdown, 1000);
                $el.data('countdown-interval', interval);
            });
        }

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

    /* ── Branch Cash Balance Cards ─────────────────────────────────── */
    .branch-balance-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }
    @media (max-width: 992px) { .branch-balance-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .branch-balance-grid { grid-template-columns: 1fr; } }

    .branch-balance-card {
        background: linear-gradient(135deg, #2c3e6a 0%, #3b5aad 100%);
        min-height: 130px;
    }
    .branch-balance-card--positive {
        background: linear-gradient(135deg, #0f6e48 0%, #27ae60 100%);
    }
    .branch-balance-card--negative {
        background: linear-gradient(135deg, #8e0a1e 0%, #c0392b 100%);
    }
    .branch-balance-card--zero {
        background: linear-gradient(135deg, #6c757d 0%, #95a5a6 100%);
    }
    .branch-balance-card--unknown {
        background: linear-gradient(135deg, #4a4e69 0%, #6b7289 100%);
    }

    .bb-value {
        font-size: 22px;
        font-weight: 700;
        line-height: 1.15;
    }
    .bb-error {
        font-size: 11px;
        opacity: 0.8;
        margin-top: 4px;
    }
    .bb-wallet-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(255,255,255,0.18);
        font-size: 12px;
        color: #fff;
    }
    .bb-wallet-chip--none {
        background: rgba(255,80,80,0.28);
        color: #ffaaaa;
    }

    /* shimmer for branch balance skeleton cards */
    .shimmer-card {
        background: linear-gradient(135deg, #dde1ea 0%, #eaecf0 100%) !important;
        box-shadow: none !important;
    }
    .shimmer-line {
        background: linear-gradient(90deg, #d0d5df 25%, #e8eaf0 50%, #d0d5df 75%);
        background-size: 200% 100%;
        animation: shimmer-sweep 1.5s infinite;
        border-radius: 4px;
        display: block;
    }
    @keyframes shimmer-sweep {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ── Branch cash balances drill-down (province > district > offices) ── */
    .province-card { grid-column: 1 / -1; }
    .district-card {
        grid-column: 1 / -1;
        background: linear-gradient(135deg, #3b4a6b 0%, #5a7fa1 100%);
    }
    .bb-group-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px;
        cursor: pointer;
        min-height: 50px;
    }
    .bb-group-header:active { opacity: 0.9; }
    .bb-group-name {
        flex: 1;
        font-weight: 600;
        color: #c7d2ff;
        font-size: 14px;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .bb-badge {
        font-size: 10px;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 999px;
        padding: 3px 8px;
    }
    .bb-group-total {
        font-weight: 700;
        color: #fff;
        min-width: 110px;
        text-align: right;
        font-size: 15px;
    }
    .bb-arrow {
        display: inline-block;
        transition: transform .2s ease, color .2s;
        color: #c7d2ff;
    }
    .bb-arrow.open { transform: rotate(90deg); color: #fff; }
    .bb-districts { padding: 0 0 0 18px; }
    .bb-offices {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        padding: 14px 0 14px 30px;
    }
    @media (max-width: 992px) {
        .bb-offices { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .bb-offices { grid-template-columns: 1fr; }
    }

    /* ── Late Disbursements Modal ─────────────────────────────────────── */
    .ld-modal-body { max-height: 65vh; overflow-y: auto; padding-right: 8px; }
    .ld-province { margin-bottom: 18px; }
    .ld-province-header {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; background: #f5f7fa; border-radius: 8px;
        font-weight: 700; color: #1f2430; font-size: 14px;
    }
    .ld-province-header .badge { font-size: 11px; }
    .ld-office { margin: 10px 0 10px 18px; padding-left: 12px; border-left: 3px solid #e8ecf1; }
    .ld-office-header {
        display: flex; align-items: center; gap: 10px;
        font-weight: 600; color: #343b48; font-size: 13px;
    }
    .ld-office-header .badge { font-size: 10px; }
    .ld-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 6px; }
    .ld-table th, .ld-table td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #eef0f3; }
    .ld-table th { color: #697386; font-weight: 600; font-size: 11px; text-transform: uppercase; background: #fafbfc; }
    .ld-table tr:hover td { background: #f9fbfe; }
    .ld-loan-id { font-family: monospace; color: #3a78eb; }
    .ld-amount { font-weight: 600; color: #1f2430; text-align: right; }
    .ld-status { text-transform: capitalize; }
    .ld-badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; }
    .ld-badge-pending { background: #fff3cd; color: #856404; }
    .ld-badge-approved { background: #d1ecf1; color: #0c5460; }
    .ld-badge-new { background: #e2e3e5; color: #383d41; }
    .ld-badge-declined { background: #f8d7da; color: #721c24; }
    .ld-badge-rejected { background: #f8d7da; color: #721c24; }
    .ld-badge-withdrawn { background: #e2e3e5; color: #383d41; }
    .ld-client-name { font-weight: 500; }
    .ld-client-phone { color: #697386; font-size: 11px; }
    .ld-officer-name { font-weight: 500; }
    .ld-officer-phone { color: #697386; font-size: 11px; }
    .ld-date { color: #697386; white-space: nowrap; }
    .ld-empty { text-align: center; color: #8a8fa3; padding: 30px; font-size: 13px; }

    /* ── Late Disbursements analytics strip ────────────────────────────── */
    .ld-analytics-bar {
        display: flex;
        align-items: stretch;
        gap: 0;
        background: linear-gradient(135deg, #fff8ec, #fffdf9);
        border: 1px solid #f5a623;
        border-radius: 10px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .ld-analytics-card {
        flex: 1;
        padding: 14px 20px;
    }
    .ld-analytics-divider {
        width: 1px;
        background: #f5a623;
        opacity: 0.35;
        margin: 10px 0;
    }
    .ld-analytics-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #b5750a;
        margin-bottom: 4px;
    }
    .ld-analytics-value {
        font-size: 26px;
        font-weight: 800;
        color: #1f2430;
        line-height: 1.1;
    }
    .ld-analytics-sub {
        font-size: 11px;
        color: #8a8fa3;
        margin-top: 2px;
    }

    /* ── Blockages Modal ───────────────────────────────────────────────── */
    .bd-modal-body { max-height: 65vh; overflow-y: auto; padding-right: 8px; }
    .bd-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .bd-table th, .bd-table td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #eef0f3; }
    .bd-table th { color: #697386; font-weight: 600; font-size: 11px; text-transform: uppercase; background: #fafbfc; }
    .bd-table tr:hover td { background: #f9fbfe; }
    .bd-office { font-weight: 500; }
    .bd-reason { color: #697386; font-size: 11px; }
    .bd-created { color: #697386; white-space: nowrap; font-size: 11px; }
    .bd-countdown { font-family: monospace; font-weight: 700; }
    .bd-countdown.unlocked { color: #27ae60; }
    .bd-countdown.locked { color: #e74c3c; }
    .bd-countdown.warning { color: #f39c12; }
    .bd-badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; }
    .bd-badge-active { background: #e8f5e9; color: #2e7d32; }
    .bd-badge-expired { background: #fce4ec; color: #c62828; }
    .bd-empty { text-align: center; color: #8a8fa3; padding: 30px; font-size: 13px; }

    /* ── Blocking History Modal ───────────────────────────────────────── */
    .bh-modal-body { max-height: 65vh; overflow-y: auto; padding-right: 8px; }
    .bh-summary-bar {
        display: flex; align-items: center; justify-content: space-between;
        background: linear-gradient(135deg, #f0e8ff, #faf5ff);
        border: 1px solid #d8b4fe;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 16px;
    }
    .bh-rate-display { display: flex; align-items: baseline; gap: 10px; }
    .bh-rate-value { font-size: 32px; font-weight: 800; color: #6c2fa0; }
    .bh-rate-formula { font-size: 12px; color: #8e44ad; opacity: 0.8; }
    .bh-total-badge {
        font-size: 11px; background: #8e44ad; color: #fff;
        border-radius: 999px; padding: 4px 12px; font-weight: 600;
    }
    .bh-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .bh-table th, .bh-table td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #eef0f3; }
    .bh-table th { color: #697386; font-weight: 600; font-size: 11px; text-transform: uppercase; background: #fafbfc; }
    .bh-table tr:hover td { background: #fdf8ff; }
    .bh-rank { color: #aaa; font-size: 11px; }
    .bh-office { font-weight: 600; color: #1f2430; }
    .bh-reason { color: #697386; font-size: 11px; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bh-bar-wrap { background: #f0e8ff; border-radius: 4px; height: 8px; overflow: hidden; }
    .bh-bar { height: 100%; border-radius: 4px; transition: width 0.4s ease; }
    .bh-bar--high  { background: #c0392b; }
    .bh-bar--mid   { background: #f39c12; }
    .bh-bar--low   { background: #27ae60; }
    .bh-count-badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; color: #fff; }
    .bh-count-badge.bh-bar--high  { background: #c0392b; }
    .bh-count-badge.bh-bar--mid   { background: #f39c12; }
    .bh-count-badge.bh-bar--low   { background: #27ae60; }
    .bh-empty { text-align: center; color: #8a8fa3; padding: 30px; font-size: 13px; }
    </style>

<!-- Late Disbursements Modal -->
<div class="modal fade" id="lateDisbursementsModal" tabindex="-1" role="dialog" aria-labelledby="lateDisbursementsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="lateDisbursementsModalLabel">
                    <i class="fa fa-file-text-o me-2"></i>Late Disbursements This Week
                </h4>
            </div>
            <div class="modal-body ld-modal-body">
                <div id="lateDisbursementsContent">
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>Loading late disbursements...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted small" id="lateDisbursementsSummary"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</div>

@include('components.client-search-bottom-sheet')

@endsection
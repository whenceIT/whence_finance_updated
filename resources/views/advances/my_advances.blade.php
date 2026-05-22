@extends('layouts.master')

@section('title')
    My Advances
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-money" style="margin-right:8px;"></i>My Advances</h3>
    </div>
    <div class="box-body">

        @php
            $pendingAdvances  = $advances->where('status', 'pending');
            $approvedAdvances = $advances->where('status', 'approved');
            $closedAdvances   = $advances->where('status', 'closed');
        @endphp

        <style>
            /* ── Tab bar ───────────────────────────────────── */
            .advance-tabs {
                border-bottom: 2px solid #ddd;
                margin-bottom: 0;
                padding: 0 2px;
                display: flex;
            }

            .advance-tabs > li {
                margin-bottom: -2px;
            }

            .advance-tabs > li > a {
                border: none;
                border-bottom: 2px solid transparent;
                border-radius: 0;
                color: #777;
                font-weight: 600;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                padding: 10px 18px;
                margin-right: 2px;
                transition: color 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
            }

            .advance-tabs > li > a:hover:not(.disabled):not(.active) {
                color: #3c8dbc;
                background-color: transparent;
                border-bottom-color: #3c8dbc;
            }

            .advance-tabs > li > a.active,
            .advance-tabs > li > a:focus.active {
                color: #fff;
                background-color: #3c8dbc;
                border-bottom: 2px solid #3c8dbc;
            }

            .advance-tabs > li > a.disabled {
                color: #ccc;
                cursor: not-allowed;
                pointer-events: none;
                opacity: 0.6;
            }

            .advance-tabs .badge {
                font-size: 11px;
                font-weight: 700;
                margin-left: 5px;
                vertical-align: middle;
                border-radius: 10px;
                padding: 2px 7px;
            }

            /* ── Tab content panel ─────────────────────────── */
            .advance-tabs + .tab-content {
                border: 1px solid #ddd;
                border-top: none;
                padding: 20px;
                background: #fff;
                border-radius: 0 0 4px 4px;
            }

            /* Fade-in for active pane — use opacity only so Bootstrap
               JS can still flip display:block on the active pane */
            .advance-tabs + .tab-content .tab-pane:not(.active) {
                opacity: 0;
            }

            /* Ensure the active pane is fully opaque
               (Bootstrap 3 adds the .active class via JS inline display)
               The !important outranks Bootstrap's inline style on opacity */
            .advance-tabs + .tab-content .tab-pane.active {
                opacity: 1;
                display: block !important;
            }

            /* ── Empty panel ────────────────────────────────── */
            .advance-empty-panel {
                text-align: center;
                padding: 50px 20px;
                color: #999;
            }

            .advance-empty-panel i {
                font-size: 48px;
                color: #ddd;
                margin-bottom: 16px;
                display: block;
            }

            /* ── Section heading inside each pane ──────────── */
            .advance-pane-heading {
                font-size: 13px;
                font-weight: 700;
                color: #555;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin: 0 0 14px 0;
                padding-bottom: 8px;
                border-bottom: 1px solid #eee;
            }

            /* ── Top-up form ───────────────────────────────── */
            .top-up-form {
                margin-top: 10px;
                padding: 14px;
                background: #f9f9fa;
                border: 1px solid #e0e0e0;
                border-radius: 4px;
            }

            .top-up-form .form-group:last-child {
                margin-bottom: 0;
            }

            .top-up-form .btn:last-child {
                margin-top: 6px;
            }

            /* ── Responsive: stack tabs on mobile ──────────── */
            @media (max-width: 767px) {
                .advance-tabs {
                    flex-wrap: wrap;
                    border-bottom: none;
                }

                .advance-tabs > li {
                    flex: 1 1 33.333%;
                    margin-bottom: 0;
                }

                .advance-tabs > li > a {
                    text-align: center;
                    font-size: 12px;
                    padding: 9px 8px;
                    border: 1px solid #ddd;
                    border-bottom: none;
                    margin-right: 0;
                    border-radius: 4px 4px 0 0;
                    white-space: nowrap;
                }

                .advance-tabs > li + li > a {
                    margin-left: -1px;
                }

                .advance-tabs > li > a.active {
                    border-bottom: 2px solid #3c8dbc;
                    z-index: 2;
                }

                .advance-tabs + .tab-content {
                    border-radius: 0 0 4px 4px;
                }
            }
        </style>

        <ul class="nav nav-tabs advance-tabs" role="tablist">
            <li role="presentation" class="active">
                <a aria-controls="pendingAdvances" aria-selected="true" data-toggle="tab"
                   href="#pendingAdvances" id="pending-tab" role="tab">
                    <i class="fa fa-clock-o"></i> Pending
                    @if($pendingAdvances->isNotEmpty())
                        <span class="badge badge-warning">{{ $pendingAdvances->count() }}</span>
                    @endif
                </a>
            </li>
            <li role="presentation">
                <a aria-controls="approvedAdvances" aria-selected="false"
                   class="@if($approvedAdvances->isEmpty()) disabled @endif" data-toggle="tab"
                   href="#approvedAdvances" id="approved-tab" role="tab">
                    <i class="fa fa-check-circle"></i> Approved
                    @if($approvedAdvances->isNotEmpty())
                        <span class="badge badge-success">{{ $approvedAdvances->count() }}</span>
                    @endif
                </a>
            </li>
            <li role="presentation">
                <a aria-controls="closedAdvances" aria-selected="false"
                   class="@if($closedAdvances->isEmpty()) disabled @endif" data-toggle="tab"
                   href="#closedAdvances" id="closed-tab" role="tab">
                    <i class="fa fa-lock"></i> Closed
                    @if($closedAdvances->isNotEmpty())
                        <span class="badge bg-gray">{{ $closedAdvances->count() }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- PENDING TAB (default active) --}}
            <div class="tab-pane fade active" id="pendingAdvances">
                <p class="advance-pane-heading"><i class="fa fa-clock-o" style="color:#f39c12;margin-right:6px;"></i>Pending Advances</p>

                @if($pendingAdvances->isNotEmpty())
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount Requested</th>
                            <th>Installment Amount</th>
                            <th>Installments</th>
                            <th>Next Payment Date</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Date Requested</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pendingAdvances as $a)
                            <tr>
                                <td><strong>{{ $a->first_name }} {{ $a->last_name }}</strong></td>
                                <td>{{ number_format($a->amount, 2) }}</td>
                                <td>{{ number_format($a->installment_amount, 2) }}</td>
                                <td><span class="label label-info">{{ $a->installments }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($a->expected_repayment_dates)->format('Y-m-d') }}</td>
                                <td>{{ number_format($a->amount_paid, 2) }}</td>
                                <td><strong style="color:#d9534f;">{{ number_format($a->remaining_amount, 2) }}</strong></td>
                                <td>{{ $a->date_requested ? $a->date_requested->format('Y-m-d') : 'N/A' }}</td>
                                <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-xs cancel-advance-btn"
                                                    data-form-id="{{ $a->id }}">
                                                <i class="fa fa-times"></i> Cancel request
                                            </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="advance-empty-panel">
                        <i class="fa fa-check-circle" style="color:#5cb85c;font-size:36px;"></i>
                        <p class="text-muted">No pending advances.</p>
                    </div>
                @endif
            </div>

            {{-- APPROVED TAB --}}
            <div class="tab-pane fade" id="approvedAdvances">
                <p class="advance-pane-heading"><i class="fa fa-check-circle" style="color:#5cb85c;margin-right:6px;"></i>Approved Advances</p>

                @if($approvedAdvances->isNotEmpty())
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount Requested</th>
                            <th>Installment Amount</th>
                            <th>Installments</th>
                            <th>Next Payment Date</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Approved On</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($approvedAdvances as $a)
                            <tr>
                                <td><strong>{{ $a->first_name }} {{ $a->last_name }}</strong></td>
                                <td>{{ number_format($a->amount, 2) }}</td>
                                <td>{{ number_format($a->installment_amount, 2) }}</td>
                                <td><span class="label label-info">{{ $a->installments }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($a->expected_repayment_dates)->format('Y-m-d') }}</td>
                                <td>{{ number_format($a->amount_paid, 2) }}</td>
                                <td><strong style="color:#d9534f;">{{ number_format($a->remaining_amount, 2) }}</strong></td>
                                <td>{{ $a->date_approved ? $a->date_approved->format('Y-m-d') : 'N/A' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-xs" onclick="toggleTopUpForm({{ $a->id }})">
                                        <i class="fa fa-plus"></i> Top-up
                                    </button>
                                    <form id="topUpForm{{ $a->id }}" class="top-up-form" style="display:none;"
                                          action="{{ route('advances.submitTopUp', $a->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="top_up_amount">Top-up Amount</label>
                                            <input type="number" name="top_up_amount" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="top_up_date">Top-up Date</label>
                                            <input type="date" name="top_up_date" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="installments">Increase Installments (Max: 3)</label>
                                            <select name="installments" class="form-control">
                                                @for($i = $a->installments; $i <= 3; $i++)
                                                    <option value="{{ $i }}" {{ $i == $a->installments ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-xs">
                                            <i class="fa fa-send"></i> Submit Top-up
                                        </button>
                                        <button type="button" class="btn btn-default btn-xs" onclick="toggleTopUpForm({{ $a->id }})">
                                            Cancel
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="advance-empty-panel">
                        <i class="fa fa-minus-circle" style="color:#ccc;font-size:36px;"></i>
                        <p class="text-muted">No approved advances.</p>
                    </div>
                @endif
            </div>

            {{-- CLOSED TAB --}}
            <div class="tab-pane fade" id="closedAdvances">
                <p class="advance-pane-heading"><i class="fa fa-lock" style="color:#777;margin-right:6px;"></i>Closed Advances</p>

                @if($closedAdvances->isNotEmpty())
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount Requested</th>
                            <th>Installment Amount</th>
                            <th>Installments</th>
                            <th>Next Payment Date</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Approved On</th>
                            <th>Closed On</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($closedAdvances as $a)
                            <tr>
                                <td><strong>{{ $a->first_name }} {{ $a->last_name }}</strong></td>
                                <td>{{ number_format($a->amount, 2) }}</td>
                                <td>{{ number_format($a->installment_amount, 2) }}</td>
                                <td><span class="label label-info">{{ $a->installments }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($a->expected_repayment_dates)->format('Y-m-d') }}</td>
                                <td>{{ number_format($a->amount_paid, 2) }}</td>
                                <td><strong style="color:#5cb85c;">{{ number_format($a->remaining_amount, 2) }}</strong></td>
                                <td>{{ $a->date_approved ? $a->date_approved->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $a->updated_at ? $a->updated_at->format('Y-m-d') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="advance-empty-panel">
                        <i class="fa fa-minus-circle" style="color:#ccc;font-size:36px;"></i>
                        <p class="text-muted">No closed advances.</p>
                    </div>
                @endif
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger" style="margin-top:16px;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" style="margin-top:16px;">
                {{ session('success') }}
            </div>
        @endif

    </div>
</div>

<!-- Modal for Pending Advances -->
<div class="modal fade" id="pendingAdvancesModal" tabindex="-1" role="dialog"
     aria-labelledby="pendingAdvancesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingAdvancesModalLabel">Pending Advances</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($pending_advances->isNotEmpty())
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Amount Requested</th>
                            <th>Installments</th>
                            <th>Installment Amount</th>
                            <th>Date Requested</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pending_advances as $advance)
                            <tr>
                                <td>{{ $advance->id }}</td>
                                <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
                                <td>{{ number_format($advance->amount, 2) }}</td>
                                <td>{{ $advance->installments }}</td>
                                <td>{{ number_format($advance->installment_amount, 2) }}</td>
                                <td>{{ $advance->date_requested ? $advance->date_requested->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                        <button type="button" class="btn btn-danger btn-xs cancel-advance-btn"
                                                data-form-id="{{ $advance->id }}">
                                            <i class="fa fa-times"></i> Cancel request
                                        </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No pending advances.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Cancel Modal (shared across all row buttons) -->
<div class="modal fade" id="confirmCancelModal" tabindex="-1" role="dialog"
     aria-labelledby="confirmCancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document" style="margin-top:15vh;">
        <div class="modal-content">
            <div class="modal-body text-center" style="padding:24px 24px 16px;">
                <div style="width:52px;height:52px;border-radius:50%;background:#fdf3f2;
                            margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa fa-exclamation-triangle" style="font-size:22px;color:#d9534f;"></i>
                </div>
                <h4 id="confirmCancelModalLabel" style="margin:0 0 6px;font-size:15px;font-weight:600;color:#333;">
                    Cancel this advance request?
                </h4>
                <p style="margin:0;color:#999;font-size:13px;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer" style="padding:8px 16px 16px;border-top:none;justify-content:center;gap:8px;">
                <button type="button" class="btn btn-default btn-flat btn-xs"
                        style="min-width:74px;" data-dismiss="modal">Keep request</button>
                <button type="button" id="confirmCancelBtn"
                        class="btn btn-danger btn-flat btn-xs"
                        style="min-width:74px;">Yes, cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-scripts')
<script>
    /* Runs after layout footer scripts (incl. local jQuery 2.x). */
    addEventListener('load', function () {
        if (typeof jQuery === 'undefined') return;

        // ── Top-up toggle ──────────────────────────────────
        window.toggleTopUpForm = function (advanceId) {
            jQuery('#topUpForm' + advanceId).toggle();
        };

        // ── Confirm-cancel modal ───────────────────────────
        jQuery('.cancel-advance-btn').on('click', function () {
            jQuery('#confirmCancelModal').data('form-id', jQuery(this).data('form-id'));
            jQuery('#confirmCancelModal').modal('show');
        });

        jQuery('#confirmCancelBtn').on('click', function () {
            var id = jQuery('#confirmCancelModal').data('form-id');
            if (!id) return;
            var form = document.createElement('form');
            form.action  = '/advance/' + id;
            form.method  = 'POST';
            form.style.display = 'none';
            form.innerHTML  = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                              '<input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
            jQuery('#confirmCancelModal').modal('hide');
        });
    });
</script>
@endsection

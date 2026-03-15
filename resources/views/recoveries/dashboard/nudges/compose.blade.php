@extends('layouts.master')

@section('title')
    Send Nudges
@endsection

@section('content')

{{-- Filter (own GET form, completely outside the POST form) --}}
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Cases</h3>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ url('recovery/nudge/compose') }}" class="form-inline">
            <div class="form-group" style="margin-right:8px">
                <select name="category" class="form-control input-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ ($filter['category'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-right:8px">
                <select name="assigned_specialist_id" class="form-control input-sm">
                    <option value="">All Specialists</option>
                    @foreach($specialists as $u)
                        <option value="{{ $u->id }}" {{ ($filter['assigned_specialist_id'] ?? '') == $u->id ? 'selected' : '' }}>
                            {{ trim($u->first_name . ' ' . $u->last_name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i> Filter</button>
            <a href="{{ url('recovery/nudge/compose') }}" class="btn btn-sm btn-link">Reset</a>
            <span class="text-muted" style="margin-left:14px;font-size:12px">{{ $cases->count() }} case(s) found</span>
        </form>
    </div>
</div>

{{-- Nudge POST form — no nested forms inside --}}
<form method="POST" action="{{ url('recovery/nudge/send-bulk') }}" id="nudge-form">
@csrf
<input type="hidden" name="channel" id="channel-input" value="sms">
<input type="hidden" name="message" id="message-hidden">
{{-- case_ids[] injected by JS on submit --}}

<div class="row">

    {{-- LEFT: case selector --}}
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-users"></i> Select Recipients
                    <span class="badge bg-blue">{{ $cases->count() }}</span>
                    &nbsp;
                    <button type="button" class="btn btn-xs btn-default" id="select-all-btn">
                        <i class="fa fa-check-square-o"></i> All
                    </button>
                    <button type="button" class="btn btn-xs btn-default" id="deselect-all-btn">
                        <i class="fa fa-square-o"></i> None
                    </button>
                </h3>
            </div>
            <div class="box-body no-padding" style="max-height:520px;overflow-y:auto">

                @forelse($cases as $case)
                @php
                    $clientName  = ($case->client->client_type ?? '') === 'business'
                        ? ($case->client->full_name ?? '—')
                        : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—');
                    $phone       = $case->client->phone ?? null;
                    $hasPhone    = (bool) $phone;
                    $outstanding = number_format(($case->loan_outstanding_amount ?? 0) - ($case->amount_recovered ?? 0), 2);
                @endphp

                <div class="case-item{{ $hasPhone ? ' selectable' : ' no-phone' }}"
                     data-id="{{ $case->id }}"
                     style="display:flex;align-items:center;gap:12px;padding:10px 14px;
                            border-bottom:1px solid #f0f0f0;
                            cursor:{{ $hasPhone ? 'pointer' : 'default' }};
                            {{ $hasPhone ? '' : 'opacity:.5' }}">

                    {{-- Pill toggle --}}
                    @if($hasPhone)
                    <div class="select-pill"
                         style="flex-shrink:0;padding:2px 10px;border-radius:12px;
                                border:1px solid #ccc;font-size:11px;color:#999;
                                background:#fff;white-space:nowrap;
                                transition:all .15s;user-select:none;pointer-events:none">
                        <i class="fa fa-circle-o" style="margin-right:3px"></i> Select
                    </div>
                    @else
                    <div style="flex-shrink:0;padding:2px 10px;border-radius:12px;
                                border:1px solid #ddd;font-size:11px;color:#bbb;
                                background:#f9f9f9;white-space:nowrap">
                        <i class="fa fa-ban"></i> No phone
                    </div>
                    @endif

                    <div style="flex:1;min-width:0">
                        <strong style="font-size:13px">{{ $clientName }}</strong>
                        <span class="label label-default" style="margin-left:4px;font-size:10px">
                            {{ $categories[$case->category] ?? $case->category }}
                        </span>
                        <br>
                        <small class="text-muted">
                            {{ $case->case_number }}
                            @if($hasPhone)
                                &nbsp;&middot;&nbsp;<i class="fa fa-phone text-success"></i> {{ $phone }}
                            @endif
                            &nbsp;&middot;&nbsp;Outstanding:
                            <strong class="text-danger">K{{ $outstanding }}</strong>
                        </small>
                    </div>

                    @if($case->assignedSpecialist)
                    <small class="text-muted" style="flex-shrink:0;white-space:nowrap">
                        <i class="fa fa-user-o"></i>
                        {{ trim($case->assignedSpecialist->first_name . ' ' . $case->assignedSpecialist->last_name) }}
                    </small>
                    @endif

                </div>
                @empty
                <div style="padding:30px;text-align:center" class="text-muted">
                    <i class="fa fa-inbox fa-2x"></i><br><br>No active cases match your filter.
                </div>
                @endforelse

            </div>
            <div class="box-footer" style="font-size:12px;color:#888;display:flex;justify-content:space-between;align-items:center">
                <span><strong id="selected-count">0</strong> selected</span>
                <span><i class="fa fa-info-circle"></i> Cases without a phone number cannot be selected</span>
            </div>
        </div>
    </div>

    {{-- RIGHT: composer --}}
    <div class="col-md-5">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-paper-plane"></i> Compose Nudge</h3>
            </div>
            <div class="box-body">

                <div class="form-group">
                    <label>Channel</label>
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary channel-btn" data-channel="sms">
                                <i class="fa fa-comment"></i> SMS
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default channel-btn" data-channel="whatsapp">
                                <i class="fa fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                    </div>
                </div>

                <div class="callout callout-info" style="padding:7px 12px;font-size:12px">
                    <strong>Placeholders:</strong>
                    <code>{name}</code> &nbsp;
                    <code>{balance}</code> &nbsp;
                    <code>{case_number}</code>
                </div>

                <div class="form-group">
                    <label>Message <span class="text-danger">*</span></label>
                    <textarea id="message-body" class="form-control" rows="7"
                              maxlength="1000" placeholder="Type your message...">{{ $defaultSms }}</textarea>
                    <span class="help-block" style="margin-bottom:0">
                        <span id="char-count">{{ strlen($defaultSms) }}</span> / 1000 chars
                    </span>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label>Preview <small class="text-muted">(first selected)</small></label>
                    <div id="preview-box" class="well well-sm"
                         style="font-size:12px;min-height:44px;white-space:pre-wrap;margin-bottom:0;color:#555">
                        Select a case to preview
                    </div>
                </div>

            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-success btn-block" id="send-btn" disabled>
                    <i class="fa fa-paper-plane"></i>
                    Send to <span id="send-count">0</span> client(s)
                </button>
            </div>
        </div>

        @if(session('nudge_errors') && count(session('nudge_errors')))
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-exclamation-circle"></i> Send Failures</h3>
            </div>
            <div class="box-body">
                <ul style="margin:0;padding-left:16px">
                    @foreach(session('nudge_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Case data ─────────────────────────────────────────────
    var caseData = {};
    @foreach($cases as $case)
    @php
        $cn  = ($case->client->client_type ?? '') === 'business'
            ? ($case->client->full_name ?? 'Valued Client')
            : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: 'Valued Client');
        $bal = 'K' . number_format(($case->loan_outstanding_amount ?? 0) - ($case->amount_recovered ?? 0), 2);
    @endphp
    caseData['{{ $case->id }}'] = { name: '{{ addslashes($cn) }}', balance: '{{ $bal }}', case_number: '{{ $case->case_number }}' };
    @endforeach

    var templates = {
        sms:      {{ json_encode($defaultSms) }},
        whatsapp: {{ json_encode($defaultWa) }}
    };

    var selected = new Set();

    // ── Pill helpers ──────────────────────────────────────────
    var PILL_OFF = { border: '1px solid #ccc',     background: '#fff',     color: '#999', html: '<i class="fa fa-circle-o" style="margin-right:3px"></i> Select'    };
    var PILL_ON  = { border: '1px solid #3c8dbc',  background: '#3c8dbc',  color: '#fff', html: '<i class="fa fa-check-circle" style="margin-right:3px"></i> Selected' };

    function applyPill(pill, state) {
        pill.style.border     = state.border;
        pill.style.background = state.background;
        pill.style.color      = state.color;
        pill.innerHTML        = state.html;
    }

    // ── Click to select / deselect ────────────────────────────
    document.querySelectorAll('.case-item.selectable').forEach(function (item) {
        item.addEventListener('click', function () {
            var id   = this.dataset.id;
            var pill = this.querySelector('.select-pill');
            if (selected.has(id)) {
                selected.delete(id);
                applyPill(pill, PILL_OFF);
                this.style.background = '';
            } else {
                selected.add(id);
                applyPill(pill, PILL_ON);
                this.style.background = '#f0f8ff';
            }
            updateUI();
        });
    });

    // ── Select all / none ─────────────────────────────────────
    document.getElementById('select-all-btn').addEventListener('click', function (e) {
        e.stopPropagation();
        document.querySelectorAll('.case-item.selectable').forEach(function (item) {
            selected.add(item.dataset.id);
            applyPill(item.querySelector('.select-pill'), PILL_ON);
            item.style.background = '#f0f8ff';
        });
        updateUI();
    });

    document.getElementById('deselect-all-btn').addEventListener('click', function (e) {
        e.stopPropagation();
        document.querySelectorAll('.case-item.selectable').forEach(function (item) {
            selected.delete(item.dataset.id);
            applyPill(item.querySelector('.select-pill'), PILL_OFF);
            item.style.background = '';
        });
        updateUI();
    });

    // ── Channel toggle ────────────────────────────────────────
    document.querySelectorAll('.channel-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.channel-btn').forEach(function (b) {
                b.classList.replace('btn-primary', 'btn-default');
            });
            this.classList.replace('btn-default', 'btn-primary');
            document.getElementById('channel-input').value = this.dataset.channel;
            document.getElementById('message-body').value  = templates[this.dataset.channel] || '';
            updateCharCount();
            updatePreview();
        });
    });

    // ── Char count ────────────────────────────────────────────
    function updateCharCount() {
        document.getElementById('char-count').textContent =
            document.getElementById('message-body').value.length;
    }
    document.getElementById('message-body').addEventListener('input', function () {
        updateCharCount();
        updatePreview();
    });

    // ── Preview ───────────────────────────────────────────────
    function buildMessage(tpl, data) {
        return tpl
            .replace(/{name}/g,        data.name        || '')
            .replace(/{balance}/g,     data.balance     || '')
            .replace(/{case_number}/g, data.case_number || '');
    }

    function updatePreview() {
        var firstId = Array.from(selected)[0];
        document.getElementById('preview-box').textContent = firstId
            ? buildMessage(document.getElementById('message-body').value, caseData[firstId] || {})
            : 'Select a case to preview';
    }

    // ── Submit: inject hidden inputs ──────────────────────────
    document.getElementById('nudge-form').addEventListener('submit', function (e) {
        if (selected.size === 0) {
            e.preventDefault();
            alert('Please select at least one recipient.');
            return;
        }
        document.getElementById('message-hidden').value =
            document.getElementById('message-body').value;

        var form = this;
        form.querySelectorAll('input[name="case_ids[]"]').forEach(function (el) { el.remove(); });
        selected.forEach(function (id) {
            var inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'case_ids[]';
            inp.value = id;
            form.appendChild(inp);
        });
    });

    // ── UI state ──────────────────────────────────────────────
    function updateUI() {
        var count = selected.size;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('send-count').textContent     = count;
        document.getElementById('send-btn').disabled          = count === 0;
        updatePreview();
    }

})();
</script>
@endpush

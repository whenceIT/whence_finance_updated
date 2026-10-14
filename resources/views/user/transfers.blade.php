@extends('layouts.master')
@section('title')
Client Transfers
@endsection

@section('content')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Transfer Clients</h3>
    </div>

    <form action="{{url('user/transfer_clients') }}" method="POST">
        @csrf

        <div class="box-body">

            {{-- Instructions --}}
            <div class="alert alert-info">
                <strong>Instructions:</strong><br>
                1. Select the <strong>Loan Consultant</strong> who will <u>receive</u> the selected clients.<br>
                2. Select one or more <strong>Clients</strong> that you want to move to the chosen Loan Consultant.<br>
                3. Click <strong>"Transfer Selected Clients"</strong> to complete the transfer.<br>
                <br>
                <strong>Note:</strong> All loans belonging to the selected clients will also be reassigned to the chosen Loan Consultant.
            </div>

            {{-- Loan Consultant --}}
            <div class="form-group">
                <label class="control-label col-md-2">Loan Consultant</label>
                <div class="col-md-4">
                    {{-- Hidden input that actually submits the value --}}
                    <input type="hidden" name="loan_consultant_id" id="loan_consultant_id_input" required>

                    <div class="searchable-select" id="consultant-dropdown">
                        <div class="searchable-select__trigger" id="consultant-trigger" tabindex="0">
                            <span class="searchable-select__label" id="consultant-label">Select loan consultant...</span>
                            <span class="searchable-select__arrow">&#9660;</span>
                        </div>
                        <div class="searchable-select__panel" id="consultant-panel">
                            <input type="text" class="searchable-select__search" id="consultant-search"
                                   placeholder="Type to search..." autocomplete="off">
                            <ul class="searchable-select__list" id="consultant-list">
                                @foreach(\App\Models\User::where('office_id',$userBranch)->get() as $key)
                                    @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                        <li class="searchable-select__item"
                                            data-value="{{$key->id}}"
                                            data-label="{{$key->first_name}} {{$key->last_name}}">
                                            {{$key->first_name}} {{$key->last_name}}
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <div class="searchable-select__empty" id="consultant-empty" style="display:none;">
                                No results found.
                            </div>
                        </div>
                    </div>

                    <small class="text-muted">
                        Select the Loan Consultant who will receive the clients.
                    </small>
                </div>
            </div>

            <div class="clearfix"></div>
            <br>

            {{-- Multiple Clients Selection --}}
            <div class="form-group">
                <label class="control-label col-md-2">Select Clients</label>
                <div class="col-md-6">
                    {{-- Hidden inputs are injected by JS when items are selected --}}
                    <div id="clients-hidden-inputs"></div>

                    <div class="searchable-select searchable-select--multi" id="clients-dropdown">
                        <div class="searchable-select__trigger searchable-select__trigger--multi" id="clients-trigger" tabindex="0">
                            <span class="searchable-select__tags" id="clients-tags">
                                <span class="searchable-select__placeholder" id="clients-placeholder">Select clients...</span>
                            </span>
                            <span class="searchable-select__arrow">&#9660;</span>
                        </div>
                        <div class="searchable-select__panel" id="clients-panel">
                            <div class="searchable-select__panel-header">
                                <input type="text" class="searchable-select__search" id="clients-search"
                                       placeholder="Type to search..." autocomplete="off">
                                <button type="button" class="searchable-select__clear-all" id="clients-clear-all" style="display:none;">
                                    Clear all
                                </button>
                            </div>
                            <ul class="searchable-select__list" id="clients-list">
                                @foreach($clients as $client)
                                    @php
                                        $label = $client->client_type === 'individual'
                                            ? trim($client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name)
                                              . ' (' . $client->account_no . ')'
                                              . ($client->nrc_number ? ' (' . $client->nrc_number . ')' : '')
                                            : $client->full_name . ' (' . $client->account_no . ')';
                                    @endphp
                                    <li class="searchable-select__item searchable-select__item--check"
                                        data-value="{{ $client->id }}"
                                        data-label="{{ $label }}">
                                        <span class="searchable-select__check">&#10003;</span>
                                        {{ $label }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="searchable-select__empty" id="clients-empty" style="display:none;">
                                No results found.
                            </div>
                        </div>
                    </div>

                    <small class="text-muted">
                        @if(isset($role) && in_array($role->role_id, ['1', '10']))
                            Showing all clients &mdash; use the search box to filter.
                        @else
                            Showing clients under your branch. Use the search box to filter.
                        @endif
                    </small>
                </div>
            </div>

        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">
                Transfer Selected Clients
            </button>
        </div>
    </form>
</div>

@endsection

@section('footer-scripts')
    <style>
        .searchable-select {
            position: relative;
            user-select: none;
        }
        .searchable-select__trigger {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 12px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            min-height: 34px;
            font-size: 14px;
            color: #555;
        }
        .searchable-select__trigger:focus {
            outline: none;
            border-color: #66afe9;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
        }
        .searchable-select__trigger.is-selected {
            color: #333;
        }
        .searchable-select__arrow {
            font-size: 10px;
            color: #999;
            margin-left: 8px;
        }
        .searchable-select__panel {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #fff;
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,.15);
        }
        .searchable-select__panel.is-open {
            display: block;
        }
        .searchable-select__search {
            width: 100%;
            padding: 6px 10px;
            border: none;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
        }
        .searchable-select__list {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
        }
        .searchable-select__item {
            padding: 7px 12px;
            font-size: 14px;
            cursor: pointer;
            color: #333;
        }
        .searchable-select__item:hover,
        .searchable-select__item.is-active {
            background: #337ab7;
            color: #fff;
        }
        .searchable-select__item.is-hidden {
            display: none;
        }
        .searchable-select__empty {
            padding: 8px 12px;
            font-size: 13px;
            color: #999;
        }

        /* ── Multi-select extensions ───────────────────────────────── */
        .searchable-select--multi .searchable-select__trigger--multi {
            align-items: flex-start;
            flex-wrap: wrap;
            min-height: 36px;
            gap: 4px;
            padding: 5px 8px;
            cursor: pointer;
        }
        .searchable-select__tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            flex: 1;
        }
        .searchable-select__placeholder {
            color: #999;
            font-size: 14px;
            line-height: 24px;
        }
        .searchable-select__tag {
            display: inline-flex;
            align-items: center;
            background: #337ab7;
            color: #fff;
            border-radius: 3px;
            padding: 2px 6px;
            font-size: 12px;
            line-height: 20px;
            gap: 5px;
        }
        .searchable-select__tag-remove {
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            opacity: 0.8;
        }
        .searchable-select__tag-remove:hover {
            opacity: 1;
        }
        .searchable-select__panel-header {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        .searchable-select__panel-header .searchable-select__search {
            border: none;
            border-bottom: none;
            flex: 1;
        }
        .searchable-select__clear-all {
            background: none;
            border: none;
            color: #a94442;
            font-size: 12px;
            padding: 4px 8px;
            cursor: pointer;
            white-space: nowrap;
        }
        .searchable-select__clear-all:hover {
            text-decoration: underline;
        }
        .searchable-select__item--check {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .searchable-select__check {
            display: inline-block;
            width: 14px;
            text-align: center;
            font-size: 12px;
            color: transparent; /* hidden until selected */
            flex-shrink: 0;
        }
        .searchable-select__item--check.is-active .searchable-select__check {
            color: #fff;
        }
        /* keep panel open on multi — handled via JS, not CSS hover */
    </style>

    <script>
        $(document).ready(function () {
            // ── Searchable dropdown (no Select2) ──────────────────────────
            (function () {
                const trigger   = document.getElementById('consultant-trigger');
                const panel     = document.getElementById('consultant-panel');
                const search    = document.getElementById('consultant-search');
                const list      = document.getElementById('consultant-list');
                const empty     = document.getElementById('consultant-empty');
                const label     = document.getElementById('consultant-label');
                const hidden    = document.getElementById('loan_consultant_id_input');
                const items     = list.querySelectorAll('.searchable-select__item');

                function openPanel() {
                    panel.classList.add('is-open');
                    search.value = '';
                    filterItems('');
                    search.focus();
                }

                function closePanel() {
                    panel.classList.remove('is-open');
                }

                function filterItems(query) {
                    let visible = 0;
                    items.forEach(function (item) {
                        const text = item.dataset.label.toLowerCase();
                        if (text.includes(query.toLowerCase())) {
                            item.classList.remove('is-hidden');
                            visible++;
                        } else {
                            item.classList.add('is-hidden');
                        }
                    });
                    empty.style.display = visible === 0 ? 'block' : 'none';
                }

                trigger.addEventListener('click', function () {
                    panel.classList.contains('is-open') ? closePanel() : openPanel();
                });

                trigger.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openPanel(); }
                    if (e.key === 'Escape') { closePanel(); }
                });

                search.addEventListener('input', function () {
                    filterItems(this.value);
                });

                items.forEach(function (item) {
                    item.addEventListener('click', function () {
                        hidden.value  = this.dataset.value;
                        label.textContent = this.dataset.label;
                        trigger.classList.add('is-selected');
                        items.forEach(function (i) { i.classList.remove('is-active'); });
                        this.classList.add('is-active');
                        closePanel();
                        trigger.focus();
                    });
                });

                // Close when clicking outside
                document.addEventListener('click', function (e) {
                    if (!document.getElementById('consultant-dropdown').contains(e.target)) {
                        closePanel();
                    }
                });

                // Validate before submit — ensure a consultant was picked
                trigger.closest('form').addEventListener('submit', function (e) {
                    if (!hidden.value) {
                        e.preventDefault();
                        trigger.focus();
                        trigger.style.borderColor = '#a94442';
                        trigger.title = 'Please select a Loan Consultant.';
                        setTimeout(function () { trigger.style.borderColor = ''; }, 3000);
                    }
                });
            }());
            // ─────────────────────────────────────────────────────────────

            // ── Multi-select clients dropdown ─────────────────────────
            (function () {
                const trigger      = document.getElementById('clients-trigger');
                const panel        = document.getElementById('clients-panel');
                const search       = document.getElementById('clients-search');
                const list         = document.getElementById('clients-list');
                const empty        = document.getElementById('clients-empty');
                const tagsWrap     = document.getElementById('clients-tags');
                const placeholder  = document.getElementById('clients-placeholder');
                const hiddenWrap   = document.getElementById('clients-hidden-inputs');
                const clearAllBtn  = document.getElementById('clients-clear-all');
                const items        = list.querySelectorAll('.searchable-select__item');

                // Track selected values: Map<value, label>
                const selected = new Map();

                /* ── Panel open/close ── */
                function openPanel() {
                    panel.classList.add('is-open');
                    search.value = '';
                    filterItems('');
                    search.focus();
                }
                function closePanel() {
                    panel.classList.remove('is-open');
                }

                /* ── Filter list items by search text ── */
                function filterItems(query) {
                    let visible = 0;
                    items.forEach(function (item) {
                        const text = item.dataset.label.toLowerCase();
                        if (text.includes(query.toLowerCase())) {
                            item.classList.remove('is-hidden');
                            visible++;
                        } else {
                            item.classList.add('is-hidden');
                        }
                    });
                    empty.style.display = visible === 0 ? 'block' : 'none';
                }

                /* ── Re-render the tag pills in the trigger ── */
                function renderTags() {
                    // Remove existing tags (but leave placeholder)
                    tagsWrap.querySelectorAll('.searchable-select__tag').forEach(function (t) {
                        t.remove();
                    });

                    if (selected.size === 0) {
                        placeholder.style.display = '';
                        clearAllBtn.style.display = 'none';
                    } else {
                        placeholder.style.display = 'none';
                        clearAllBtn.style.display = '';
                        selected.forEach(function (label, value) {
                            const tag = document.createElement('span');
                            tag.className = 'searchable-select__tag';
                            tag.dataset.value = value;
                            tag.innerHTML =
                                '<span class="searchable-select__tag-text">' + escHtml(label) + '</span>' +
                                '<span class="searchable-select__tag-remove" data-value="' + escHtml(value) + '" title="Remove">&times;</span>';
                            tagsWrap.insertBefore(tag, placeholder);
                        });
                    }

                    // Sync hidden inputs
                    hiddenWrap.innerHTML = '';
                    selected.forEach(function (label, value) {
                        const inp = document.createElement('input');
                        inp.type  = 'hidden';
                        inp.name  = 'clients[]';
                        inp.value = value;
                        hiddenWrap.appendChild(inp);
                    });
                }

                /* ── Toggle a single item's selected state ── */
                function toggleItem(item) {
                    const value = item.dataset.value;
                    const label = item.dataset.label;
                    if (selected.has(value)) {
                        selected.delete(value);
                        item.classList.remove('is-active');
                    } else {
                        selected.set(value, label);
                        item.classList.add('is-active');
                    }
                    renderTags();
                }

                /* ── Clear all selections ── */
                function clearAll() {
                    selected.clear();
                    items.forEach(function (i) { i.classList.remove('is-active'); });
                    renderTags();
                }

                /* ── HTML escape helper ── */
                function escHtml(str) {
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;');
                }

                /* ── Events ── */
                trigger.addEventListener('click', function (e) {
                    // Don't toggle panel when clicking a tag remove button
                    if (e.target.classList.contains('searchable-select__tag-remove')) { return; }
                    panel.classList.contains('is-open') ? closePanel() : openPanel();
                });

                trigger.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openPanel(); }
                    if (e.key === 'Escape') { closePanel(); }
                });

                // Tag remove — delegate from tagsWrap
                tagsWrap.addEventListener('click', function (e) {
                    const btn = e.target.closest('.searchable-select__tag-remove');
                    if (!btn) { return; }
                    e.stopPropagation();
                    const value = btn.dataset.value;
                    selected.delete(value);
                    // Deactivate list item
                    list.querySelectorAll('.searchable-select__item').forEach(function (i) {
                        if (i.dataset.value === value) { i.classList.remove('is-active'); }
                    });
                    renderTags();
                });

                search.addEventListener('input', function () {
                    filterItems(this.value);
                });

                // Keep panel open — stop click inside panel from bubbling to document
                panel.addEventListener('click', function (e) {
                    e.stopPropagation();
                });

                items.forEach(function (item) {
                    item.addEventListener('click', function (e) {
                        e.stopPropagation();
                        toggleItem(this);
                    });
                });

                clearAllBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    clearAll();
                });

                // Close when clicking outside
                document.addEventListener('click', function (e) {
                    if (!document.getElementById('clients-dropdown').contains(e.target)) {
                        closePanel();
                    }
                });

                // Validate on submit — at least one client must be selected
                trigger.closest('form').addEventListener('submit', function (e) {
                    if (selected.size === 0) {
                        e.preventDefault();
                        trigger.style.borderColor = '#a94442';
                        trigger.title = 'Please select at least one client.';
                        openPanel();
                        setTimeout(function () { trigger.style.borderColor = ''; }, 3000);
                    }
                });
            }());
            // ─────────────────────────────────────────────────────────────
        });
    </script>
@endsection


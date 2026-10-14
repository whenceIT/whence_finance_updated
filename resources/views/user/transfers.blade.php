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
                    <select name="clients[]" id="clients-select" class="form-control select2" multiple required>
                    </select>
                    <small class="text-muted">
                        Select the clients you want to move. Hold Ctrl (or Cmd on Mac) to select multiple clients.
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

            // Initialize Select2 with AJAX for clients dropdown
            $('#clients-select').select2({
                placeholder: "Select clients...",
                allowClear: true,
                multiple: true,
                ajax: {
                    url: '/ajax/clients',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });
        });
    </script>
@endsection


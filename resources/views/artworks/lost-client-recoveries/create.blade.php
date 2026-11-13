@extends('layouts.master')
@section('title') Register Recovery @endsection
@section('content')
<section class="content-header"><h1>Register Recovery</h1></section>
<section class="content"><div class="box"><div class="box-body">
<form method="POST" action="{{ route('lost-client-recoveries.store') }}">@csrf

<div class="wizard-steps">
    <div class="wizard-step active" data-step="1">1. Client Details</div>
    <div class="wizard-step" data-step="2">2. Recovery Details</div>
</div>

<!-- Step 1: Client -->
<div class="wizard-panel active" data-step="1">
    <div class="form-group">
        <label>Client Name <span class="text-danger">*</span></label>
        <input type="text" name="client_name" id="client_name" class="form-control" required
            placeholder="Search and select a client..." autocomplete="off">
        <input type="hidden" name="client_id" id="client_id">
        <div id="client_results" class="client-results"></div>
        <small class="form-hint">Search and select an existing client.</small>
    </div>
    <div class="form-group">
        <label>NRC Number</label>
        <input type="text" name="nrc_number" class="form-control" value="{{ old('nrc_number') }}" placeholder="e.g. 123456/78/1">
        <small class="form-hint">National Registration Card number (optional).</small>
    </div>
    <div style="margin-top:16px;">
        <button type="button" class="btn btn-primary btn-next" data-from="1" data-to="2">Next</button>
    </div>
</div>

<!-- Step 2: Recovery -->
<div class="wizard-panel" data-step="2">
    <div class="form-group">
        <label>Contact</label>
        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="Primary phone contact">
        <small class="form-hint">Primary phone contact for the client.</small>
    </div>
    <div class="form-group">
        <label>Branch</label>
        <input type="text" name="branch_name" id="branch_name" class="form-control"
            placeholder="Search branch..." autocomplete="off">
        <input type="hidden" name="branch_id" id="branch_id">
        <div id="branch_results" class="client-results"></div>
        <small class="form-hint">Select the branch handling this recovery.</small>
    </div>
    <div class="form-group">
        <label>Recovery Date <span class="text-danger">*</span></label>
        <input type="date" name="recovery_date" class="form-control" value="{{ old('recovery_date') }}" required>
        <small class="form-hint">Date the recovery was made.</small>
    </div>
    <div class="form-group">
        <label>Linked Loan <span class="text-muted">(optional)</span></label>
        <input type="text" name="loan_name" id="loan_name" class="form-control"
            placeholder="Search and link a loan account..." autocomplete="off">
        <input type="hidden" name="loan_id" id="loan_id">
        <div id="loan_results" class="client-results"></div>
        <small class="form-hint">Link the client's existing loan record for profit share calculation.</small>
    </div>
    <div class="form-group">
        <label>Responsible Staff</label>
        <input type="text" name="staff_name" id="staff_name" class="form-control"
            placeholder="Search staff..." autocomplete="off">
        <input type="hidden" name="responsible_staff_id" id="responsible_staff_id">
        <div id="staff_results" class="client-results"></div>
        <small class="form-hint">Staff member responsible for this recovery.</small>
    </div>
    <div class="form-group" style="grid-column: 1 / -1;">
        <label>Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Additional recovery notes...">{{ old('notes') }}</textarea>
        <small class="form-hint">Additional recovery notes or comments.</small>
    </div>
    <div style="margin-top:16px; display:flex; gap:10px;">
        <button type="button" class="btn btn-default btn-prev" data-from="2" data-to="1">Back</button>
        <button type="submit" class="btn btn-primary">Register</button>
    </div>
</div>

</form>
</div></div></section>

<style>
.wizard-steps { display:flex; gap:8px; margin-bottom:16px; font-size:13px; color:#666; }
.wizard-step { padding:6px 12px; border:1px solid #ddd; border-radius:4px; background:#fafafa; }
.wizard-step.active { background:#00a04a; color:#fff; border-color:#00a04a; font-weight:bold; }
.wizard-panel { display:none; }
.wizard-panel.active { display:block; animation: fadeIn 0.25s; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
.has-error { border-color:#a94442; }

.client-results {
    position: absolute;
    z-index: 1050;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    background: #fff;
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: none;
    width: 100%;
    box-sizing: border-box;
}
.client-result-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}
.client-result-item:hover { background: #f5f5f5; }
.client-result-item.selected { background: #e8f5e9; }
.client-result-name { font-weight: 600; }
.client-result-meta { font-size: 12px; color: #666; margin-top: 2px; }
</style>

<script>
$(document).ready(function() {
    var clientUrl = "{{ route('lost-client-recoveries.clients-search') }}";
    var $clientInput = $('#client_name');
    var $clientResults = $('#client_results');
    var clientTimer = null;

    function positionResults() {
        var offset = $clientInput.offset();
        $clientResults.css({
            top: offset.top + $clientInput.outerHeight(),
            left: offset.left,
            width: $clientInput.outerWidth()
        });
    }

    function renderClients(clients) {
        $clientResults.empty();
        if (!clients || clients.length === 0) {
            $clientResults.hide();
            return;
        }
        $.each(clients, function(i, c) {
            var full = c.full_name || '';
            var item = $(
                '<div class="client-result-item" data-full="' + (c.full_name || '') + '" ' +
                'data-first="' + (c.first_name || '') + '" data-last="' + (c.last_name || '') + '" ' +
                'data-nrc="' + (c.nrc_number || '') + '" data-phone="' + (c.phone || '') + '">' +
                '<div class="client-result-name">' + full + '</div>' +
                '<div class="client-result-meta">First: ' + (c.first_name || '') + ' | Last: ' + (c.last_name || '') +
                ' | NRC: ' + (c.nrc_number || '') + ' | Phone: ' + (c.phone || '') + '</div>' +
                '</div>'
            );
            item.on('click', function() {
                $clientInput.val(c.full_name || '');
                $('#client_id').val(c.id || '');
                $clientInput.closest('.form-group').removeClass('has-error');
                $('input[name="nrc_number"]').val(c.nrc_number || '');
                $('input[name="contact_number"]').val(c.phone || '');
                $clientResults.hide();
            });
            $clientResults.append(item);
        });
        $clientResults.show();
    }

    function doSearch() {
        var q = $clientInput.val();
        if (q.length === 0) {
            $clientResults.hide();
            return;
        }
        positionResults();
        $.ajax({
            url: clientUrl,
            method: 'GET',
            dataType: 'json',
            data: { q: q },
            success: function(data) { renderClients(data || []); },
            error: function(xhr) { console.log('client search error', xhr.status, xhr.responseText); }
        });
    }

    $clientInput.on('input', function() {
        clearTimeout(clientTimer);
        clientTimer = setTimeout(doSearch, 250);
    });

    $clientInput.on('focus', function() {
        if ($clientInput.val().length > 0) { positionResults(); doSearch(); }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#client_name, #client_results').length) {
            $clientResults.hide();
        }
    });

    $(window).on('resize', positionResults);

    function liveSearch(config) {
        var $input = $(config.input);
        var $results = $(config.results);
        var $hidden = $(config.hidden);
        var url = config.url;
        var timer = null;

        function positionResults() {
            var offset = $input.offset();
            $results.css({
                top: offset.top + $input.outerHeight(),
                left: offset.left,
                width: $input.outerWidth()
            });
        }

        function renderItems(items) {
            $results.empty();
            if (!items || items.length === 0) {
                $results.hide();
                return;
            }
            $.each(items, function(i, it) {
                var item = $(
                    '<div class="client-result-item" data-id="' + (it.id || '') + '">' +
                    '<div class="client-result-name">' + (it.text || '') + '</div>' +
                    '</div>'
                );
                item.on('click', function() {
                    $input.val(it.text || '');
                    $hidden.val(it.id || '');
                    $input.closest('.form-group').removeClass('has-error');
                    $results.hide();
                });
                $results.append(item);
            });
            $results.show();
        }

        function doSearch() {
            var q = $input.val();
            if (q.length === 0) {
                $results.hide();
                $hidden.val('');
                return;
            }
            positionResults();
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                data: { q: q },
                success: function(data) { renderItems(data || []); },
                error: function(xhr) { console.log('search error', config.label, xhr.status, xhr.responseText); }
            });
        }

        $input.on('input', function() {
            clearTimeout(timer);
            timer = setTimeout(doSearch, 250);
        });

        $input.on('focus', function() {
            if ($input.val().length > 0) { positionResults(); doSearch(); }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest($input.add($results)).length) {
                $results.hide();
            }
        });

        $(window).on('resize', positionResults);
    }

    liveSearch({
        input: '#branch_name',
        results: '#branch_results',
        hidden: '#branch_id',
        url: "{{ route('lost-client-recoveries.offices-search') }}",
        label: 'branch'
    });

    liveSearch({
        input: '#staff_name',
        results: '#staff_results',
        hidden: '#responsible_staff_id',
        url: "{{ route('lost-client-recoveries.users-search') }}",
        label: 'staff'
    });

    liveSearch({
        input: '#loan_name',
        results: '#loan_results',
        hidden: '#loan_id',
        url: "{{ route('lost-client-recoveries.loans-search') }}",
        label: 'loan'
    });

    function showStep(step) {
        $('.wizard-panel').removeClass('active');
        $('.wizard-step').removeClass('active');
        $('[data-step="' + step + '"]').addClass('active');
    }

    $('.btn-next').on('click', function() {
        var from = $(this).data('from');
        var to = $(this).data('to');
        if (from == 1) {
            if (!$clientInput.val()) {
                $clientInput.closest('.form-group').addClass('has-error');
                $clientInput.focus();
                return;
            }
        }
        showStep(to);
    });

    $('.btn-prev').on('click', function() {
        showStep($(this).data('to'));
    });

    $clientInput.on('change', function() {
        $(this).closest('.form-group').removeClass('has-error');
    });
});
</script>
@endsection

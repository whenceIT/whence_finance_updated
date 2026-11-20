@extends('layouts.master')
@section('title') Register Referral @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">
<form method="POST" action="{{ route('loan-referrals.store') }}">@csrf
<div class="referral-grid">
    <div class="form-group">
        <label>First Name <span class="text-danger">*</span></label>
        <input type="text" name="fn_name" id="fn_name" class="form-control" value="{{ old('fn_name') }}" required
            placeholder="Search and select a client..." autocomplete="off">
        <input type="hidden" id="client_id">
        <input type="hidden" name="nrc_number" id="client_nrc" value="{{ old('nrc_number') }}">
        <input type="hidden" name="contact_number" id="client_contact">
        <div id="client_results" class="client-results"></div>
        <small class="form-hint">Search an existing client or enter a first name.</small>
    </div>

    <div class="form-group">
        <label>Last Name <span class="text-danger">*</span></label>
        <input type="text" name="lname" id="lname" class="form-control" value="{{ old('lname') }}" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
    </div>

    <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
    </div>

    <div class="form-group">
        <label>Gender</label>
        <select name="gender" class="form-control">
            <option value="">-- Select Gender --</option>
            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other', 'unspecified' => 'Unspecified'] as $value => $label)
                <option value="{{ $value }}" {{ old('gender') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Client Type <span class="text-danger">*</span></label>
        <select name="client_type" class="form-control" required>
            <option value="">-- Select Client Type --</option>
            @foreach(['ngo' => 'NGO', 'business' => 'Business', 'individual' => 'Individual', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" {{ old('client_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Marital Status</label>
        <select name="marital_status" class="form-control">
            <option value="">-- Select Marital Status --</option>
            @foreach(['married' => 'Married', 'single' => 'Single', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'unspecified' => 'Unspecified'] as $value => $label)
                <option value="{{ $value }}" {{ old('marital_status') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
    </div>

    <div class="form-group">
        <label>Working Place</label>
        <input type="text" name="working_place" class="form-control" value="{{ old('working_place') }}">
    </div>

    <div class="form-group">
        <label>Working Position</label>
        <input type="text" name="working_position" class="form-control" value="{{ old('working_position') }}">
    </div>

    <div class="form-group">
        <label>Salary</label>
        <input type="number" name="salary" min="0" step="0.01" class="form-control" value="{{ old('salary') }}">
    </div>

    <div class="form-group">
        <label>Employer</label>
        <input type="text" name="employer" class="form-control" value="{{ old('employer') }}">
    </div>

    <div class="form-group">
        <label>Business Name</label>
        <input type="text" name="business_name" class="form-control" value="{{ old('business_name') }}">
    </div>

    <div class="form-group">
        <label>Branch</label>
        <input type="text" name="branch_name" id="branch_name" class="form-control"
            placeholder="Search branch..." autocomplete="off">
        <input type="hidden" name="branch_id" id="branch_id">
        <div id="branch_results" class="client-results"></div>
        <small class="form-hint">Select the branch handling this referral.</small>
    </div>

    <div class="form-group">
        <label>Referring Department</label>
        <input type="text" name="referring_department" class="form-control" value="{{ old('referring_department') }}" placeholder="Department initiating the referral">
        <small class="form-hint">Department initiating the referral.</small>
    </div>

    <div class="form-group">
        <label>Referring Officer</label>
        <select name="referring_officer_id" id="referring_officer_id" class="form-control" disabled>
            <option value="">-- Select Branch First --</option>
        </select>
        <small class="form-hint">Staff member referring the client (filtered by selected branch).</small>
    </div>

    <div class="form-group">
        <label>Referral Date <span class="text-danger">*</span></label>
        <input type="date" name="referral_date" class="form-control" value="{{ old('referral_date') }}" required>
        <small class="form-hint">Date the referral was made.</small>
    </div>

    <div class="form-group" style="grid-column: 1 / -1;">
        <label>Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Additional referral notes...">{{ old('notes') }}</textarea>
        <small class="form-hint">Additional referral notes or comments.</small>
    </div>

    <div style="grid-column: 1 / -1; display: grid; grid-template-columns: auto 1fr; gap: 10px; align-items: center; margin-top: 8px;">
        <button type="submit" class="btn btn-primary">Register</button>
    </div>
</div>
</form>
</div></div></section>

<style>
.referral-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px 24px;
}
.form-hint {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #666;
}
@media (max-width: 768px) {
    .referral-grid {
        grid-template-columns: 1fr;
    }
}

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
.has-error { border-color:#a94442; }
</style>

<script>
$(document).ready(function() {
    function positionResults($input, $results) {
        var offset = $input.offset();
        $results.css({
            top: offset.top + $input.outerHeight(),
            left: offset.left,
            width: $input.outerWidth()
        });
    }

    function renderItems($results, items, config) {
        $results.empty();
        if (!items || items.length === 0) {
            $results.hide();
            return;
        }
        $.each(items, function(i, it) {
            var item = $(
                '<div class="client-result-item" data-id="' + (it.id || '') + '">' +
                '<div class="client-result-name">' + (it.text || '') + '</div>' +
                (it.nrc_number ? '<div class="client-result-meta">NRC: ' + it.nrc_number + ' | Phone: ' + (it.phone || '') + '</div>' : '') +
                '</div>'
            );
            item.on('click', function() {
                config.input.val(it.text || '');
                config.hidden.val(it.id || '');
                if (config.triggerChange) config.hidden.trigger('change');
                if (config.nrcField) config.nrcField.val(it.nrc_number || '');
                if (config.phoneField) config.phoneField.val(it.phone || '');
                if (config.onSelect) config.onSelect(it);
                config.input.closest('.form-group').removeClass('has-error');
                $results.hide();
            });
            $results.append(item);
        });
        $results.show();
    }

    function doSearch($input, $results, url, config) {
        var q = $input.val();
        if (q.length === 0) {
            $results.hide();
            config.hidden.val('');
            return;
        }
        positionResults($input, $results);
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            data: { q: q },
            success: function(data) { renderItems($results, data || [], config); },
            error: function(xhr) { console.log('search error', config.label, xhr.status, xhr.responseText); }
        });
    }

    function liveSearch(config) {
        var $input = $(config.input);
        var $results = $(config.results);
        var $hidden = $(config.hidden);
        var url = config.url;
        var timer = null;

        $input.on('input', function() {
            clearTimeout(timer);
            if (config.onInput) config.onInput();
            timer = setTimeout(function() { doSearch($input, $results, url, config); }, 250);
        });

        $input.on('focus', function() {
            if ($input.val().length > 0) { positionResults($input, $results); doSearch($input, $results, url, config); }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest($input.add($results)).length) {
                $results.hide();
            }
        });

        $(window).on('resize', function() { positionResults($input, $results); });
    }

    liveSearch({
        input: $('#fn_name'),
        results: $('#client_results'),
        hidden: $('#client_id'),
        url: "{{ route('loan-referrals.clients-search') }}",
        label: 'client',
        nrcField: $('#client_nrc'),
        phoneField: $('#client_contact'),
        onInput: function() {
            $('#client_nrc, #client_contact, #lname').val('');
        },
        onSelect: function(client) {
            $('#fn_name').val(client.first_name || '');
            $('#lname').val(client.last_name || '');
        }
    });

    liveSearch({
        input: $('#branch_name'),
        results: $('#branch_results'),
        hidden: $('#branch_id'),
        url: "{{ route('loan-referrals.offices-search') }}",
        label: 'branch',
        triggerChange: true
    });

    // Dependent dropdown: Referring Officer filtered by selected Branch
    $('#branch_id').on('change', function() {
        var officeId = $(this).val();
        var $officerSelect = $('#referring_officer_id');
        $officerSelect.empty().append('<option value="">Loading officers...</option>').prop('disabled', true);

        if (!officeId) {
            $officerSelect.empty().append('<option value="">-- Select Branch First --</option>').prop('disabled', true);
            return;
        }

        $.ajax({
            url: "{{ route('loan-referrals.users-by-office') }}",
            method: 'GET',
            dataType: 'json',
            data: { office_id: officeId },
            success: function(data) {
                $officerSelect.empty().append('<option value="">-- Select Officer --</option>');
                if (data && data.length) {
                    $.each(data, function(i, u) {
                        $officerSelect.append('<option value="' + u.id + '">' + u.text + '</option>');
                    });
                }
                $officerSelect.prop('disabled', false);
            },
            error: function(xhr) {
                console.log('officer search error', xhr.status, xhr.responseText);
                $officerSelect.empty().append('<option value="">-- Error loading officers --</option>').prop('disabled', true);
            }
        });
    });
});
</script>
@endsection

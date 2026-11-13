@extends('layouts.master')
@section('title') Register Referral @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">
<form method="POST" action="{{ route('loan-referrals.store') }}">@csrf
<div class="referral-grid">
    <div class="form-group">
        <label>Client Name <span class="text-danger">*</span></label>
        <select name="client_name" id="client_name" class="form-control select2" required>
            <option value="">-- Select Client --</option>
            @foreach($clients as $c)
                @php
                    $clientLabel = $c->full_name ?: ($c->display_name ?: ($c->business_name ?: $c->nrc_number));
                @endphp
                <option value="{{ e($clientLabel) }}" {{ old('client_name') == $clientLabel ? 'selected' : '' }}>
                    {{ e($clientLabel) }} <small class="text-muted">({{ e($c->nrc_number) }})</small>
                </option>
            @endforeach
        </select>
        <small class="form-hint">Search and select an existing client from the directory.</small>
    </div>

    <div class="form-group">
        <label>NRC Number</label>
        <input type="text" name="nrc_number" class="form-control" value="{{ old('nrc_number') }}" placeholder="e.g. 123456/78/1">
        <small class="form-hint">National Registration Card number (optional).</small>
    </div>

    <div class="form-group">
        <label>Contact</label>
        <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="Primary phone contact">
        <small class="form-hint">Primary phone contact for the client.</small>
    </div>

    <div class="form-group">
        <label>Branch</label>
        <select name="branch_id" class="form-control select2">
            <option value="">--</option>
            @foreach($offices as $o)
                <option value="{{ $o->id }}">{{ $o->name }}</option>
            @endforeach
        </select>
        <small class="form-hint">Select the branch handling this referral.</small>
    </div>

    <div class="form-group">
        <label>Referring Department</label>
        <input type="text" name="referring_department" class="form-control" value="{{ old('referring_department') }}" placeholder="Department initiating the referral">
        <small class="form-hint">Department initiating the referral.</small>
    </div>

    <div class="form-group">
        <label>Referral Date <span class="text-danger">*</span></label>
        <input type="date" name="referral_date" class="form-control" value="{{ old('referral_date') }}" required>
        <small class="form-hint">Date the referral was made.</small>
    </div>

    <div class="form-group">
        <label>Referring Officer</label>
        <select name="referring_officer_id" class="form-control select2">
            <option value="">--</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
            @endforeach
        </select>
        <small class="form-hint">Staff member referring the client.</small>
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

<script>
$(document).ready(function() {
    $('#client_name').select2({
        placeholder: 'Search client...',
        allowClear: true,
        width: '100%'
    });
});
</script>

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
</style>
@endsection

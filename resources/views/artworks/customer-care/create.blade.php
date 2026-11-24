@extends('layouts.master')
@section('title')
    Register Customer Care Case
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Case Details</h3>
                    </div>
                    <form action="{{ route('customer-care.store') }}" method="post">
                        @csrf
                    <div class="box-body">
                        @if (Session::has('error'))
                            <div class="alert alert-danger">{{ Session::get('error') }}</div>
                        @endif
                        <div class="form-group">
                            <label class="control-label">Client Name <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}" class="form-control" required placeholder="Search and select a client..." autocomplete="off">
                            <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">
                            <div id="client_results" class="client-results"></div>
                            <small class="form-hint">Search an existing client or enter a name manually.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">NRC Number</label>
                                    <input type="text" name="nrc_number" id="client_nrc" value="{{ old('nrc_number') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Contact Number</label>
                                    <input type="text" name="contact_number" id="client_contact" value="{{ old('contact_number') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Complaint Type <span class="text-danger">*</span></label>
                                    <select name="complaint_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="Payment amount disputes" {{ old('complaint_type')=='Payment amount disputes'?'selected':'' }}>Payment amount disputes</option>
                                        <option value="Service complaints" {{ old('complaint_type')=='Service complaints'?'selected':'' }}>Service complaints</option>
                                        <option value="Delayed disbursement issues" {{ old('complaint_type')=='Delayed disbursement issues'?'selected':'' }}>Delayed disbursement issues</option>
                                        <option value="Communication breakdown" {{ old('complaint_type')=='Communication breakdown'?'selected':'' }}>Communication breakdown between clients and branches or Loan Officers</option>
                                        <option value="Other" {{ old('complaint_type')=='Other'?'selected':'' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Date Reported <span class="text-danger">*</span></label>
                                    <input type="date" name="date_reported" value="{{ old('date_reported', date('Y-m-d')) }}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Complaint Description <span class="text-danger">*</span></label>
                            <textarea name="complaint_description" class="form-control" rows="4" required>{{ old('complaint_description') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Additional Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Register Case</button>
                        <a href="{{ route('customer-care.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

<style>
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
.form-hint {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #666;
}
</style>

@section('footer-scripts')
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
        input: $('#client_name'),
        results: $('#client_results'),
        hidden: $('#client_id'),
        url: "{{ route('customer-care.clients-search') }}",
        label: 'client',
        nrcField: $('#client_nrc'),
        phoneField: $('#client_contact'),
        onInput: function() {
            $('#client_nrc, #client_contact').val('');
        }
    });
});
</script>
@endsection
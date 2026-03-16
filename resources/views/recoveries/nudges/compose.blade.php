@extends('layouts.master')

@section('title')
    Send Nudges
@endsection

@section('content')

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Cases</h3>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ url('recovery/nudge/compose') }}" class="form-inline">
            <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-search"></i> Filter</button>
            <a href="{{ url('recovery/nudge/compose') }}" class="btn btn-sm btn-link">Reset</a>
        </form>
    </div>
</div>

<form method="POST" action="{{ url('recovery/nudge/send-bulk') }}" id="nudge-form">
@csrf

<div class="row">
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-users"></i> Select Recipients
                </h3>
            </div>
            <div class="box-body no-padding" style="max-height:520px;overflow-y:auto">

                @forelse($cases as $case)
                <div class="case-item selectable" data-id="{{ $case->id }}"
                     style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-bottom:1px solid #f0f0f0;cursor:pointer">
                    <div style="flex:1;min-width:0">
                        <strong style="font-size:13px">{{ ($case->client->client_type ?? '') === 'business' ? ($case->client->full_name ?? '—') : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—') }}</strong>
                        <br>
                        <small class="text-muted">{{ $case->case_number }}</small>
                    </div>
                </div>
                @empty
                <div style="padding:30px;text-align:center" class="text-muted">
                    No active cases.
                </div>
                @endforelse

            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-paper-plane"></i> Compose Nudge</h3>
            </div>
            <div class="box-body">

                <div class="form-group">
                    <label>Channel</label>
                    <div class="btn-group btn-group-justified">
                        <button type="button" class="btn btn-primary">SMS</button>
                        <button type="button" class="btn btn-default">WhatsApp</button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Message <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="7"
                              maxlength="1000" required placeholder="Type your message..."></textarea>
                </div>

            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fa fa-paper-plane"></i> Send
                </button>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

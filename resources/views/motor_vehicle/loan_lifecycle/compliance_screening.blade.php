@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>
        <i class="fa fa-shield-alt"></i> PEP & Sanctions Screening
        <small>Motor Vehicle Loan #{{ $loan->id }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/') }}/motor-vehicle-loans">Motor Vehicle Loans</a></li>
        <li><a href="{{ route('motor-vehicle-loans.show', $loan->id) }}">Loan #{{ $loan->id }}</a></li>
        <li class="active">PEP & Sanctions Screening</li>
    </ol>
</section>

<section class="content">

<div class="alert alert-info">
    <i class="fa fa-info-circle"></i>
    <strong>What is this?</strong> Before disbursing a Motor Vehicle Loan, we must check the client
    against PEP (Politically Exposed Persons) and Sanctions lists. This protects the bank from
    fraud and regulatory risk. The client name is: <strong>{{ $loan->client->first_name ?? '' }} {{ $loan->client->last_name ?? '' }}</strong>
</div>

<a href="{{ route('motor-vehicle-loans.show', $loan->id) }}" class="btn btn-default btn-sm">
    <i class="fa fa-arrow-left"></i> Back to Loan Lifecycle
</a>
<br><br>

<div class="row">
    <div class="col-md-5">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Latest Screening Result</h3>
                @if($latest)
                    <span class="label label-primary pull-right">Editing</span>
                @endif
            </div>
            <div class="box-body">
                @if($latest)
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>PEP Result</th>
                            <td>{{ $latest->pep_result ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Sanctions Result</th>
                            <td>{{ $latest->sanctions_result ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Screening Date</th>
                            <td>{{ $latest->screening_date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Match Level</th>
                            <td>
                                @if($latest->match_level == 'low')
                                    <span class="label label-success">Low</span>
                                @elseif($latest->match_level == 'medium')
                                    <span class="label label-warning">Medium</span>
                                @elseif($latest->match_level == 'high')
                                    <span class="label label-danger">High</span>
                                @else
                                    {{ $latest->match_level ?? 'N/A' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($latest->status == 'cleared')
                                    <span class="label label-success">Cleared</span>
                                @elseif($latest->status == 'flagged')
                                    <span class="label label-danger">Flagged</span>
                                @elseif($latest->status == 'pending')
                                    <span class="label label-warning">Pending</span>
                                @elseif($latest->status == 'requires_review')
                                    <span class="label label-info">Requires Review</span>
                                @else
                                    {{ $latest->status ?? 'N/A' }}
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($latest->comments)
                    <p class="text-muted"><strong>Comments:</strong> {{ $latest->comments }}</p>
                    @endif

                    @if($latest->supporting_evidence)
                    <p><strong>Supporting Evidence:</strong> <a href="{{ $latest->supporting_evidence }}" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-file-pdf-o"></i> View Document</a></p>
                    @endif
                @else
                    <div class="text-center text-muted" style="padding: 30px;">
                        <i class="fa fa-exclamation-circle fa-3x"></i>
                        <p>No compliance screening has been recorded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Record New Screening</h3>
                <p class="text-muted" style="margin: 5px 0 0 0; font-size: 13px;">
                    Fill in the results from your PEP and Sanctions check
                </p>
            </div>
            <div class="box-body">
                <form method="POST" action="{{ route('motor-vehicle-loans.store-compliance-screening', $loan->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PEP Result <span class="text-danger">*</span></label>
                                <small class="form-text text-muted">Did the client match any Politically Exposed Person (PEP) database? Enter "None" or "No match" if clear.</small>
                                <input type="text" name="pep_result" class="form-control" required placeholder="e.g., No match found / Cleared" value="{{ old('pep_result', $latest->pep_result ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Sanctions Result <span class="text-danger">*</span></label>
                                <small class="form-text text-muted">Did the client appear on any sanctions watchlist (UN, UN, OFAC, etc.)? Enter "None" if no match.</small>
                                <input type="text" name="sanctions_result" class="form-control" required placeholder="e.g., No sanctions listed / Cleared" value="{{ old('sanctions_result', $latest->sanctions_result ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Screening Date <span class="text-danger">*</span></label>
                                <small class="form-text text-muted">The date this PEP and Sanctions check was performed.</small>
                                <input type="date" name="screening_date" class="form-control" required value="{{ old('screening_date', $latest->screening_date ? $latest->screening_date->format('Y-m-d') : date('Y-m-d')) }}">
                            </div>
                            <div class="form-group">
                                <label>Match Level <span class="text-danger">*</span></label>
                                <small class="form-text text-muted">
                                    How confident you are that this is the same person:
                                </small>
                                <select name="match_level" class="form-control" required>
                                    <option value="">-- Select Match Level --</option>
                                    <option value="low" {{ old('match_level', $latest->match_level ?? '') == 'low' ? 'selected' : '' }}>Low — Unlikely to be the same person</option>
                                    <option value="medium" {{ old('match_level', $latest->match_level ?? '') == 'medium' ? 'selected' : '' }}>Medium — Possible match, needs review</option>
                                    <option value="high" {{ old('match_level', $latest->match_level ?? '') == 'high' ? 'selected' : '' }}>High — Likely the same person</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Overall Status <span class="text-danger">*</span></label>
                                <small class="form-text text-muted">The final decision for this screening check:</small>
                                <select name="status" class="form-control" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="pending" {{ old('status', $latest->status ?? '') == 'pending' ? 'selected' : '' }}>Pending — Waiting for more info or review</option>
                                    <option value="cleared" {{ old('status', $latest->status ?? '') == 'cleared' ? 'selected' : '' }}>Cleared — No issues found, loan can proceed</option>
                                    <option value="flagged" {{ old('status', $latest->status ?? '') == 'flagged' ? 'selected' : '' }}>Flagged — Match found, needs escalation</option>
                                    <option value="requires_review" {{ old('status', $latest->status ?? '') == 'requires_review' ? 'selected' : '' }}>Requires Review — Needs manual assessment</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Supporting Evidence</label>
                                <small class="form-text text-muted">Upload any documents from the screening (e.g., screenshots from the PEP database, sanctions report). Leave blank to keep existing file.</small>
                                @if($latest && $latest->supporting_evidence)
                                    <p class="text-muted" style="margin-top: 5px;">
                                        <i class="fa fa-file-pdf-o"></i> Current file:
                                        <a href="{{ $latest->supporting_evidence }}" target="_blank">View</a>
                                    </p>
                                @endif
                                <input type="file" name="supporting_evidence" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Comments</label>
                                <small class="form-text text-muted">Any additional notes, observations, or reasons for the match level chosen.</small>
                                <textarea name="comments" class="form-control" rows="4" placeholder="Enter any relevant notes...">{{ old('comments', $latest->comments ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save &amp; Proceed to Vehicle Ownership
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($screenings->count() > 1)
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Screening History</h3>
                <p class="text-muted" style="margin: 5px 0 0 0; font-size: 13px;">Previous PEP & Sanctions screening records</p>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>PEP Result</th>
                            <th>Sanctions Result</th>
                            <th>Match Level</th>
                            <th>Status</th>
                            <th>Officer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($screenings->skip(1) as $screening)
                        <tr>
                            <td>{{ $screening->screening_date }}</td>
                            <td>{{ $screening->pep_result }}</td>
                            <td>{{ $screening->sanctions_result }}</td>
                            <td>{{ $screening->match_level }}</td>
                            <td>
                                @if($screening->status == 'cleared')
                                    <span class="label label-success">Cleared</span>
                                @elseif($screening->status == 'flagged')
                                    <span class="label label-danger">Flagged</span>
                                @elseif($screening->status == 'pending')
                                    <span class="label label-warning">Pending</span>
                                @elseif($screening->status == 'requires_review')
                                    <span class="label label-info">Requires Review</span>
                                @else
                                    {{ $screening->status }}
                                @endif
                            </td>
                            <td>
                                @if($screening->screeningOfficer)
                                    {{ $screening->screeningOfficer->first_name }} {{ $screening->screeningOfficer->last_name }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

</section>

@endsection

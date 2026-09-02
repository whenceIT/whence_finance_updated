@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>
        Compliance Screening
        <small>Loan #{{ $loan->id }}</small>
    </h1>
</section>

<section class="content">

<a href="{{ route('motor-vehicle-loans.show', $loan->id) }}" class="btn btn-default btn-sm">
    <i class="fa fa-arrow-left"></i> Back to Loan Lifecycle
</a>
<br><br>

<div class="row">
    <div class="col-md-5">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Latest Screening</h3>
            </div>
            <div class="box-body">
                @if($latest)
                <p><strong>PEP Result:</strong> {{ $latest->pep_result ?? 'N/A' }}</p>
                <p><strong>Sanctions Result:</strong> {{ $latest->sanctions_result ?? 'N/A' }}</p>
                <p><strong>Screening Date:</strong> {{ $latest->screening_date ?? 'N/A' }}</p>
                <p><strong>Match Level:</strong> {{ $latest->match_level ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ $latest->status ?? 'N/A' }}</p>
                @if($latest->comments)
                <p><strong>Comments:</strong> {{ $latest->comments }}</p>
                @endif
                @if($latest->supporting_evidence)
                <p><strong>Supporting Evidence:</strong> <a href="{{ $latest->supporting_evidence }}" target="_blank">View File</a></p>
                @endif
                @else
                <p class="text-center">No compliance screening recorded</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">New Compliance Screening</h3>
            </div>
            <div class="box-body">
                <form method="POST" action="{{ route('motor-vehicle-loans.store-compliance-screening', $loan->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PEP Result</label>
                                <input type="text" name="pep_result" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Sanctions Result</label>
                                <input type="text" name="sanctions_result" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Screening Date</label>
                                <input type="date" name="screening_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Match Level</label>
                                <select name="match_level" class="form-control" required>
                                    <option value="">Select Level</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="cleared">Cleared</option>
                                    <option value="flagged">Flagged</option>
                                    <option value="requires_review">Requires Review</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Supporting Evidence</label>
                                <input type="file" name="supporting_evidence" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Comments</label>
                                <textarea name="comments" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Screening</button>
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
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>PEP Result</th>
                            <th>Sanctions Result</th>
                            <th>Match Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($screenings->skip(1) as $screening)
                        <tr>
                            <td>{{ $screening->screening_date }}</td>
                            <td>{{ $screening->pep_result }}</td>
                            <td>{{ $screening->sanctions_result }}</td>
                            <td>{{ $screening->match_level }}</td>
                            <td>{{ $screening->status }}</td>
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

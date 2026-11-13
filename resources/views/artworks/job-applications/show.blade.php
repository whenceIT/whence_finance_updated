@extends('layouts.master')
@section('title')
    Job Application Details
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr><th>NRC Number</th><td>{{ $application->nrc_number }}</td></tr>
                            <tr><th>Full Name</th><td>{{ $application->full_name }}</td></tr>
                            <tr><th>Contact Number</th><td>{{ $application->contact_number }}</td></tr>
                            <tr><th>Email</th><td>{{ $application->email }}</td></tr>
                            <tr><th>Position Applied For</th><td>{{ $application->position_applied_for }}</td></tr>
                            <tr><th>Recommending Department</th><td>{{ $application->recommending_department }}</td></tr>
                            <tr><th>Status</th><td>{{ $application->status }}</td></tr>
                            <tr><th>Date Recommended</th><td>{{ $application->date_recommended }}</td></tr>
                            <tr><th>Notes</th><td>{{ $application->notes }}</td></tr>
                        </table>
                    </div>
                    <div class="box-footer">
                        <a href="{{ route('job-applications.index') }}" class="btn btn-default">Back</a>
                        <a href="{{ route('job-applications.edit', $application->id) }}" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

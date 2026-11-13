@extends('layouts.master')
@section('title')
    Customer Care Case Details
@endsection
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr><th>Case Reference</th><td>{{ $case->case_reference }}</td></tr>
                            <tr><th>Client Name</th><td>{{ $case->client_name }}</td></tr>
                            <tr><th>NRC Number</th><td>{{ $case->nrc_number }}</td></tr>
                            <tr><th>Contact Number</th><td>{{ $case->contact_number }}</td></tr>
                            <tr><th>Complaint Type</th><td>{{ $case->complaint_type }}</td></tr>
                            <tr><th>Description</th><td>{{ $case->complaint_description }}</td></tr>
                            <tr><th>Date Reported</th><td>{{ $case->date_reported }}</td></tr>
                            <tr><th>Status</th><td>{{ $case->status }}</td></tr>
                            <tr><th>Notes</th><td>{{ $case->notes }}</td></tr>
                        </table>
                    </div>
                    <div class="box-footer">
                        <a href="{{ route('customer-care.index') }}" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

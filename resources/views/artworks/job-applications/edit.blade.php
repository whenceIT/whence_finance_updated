@extends('layouts.master')
@section('title')
    Edit Job Application
@endsection
@section('content')

    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Applicant Details</h3>
                    </div>
                    <form action="{{ route('job-applications.update', $application->id) }}" method="post">
                        @csrf
                        @method('PUT')
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label">NRC Number *</label>
                            <input type="text" name="nrc_number" value="{{ old('nrc_number', $application->nrc_number) }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Full Name *</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $application->full_name) }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Contact Number *</label>
                            <input type="text" name="contact_number" value="{{ old('contact_number', $application->contact_number) }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $application->email) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Position Applied For *</label>
                            <input type="text" name="position_applied_for" value="{{ old('position_applied_for', $application->position_applied_for) }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Recommending Department</label>
                            <input type="text" name="recommending_department" value="{{ old('recommending_department', $application->recommending_department) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $application->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('job-applications.show', $application->id) }}" class="btn btn-default">Cancel</a>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

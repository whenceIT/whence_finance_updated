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
                            <label class="control-label">Client Name *</label>
                            <input type="text" name="client_name" value="{{ old('client_name') }}" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">NRC Number</label>
                                    <input type="text" name="nrc_number" value="{{ old('nrc_number') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Contact Number</label>
                                    <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Complaint Type *</label>
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
                                    <label class="control-label">Date Reported *</label>
                                    <input type="date" name="date_reported" value="{{ old('date_reported', date('Y-m-d')) }}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Complaint Description *</label>
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

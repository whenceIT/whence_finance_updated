@extends('layouts.master')

@section('title')
    Annual Leave
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Annual Leave Application Form
        </h3>
        <div class="box-tools pull-right"></div>
    </div>

    <form method="post" action="{{ route('leave.submit') }}">
        @csrf
        <div class="box-body">
            <div class="form-group row">
                <label for="first_name" class="control-label col-md-2 col-form-label">{{ trans('general.first_name') }}</label>
                <div class="col-md-6">
                    <input type="text" name="first_name" class="form-control" value="{{ $firstName }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="last_name" class="control-label col-md-2 col-form-label">{{ trans('general.last_name') }}</label>
                <div class="col-md-6">
                    <input type="text" name="last_name" class="form-control" value="{{ $lastName }}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="office_id" class="control-label col-md-2 col-form-label">{{ trans_choice('general.office', 1) }}</label>
                <div class="col-md-6">
                    <select name="office_id" class="form-control" id="office_id" required>
                        <option value="">Office</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="department" class="control-label col-md-2 col-form-label"> Department </label>
                <div class="col-md-6">
                    <input type="text" name="department" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="position" class="control-label col-md-2 col-form-label"> Position </label>
                <div class="col-md-6">
                    <input type="text" name="position" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="reason" class="control-label col-md-2 col-form-label">{{ __('Reason for requesting leave (please select appropriate field):') }}</label>
                <div class="col-md-6">
                    <select name="reason" class="form-control" required>
                        <option value="Annual Leave">Annual Leave</option>
                        <option value="Compassionate Leave">Compassionate Leave</option>
                        <option value="Maternity Leave">Maternity Leave</option>
                        <option value="Parental Leave">Parental Leave</option>
                        <option value="Sick Leave">Sick Leave</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="notes" class="control-label col-md-2 col-form-label"> Notes </label>
                <div class="col-md-6">
                    <input type="text" name="notes" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="commencement_date" class="control-label col-md-2 col-form-label">Commencement Date</label>
                <div class="col-md-6">
                    <input type="date" class="form-control" id="commencement_date" name="commencement_date" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="return_date" class="control-label col-md-2 col-form-label">Return Date</label>
                <div class="col-md-6">
                    <input type="date" class="form-control" id="return_date" name="return_date" required>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>
@endsection


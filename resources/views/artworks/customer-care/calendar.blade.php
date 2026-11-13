@extends('layouts.master')
@section('title')
    Institutional Activities Calendar
@endsection
@section('content')
    <section class="content">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Schedule Activity</h3>
                    </div>
                    <form action="{{ route('customer-care.calendar.store') }}" method="post">
                        @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label">Activity Title *</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Type</label>
                            <select name="activity_type" class="form-control">
                                <option value="">Select</option>
                                <option value="Meeting" {{ old('activity_type')=='Meeting'?'selected':'' }}>Meeting</option>
                                <option value="Campaign" {{ old('activity_type')=='Campaign'?'selected':'' }}>Campaign</option>
                                <option value="Event" {{ old('activity_type')=='Event'?'selected':'' }}>Event</option>
                                <option value="Training" {{ old('activity_type')=='Training'?'selected':'' }}>Training</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Start Date *</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Start Time</label>
                            <input type="time" name="start_time" value="{{ old('start_time') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Venue</label>
                            <input type="text" name="venue" value="{{ old('venue') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Responsible Department</label>
                            <input type="text" name="responsible_department" value="{{ old('responsible_department') }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Schedule Activity</button>
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Scheduled Activities</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Venue</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td>{{ $activity->title }}</td>
                                            <td>{{ $activity->activity_type }}</td>
                                            <td>{{ date('d-M-Y', strtotime($activity->start_date)) }}</td>
                                            <td>{{ $activity->start_time }}</td>
                                            <td>{{ $activity->venue }}</td>
                                            <td>{{ $activity->responsible_department }}</td>
                                            <td><span class="label label-primary">{{ $activity->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No activities scheduled.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

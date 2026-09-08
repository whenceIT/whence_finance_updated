@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Roll Call History</h1>
</section>

<section class="content">

    <a href="{{ url('vehicles/'.$vehicle->id) }}" class="btn btn-default" style="margin-bottom: 10px;">
        <i class="fa fa-arrow-left"></i> Back to Vehicle
    </a>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-clipboard"></i> Add Roll Call
            </h3>
        </div>

        <form method="POST" action="{{ route('vehicles.roll-calls.store', $vehicle->id) }}">
            {{ csrf_field() }}

            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Verification Date</label>
                            <input type="date" name="verification_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Officer</label>
                            <select name="officer_id" class="form-control" required>
                                <option value="">Select Officer</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Location Confirmed</label><br>
                            <input type="checkbox" name="location_confirmed" value="1">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Vehicle Present</label><br>
                            <input type="checkbox" name="vehicle_present" value="1">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Current Mileage</label>
                            <input type="number" name="current_mileage" class="form-control" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="verified">Verified</option>
                                <option value="missing">Missing</option>
                                <option value="damaged">Damaged</option>
                                <option value="relocated">Relocated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Condition</label>
                            <textarea name="condition" class="form-control" rows="3" placeholder="Describe vehicle condition"></textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Additional remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Roll Call
                </button>
            </div>
        </form>
    </div>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-history"></i> Roll Call History
            </h3>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Officer</th>
                        <th>Location Confirmed</th>
                        <th>Vehicle Present</th>
                        <th>Status</th>
                        <th>Mileage</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rollCalls as $rollCall)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($rollCall->verification_date)->format('Y-m-d') }}</td>
                            <td>{{ $rollCall->officer->first_name ?? '' }} {{ $rollCall->officer->last_name ?? '' }}</td>
                            <td>
                                @if($rollCall->location_confirmed)
                                    <span class="label label-success">Yes</span>
                                @else
                                    <span class="label label-danger">No</span>
                                @endif
                            </td>
                            <td>
                                @if($rollCall->vehicle_present)
                                    <span class="label label-success">Yes</span>
                                @else
                                    <span class="label label-danger">No</span>
                                @endif
                            </td>
                            <td>
                                @if($rollCall->status == 'verified')
                                    <span class="label label-success">Verified</span>
                                @elseif($rollCall->status == 'missing')
                                    <span class="label label-danger">Missing</span>
                                @elseif($rollCall->status == 'damaged')
                                    <span class="label label-warning">Damaged</span>
                                @elseif($rollCall->status == 'relocated')
                                    <span class="label label-info">Relocated</span>
                                @else
                                    {{ ucfirst($rollCall->status) }}
                                @endif
                            </td>
                            <td>{{ $rollCall->current_mileage ?? '-' }}</td>
                            <td>{{ $rollCall->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No roll call records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="box-footer clearfix">
            {{ $rollCalls->links() }}
        </div>
    </div>

</section>

@endsection

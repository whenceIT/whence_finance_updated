@extends('layouts.master')
@section('title') Register Conflict Resolution Case @endsection
@section('content')
<section class="content">
<div class="box"><div class="box-body">
<form method="POST" action="{{ route('conflict-resolution.store') }}">
@csrf
<div class="form-group"><label>Client Name</label><input type="text" name="client_name" class="form-control" required></div>
<div class="form-group"><label>NRC</label><input type="text" name="nrc_number" class="form-control"></div>
<div class="form-group"><label>Contact</label><input type="text" name="contact_number" class="form-control"></div>
<div class="form-group"><label>Branch</label><select name="branch_id" class="form-control select2"><option value="">-- Select --</option>@foreach($offices as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach</select></div>
<div class="form-group"><label>Complaint Type</label><select name="complaint_type" class="form-control" required><option value="Payment amount disputes">Payment amount disputes</option><option value="Service complaints">Service complaints</option><option value="Delayed disbursement issues">Delayed disbursement issues</option><option value="Communication breakdown">Communication breakdown</option></select></div>
<div class="form-group"><label>Description</label><textarea name="complaint_description" class="form-control" required></textarea></div>
<div class="form-group"><label>Date Reported</label><input type="date" name="date_reported" class="form-control" required></div>
<div class="form-group"><label>Assigned Officer</label><select name="assigned_to_user_id" class="form-control select2"><option value="">-- Select --</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>@endforeach</select></div>
<div class="form-group"><label>Notes</label><textarea name="notes" class="form-control"></textarea></div>
<button type="submit" class="btn btn-primary">Register (K20.00 charge)</button>
</form>
</div></div>
</section>
@endsection



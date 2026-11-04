@extends('layouts.master')
@section('title') Register Recovery @endsection
@section('content')
<section class="content-header"><h1>Register Recovery</h1></section>
<section class="content"><div class="box"><div class="box-body">
<form method="POST" action="{{ route('lost-client-recoveries.store') }}">@csrf
<div class="form-group"><label>Client Name</label><input type="text" name="client_name" class="form-control" required></div>
<div class="form-group"><label>NRC</label><input type="text" name="nrc_number" class="form-control"></div>
<div class="form-group"><label>Contact</label><input type="text" name="contact_number" class="form-control"></div>
<div class="form-group"><label>Branch</label><select name="branch_id" class="form-control select2"><option value="">--</option>@foreach($offices as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach</select></div>
<div class="form-group"><label>Recovery Date</label><input type="date" name="recovery_date" class="form-control" required></div>
<div class="form-group"><label>Responsible Staff</label><select name="responsible_staff_id" class="form-control select2"><option value="">--</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>@endforeach</select></div>
<div class="form-group"><label>Notes</label><textarea name="notes" class="form-control"></textarea></div>
<button type="submit" class="btn btn-primary">Register</button>
</form>
</div></div></section>
@endsection

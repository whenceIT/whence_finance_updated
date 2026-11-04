@extends('layouts.master')
@section('title') Register Referral @endsection
@section('content')
<section class="content"><div class="box"><div class="box-body">
<form method="POST" action="{{ route('loan-referrals.store') }}">@csrf
<div class="form-group"><label>Client Name</label><input type="text" name="client_name" class="form-control" required></div>
<div class="form-group"><label>NRC</label><input type="text" name="nrc_number" class="form-control"></div>
<div class="form-group"><label>Contact</label><input type="text" name="contact_number" class="form-control"></div>
<div class="form-group"><label>Branch</label><select name="branch_id" class="form-control select2"><option value="">--</option>@foreach($offices as $o)<option value="{{ $o->id }}">{{ $o->name }}</option>@endforeach</select></div>
<div class="form-group"><label>Referring Department</label><input type="text" name="referring_department" class="form-control"></div>
<div class="form-group"><label>Referral Date</label><input type="date" name="referral_date" class="form-control" required></div>
<div class="form-group"><label>Referring Officer</label><select name="referring_officer_id" class="form-control select2"><option value="">--</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>@endforeach</select></div>
<div class="form-group"><label>Notes</label><textarea name="notes" class="form-control"></textarea></div>
<button type="submit" class="btn btn-primary">Register</button>
</form>
</div></div></section>
@endsection

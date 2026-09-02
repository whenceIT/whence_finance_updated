@extends('layouts.master')
@section('title')
    Edit Client KYC
@endsection
@section('content')

<section class="content-header">
    <h1>
        Motor Vehicle Loan Lifecycle
        <small>Edit Client KYC</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/') }}/motor-vehicle-loans">Motor Vehicle Loans</a></li>
        <li class="active">Edit KYC</li>
    </ol>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Client KYC Information</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('motor-vehicle-loans.index') }}" class="btn btn-info btn-sm">
                    Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('clients.update-kyc', $client->id) }}" class="form-horizontal" enctype="multipart/form-data">
            @csrf

            <div class="box-body">
                <div class="form-group">
                    <label for="nrc_number" class="control-label col-md-2">NRC Number</label>
                    <div class="col-md-4">
                        <input type="text" name="nrc_number" class="form-control" id="nrc_number" value="{{ old('nrc_number', $client->nrc_number) }}">
                    </div>
                    <label for="tpin" class="control-label col-md-2">TPIN</label>
                    <div class="col-md-4">
                        <input type="text" name="tpin" class="form-control" id="tpin" value="{{ old('tpin', $client->tpin) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address_line1" class="control-label col-md-2">Address Line 1</label>
                    <div class="col-md-4">
                        <input type="text" name="address_line1" class="form-control" id="address_line1" value="{{ old('address_line1', $client->address_line1) }}">
                    </div>
                    <label for="address_line2" class="control-label col-md-2">Address Line 2</label>
                    <div class="col-md-4">
                        <input type="text" name="address_line2" class="form-control" id="address_line2" value="{{ old('address_line2', $client->address_line2) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="city" class="control-label col-md-2">City</label>
                    <div class="col-md-4">
                        <input type="text" name="city" class="form-control" id="city" value="{{ old('city', $client->city) }}">
                    </div>
                    <label for="employer" class="control-label col-md-2">Employer</label>
                    <div class="col-md-4">
                        <input type="text" name="employer" class="form-control" id="employer" value="{{ old('employer', $client->employer) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="employer_address" class="control-label col-md-2">Employer Address</label>
                    <div class="col-md-4">
                        <input type="text" name="employer_address" class="form-control" id="employer_address" value="{{ old('employer_address', $client->employer_address) }}">
                    </div>
                    <label for="business_name" class="control-label col-md-2">Business Name</label>
                    <div class="col-md-4">
                        <input type="text" name="business_name" class="form-control" id="business_name" value="{{ old('business_name', $client->business_name) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="business_type" class="control-label col-md-2">Business Type</label>
                    <div class="col-md-4">
                        <input type="text" name="business_type" class="form-control" id="business_type" value="{{ old('business_type', $client->business_type) }}">
                    </div>
                    <label for="annual_income" class="control-label col-md-2">Annual Income</label>
                    <div class="col-md-4">
                        <input type="text" name="annual_income" class="form-control" id="annual_income" value="{{ old('annual_income', $client->annual_income) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone_primary" class="control-label col-md-2">Phone Primary</label>
                    <div class="col-md-4">
                        <input type="text" name="phone_primary" class="form-control" id="phone_primary" value="{{ old('phone_primary', $client->phone_primary) }}">
                    </div>
                    <label for="phone_secondary" class="control-label col-md-2">Phone Secondary</label>
                    <div class="col-md-4">
                        <input type="text" name="phone_secondary" class="form-control" id="phone_secondary" value="{{ old('phone_secondary', $client->phone_secondary) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email_primary" class="control-label col-md-2">Email Primary</label>
                    <div class="col-md-4">
                        <input type="email" name="email_primary" class="form-control" id="email_primary" value="{{ old('email_primary', $client->email_primary) }}">
                    </div>
                </div>

                <hr>
                <h4 class="text-semibold">Next of Kin</h4>
                <div class="form-group">
                    <label for="next_of_kin_name" class="control-label col-md-2">Name</label>
                    <div class="col-md-4">
                        <input type="text" name="next_of_kin_name" class="form-control" id="next_of_kin_name" value="{{ old('next_of_kin_name', $client->next_of_kin_name) }}">
                    </div>
                    <label for="next_of_kin_relationship" class="control-label col-md-2">Relationship</label>
                    <div class="col-md-4">
                        <input type="text" name="next_of_kin_relationship" class="form-control" id="next_of_kin_relationship" value="{{ old('next_of_kin_relationship', $client->next_of_kin_relationship) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="next_of_kin_phone" class="control-label col-md-2">Phone</label>
                    <div class="col-md-4">
                        <input type="text" name="next_of_kin_phone" class="form-control" id="next_of_kin_phone" value="{{ old('next_of_kin_phone', $client->next_of_kin_phone) }}">
                    </div>
                    <label for="next_of_kin_address" class="control-label col-md-2">Address</label>
                    <div class="col-md-4">
                        <input type="text" name="next_of_kin_address" class="form-control" id="next_of_kin_address" value="{{ old('next_of_kin_address', $client->next_of_kin_address) }}">
                    </div>
                </div>

                <hr>
                <h4 class="text-semibold">Guarantor</h4>
                <div class="form-group">
                    <label for="guarantor_name" class="control-label col-md-2">Name</label>
                    <div class="col-md-4">
                        <input type="text" name="guarantor_name" class="form-control" id="guarantor_name" value="{{ old('guarantor_name', $client->guarantor_name) }}">
                    </div>
                    <label for="guarantor_nrc" class="control-label col-md-2">NRC</label>
                    <div class="col-md-4">
                        <input type="text" name="guarantor_nrc" class="form-control" id="guarantor_nrc" value="{{ old('guarantor_nrc', $client->guarantor_nrc) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="guarantor_phone" class="control-label col-md-2">Phone</label>
                    <div class="col-md-4">
                        <input type="text" name="guarantor_phone" class="form-control" id="guarantor_phone" value="{{ old('guarantor_phone', $client->guarantor_phone) }}">
                    </div>
                    <label for="guarantor_address" class="control-label col-md-2">Address</label>
                    <div class="col-md-4">
                        <input type="text" name="guarantor_address" class="form-control" id="guarantor_address" value="{{ old('guarantor_address', $client->guarantor_address) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="guarantor_employer" class="control-label col-md-2">Employer</label>
                    <div class="col-md-4">
                        <input type="text" name="guarantor_employer" class="form-control" id="guarantor_employer" value="{{ old('guarantor_employer', $client->guarantor_employer) }}">
                    </div>
                    <label for="guarantor_relationship" class="control-label col-md-2">Relationship</label>
                    <div class="col-md-4">
                        <input type="text" name="guarantor_relationship" class="form-control" id="guarantor_relationship" value="{{ old('guarantor_relationship', $client->guarantor_relationship) }}">
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">Save KYC</button>
                    <button type="button" onclick="window.history.back()" class="btn btn-info pull-right" style="margin-right: 10px;">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Edit Master Record</h1>
</section>

<section class="content">

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Motor Vehicle Loan Master Record</h3>
    </div>

    <form method="POST"
          action="{{ route('motor-vehicle-loans.update-master-record', $loan->id) }}">

        @csrf
        @method('PUT')

        <div class="box-body">

            <div class="form-group">
                <label>Loan Consultant</label>
                <select name="loan_consultant_id"
                        class="form-control">
                    <option value="">Select Consultant</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                                {{ old('loan_consultant_id', $loan->loan_consultant_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Originating Branch</label>
                <select name="originating_branch_id"
                        class="form-control">
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                                {{ old('originating_branch_id', $loan->originating_branch_id) == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Branch Assessor</label>
                <select name="branch_assessor_id"
                        class="form-control">
                    <option value="">Select Assessor</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                                {{ old('branch_assessor_id', $loan->branch_assessor_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>District</label>
                <select name="district_id"
                        class="form-control">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}"
                                {{ old('district_id', $loan->district_id) == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Province</label>
                <select name="province_id"
                        class="form-control">
                    <option value="">Select Province</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}"
                                {{ old('province_id', $loan->province_id) == $province->id ? 'selected' : '' }}>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Vehicle Status</label>
                <input type="text"
                       name="vehicle_status"
                       class="form-control"
                       value="{{ old('vehicle_status', $loan->vehicle_status) }}">
            </div>

            <div class="form-group">
                <label>Custody Status</label>
                <input type="text"
                       name="custody_status"
                       class="form-control"
                       value="{{ old('custody_status', $loan->custody_status) }}">
            </div>

            <div class="form-group">
                <label>Current Storage Location</label>
                <input type="text"
                       name="current_storage_location"
                       class="form-control"
                       value="{{ old('current_storage_location', $loan->current_storage_location) }}">
            </div>

            <div class="form-group">
                <label>Current Custodian</label>
                <select name="current_custodian_id"
                        class="form-control">
                    <option value="">Select Custodian</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                                {{ old('current_custodian_id', $loan->current_custodian_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Current Custodian Phone</label>
                <input type="text"
                       name="current_custodian_phone"
                       class="form-control"
                       value="{{ old('current_custodian_phone', $loan->current_custodian_phone) }}">
            </div>

            <div class="form-group">
                <label>Current Custodian NRC</label>
                <input type="text"
                       name="current_custodian_nrc"
                       class="form-control"
                       value="{{ old('current_custodian_nrc', $loan->current_custodian_nrc) }}">
            </div>

            <div class="form-group">
                <label>Current Custodian Alternative Contact</label>
                <input type="text"
                       name="current_custodian_alternative_contact"
                       class="form-control"
                       value="{{ old('current_custodian_alternative_contact', $loan->current_custodian_alternative_contact) }}">
            </div>

            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks"
                          class="form-control"
                          rows="4">{{ old('remarks', $loan->remarks) }}</textarea>
            </div>

            <button type="submit"
                    class="btn btn-success">
                Update Record
            </button>

            <a href="{{ route('motor-vehicle-loans.show', $loan->id) }}"
               class="btn btn-default">
                Cancel
            </a>

        </div>

    </form>

</div>

</section>

@endsection

@extends('layouts.master')
@section('title')
    My Payroll Details
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card" style="box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 10px; border: none;">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; text-align: center; font-size: 24px; font-weight: bold;">
                       Employee Payroll Data Capture
                    </div>
                    <div class="card-body" style="padding: 30px;">
                        <form action="{{ url('user/payroll-details') }}" method="post" onsubmit="return validateForm()">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 style="margin-bottom: 20px; color: #333;">Salary Information</h4>
                                    <div class="form-group">
                                        <label for="salary_mode" style="font-weight: bold; color: #333;">Method of Salary Receipt</label>
                                        <select name="salary_mode" id="salary_mode" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" required>
                                            <option value="">Select Method</option>
                                            <option value="Cash" {{ old('salary_mode', $user->salary_mode) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Mobile" {{ old('salary_mode', $user->salary_mode) == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                            <option value="Bank" {{ old('salary_mode', $user->salary_mode) == 'Bank' ? 'selected' : '' }}>Bank</option>
                                        </select>
                                    </div>
                                    <div id="bank-fields" style="display: {{ old('salary_mode', $user->salary_mode) == 'Bank' ? 'block' : 'none' }};">
                                        <div class="form-group">
                                            <label for="bank_name" style="font-weight: bold; color: #333;">Bank Name</label>
                                             <input type="text" name="bank_name" id="bank_name" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('bank_name', $user->bank_name) }}">
                                             <small class="form-text text-muted">e.g., First National Bank</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="bank_account_number" style="font-weight: bold; color: #333;">Bank Account Number</label>
                                             <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('bank_account_number', $user->bank_account_number) }}">
                                             <small class="form-text text-muted">e.g., 12345678901234</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="branch" style="font-weight: bold; color: #333;">Bank Branch Name</label>
                                             <input type="text" name="branch" id="branch" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('branch', $user->branch) }}">
                                             <small class="form-text text-muted">e.g., Main Branch</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="employment_type" style="font-weight: bold; color: #333;">Employment Type</label>
                                        <select name="employment_type" id="employment_type" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" required>
                                            <option value="">Select Employment Type</option>
                                            <option value="permanent" {{ old('employment_type', $user->employment_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                            <option value="contract" {{ old('employment_type', $user->employment_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                            <option value="temporary" {{ old('employment_type', $user->employment_type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4 style="margin-bottom: 20px; color: #333;">Mandatory Details</h4>
                                    <div class="form-group">
                                        <label for="tpin" style="font-weight: bold; color: #333;">TPIN (Tax Payer Identification Number)</label>
                                         <input type="text" name="tpin" id="tpin" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('tpin', $user->tpin) }}" pattern="[0-9]{10}" maxlength="10" minlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                         <small class="form-text text-muted">e.g., 1234567890</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="ssn" style="font-weight: bold; color: #333;">SSN (Social Security Number)</label>
                                         <input type="text" name="ssn" id="ssn" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('ssn', $user->ssn) }}" required>
                                         <small class="form-text text-muted">e.g., Z12345678</small>
                                    </div>
                                    <div id="nhima-field" style="display: {{ old('employment_type', $user->employment_type) == 'permanent' ? 'block' : 'none' }};">
                                        <div class="form-group">
                                            <label for="nhima" style="font-weight: bold; color: #333;">NIHMA (National Health Insurance Membership Authority)</label>
                                             <input type="text" name="nhima" id="nhima" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('nhima', $user->nhima) }}" required>
                                             <small class="form-text text-muted">e.g., NHIMA123456</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nrc_id" style="font-weight: bold; color: #333;">NRC (National Registration Card)</label>
                                         <input type="text" name="nrc_id" id="nrc_id" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('nrc_id', $user->nrc_id) }}" required>
                                         <small class="form-text text-muted">e.g., 123456/78/9</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_of_birth" style="font-weight: bold; color: #333;">Date of Birth</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" style="border-radius: 5px; border: 1px solid #ddd;;" value="{{ old('date_of_birth', $user->date_of_birth) }}" required>
                                    </div>
                                </div>
                            </div>
                            <!-- <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; border-radius: 5px; padding: 12px; font-size: 18px; font-weight: bold; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">Save Details</button> -->
                        </form>
                    </div>
                </div>

                <div class="text-center" style="margin-top: 30px;">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/processing-employee-salaries-every-month-illustration-svg-download-png-14347944.png" alt="Employee receiving salary payment" style="max-width: 200px; height: auto;">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    $('#salary_mode').change(function() {
        if ($(this).val() == 'Bank') {
            $('#bank-fields').show();
        } else {
            $('#bank-fields').hide();
        }
    });

    $('#employment_type').change(function() {
        if ($(this).val() == 'permanent') {
            $('#nhima-field').show();
        } else {
            $('#nhima-field').hide();
        }
    });

    $('form').on('submit', function() {
        $('button[type=submit]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving Details...');
    });
    });
});
</script>
@endsection
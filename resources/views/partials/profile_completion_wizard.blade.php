@if($user && !$user->has_completed_profile && $user->has_seen_induction && !$showPolicyModal)
<!-- Profile Completion Wizard Modal -->
<div id="profileWizardModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,20,0.7); z-index: 100000; display: none; align-items: center; justify-content: center; animation: modalFadeIn 0.4s ease-out;">
    <style>
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .wizard-step { transition: opacity 0.3s ease; }
        @media (max-width: 768px) {
            .wizard-content-flex { flex-direction: column !important; padding: 20px !important; gap: 20px !important; }
            .wizard-step-img { display: none; }
            #profileWizardModal>div { width: 100% !important; max-width: none !important; }
        }
        
        /* Fix Select2 dropdown display in modal */
        #profileWizardModal .select2-container {
            width: 100% !important;
        }
        
        #profileWizardModal .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 1px;
        }
        
        #profileWizardModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 10px;
            color: #333;
        }
        
        #profileWizardModal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        #profileWizardModal .select2-dropdown {
            z-index: 999999 !important;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: absolute !important;
        }
        
        /* Ensure Select2 dropdown appears above modal when open */
        .select2-container--open .select2-dropdown {
            z-index: 999999 !important;
            position: absolute !important;
        }
        
        /* Fix modal content overflow when Select2 is open */
        #profileWizardContent.has-open-select2 {
            overflow: visible !important;
        }
        
        #profileWizardModal .select2-results__option {
            padding: 10px 12px;
            color: #333;
        }
        
        #profileWizardModal .select2-results__option--highlighted {
            background-color: #00a04a;
            color: white;
        }
        
        #profileWizardModal .select2-results__option[aria-selected="true"] {
            background-color: #e8f5e9;
        }
        
        /* Loading spinner for Complete Profile button */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div style="background: white; border-radius: 12px; overflow: hidden; max-width: 900px; width: 50%; box-shadow: 0 20px 50px rgba(0,0,0,0.3); position: relative; border: 1px solid #eee; animation: modalContentAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;">

        <!-- Wizard Header -->
        <div style="padding: 10px 15px; border-bottom: 3px solid #00a04a; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <img src="{{ asset('anim/resume.gif') }}" alt="Resume" style="width: 40px; height: 40px; object-fit: contain;">
                <h3 id="profileWizardTitle" style="margin: 0; color: #000041; font-weight: 800; font-size: 22px; letter-spacing: -0.5px;">Complete Your Profile</h3>
            </div>
            <div style="font-size: 14px; color: #000041; font-weight: 600; background: #e8f5e9; padding: 5px 12px; border-radius: 20px; border: 1px solid #c8e6c9;">
                Step <span id="profileStepNumber">1</span> of 7
            </div>
        </div>

        <!-- Wizard Content -->
        <div id="profileWizardContent" style="padding: 0; background: white; min-height: 350px; max-height: 500px; overflow-y: auto;">
            <form id="profile-form" action="{{ url('user/profile_completion') }}" method="POST">
                @csrf

                <!-- Step 1: Basic Information -->
                <div id="profileStep1" class="wizard-step" style="display: block;">
                    <div style="padding: 30px;">

                        <h2 style="color: #000041; font-weight: 900; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Basic Information</h2>
                        <p style="font-size: 14px; color: #444; margin-bottom: 20px;">Let's start with some essential details about you.</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Salutation</label>
                                <select name="salutation" class="form-control" style="padding: 1px; border-radius: 6px; border: 1px solid #ddd;">
                                    <option value="">Select</option>
                                    <option value="Mr." {{ old('salutation', $user->salutation) == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                    <option value="Ms." {{ old('salutation', $user->salutation) == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                    <option value="Mrs." {{ old('salutation', $user->salutation) == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                    <option value="Dr." {{ old('salutation', $user->salutation) == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Employment Type</label>
                                <select name="employment_type" class="form-control" style="padding: 1px; border-radius: 6px; border: 1px solid #ddd;">
                                    <option value="">Select</option>
                                    <option value="Permanent" {{ old('employment_type', $user->employment_type) == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                    <option value="Probation" {{ old('employment_type', $user->employment_type) == 'Probation' ? 'selected' : '' }}>Probation</option>
                                    <option value="Provisional" {{ old('employment_type', $user->employment_type) == 'Provisional' ? 'selected' : '' }}>Provisional</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Mobile Number</label>
                                <input type="tel" name="mobile_number" id="mobile_number" class="form-control" value="{{ old('mobile_number', $user->mobile_number) }}" pattern="\d{10}" maxlength="10" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Personal Email</label>
                                <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email', $user->personal_email) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Address -->
                <div id="profileStep2" class="wizard-step" style="display: none;">
                    <div style="padding: 30px;">
                        <h2 style="color: #000041; font-weight: 900; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Address</h2>
                        <p style="font-size: 14px; color: #444; margin-bottom: 20px;">Please provide your current residential address.</p>
                        <div class="form-group">
                            <label style="font-weight: 600; color: #000041;">Current Address *</label>
                            <textarea name="current_address" class="form-control" rows="4" required style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">{{ old('current_address', $user->current_address) }}</textarea>
                            <div class="invalid-feedback">Please enter your current address.</div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Emergency Contact -->
                <div id="profileStep3" class="wizard-step" style="display: none;">
                    <div style="padding: 30px;">
                        <h2 style="color: #000041; font-weight: 900; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Emergency Contact</h2>
                        <p style="font-size: 14px; color: #444; margin-bottom: 20px;">Who should we contact in case of an emergency?</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Emergency Phone</label>
                                <input type="tel" name="emergency_phone" class="form-control" value="{{ old('emergency_phone', $user->emergency_phone) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label style="font-weight: 600; color: #000041;">Relation to Emergency Contact</label>
                            <select name="relation_to_emergency" class="form-control select2">
                                <option value="">Select Relation</option>
                                <option value="Father" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Father' ? 'selected' : '' }}>Father</option>
                                <option value="Mother" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Mother' ? 'selected' : '' }}>Mother</option>
                                <option value="Spouse" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                <option value="Son" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Son' ? 'selected' : '' }}>Son</option>
                                <option value="Daughter" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                <option value="Brother" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Brother' ? 'selected' : '' }}>Brother</option>
                                <option value="Sister" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Sister' ? 'selected' : '' }}>Sister</option>
                                <option value="Grandfather" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Grandfather' ? 'selected' : '' }}>Grandfather</option>
                                <option value="Grandmother" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Grandmother' ? 'selected' : '' }}>Grandmother</option>
                                <option value="Uncle" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                <option value="Aunt" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                <option value="Cousin" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                                <option value="Friend" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Friend' ? 'selected' : '' }}>Friend</option>
                                <option value="Colleague" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Colleague' ? 'selected' : '' }}>Colleague</option>
                                <option value="Neighbor" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Neighbor' ? 'selected' : '' }}>Neighbor</option>
                                <option value="Other" {{ old('relation_to_emergency', $user->relation_to_emergency) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Employment Details -->
                <div id="profileStep4" class="wizard-step" style="display: none;">
                    <div style="padding: 30px;">
                        <h2 style="color: #000041; font-weight: 900; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Employment Details</h2>
                        <p style="font-size: 14px; color: #444; margin-bottom: 20px;">Please provide your employment information.</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Date of Joining</label>
                                <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining', $user->created_at ? $user->created_at->format('Y-m-d') : '') }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Company</label>
                                <input type="text" disabled name="company" class="form-control" value="Whence Finance Services" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Employee Number</label>
                                <input type="text"
                                    name="employee_number"
                                    class="form-control"
                                    value="{{ 'WFS' . date('Y') . $user->id }}"
                                    readonly
                                    style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Department</label>
                                <select name="department" class="form-control">
                                    <option value="">Select Department</option>
                                    <option value="Administration" {{ old('department', $user->department) == 'Administration' ? 'selected' : '' }}>Administration</option>
                                    <option value="Finance" {{ old('department', $user->department) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Management" {{ old('department', $user->department) == 'Management' ? 'selected' : '' }}>Management</option>
                                    <option value="Payroll" {{ old('department', $user->department) == 'Payroll' ? 'selected' : '' }}>Payroll</option>
                                    <option value="Operations" {{ old('department', $user->department) == 'Operations' ? 'selected' : '' }}>Operations</option>
                                    <option value="IT" {{ old('department', $user->department) == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="HR" {{ old('department', $user->department) == 'HR' ? 'selected' : '' }}>HR</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Designation</label>
                                <input type="text" name="designation" class="form-control" value="{{ $user->roles->first() ? $user->roles->first()->name : '' }}" readonly style="padding: 1px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Branch</label>
                                <input type="text" name="branch" class="form-control" value="{{ $user->office ? $user->office->name : '' }}" readonly style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Reports to</label>
                                <select name="reports_to" class="form-control">
                                    <option value="">Select Reports To</option>
                                    @foreach(\App\Models\User::all() as $staff)
                                        <option value="{{ $staff->id }}" {{ old('reports_to', $user->reports_to) == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->first_name }} {{ $staff->last_name }}
                                            @if($staff->designation)
                                                ({{ $staff->designation }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Financial & Health -->
                <div id="profileStep5" class="wizard-step" style="display: none;">
                    <div style="padding: 30px;">
                        <h2 style="color: #000041; font-weight: 900; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Financial & Health Information</h2>
                        <p style="font-size: 14px; color: #444; margin-bottom: 20px;">Please provide your financial and health details.</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Salary Currency</label>
                                <input type="text" name="salary_currency" disabled class="form-control" value="ZMW" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Salary Mode *</label>
                                <input type="text" disabled name="salary_mode" class="form-control" value="{{ old('salary_mode', $user->salary_mode) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Bank Name (optional)</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $user->bank_name) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Bank Account Number (optional)</label>
                                <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $user->bank_account_number) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Health Insurance Provider</label>
                                <input disabled type="text" name="health_insurance_provider" class="form-control" value="NHIMA" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Health Insurance Number</label>
                                <input disabled type="text" name="health_insurance_number" class="form-control" value="{{ old('health_insurance_number', $user->health_insurance_number) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label style="font-weight: 600; color: #000041;">Health Details</label>
                            <textarea name="health_details" class="form-control" rows="3" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">{{ old('health_details', $user->health_details) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Education -->
                <div id="profileStep6" class="wizard-step" style="display: none;">
                    <div style="padding: 30px;">
                        <h2 style="color: #000041; font-weight: 900; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Education</h2>
                        <p style="font-size: 14px; color: #444; margin-bottom: 20px;">Please provide your educational background.</p>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Qualification</label>
                                <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $user->qualification) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">School/University</label>
                                <input type="text" name="school_university" class="form-control" value="{{ old('school_university', $user->school_university) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Level of Education</label>
                                <select name="level_of_education" class="form-control select2">
                                    <option value="">Select Level of Education</option>
                                    <option value="Certificate" {{ old('level_of_education', $user->level_of_education) == 'Certificate' ? 'selected' : '' }}>Certificate</option>
                                    <option value="Diploma" {{ old('level_of_education', $user->level_of_education) == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                    <option value="Bachelor Degree" {{ old('level_of_education', $user->level_of_education) == 'Bachelor Degree' ? 'selected' : '' }}>Bachelor Degree</option>
                                    <option value="Master Degree" {{ old('level_of_education', $user->level_of_education) == 'Master Degree' ? 'selected' : '' }}>Master Degree</option>
                                    <option value="Doctorate (PhD)" {{ old('level_of_education', $user->level_of_education) == 'Doctorate (PhD)' ? 'selected' : '' }}>Doctorate (PhD)</option>
                                    <option value="Other" {{ old('level_of_education', $user->level_of_education) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Year Completed</label>
                                <input type="number" name="year_completed" class="form-control" value="{{ old('year_completed', $user->year_completed) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label style="font-weight: 600; color: #000041;">Major/Optional Subjects</label>
                                <input type="text" name="major" class="form-control" value="{{ old('major', $user->major) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 7: Work History -->
                <div id="profileStep7" class="wizard-step" style="display: none;">
                    <div style="padding: 30px;">
                        <h2 style="color: #000041; font-weight: 900; font-size: 24px; margin-top: 0; margin-bottom: 15px;">Work History</h2>
                        <i>Please provide your work experience details.</i>
                        <h3 style="color: #000041; font-weight: 700; font-size: 18px; margin-top: 20px; margin-bottom: 10px;">External Work History</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Company</label>
                                <input type="text" name="external_company" class="form-control" value="{{ old('external_company', $user->external_company) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Designation</label>
                                <input type="text" name="external_designation" class="form-control" value="{{ old('external_designation', $user->external_designation) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Contact</label>
                                <input type="tel" name="external_contact" class="form-control" value="{{ old('external_contact', $user->external_contact) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Total Experience</label>
                                <input type="text" name="external_total_experience" class="form-control" value="{{ old('external_total_experience', $user->external_total_experience) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                        </div>
                        <h3 style="color: #000041; font-weight: 700; font-size: 18px; margin-top: 20px; margin-bottom: 10px;">Internal Work History</h3>
                        <i>Have you worked at Whence Financial Services at other different branches?</i>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Branch</label>
                                <input type="text" name="internal_branch" class="form-control" value="{{ old('internal_branch', $user->internal_branch) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">Designation</label>
                                <input type="text" name="internal_designation" class="form-control" value="{{ old('internal_designation', $user->internal_designation) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">From Date</label>
                                <input type="date" name="internal_from_date" class="form-control" value="{{ old('internal_from_date', $user->internal_from_date) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; color: #000041;">To Date</label>
                                <input type="date" name="internal_to_date" class="form-control" value="{{ old('internal_to_date', $user->internal_to_date) }}" style="padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Wizard Footer -->
        <div style="padding: 8px 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; gap: 12px; background: #f1f8e9;">
            <button id="profileBtnPrev" style="display: none; padding: 8px 20px; border: 2px solid #ddd; background: white; border-radius: 6px; cursor: pointer; font-weight: 600; color: #555; transition: all 0.2s ease; font-size: 14px;">
                <i class="fa fa-arrow-left" style="margin-right: 6px;"></i> Previous
            </button>
            <div style="display: flex; gap: 12px;">
                <button id="profileBtnSkip" style="padding: 8px 20px; border: 2px solid #6c757d; background: white; color: #6c757d; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">Skip</button>
                <button id="profileBtnNext" style="padding: 8px 24px; border: none; background: #00a04a; color: white; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">
                    Next <i class="fa fa-arrow-right" style="margin-left: 6px;"></i>
                </button>
                <button id="profileBtnFinish" style="display: none; padding: 8px 24px; border: none; background: #000041; color: white; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">
                    Complete Profile <i class="fa fa-check" style="margin-left: 6px;"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var currentStep = 1;
        var totalSteps = 7;
        var btnNext = document.getElementById('profileBtnNext');
        var btnPrev = document.getElementById('profileBtnPrev');
        var btnSkip = document.getElementById('profileBtnSkip');
        var btnFinish = document.getElementById('profileBtnFinish');
        var stepNumber = document.getElementById('profileStepNumber');
        var wizardTitle = document.getElementById('profileWizardTitle');

        function validateCurrentStep() {
            if (currentStep === 1) {
                var mobile = document.getElementById('mobile_number').value.trim();
                var email = document.querySelector('input[name="personal_email"]').value.trim();
                if (mobile === '' || mobile.length !== 10 || !/^\d{10}$/.test(mobile)) {
                    toastr.error('Please enter a valid 10-digit mobile number.', 'Validation Error');
                    return false;
                }
                if (email === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    toastr.error('Please enter a valid email address.', 'Validation Error');
                    return false;
                }
            } else if (currentStep === 2) {
                var address = document.querySelector('textarea[name="current_address"]').value.trim();
                if (address === '') {
                    toastr.error('Please enter your current address.', 'Validation Error');
                    return false;
                }
            } else if (currentStep === 3) {
                var name = document.querySelector('input[name="emergency_contact_name"]').value.trim();
                var phone = document.querySelector('input[name="emergency_phone"]').value.trim();
                var relationSelect = document.querySelector('select[name="relation_to_emergency"]');
                if (name === '') {
                    toastr.error('Please enter emergency contact name.', 'Validation Error');
                    return false;
                }
                if (phone === '' || phone.length !== 10 || !/^\d{10}$/.test(phone)) {
                    toastr.error('Please enter a valid 10-digit emergency phone number.', 'Validation Error');
                    return false;
                }
                if (relationSelect && relationSelect.selectedIndex === 0) {
                    toastr.error('Please select relation to emergency contact.', 'Validation Error');
                    return false;
                }
            } else if (currentStep === 4) {
                var deptSelect = document.querySelector('select[name="department"]');
                var reportsSelect = document.querySelector('select[name="reports_to"]');
                var dobInput = document.querySelector('input[name="date_of_birth"]');
                var dojInput = document.querySelector('input[name="date_of_joining"]');
                var dob = dobInput ? dobInput.value.trim() : '';
                var doj = dojInput ? dojInput.value.trim() : '';
                
                if (deptSelect && deptSelect.selectedIndex === 0) {
                    toastr.error('Please select a department.', 'Validation Error');
                    return false;
                }
                // if (reportsSelect && reportsSelect.selectedIndex === 0) {
                //     toastr.error('Please select reports to.', 'Validation Error');
                //     return false;
                // }
                if (!dob || dob === '') {
                    toastr.error('Please enter date of birth.', 'Validation Error');
                    return false;
                }
                var birthDate = new Date(dob);
                if (isNaN(birthDate.getTime())) {
                    toastr.error('Please enter a valid date of birth.', 'Validation Error');
                    return false;
                }
                var today = new Date();
                var age = today.getFullYear() - birthDate.getFullYear();
                var m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                if (age < 18) {
                    toastr.error('You must be at least 18 years old.', 'Validation Error');
                    return false;
                }
                if (!doj || doj === '') {
                    toastr.error('Please enter date of joining.', 'Validation Error');
                    return false;
                }
                var joinDate = new Date(doj);
                if (isNaN(joinDate.getTime())) {
                    toastr.error('Please enter a valid date of joining.', 'Validation Error');
                    return false;
                }
            } else if (currentStep === 5) {
                var salaryMode = document.querySelector('input[name="salary_mode"]').value.trim();
                var bankName = document.querySelector('input[name="bank_name"]').value.trim();
                var bankAccount = document.querySelector('input[name="bank_account_number"]').value.trim();
                var healthInsNum = document.querySelector('input[name="health_insurance_number"]').value.trim();
                // if (salaryMode === '') {
                //     toastr.error('Please enter salary mode.', 'Validation Error');
                //     return false;
                // }
                // if (bankName === '') {
                //     toastr.error('Please enter bank name.', 'Validation Error');
                //     return false;
                // }
                // if (bankAccount === '') {
                //     toastr.error('Please enter bank account number.', 'Validation Error');
                //     return false;
                // }
                // if (healthInsNum === '') {
                //     toastr.error('Please enter health insurance number.', 'Validation Error');
                //     return false;
                // }
            } else if (currentStep === 6) {
                var levelEduSelect = document.querySelector('select[name="level_of_education"]');
                var yearComp = document.querySelector('input[name="year_completed"]').value.trim();
                var qual = document.querySelector('input[name="qualification"]').value.trim();
                var school = document.querySelector('input[name="school_university"]').value.trim();
                // if (levelEduSelect.selectedIndex === 0) {
                //     toastr.error('Please select level of education.', 'Validation Error');
                //     return false;
                // }
                // if (yearComp === '' || isNaN(yearComp) || yearComp < 1900 || yearComp > new Date().getFullYear()) {
                //     toastr.error('Please enter a valid year completed.', 'Validation Error');
                //     return false;
                // }
                // if (qual === '') {
                //     toastr.error('Please enter qualification.', 'Validation Error');
                //     return false;
                // }
                // if (school === '') {
                //     toastr.error('Please enter school/university.', 'Validation Error');
                //     return false;
                // }
            } else if (currentStep === 7) {
                var fromDate = document.querySelector('input[name="internal_from_date"]').value.trim();
                var toDate = document.querySelector('input[name="internal_to_date"]').value.trim();
                // if (fromDate && !toDate) {
                //     toastr.error('Please enter to date if from date is provided.', 'Validation Error');
                //     return false;
                // }
                // if (!fromDate && toDate) {
                //     toastr.error('Please enter from date if to date is provided.', 'Validation Error');
                //     return false;
                // }
                // if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
                //     toastr.error('From date cannot be after to date.', 'Validation Error');
                //     return false;
                // }
            }
            return true;
        }

        function updateWizard() {
            // Hide ALL steps first
            document.querySelectorAll('#profileWizardContent .wizard-step').forEach(step => {
                step.style.display = 'none';
            });

            // Show current step
            document.getElementById('profileStep' + currentStep).style.display = 'block';

            // Update Header & Buttons
            stepNumber.textContent = currentStep;

            if (currentStep === 1) {
                wizardTitle.textContent = 'Complete Your Profile';
                btnPrev.style.display = 'none';
                btnNext.style.display = 'block';
                btnFinish.style.display = 'none';
            } else if (currentStep === totalSteps) {
                wizardTitle.textContent = 'Final Step';
                btnPrev.style.display = 'block';
                btnNext.style.display = 'none';
                btnFinish.style.display = 'block';
            } else {
                wizardTitle.textContent = 'Complete Your Profile';
                btnPrev.style.display = 'block';
                btnNext.style.display = 'block';
                btnFinish.style.display = 'none';
            }
        }

        btnNext.addEventListener('click', function () {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateWizard();
                }
            }
        });

        btnPrev.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                updateWizard();
            }
        });

        btnSkip.addEventListener('click', function () {
            document.getElementById('profileWizardModal').style.display = 'none';
        });

        btnFinish.addEventListener('click', function () {
            if (validateCurrentStep()) {
                // Add loading state
                btnFinish.classList.add('btn-loading');
                btnFinish.innerHTML = ' ';
                
                // Submit the form
                document.getElementById('profile-form').submit();
            }
        });

        // Show modal after page load
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('profileWizardModal').style.display = 'flex';
            // Initialize Select2 on all select elements with select2 class
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#profileWizardContent .select2').select2({
                    dropdownParent: $('#profileWizardModal'),
                    width: '100%',
                    height: '100%',
                    theme: 'default',
                    minimumResultsForSearch: Infinity,
                    dropdownAutoWidth: false,
                    width: 'resolve'
                }).on('select2:open', function() {
                    // Allow dropdown to overflow modal content
                    $('#profileWizardContent').css('overflow', 'visible');
                }).on('select2:close', function() {
                    // Restore overflow after dropdown closes
                    $('#profileWizardContent').css('overflow', 'auto');
                });
            }
            updateWizard();
        });
    })();
</script>
@endif
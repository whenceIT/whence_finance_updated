@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }} @if($loan_product->id == 0) (Motor Vehicle Loan) @endif
@endsection

@section('content')
<?php $todaysDate = date('Y-m-d'); ?>
<div class="box box-primary">
    <div class="box-header with-border" style="padding: 18px 20px; background: linear-gradient(135deg, #1a3a5c 0%, #2c6fad 100%); border-radius: 4px 4px 0 0;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="background:rgba(255,255,255,0.15); border-radius:8px; width:46px; height:46px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fa fa-file-text" style="font-size:20px; color:#fff;"></i>
            </div>
            <div>
                <p style="margin:0 0 2px 0; font-size:11px; font-weight:600; letter-spacing:1.2px; text-transform:uppercase; color:rgba(255,255,255,0.65);">
                    New Loan Application
                </p>
                <h3 style="margin:0; font-size:20px; font-weight:800; color:#ffffff; letter-spacing:0.3px; line-height:1.2;">
                    {{ trans_choice('general.add',1) }} {{ $loan_product->name }} {{ trans_choice('general.loan',1) }}
                    @if($loan_product->id == 0)
                        &nbsp;<span style="font-size:13px; font-weight:600; background:rgba(255,255,255,0.2); color:#fff; padding:2px 10px; border-radius:20px; vertical-align:middle;">Motor Vehicle</span>
                    @endif
                </h3>
            </div>
        </div>
        <div class="box-tools pull-right" style="margin-top:8px;">
            <button onclick="window.history.back()" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.35); font-weight:600;">
                <i class="fa fa-arrow-left"></i> {{ trans_choice('general.cancel',1) }}
            </button>
        </div>
    </div>

    <form method="post" action="{{url('loan/create_client_loan/'.$client->id.'/'.$loan_product->id.'/store')}}" class="form-horizontal" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="box-body">

            {{-- ===================== LOAN DETAILS ===================== --}}
            <div class="panel panel-default" style="border-radius:6px; padding:15px; margin-bottom:20px;">
                <h4 style="color:#3c8dbc; font-weight:600; margin-bottom:15px;">Loan Details</h4>

                {{-- Row 1: Loan Officer | Loan Purpose --}}
                <div class="form-group">
                    <label for="loan_officer_id" class="control-label col-md-2">
                        {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-title="The financial institution representative responsible for this client"></i>
                    </label>
                    <div class="col-md-4">
                        <select name="loan_officer_id" class="form-control select2" id="loan_officer_id" required>
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}" @if($client->staff_id==$key->id) selected @endif>
                                        {{$key->first_name}} {{$key->last_name}}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <label for="loan_purpose_id" class="control-label col-md-2">
                        {{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-title="Purpose of the loan for reporting"></i>
                    </label>
                    <div class="col-md-4">
                        <select name="loan_purpose_id" class="form-control select2" id="loan_purpose_id">
                            <option></option>
                            @foreach(\App\Models\LoanPurpose::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Row 2: Fund | Submitted On --}}
                <div class="form-group">
                    <label for="fund_id" class="control-label col-md-2">
                        {{trans_choice('general.fund',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-title="The original source of funds"></i>
                    </label>
                    <div class="col-md-4">
                        <select name="fund_id" class="form-control select2" id="fund_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <label for="created_date" class="control-label col-md-2">
                        {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-title="The date the loan application was received"></i>
                    </label>
                    <div class="col-md-4">
                        <input type="date" name="created_date" class="form-control" min="{{$todaysDate}}" value="{{date('Y-m-d')}}" required id="created_date">
                    </div>
                </div>

                {{-- Row 3: External ID --}}
                <div class="form-group">
                    <label for="external_id" class="control-label col-md-2">{{trans_choice('general.external_id',1)}}</label>
                    <div class="col-md-4">
                        <input type="text" name="external_id" class="form-control" value="{{old('external_id')}}" id="external_id">
                    </div>
                </div>
            </div>

            {{-- ===================== LOAN TERMS ===================== --}}
            <div class="panel panel-default" style="border-radius:6px; padding:15px; margin-bottom:20px;">
                <h4 style="color:#3c8dbc; font-weight:600; margin-bottom:15px;">Loan Terms</h4>
                <input type="hidden" name="phone_number" id="phone_number">

                @if($loan_product->id == 1)
                {{-- Row 1: Schedule Type | (spacer) --}}
                <div class="form-group">
                    <label for="schedule_type" class="control-label col-md-2">Payroll Loan Schedule Type</label>
                    <div class="col-md-4">
                        <select name="schedule_type" class="form-control select2" id="schedule_type" required>
                            <option value="new" selected>New Schedule</option>
                            <option value="old">Old Schedule</option>
                        </select>
                        <small class="text-muted">Select "Old Schedule" for legacy payroll loan repayment rates</small>
                    </div>
                </div>
                @endif

                @if($loan_product->id == 1)
                {{-- ---- OLD SCHEDULE: Disbursement dropdown + Term dropdown ---- --}}
                @php
                    $oldSchedules = \App\Models\PayrollLoanOldSchedule::orderBy('disbursement_amount')->get();
                    // Build JS-friendly map: { amount: { 9: repayment, 12: repayment, ... } }
                    $oldScheduleMap = [];
                    foreach($oldSchedules as $row) {
                        $oldScheduleMap[$row->disbursement_amount] = [
                            9  => $row->repayment_9_months,
                            12 => $row->repayment_12_months,
                            18 => $row->repayment_18_months,
                            24 => $row->repayment_24_months,
                        ];
                    }
                @endphp

                {{-- Old-schedule principal & term shown only when schedule_type = old --}}
                <div id="old_schedule_fields" style="display:none;">
                    <div class="form-group">
                        <label class="control-label col-md-2">Disbursement Amount</label>
                        <div class="col-md-4">
                            <select id="old_principal_select" class="form-control">
                                <option value="">-- Select Amount --</option>
                                @foreach($oldSchedules as $row)
                                    <option value="{{ $row->disbursement_amount }}">
                                        K{{ number_format($row->disbursement_amount, 0) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <label class="control-label col-md-2">Loan Term</label>
                        <div class="col-md-4">
                            <select id="old_term_select" class="form-control" disabled>
                                <option value="">-- Select Amount First --</option>
                            </select>
                        </div>
                    </div>

                    {{-- Interest summary card --}}
                    <div class="form-group" id="old_schedule_summary" style="display:none;">
                        <div class="col-md-offset-2 col-md-10">
                            <div style="background:#f0f7ff; border:1px solid #bcd8f1; border-radius:6px; padding:12px 16px;">
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        <small class="text-muted" style="display:block; font-size:11px; text-transform:uppercase; letter-spacing:.8px;">Monthly Repayment</small>
                                        <strong id="oss_monthly" style="font-size:18px; color:#2c6fad;">—</strong>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <small class="text-muted" style="display:block; font-size:11px; text-transform:uppercase; letter-spacing:.8px;">Total Repayment</small>
                                        <strong id="oss_total" style="font-size:18px; color:#2c6fad;">—</strong>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <small class="text-muted" style="display:block; font-size:11px; text-transform:uppercase; letter-spacing:.8px;">Total Interest</small>
                                        <strong id="oss_interest" style="font-size:18px; color:#e67e22;">—</strong>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <small class="text-muted" style="display:block; font-size:11px; text-transform:uppercase; letter-spacing:.8px;">Effective Rate</small>
                                        <strong id="oss_rate" style="font-size:18px; color:#27ae60;">—</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden real inputs that get populated from the dropdowns --}}
                <input type="hidden" name="principal" id="principal" value="{{ $amount }}" required>
                <input type="hidden" name="loan_term" id="loan_term" value="1" required>
                <input type="hidden" name="loan_term_type" id="loan_term_type" value="months">

                {{-- New-schedule principal & term shown when schedule_type = new --}}
                <div id="new_schedule_fields">
                    <div class="form-group">
                        <label for="new_principal" class="control-label col-md-2">{{trans_choice('general.principal',1)}}</label>
                        <div class="col-md-4">
                            <input type="number" id="new_principal" class="form-control"
                                   min="{{$loan_product->minimum_principal}}"
                                   max="{{$loan_product->maximum_principal}}"
                                   value="{{$amount}}">
                            <small class="text-muted">Maximum: K{{ number_format($loan_product->maximum_principal, 2) }}</small>
                        </div>

                        <label class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.term',1)}}</label>
                        <div class="col-md-2">
                            <input type="number" id="new_loan_term" class="form-control" min=1 max=1 value=1>
                        </div>
                        <div class="col-md-2">
                            <select id="new_loan_term_type" class="form-control">
                                <option value="months" @if($loan_product->repayment_frequency_type=="months") selected @endif>
                                    {{trans_choice('general.month',2)}}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                @else
                {{-- ---- ALL OTHER PRODUCTS: standard number input ---- --}}
                <div class="form-group">
                    <label for="principal" class="control-label col-md-2">{{trans_choice('general.principal',1)}}</label>
                    <div class="col-md-4">
                        <input type="number" name="principal" class="form-control"
                               min="{{$loan_product->minimum_principal}}"
                               max="{{$loan_product->maximum_principal}}"
                               value="{{$amount}}" required id="principal">
                        <small class="text-muted">Maximum: K{{ number_format($loan_product->maximum_principal, 2) }}</small>
                    </div>

                    <label for="loan_term" class="control-label col-md-2">
                        {{trans_choice('general.loan',1)}} {{trans_choice('general.term',1)}}
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="loan_term" class="form-control" min=1 max=1 value=1 required id="loan_term">
                    </div>
                    <div class="col-md-2">
                        <select name="loan_term_type" class="form-control" id="loan_term_type" required>
                            <option value="months" @if($loan_product->repayment_frequency_type=="months") selected @endif>
                                {{trans_choice('general.month',2)}}
                            </option>
                        </select>
                    </div>
                </div>
                @endif

                {{-- Row 2: Repayment Every | Interest Rate --}}
                <div class="form-group">
                    <label for="repayment_frequency" class="control-label col-md-2">
                        {{trans_choice('general.repayment',1)}} {{trans_choice('general.every',1)}}
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="repayment_frequency" class="form-control" min=1 max=1 value=1 required id="repayment_frequency">
                    </div>
                    <div class="col-md-2">
                        <select name="repayment_frequency_type" class="form-control" id="repayment_frequency_type" required>
                            <option value="months" @if($loan_product->repayment_frequency_type=="months") selected @endif>
                                {{trans_choice('general.month',2)}}
                            </option>
                        </select>
                    </div>

                    @if($loan_product->id != 1)
                    {{-- Show interest rate field for all products except payroll loan --}}
                    <label for="interest_rate" class="control-label col-md-2">
                        {{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                    </label>
                    @if($loan_product->id == 0)
                        <div class="col-md-2">
                            <input type="number" name="interest_rate" class="form-control" min="20" max="35" value="{{$loan_product->default_interest_rate}}" required id="interest_rate">
                        </div>
                    @else
                        <div class="col-md-2">
                            <input type="number" name="interest_rate" class="form-control" min="40" max="40" value="{{$loan_product->default_interest_rate}}" required id="interest_rate">
                        </div>
                    @endif
                    <label class="control-label col-md-1 text-left" style="padding-left:0;">
                        % {{trans_choice('general.per',1)}}
                        @if($loan_product->interest_rate_type=="month") {{trans_choice('general.month',1)}} @endif
                        @if($loan_product->interest_rate_type=="year") {{trans_choice('general.year',1)}} @endif
                    </label>
                    @else
                    {{-- Payroll loan: interest is derived from the schedule, not entered manually --}}
                    <input type="hidden" name="interest_rate" id="interest_rate" value="0">
                    @endif
                </div>

                @if($loan_product->id != 1)
                {{-- Row 3: Override Interest | Override Interest Rate (conditional) --}}
                <div class="form-group">
                    <label for="override_interest" class="control-label col-md-2">
                        {{trans_choice('general.override',1)}} {{trans_choice('general.interest',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip" data-title="Set yes if you want the system to use this interest per period for calculation"></i>
                    </label>
                    <div class="col-md-4">
                        <select name="override_interest" class="form-control select2" id="override_interest">
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                        </select>
                    </div>

                    <div id="override_interest_rate_div" style="display:none; padding:0;" class="col-md-6">
                        <div class="col-md-4" style="padding-left:0;">
                            <label for="override_interest_rate" class="control-label" style="padding-top:7px;">
                                {{trans_choice('general.override',1)}} {{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="override_interest_rate" class="form-control" value="{{$loan_product->default_interest_rate}}" id="override_interest_rate">
                        </div>
                    </div>
                </div>
                @endif {{-- end @if($loan_product->id != 1) --}}

                {{-- Row 4: Collateral (non-MVL, non-payroll only) --}}
                @if($loan_product->id != 0 && $loan_product->id != 1)
                <div class="form-group">
                    <label for="has_collateral" class="control-label col-md-2">
                        Loan Has Collateral? <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-4">
                        <select name="has_collateral" class="form-control select2" id="has_collateral" required>
                            <option value="">-- Select --</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        <small class="text-muted">Select Yes to add collateral information</small>
                    </div>
                </div>
                @endif

                <input type="hidden" name="redirect_to_collateral" id="redirect_to_collateral" value="0">
                <input type="hidden" name="mvl_next" id="mvl_next" value="@if($loan_product->id == 0) 1 @else 0 @endif">
            </div>

            {{-- ===================== DISBURSEMENT & VERIFICATION ===================== --}}
            <div class="panel panel-default" style="border-radius:6px; padding:15px; margin-bottom:20px;">
                <h4 style="color:#3c8dbc; font-weight:600; margin-bottom:15px;">Disbursement &amp; Verification</h4>

                {{-- Row 1: Disbursement Date | First Repayment Date --}}
                <div class="form-group">
                    <label for="expected_disbursement_date" class="control-label col-md-2">
                        {{trans_choice('general.disbursement',1)}} {{trans_choice('general.on',1)}}
                    </label>
                    <div class="col-md-4">
                        <input type="date" name="expected_disbursement_date" class="form-control" min="{{$todaysDate}}" value="{{date('Y-m-d')}}" required id="expected_disbursement_date">
                    </div>

                    <label for="expected_first_repayment_date" class="control-label col-md-2">
                        {{trans_choice('general.first',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.on',1)}}
                    </label>
                    <div class="col-md-4">
                        <input type="text" name="expected_first_repayment_date" class="form-control date-picker" required id="expected_first_repayment_date">
                    </div>
                </div>

                @if($loan_product->id == 0)
                {{-- MVL: autocomplete search fields --}}
                {{-- Row 2: Vetted by | Verified by --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Vetted by</label>
                    <div class="col-md-4" style="position:relative;">
                        <input type="text" id="vetted_by_search" class="form-control" placeholder="Search employee...">
                        <input type="hidden" name="vetted_by" id="vetted_by">
                        <div id="vetted_results" style="position:absolute;background:white;border:1px solid #ddd;width:100%;z-index:9999;"></div>
                    </div>

                    <label class="control-label col-md-2">Verified by</label>
                    <div class="col-md-4" style="position:relative;">
                        <input type="text" id="verified_by_search" class="form-control" placeholder="Search employee...">
                        <input type="hidden" name="verified_by" id="verified_by">
                        <div id="verified_results" style="position:absolute;background:white;border:1px solid #ddd;width:100%;z-index:9999;"></div>
                    </div>
                </div>
                @else
                {{-- Standard: dropdown selects --}}
                {{-- Row 2: Vetted by | Verified by --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Vetted by</label>
                    <div class="col-md-4">
                        <select name="vetted_by" class="form-control select2" required>
                            <option></option>
                            @foreach(\App\Models\User::where('office_id',$userBranch)->get() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <label class="control-label col-md-2">Verified by</label>
                    <div class="col-md-4">
                        <select name="verified_by" class="form-control select2" required>
                            <option></option>
                            @foreach(\App\Models\User::where('office_id',$userBranch)->get() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
            </div>

            {{-- ===================== VEHICLE DETAILS (Motor Vehicle Loan only) ===================== --}}
            @if($loan_product->id == 0)
            <div class="panel panel-default" style="border-radius:6px; padding:15px; margin-bottom:20px;">
                <h4 style="color:#3c8dbc; font-weight:600; margin-bottom:15px;">Vehicle Details</h4>

                {{-- Row 1: Make | Model --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Make</label>
                    <div class="col-md-4">
                        <input type="text" name="make" class="form-control">
                    </div>

                    <label class="control-label col-md-2">Model</label>
                    <div class="col-md-4">
                        <input type="text" name="model" class="form-control">
                    </div>
                </div>

                {{-- Row 2: Year | Registration Number --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Year</label>
                    <div class="col-md-4">
                        <input type="number" name="year" class="form-control">
                    </div>

                    <label class="control-label col-md-2">Registration Number</label>
                    <div class="col-md-4">
                        <input type="text" name="registration_number" class="form-control">
                    </div>
                </div>

                {{-- Row 3: Market Value | Insurance Policy No --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Market Value</label>
                    <div class="col-md-4">
                        <input type="number" name="market_value" class="form-control">
                    </div>

                    <label class="control-label col-md-2">Comprehensive Insurance Policy No</label>
                    <div class="col-md-4">
                        <input type="text" name="insurance_policy_number" class="form-control">
                    </div>
                </div>

                {{-- Row 4: Referrer | Referrer Branch --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Referrer</label>
                    <div class="col-md-4" style="position:relative;">
                        <input type="text" id="referrer_search" class="form-control" placeholder="Search referrer...">
                        <input type="hidden" name="referrer" id="referrer">
                        <div id="referrer_results" style="position:absolute;background:white;border:1px solid #ddd;width:100%;z-index:9999;"></div>
                    </div>

                    <label for="office_id" class="control-label col-md-2">Referrer Branch</label>
                    <div class="col-md-4">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @php $offices = \App\Helpers\GeneralHelper::get_filtered_offices_new(); @endphp
                            @foreach($offices as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endif

            {{-- ===================== CLIENT BANK DETAILS (Product 1 only) ===================== --}}
            @if($loan_product->id == 1)
            <div class="panel panel-default" style="border-radius:6px; padding:15px; margin-bottom:20px;">
                <h4 style="color:#3c8dbc; font-weight:600; margin-bottom:15px;">Client Bank Details</h4>

                {{-- Row 1: Account Number | Bank Name --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Account Number</label>
                    <div class="col-md-4">
                        <input type="number" name="account_number" class="form-control">
                    </div>

                    <label class="control-label col-md-2">Bank Name</label>
                    <div class="col-md-4">
                        <input type="text" name="bank_name" class="form-control">
                    </div>
                </div>

                {{-- Row 2: Branch Name | Branch Code --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Branch Name</label>
                    <div class="col-md-4">
                        <input type="text" name="branch_name" class="form-control">
                    </div>

                    <label class="control-label col-md-2">Branch Code</label>
                    <div class="col-md-4">
                        <input type="text" name="branch_code" class="form-control">
                    </div>
                </div>

                {{-- Row 3: Sort Code --}}
                <div class="form-group">
                    <label class="control-label col-md-2">Sort Code</label>
                    <div class="col-md-4">
                        <input type="text" name="sort_code" class="form-control">
                    </div>
                </div>
            </div>
            @endif

            {{-- ===================== CUSTOM FIELDS ===================== --}}
            @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                @foreach(\App\Models\CustomField::where('category','loans')->get() as $key)
                    <div class="form-group">
                        <label class="control-label col-md-2">{{$key->name}}</label>
                        <div class="col-md-4">
                            @if($key->field_type=="number")
                                <input type="number" class="form-control" name="custom_field_{{$key->id}}" @if($key->required==1) required @endif>
                            @endif
                            @if($key->field_type=="textfield")
                                <input type="text" class="form-control" name="custom_field_{{$key->id}}" @if($key->required==1) required @endif>
                            @endif
                            @if($key->field_type=="date")
                                <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}" @if($key->required==1) required @endif>
                            @endif
                            @if($key->field_type=="textarea")
                                <textarea class="form-control" name="custom_field_{{$key->id}}" @if($key->required==1) required @endif></textarea>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

        </div><!-- /.box-body -->

        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right">
                {{trans_choice('general.save',1)}}
                @if($loan_product->id == 0) and proceed to KYC verification @endif
            </button>
        </div>
    </form>
</div>
@endsection

@section('footer-scripts')
<script>

    // ===================== OLD SCHEDULE LOGIC (product_id == 1) =====================
    @if($loan_product->id == 1)
    var oldScheduleMap = @json($oldScheduleMap);
    @php
        $newScheduleRows = DB::table('payroll_loan_schedules')->get();
        $newScheduleMap  = [];
        foreach ($newScheduleRows as $row) {
            $newScheduleMap[$row->loan_amount] = [
                3  => $row->months_3  ?? null,
                6  => $row->months_6  ?? null,
                9  => $row->months_9  ?? null,
                12 => $row->months_12 ?? null,
                18 => $row->months_18 ?? null,
                24 => $row->months_24 ?? null,
            ];
        }
    @endphp
    var newScheduleMap = @json($newScheduleMap);

    function formatK(n) {
        return 'K' + Number(n).toLocaleString('en-ZM', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function syncHiddenInputs(amount, term, termType) {
        $('#principal').val(amount);
        $('#loan_term').val(term);
        $('#loan_term_type').val(termType || 'months');
    }

    function buildTermOptions(amount) {
        var row = oldScheduleMap[amount];
        var html = '<option value="">-- Select Term --</option>';
        if (!row) { return html; }
        var labels = { 9: '9 Months', 12: '12 Months', 18: '18 Months', 24: '24 Months' };
        [9, 12, 18, 24].forEach(function(m) {
            if (row[m] !== null && row[m] !== undefined) {
                html += '<option value="' + m + '" data-repayment="' + row[m] + '">' + labels[m] + ' — ' + formatK(row[m]) + '/mo</option>';
            }
        });
        return html;
    }

    function updateSummary(amount, term, monthly) {
        if (!amount || !term || !monthly) {
            $('#old_schedule_summary').hide();
            return;
        }
        var total    = monthly * term;
        var interest = total - parseFloat(amount);
        var rate     = ((interest / parseFloat(amount)) * 100).toFixed(2);
        $('#oss_monthly').text(formatK(monthly));
        $('#oss_total').text(formatK(total));
        $('#oss_interest').text(formatK(interest));
        $('#oss_rate').text(rate + '%');
        $('#old_schedule_summary').show();
        // Push effective rate into the hidden interest_rate field
        $('#interest_rate').val(rate);
    }

    function applyScheduleToggle() {
        var type = $('#schedule_type').val();
        if (type === 'old') {
            $('#old_schedule_fields').show();
            $('#new_schedule_fields').hide();
            // clear hidden inputs until user selects
            syncHiddenInputs('', '', 'months');
        } else {
            $('#old_schedule_fields').hide();
            $('#new_schedule_fields').show();
            $('#old_schedule_summary').hide();
            // sync hidden inputs from new-schedule fields
            syncHiddenInputs($('#new_principal').val(), $('#new_loan_term').val(), $('#new_loan_term_type').val());
        }
    }

    // Sync new-schedule visible inputs → hidden inputs in real time
    // Also compute & push effective rate into #interest_rate
    function updateNewScheduleRate() {
        if ($('#schedule_type').val() === 'old') return;
        var amount = parseFloat($('#new_principal').val());
        var term   = parseInt($('#new_loan_term').val());
        if (!amount || !term || !newScheduleMap) { return; }
        var row = newScheduleMap[amount];
        if (!row) { return; }
        var monthly = row[term];
        if (!monthly) { return; }
        var total    = monthly * term;
        var interest = total - amount;
        var rate     = ((interest / amount) * 100).toFixed(2);
        $('#interest_rate').val(rate);
    }

    $('#new_principal, #new_loan_term, #new_loan_term_type').on('input change', function() {
        if ($('#schedule_type').val() !== 'old') {
            syncHiddenInputs($('#new_principal').val(), $('#new_loan_term').val(), $('#new_loan_term_type').val());
            updateNewScheduleRate();
        }
    });

    // Schedule type toggle
    $('#schedule_type').on('change', applyScheduleToggle);
    applyScheduleToggle(); // run on page load

    // Disbursement amount chosen
    $('#old_principal_select').on('change', function() {
        var amount = $(this).val();
        $('#old_term_select').html(buildTermOptions(amount)).prop('disabled', !amount);
        $('#old_schedule_summary').hide();
        syncHiddenInputs(amount || '', '', 'months');
    });

    // Term chosen
    $('#old_term_select').on('change', function() {
        var term    = $(this).val();
        var monthly = $(this).find('option:selected').data('repayment');
        var amount  = $('#old_principal_select').val();
        syncHiddenInputs(amount, term, 'months');
        updateSummary(amount, parseInt(term), parseFloat(monthly));
    });
    @endif
    // ===================== END OLD SCHEDULE LOGIC =====================

    function setupEmployeeSearch(input, hidden, results) {
        $(input).on('keyup', function () {
            let search = $(this).val();
            if (search.length < 2) {
                $(results).html('');
                return;
            }
            $.ajax({
                url: "{{ route('users.search') }}",
                type: "GET",
                data: { search: search },
                success: function (data) {
                    let html = '';
                    data.results.forEach(function (user) {
                        html += `
                            <div class="autocomplete-item"
                                 style="padding:8px;cursor:pointer;background:#fff;border-bottom:1px solid #eee;"
                                 onmouseover="this.style.background='#f5f5f5'"
                                 onmouseout="this.style.background='#fff'"
                                 data-id="${user.id}"
                                 data-name="${user.text}">
                                ${user.text}
                            </div>`;
                    });
                    $(results).html(html);
                }
            });
        });

        $(document).on('click', results + ' .autocomplete-item', function () {
            $(input).val($(this).data('name'));
            $(hidden).val($(this).data('id'));
            $(results).html('');
        });
    }

    setupEmployeeSearch('#vetted_by_search',   '#vetted_by',   '#vetted_results');
    setupEmployeeSearch('#verified_by_search', '#verified_by', '#verified_results');
    setupEmployeeSearch('#referrer_search',    '#referrer',    '#referrer_results');

    // Override interest toggle
    function toggleOverrideInterest() {
        if ($("#override_interest").val() == 0) {
            $("#override_interest_rate_div").hide();
            $("#override_interest_rate").removeAttr("required");
            $("#interest_rate").attr("required", "required");
        } else {
            $("#override_interest_rate_div").show();
            $("#override_interest_rate").attr("required", "required");
            $("#interest_rate").removeAttr("required");
        }
    }

    toggleOverrideInterest();
    $("#override_interest").change(toggleOverrideInterest);

    // Charge helpers (kept for when charges section is re-enabled)
    $('#currency_id').change(function () {
        var id = $('#currency_id').val();
        var url = "{!! url('loan/product') !!}/" + id + "/get_currency_charges";
        var items = "<option></option>";
        $.getJSON(url, function (data) {
            $.each(data, function (index, item) {
                items += "<option value='" + item.id + "'>" + item.name + "</option>";
            });
            $("#charges_dropdown").html(items);
        });
    });

    $('#add_charge').click(function () {
        if ($('#charges_dropdown').val() == "") {
            alert("Please select an item");
        } else {
            var id = $('#charges_dropdown').val();
            $.ajax({
                type: 'GET',
                url: "{{url('loan/product/')}}" + "/" + id + "/get_charge_detail",
                dataType: "json",
                success: function (data) {
                    var to_append = '<tr id="row' + id + '"><td>' + data.name + '</td><td>' + data.charge_option + '</td>';
                    if (data.override == "1") {
                        to_append += '<td><input type="number" class="form-control" name="charge_amount[' + data.id + ']" value="' + data.amount + '" required></td>';
                    } else {
                        to_append += '<td><input type="hidden" name="charge_amount[' + data.id + ']" value="' + data.amount + '">' + data.amount + '</td>';
                    }
                    to_append += '<td>' + data.collected_on + '</td>';
                    to_append += '<td><input type="text" class="form-control date-picker" name="charge_date[' + data.id + ']" value="" required></td>';
                    to_append += '<td><button type="button" class="btn btn-danger btn-xs" data-id="' + id + '" onclick="delete_charge(this)"><i class="fa fa-trash"></i></button></td></tr>';
                    $('#charges_table').append(to_append);
                    $('#saved_charges').append('<input name="charges[]" id="charge' + id + '" value="' + id + '">');
                },
                error: function () {
                    swal({ title: 'Error', text: 'An Error occurred, please try again', type: 'warning', confirmButtonColor: '#3085d6', confirmButtonText: 'Ok', timer: 2000 });
                }
            });
        }
    });

    function delete_charge(e) {
        swal({
            title: 'Are you sure?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok',
            cancelButtonText: 'Cancel'
        }).then(function () {
            $('#charge' + $(e).attr("data-id")).remove();
            $('#row'    + $(e).attr("data-id")).remove();
        });
    }

    // Form validation
    $(".form-horizontal").validate({
        highlight: function (element) {
            $(element).closest('.form-group div').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group div').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    let phone_numbeer = "{{ $number }}";
    $('#phone_number').val(phone_numbeer);

</script>
@endsection

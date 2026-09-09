@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Ownership Verification</h1>
    <ol class="breadcrumb">
        <li><a href="../mvl/motor-vehicle-loans">Motor Vehicle Loans</a></li>
        <li><a href="{{ url('/') }}/vehicles/{{ $vehicle->id }}">Vehicle Details</a></li>
        <li class="active">Ownership Verification</li>
    </ol>
</section>

<section class="content">

    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i>
        <strong>Ownership Verification</strong> — Record ownership, insurance, inspection, documents, photos, and custody details for this vehicle.
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Vehicle Details</h3>
                </div>
                <div class="box-body text-center">
                    <i class="fa fa-car" style="font-size: 80px; color: #3c8dbc; margin-bottom: 15px;"></i>
                    <h4>{{ $vehicle->make }} {{ $vehicle->model }}</h4>
                    <p class="text-muted">{{ $vehicle->year ?? 'N/A' }} | {{ $vehicle->color ?? 'N/A' }}</p>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped" id="vehicleDetailsTable">
                        <tr>
                            <th style="width: 40%;">Number Plate</th>
                            <td>
                                <span class="editable-field" data-field="registration_number" data-value="{{ $vehicle->registration_number ?? '' }}">
                                    {{ $vehicle->registration_number ?? 'N/A' }}
                                </span>
                                <a href="javascript:void(0)" class="edit-btn pull-right" data-field="registration_number" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Color</th>
                            <td>
                                <span class="editable-field" data-field="color" data-value="{{ $vehicle->color ?? '' }}">
                                    {{ $vehicle->color ?? 'N/A' }}
                                </span>
                                <a href="javascript:void(0)" class="edit-btn pull-right" data-field="color" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Engine No.</th>
                            <td>
                                <span class="editable-field" data-field="engine_number" data-value="{{ $vehicle->engine_number ?? '' }}">
                                    {{ $vehicle->engine_number ?? 'N/A' }}
                                </span>
                                <a href="javascript:void(0)" class="edit-btn pull-right" data-field="engine_number" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Chassis No.</th>
                            <td>
                                <span class="editable-field" data-field="chassis_number" data-value="{{ $vehicle->chassis_number ?? '' }}">
                                    {{ $vehicle->chassis_number ?? 'N/A' }}
                                </span>
                                <a href="javascript:void(0)" class="edit-btn pull-right" data-field="chassis_number" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Mileage</th>
                            <td>
                                <span class="editable-field" data-field="mileage" data-value="{{ $vehicle->mileage ?? '' }}">
                                    {{ $vehicle->mileage ?? 'N/A' }}
                                </span>
                                <a href="javascript:void(0)" class="edit-btn pull-right" data-field="mileage" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Fuel Type</th>
                            <td>
                                <span class="editable-field" data-field="fuel_type" data-value="{{ $vehicle->fuel_type ?? '' }}">
                                    {{ $vehicle->fuel_type ?? 'N/A' }}
                                </span>
                                <a href="javascript:void(0)" class="edit-btn pull-right" data-field="fuel_type" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Transmission</th>
                            <td>
                                <span class="editable-field" data-field="transmission" data-value="{{ $vehicle->transmission ?? '' }}">
                                    {{ $vehicle->transmission ?? 'N/A' }}
                                </span>
                                <a href="javascript:void(0)" class="edit-btn pull-right" data-field="transmission" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#ownership_tab" data-toggle="tab"><i class="fa fa-user"></i> Ownership Verification @if(isset($records) && count($records) > 0)<i class="fa fa-check text-success"></i>@else<i class="fa fa-times text-danger"></i>@endif</a></li>
                    <li><a href="#insurance_tab" data-toggle="tab"><i class="fa fa-shield-alt"></i> Insurance @if($vehicle->insurancePolicies && count($vehicle->insurancePolicies) > 0)<i class="fa fa-check text-success"></i>@else<i class="fa fa-times text-danger"></i>@endif</a></li>
                    <li><a href="#inspection_tab" data-toggle="tab"><i class="fa fa-clipboard-check"></i> Inspection @if($vehicle->inspections && count($vehicle->inspections) > 0)<i class="fa fa-check text-success"></i>@else<i class="fa fa-times text-danger"></i>@endif</a></li>
                    <li><a href="#valuation_tab" data-toggle="tab"><i class="fa fa-calculator"></i> Valuation @if($vehicle->valuations && count($vehicle->valuations) > 0)<i class="fa fa-check text-success"></i>@else<i class="fa fa-times text-danger"></i>@endif</a></li>
                    <li><a href="#documents_tab" data-toggle="tab"><i class="fa fa-folder"></i> Documents @if($vehicle->documents && count($vehicle->documents) > 0)<i class="fa fa-check text-success"></i>@else<i class="fa fa-times text-danger"></i>@endif</a></li>
                    <li><a href="#photos_tab" data-toggle="tab"><i class="fa fa-image"></i> Photos @if($vehicle->photos && count($vehicle->photos) > 0)<i class="fa fa-check text-success"></i>@else<i class="fa fa-times text-danger"></i>@endif</a></li>
                    <li><a href="#custody_tab" data-toggle="tab"><i class="fa fa-warehouse"></i> Custody @if($vehicle->custody)<i class="fa fa-check text-success"></i>@else<i class="fa fa-times text-danger"></i>@endif</a></li>
                </ul>

                <div class="tab-content">

                    <!-- ===================== Ownership Verification Tab ===================== -->
                    <div class="tab-pane active" id="ownership_tab">

                        @if(isset($records) && count($records) > 0)
                        <div class="box box-default" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Existing Ownership Records</h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Registered Owner</th>
                                            <th>Seller</th>
                                            <th>Company</th>
                                            <th>Authorized Rep.</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($records as $record)
                                        <tr>
                                            <td><span class="label label-primary">{{ ucfirst($record->ownership_type) }}</span></td>
                                            <td>{{ $record->registered_owner_name ?? '-' }}</td>
                                            <td>{{ $record->seller_name ?? '-' }}</td>
                                            <td>{{ $record->company_name ?? '-' }}</td>
                                            <td>{{ $record->authorized_representative_name ?? '-' }}</td>
                                            <td>{{ $record->created_at ? $record->created_at->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                <a href="{{ url('vehicles/'.$vehicle->id.'/ownership-verification/'.$record->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="box box-primary" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add Ownership Record</h3>
                                <div class="box-tools pull-right">
                                    <a href="{{ url('vehicles/'.$vehicle->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-arrow-left"></i> Back to Vehicle
                                    </a>
                                </div>
                            </div>

                            <form method="post" action="{{ route('vehicles.store-ownership-verification', $vehicle->id) }}" class="form-horizontal" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="box-body">

                                    <div class="form-group">
                                        <label for="ownership_type" class="control-label col-md-2">Ownership Type <span class="text-danger">*</span></label>
                                        <div class="col-md-4">
                                            <select name="ownership_type" class="form-control" id="ownership_type" required>
                                                <option value="">-- Select Ownership Type --</option>
                                                <option value="individual">Individual</option>
                                                <option value="letter_of_sale">Letter of Sale</option>
                                                <option value="company">Company / Corporate</option>
                                            </select>
                                            <small class="form-text text-muted">Choose the ownership basis for this vehicle.</small>
                                        </div>
                                    </div>

                                    <hr>

                                    <div id="individual_fields" class="ownership-section" style="display: none;">
                                        <h4 class="text-semibold" style="margin-bottom: 15px;">
                                            <i class="fa fa-user"></i> Individual Ownership Details
                                        </h4>
                                        <div class="form-group">
                                            <label for="registered_owner" class="control-label col-md-2">Registered Owner Name <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="registered_owner" class="form-control" value="{{ old('registered_owner', $vehicle->registered_owner ?? '') }}" id="registered_owner">
                                                <small class="form-text text-muted">Full name as it appears on the vehicle registration.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ownership_documents" class="control-label col-md-2">Ownership Documents <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="file" name="ownership_documents" class="form-control" id="ownership_documents">
                                                <small class="form-text text-muted">Upload vehicle registration, logbook, or other proof of ownership.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="letter_of_sale_fields" class="ownership-section" style="display: none;">
                                        <h4 class="text-semibold" style="margin-bottom: 15px;">
                                            <i class="fa fa-file-text-o"></i> Letter of Sale Details
                                        </h4>
                                        <div class="form-group">
                                            <label for="seller_name" class="control-label col-md-2">Seller Name <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="seller_name" class="form-control" value="{{ old('seller_name') }}" id="seller_name">
                                                <small class="form-text text-muted">Full legal name of the person selling the vehicle.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="seller_nrc" class="control-label col-md-2">Seller NRC <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="seller_nrc" class="form-control" value="{{ old('seller_nrc') }}" id="seller_nrc">
                                                <small class="form-text text-muted">National Registration Card number of the seller.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="seller_phone" class="control-label col-md-2">Seller Phone <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="seller_phone" class="form-control" value="{{ old('seller_phone') }}" id="seller_phone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="letter_of_sale_file" class="control-label col-md-2">Letter of Sale <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="file" name="letter_of_sale_file" class="form-control" id="letter_of_sale_file">
                                                <small class="form-text text-muted">Upload the signed Letter of Sale document.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="witness_1_name" class="control-label col-md-2">Witness 1 Name <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="witness_1_name" class="form-control" value="{{ old('witness_1_name') }}" id="witness_1_name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="witness_1_nrc" class="control-label col-md-2">Witness 1 NRC <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="witness_1_nrc" class="form-control" value="{{ old('witness_1_nrc') }}" id="witness_1_nrc">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="witness_2_name" class="control-label col-md-2">Witness 2 Name <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="witness_2_name" class="form-control" value="{{ old('witness_2_name') }}" id="witness_2_name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="witness_2_nrc" class="control-label col-md-2">Witness 2 NRC <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="witness_2_nrc" class="form-control" value="{{ old('witness_2_nrc') }}" id="witness_2_nrc">
                                            </div>
                                        </div>
                                    </div>

                                    <div id="company_fields" class="ownership-section" style="display: none;">
                                        <h4 class="text-semibold" style="margin-bottom: 15px;">
                                            <i class="fa fa-building"></i> Corporate Ownership Details
                                        </h4>
                                        <div class="form-group">
                                            <label for="company_name" class="control-label col-md-2">Company Name <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" id="company_name">
                                                <small class="form-text text-muted">Full registered company name.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="company_registration" class="control-label col-md-2">Company Registration Number <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="company_registration" class="form-control" value="{{ old('company_registration') }}" id="company_registration">
                                                <small class="form-text text-muted">e.g., CR/123/45/2024 or Company Registration Certificate number.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="directors" class="control-label col-md-2">Directors <span class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <textarea name="directors" class="form-control" id="directors" rows="3" placeholder="List all directors, one per line">{{ old('directors') }}</textarea>
                                                <small class="form-text text-muted">Enter full names and ID numbers of all company directors.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="authorized_representative" class="control-label col-md-2">Authorized Representative Name <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="authorized_representative" class="form-control" value="{{ old('authorized_representative') }}" id="authorized_representative">
                                                <small class="form-text text-muted">Person authorized to pledge the vehicle on behalf of the company.</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="authorized_representative_nrc" class="control-label col-md-2">Authorized Representative NRC <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="authorized_representative_nrc" class="form-control" value="{{ old('authorized_representative_nrc') }}" id="authorized_representative_nrc">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ownership_documents" class="control-label col-md-2">Ownership Documents <span class="text-danger">*</span></label>
                                            <div class="col-md-4">
                                                <input type="file" name="ownership_documents" class="form-control" id="ownership_documents_company">
                                                <small class="form-text text-muted">Upload company registration certificate, vehicle registration, and any relevant board resolutions.</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary pull-right">
                                        <i class="fa fa-save"></i> Save Ownership Details
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- ===================== Insurance Tab ===================== -->
                    <div class="tab-pane" id="insurance_tab">

                        @if($vehicle->insurancePolicies && count($vehicle->insurancePolicies) > 0)
                        <div class="box box-default" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Existing Insurance Policies</h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered table-striped">
                                         <thead>
                                             <tr>
                                                 <th>Insurer</th>
                                                 <th>Policy Number</th>
                                                 <th>Start Date</th>
                                                 <th>Expiry Date</th>
                                                 <th>Insured Value</th>
                                                 <th>Premium</th>
                                                 <th>Cover Type</th>
                                                 <th>Status</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @foreach($vehicle->insurancePolicies as $policy)
                                             <tr>
                                                 <td>{{ $policy->insurer_name ?? '-' }}</td>
                                                 <td>{{ $policy->policy_number ?? '-' }}</td>
                                                 <td>{{ $policy->start_date ? \Carbon\Carbon::parse($policy->start_date)->format('Y-m-d') : '-' }}</td>
                                                 <td>{{ $policy->expiry_date ? \Carbon\Carbon::parse($policy->expiry_date)->format('Y-m-d') : '-' }}</td>
                                                 <td>{{ $policy->insured_value ?? '-' }}</td>
                                                 <td>{{ $policy->premium ?? '-' }}</td>
                                                 <td>{{ $policy->cover_type ?? '-' }}</td>
                                                 <td>
                                                     @php
                                                         $expiry = $policy->expiry_date ? \Carbon\Carbon::parse($policy->expiry_date) : null;
                                                         $now = \Carbon\Carbon::now();
                                                     @endphp
                                                     @if(!$expiry)
                                                         <span class="label label-default">N/A</span>
                                                     @elseif($expiry->isPast())
                                                         <span class="label label-danger">Expired</span>
                                                     @elseif($expiry->diffInDays($now) <= 30)
                                                         <span class="label label-warning">Expiring in {{ $expiry->diffInDays($now) }} days</span>
                                                     @else
                                                         <span class="label label-success">Active</span>
                                                     @endif
                                                 </td>
                                             </tr>
                                             @endforeach
                                         </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="box box-success" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add Insurance Record</h3>
                            </div>

                            <form method="POST" action="{{ url('vehicles/'.$vehicle->id.'/insurance/store') }}">
                                @csrf
                                <div class="box-body">

                                    <div class="alert alert-info">
                                        Vehicle:
                                        <strong>{{ $vehicle->make }} {{ $vehicle->model }}</strong>
                                        <br>
                                        Registration: <strong>{{ $vehicle->registration_number }}</strong>
                                    </div>

                                    <div class="form-group">
                                        <label>Insurer Name</label>
                                        <input type="text" name="insurer_name" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Policy Number</label>
                                        <input type="text" name="policy_number" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <input type="date" name="expiry_date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Insured Value</label>
                                        <input type="number" step="0.01" name="insured_value" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Premium</label>
                                        <input type="number" step="0.01" name="premium" class="form-control">
                                        <small class="form-text text-muted">Optional premium amount.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Cover Type</label>
                                        <select name="cover_type" class="form-control">
                                            <option value="">Select Cover Type</option>
                                            <option>Comprehensive</option>
                                            <option>Third Party Only</option>
                                            <option>Third Party Fire & Theft</option>
                                            <option>Comprehensive Plus</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-success">Save Insurance</button>
                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- ===================== Inspection Tab ===================== -->
                    <div class="tab-pane" id="inspection_tab">

                        @if($vehicle->inspections && count($vehicle->inspections) > 0)
                        <div class="box box-default" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Existing Inspections</h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Inspector</th>
                                                    <th>Mileage</th>
                                                    <th>Result</th>
                                                    <th>Rating</th>
                                                    <th>Score</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($vehicle->inspections as $inspection)
                                                <tr>
                                                    <td>{{ $inspection->inspection_date ? \Carbon\Carbon::parse($inspection->inspection_date)->format('Y-m-d') : '-' }}</td>
                                                    <td>{{ $inspection->inspection_type ?? '-' }}</td>
                                                    <td>{{ $inspection->inspector ?? '-' }}</td>
                                                    <td>{{ $inspection->mileage ?? '-' }}</td>
                                                    <td>{{ $inspection->result ?? '-' }}</td>
                                                    <td>{{ $inspection->condition_rating ?? '-' }}</td>
                                                    <td>{{ $inspection->condition_score ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="box box-warning" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add Inspection Record</h3>
                            </div>

                            <form method="POST" enctype="multipart/form-data" action="{{ url('vehicles/'.$vehicle->id.'/inspections/store') }}">
                                @csrf
                                <div class="box-body">

                                    <div class="alert alert-info">
                                        {{ $vehicle->make }} {{ $vehicle->model }}
                                        ({{ $vehicle->registration_number }})
                                    </div>

                                    <div class="form-group">
                                        <label>Inspection Type</label>
                                        <select name="inspection_type" class="form-control" required>
                                            <option value="receipt">Vehicle Receipt</option>
                                            <option value="release">Vehicle Release</option>
                                            <option value="routine">Routine Inspection</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Inspection Date</label>
                                        <input type="date" name="inspection_date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Inspector</label>
                                        <input type="text" name="inspector" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Mileage</label>
                                        <input type="number" name="mileage" class="form-control">
                                    </div>

                                    <hr>
                                    <h4 class="text-semibold" style="margin-bottom: 15px; margin-top: 20px;">
                                        <i class="fa fa-car"></i> Condition Assessment
                                    </h4>

                                    <div class="form-group">
                                        <label>Mechanical Condition</label>
                                        <textarea name="mechanical_condition" rows="3" class="form-control" placeholder="Engine, transmission, brakes, suspension, etc."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Interior Condition</label>
                                        <textarea name="interior_condition" rows="3" class="form-control" placeholder="Seats, dashboard, controls, carpets, etc."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Exterior Condition</label>
                                        <textarea name="exterior_condition" rows="3" class="form-control" placeholder="Body panels, paint, lights, glass, etc."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Tyres Condition</label>
                                        <textarea name="tyres_condition" rows="3" class="form-control" placeholder="Tread depth, sidewall condition, spare tyre, etc."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Battery Condition</label>
                                        <textarea name="battery_condition" rows="3" class="form-control" placeholder="Battery health, terminals, charge level, etc."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Accessories Condition</label>
                                        <textarea name="accessories_condition" rows="3" class="form-control" placeholder="Radio, navigation, spare tyre, tools, etc."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Condition Score (0-100)</label>
                                        <input type="number" name="condition_score" class="form-control" min="0" max="100" placeholder="Overall condition rating">
                                        <small class="form-text text-muted">Numeric score representing overall vehicle condition. Higher is better.</small>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Fuel Level</label>
                                        <select name="fuel_level" class="form-control">
                                            <option value="">Select Fuel Level</option>
                                            <option value="Empty">Empty</option>
                                            <option value="1/4 Tank">1/4 Tank</option>
                                            <option value="1/2 Tank">1/2 Tank</option>
                                            <option value="3/4 Tank">3/4 Tank</option>
                                            <option value="Full Tank">Full Tank</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Condition Rating</label>
                                        <select name="condition_rating" class="form-control">
                                            <option></option>
                                            <option>Excellent</option>
                                            <option>Good</option>
                                            <option>Fair</option>
                                            <option>Poor</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Result</label>
                                        <select name="result" class="form-control">
                                            <option>Passed</option>
                                            <option>Failed</option>
                                            <option>Pending</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Inspection Report</label>
                                        <input type="file" name="report_file" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>General Remarks</label>
                                        <textarea name="notes" rows="5" class="form-control"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Supporting Photographs</label>
                                        <input type="file" name="photos[]" multiple accept="image/*" class="form-control">
                                        <p class="help-block">You may upload multiple images showing damages, mileage, fuel gauge, accessories, engine bay, or any other inspection evidence.</p>
                                    </div>

                                </div>

                                <div class="box-footer">
                                    <button class="btn btn-warning">Save Inspection</button>
                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- ===================== Valuation Tab ===================== -->
                    <div class="tab-pane" id="valuation_tab">

                        @if($vehicle->valuations && count($vehicle->valuations) > 0)
                        <div class="box box-default" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Valuation History</h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Company</th>
                                            <th>Valuator</th>
                                            <th>Market Value</th>
                                            <th>Forced Sale</th>
                                            <th>Cost</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vehicle->valuations as $valuation)
                                        <tr>
                                            <td>{{ $valuation->valuation_date ? \Carbon\Carbon::parse($valuation->valuation_date)->format('Y-m-d') : '-' }}</td>
                                            <td>{{ $valuation->valuation_company ?? '-' }}</td>
                                            <td>{{ $valuation->valuator_name ?? '-' }}</td>
                                            <td>{{ $valuation->market_value ?? '-' }}</td>
                                            <td>{{ $valuation->forced_sale_value ?? '-' }}</td>
                                            <td>{{ $valuation->valuation_cost ?? '-' }}</td>
                                            <td>{{ $valuation->expiry_date ? \Carbon\Carbon::parse($valuation->expiry_date)->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                @php
                                                    $expiry = $valuation->expiry_date ? \Carbon\Carbon::parse($valuation->expiry_date) : null;
                                                    $now = \Carbon\Carbon::now();
                                                @endphp
                                                @if(!$expiry)
                                                    <span class="label label-default">N/A</span>
                                                @elseif($expiry->isPast())
                                                    <span class="label label-danger">Expired</span>
                                                @elseif($expiry->diffInDays($now) <= 30)
                                                    <span class="label label-warning">Expires in {{ $expiry->diffInDays($now) }} days</span>
                                                @else
                                                    <span class="label label-success">Valid</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($valuation->report_file_path)
                                                    <a href="{{ $valuation->report_file_path }}" target="_blank" class="btn btn-xs btn-info">Report</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="box box-info" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add Valuation Record</h3>
                            </div>

                            <form method="POST" enctype="multipart/form-data" action="{{ url('vehicles/'.$vehicle->id.'/valuations/store') }}">
                                @csrf
                                <div class="box-body">

                                    <div class="alert alert-info">
                                        {{ $vehicle->make }} {{ $vehicle->model }}
                                        ({{ $vehicle->registration_number }})
                                    </div>

                                    <div class="form-group">
                                        <label>Valuation Company <span class="text-danger">*</span></label>
                                        <input type="text" name="valuation_company" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Valuator</label>
                                        <input type="text" name="valuator_name" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Valuation Date <span class="text-danger">*</span></label>
                                        <input type="date" name="valuation_date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Market Value <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="market_value" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Forced Sale Value</label>
                                        <input type="number" step="0.01" name="forced_sale_value" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Valuation Cost</label>
                                        <input type="number" step="0.01" name="valuation_cost" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <input type="date" name="expiry_date" class="form-control">
                                        <small class="form-text text-muted">Date after which this valuation is no longer valid.</small>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Valuation Report</label>
                                        <input type="file" name="report_file" class="form-control">
                                        <small class="form-text text-muted">Upload the full valuation report document.</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Valuation Photos</label>
                                        <input type="file" name="photos[]" multiple accept="image/*" class="form-control">
                                        <p class="help-block">Upload photos of the vehicle taken during valuation (front, rear, sides, interior, engine bay, odometer).</p>
                                    </div>

                                    <div class="form-group">
                                        <label>Supporting Documents</label>
                                        <input type="file" name="supporting_documents[]" multiple class="form-control">
                                        <p class="help-block">Upload any supporting documents (purchase invoice, logbook, service records, etc.).</p>
                                    </div>

                                </div>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-info">Save Valuation</button>
                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- ===================== Documents Tab ===================== -->
                    <div class="tab-pane" id="documents_tab">

                        @if($vehicle->documents && count($vehicle->documents) > 0)
                        <div class="box box-default" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Existing Documents</h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Uploaded</th>
                                            <th>File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vehicle->documents as $doc)
                                        <tr>
                                            <td>{{ $doc->document_type ?? '-' }}</td>
                                            <td>{{ $doc->document_name ?? '-' }}</td>
                                            <td>{{ $doc->created_at ? $doc->created_at->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                @if($doc->document_file)
                                                    <a href="{{ $doc->document_file }}" target="_blank">View</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="box box-primary" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Upload Vehicle Document</h3>
                            </div>

                            <form method="POST" enctype="multipart/form-data" action="{{ url('vehicles/'.$vehicle->id.'/documents/store') }}">
                                @csrf
                                <div class="box-body">

                                    <div class="alert alert-info">
                                        Vehicle: <strong>{{ $vehicle->make }} {{ $vehicle->model }}</strong>
                                        <br>
                                        Registration: <strong>{{ $vehicle->registration_number }}</strong>
                                    </div>

                                    <div class="form-group">
                                        <label>Document Type</label>
                                        <select name="document_type" class="form-control" required>
                                            <option value="">Select Document Type</option>
                                            <option value="Whitebook">Whitebook</option>
                                            <option value="Insurance Certificate">Insurance Certificate</option>
                                            <option value="Valuation Report">Valuation Report</option>
                                            <option value="Purchase Invoice">Purchase Invoice</option>
                                            <option value="Road Tax">Road Tax</option>
                                            <option value="Import Papers">Import Papers</option>
                                            <option value="Inspection Report">Inspection Report</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Document Name</label>
                                        <input type="text" name="document_name" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Select File</label>
                                        <input type="file" name="document_file" class="form-control" required>
                                    </div>

                                </div>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Upload Document</button>
                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- ===================== Photos Tab ===================== -->
                    <div class="tab-pane" id="photos_tab">

                        @if($vehicle->photos && count($vehicle->photos) > 0)
                        <div class="box box-default" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Existing Photos</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    @foreach($vehicle->photos as $photo)
                                    <div class="col-md-3">
                                        <div class="box">
                                            <div class="box-body">
                                                <img src="{{ $photo->photo_url }}" alt="{{ $photo->photo_type ?? 'Photo' }}" class="img-responsive" style="max-height: 150px; object-fit: cover; width: 100%;">
                                                <p class="text-center">{{ $photo->photo_type ?? '-' }}</p>
                                                <p class="text-center text-muted">{{ $photo->caption ?? '' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="box box-primary" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Upload Vehicle Photo</h3>
                            </div>

                            <form method="POST" enctype="multipart/form-data" action="{{ url('vehicles/'.$vehicle->id.'/photos/store') }}">
                                @csrf
                                <div class="box-body">

                                    <div class="alert alert-info">
                                        {{ $vehicle->make }} {{ $vehicle->model }}
                                        ({{ $vehicle->registration_number }})
                                    </div>

                                    <div class="form-group">
                                        <label>Photo Type</label>
                                        <select name="photo_type" class="form-control" required>
                                            <option value="">Select</option>
                                            <option>Front View</option>
                                            <option>Rear View</option>
                                            <option>Left Side</option>
                                            <option>Right Side</option>
                                            <option>Interior</option>
                                            <option>Dashboard</option>
                                            <option>Engine</option>
                                            <option>Odometer</option>
                                            <option>Damage</option>
                                            <option>Other</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Caption</label>
                                        <input type="text" name="caption" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Select Photo</label>
                                        <input type="file" name="photo" class="form-control" required>
                                    </div>

                                </div>

                                <div class="box-footer">
                                    <button class="btn btn-primary">Upload Photo</button>
                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- ===================== Custody Tab ===================== -->
                    <div class="tab-pane" id="custody_tab">

                        @if($vehicle->custody)
                        <div class="box box-default" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Current Custody Record</h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tr><th>Received At</th><td>{{ $vehicle->custody->received_at ? \Carbon\Carbon::parse($vehicle->custody->received_at)->format('Y-m-d H:i') : '-' }}</td></tr>
                                    <tr><th>Received By</th><td>{{ optional($vehicle->custody->receiver)->first_name ?? '' }} {{ optional($vehicle->custody->receiver)->last_name ?? '' }}</td></tr>
                                    <tr><th>Garage / Facility</th><td>{{ $vehicle->custody->garage_name ?? '-' }}</td></tr>
                                    <tr><th>Location</th><td>{{ $vehicle->custody->garage_location ?? '-' }}</td></tr>
                                    <tr><th>Status</th><td>{{ ucfirst($vehicle->custody->status ?? 'N/A') }}</td></tr>
                                    <tr><th>Approved</th><td>{{ $vehicle->custody->custody_approved ? 'Yes' : 'No' }}</td></tr>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="box box-danger" style="border: none; box-shadow: none;">
                            <div class="box-header with-border">
                                <h3 class="box-title">Receive Vehicle Into Custody</h3>
                            </div>

                            <form method="POST" action="{{ url('vehicles/'.$vehicle->id.'/custody') }}">
                                @csrf
                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Date & Time Received</label>
                                                <input type="datetime-local" name="received_at" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Receiving Officer</label>
                                                <select name="received_by" class="form-control">
                                                    <option></option>
                                                    @foreach(\App\Models\User::all() as $user)
                                                        @if(!\Cartalyst\Sentinel\Laravel\Facades\Sentinel::findUserById($user->id)->inRole('client'))
                                                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h4 class="box-title">Garage / Storage Facility Details</h4>
                                        </div>
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label>Garage / Storage Facility Name</label>
                                                <input type="text" name="garage_name" class="form-control" placeholder="e.g. Whence Main Yard" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Physical Location</label>
                                                <textarea name="garage_location" class="form-control" rows="2" placeholder="Enter the physical address or description"></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>GPS Coordinates (Optional)</label>
                                                        <input type="text" name="garage_gps" class="form-control" placeholder="-15.3875, 28.3228">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Parking Bay / Slot (Optional)</label>
                                                        <input type="text" name="parking_bay" class="form-control" placeholder="e.g. Bay A12">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Contact Person</label>
                                                        <input type="text" name="garage_contact_person" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Contact Number</label>
                                                        <input type="text" name="garage_contact_phone" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Keys Received</label>
                                                <input type="number" name="keys_received" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Key Tag Numbers</label>
                                                <input type="text" name="key_tag_numbers" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control"></textarea>
                                    </div>

                                </div>

                                <div class="box-footer">
                                    <button class="btn btn-danger">Receive Vehicle</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

</section>

@endsection

@section('footer-scripts')
<style>
.editable-field {
    display: inline-block;
    padding: 3px 5px;
    border-radius: 3px;
    transition: background-color 0.2s;
}
.editable-field:hover {
    background-color: #f5f5f5;
}
.edit-btn {
    color: #3c8dbc;
    margin-left: 8px;
    opacity: 0.6;
    transition: opacity 0.2s;
}
.edit-btn:hover {
    opacity: 1;
    text-decoration: none;
}
.edit-input {
    display: inline-block;
    padding: 3px 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
    width: 150px;
    font-size: 14px;
}
.save-btn, .cancel-btn {
    padding: 3px 8px;
    margin-left: 5px;
    border-radius: 3px;
    border: none;
    cursor: pointer;
    font-size: 12px;
}
.save-btn {
    background-color: #28a745;
    color: white;
}
.save-btn:hover {
    background-color: #218838;
}
.cancel-btn {
    background-color: #6c757d;
    color: white;
}
.cancel-btn:hover {
    background-color: #5a6268;
}
.save-btn:disabled, .cancel-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.inline-edit-wrapper {
    display: flex;
    align-items: center;
}
.loading-spinner {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3c8dbc;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-left: 8px;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<script>
document.getElementById('ownership_type').addEventListener('change', function() {
    var sections = document.querySelectorAll('.ownership-section');
    sections.forEach(function(section) {
        section.style.display = 'none';
    });

    var selected = this.value;
    if (selected === 'individual') {
        document.getElementById('individual_fields').style.display = 'block';
    } else if (selected === 'letter_of_sale') {
        document.getElementById('letter_of_sale_fields').style.display = 'block';
    } else if (selected === 'company') {
        document.getElementById('company_fields').style.display = 'block';
    }
});

document.getElementById('ownership_type').dispatchEvent(new Event('change'));

// Inline Edit Functionality
document.addEventListener('DOMContentLoaded', function() {
    var vehicleId = {{ $vehicle->id }};
    var csrfToken = '{{ csrf_token() }}';
    
    document.querySelectorAll('.edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var field = this.getAttribute('data-field');
            var row = this.closest('tr');
            var displaySpan = row.querySelector('.editable-field');
            var currentValue = displaySpan.getAttribute('data-value') || displaySpan.textContent.trim();
            
            if (currentValue === 'N/A') currentValue = '';
            
            // Create input field
            var input = document.createElement('input');
            input.type = 'text';
            input.className = 'edit-input';
            input.value = currentValue;
            input.setAttribute('data-original-value', currentValue);
            
            // Create save button
            var saveBtn = document.createElement('button');
            saveBtn.className = 'save-btn';
            saveBtn.innerHTML = '<i class="fa fa-check"></i> Save';
            saveBtn.setAttribute('data-field', field);
            
            // Create cancel button
            var cancelBtn = document.createElement('button');
            cancelBtn.className = 'cancel-btn';
            cancelBtn.innerHTML = '<i class="fa fa-times"></i> Cancel';
            
            // Create wrapper
            var wrapper = document.createElement('div');
            wrapper.className = 'inline-edit-wrapper';
            wrapper.appendChild(input);
            wrapper.appendChild(saveBtn);
            wrapper.appendChild(cancelBtn);
            
            // Replace display with wrapper
            displaySpan.style.display = 'none';
            this.style.display = 'none';
            row.querySelector('td').insertBefore(wrapper, row.querySelector('td').firstChild);
            
            // Focus input
            input.focus();
            input.select();
            
            // Cancel handler
            cancelBtn.addEventListener('click', function() {
                wrapper.remove();
                displaySpan.style.display = 'inline-block';
                row.querySelector('.edit-btn').style.display = 'inline-block';
            });
            
            // Save handler
            saveBtn.addEventListener('click', function() {
                var newValue = input.value.trim();
                var originalValue = input.getAttribute('data-original-value');
                
                if (newValue === originalValue) {
                    // No change, just cancel
                    wrapper.remove();
                    displaySpan.style.display = 'inline-block';
                    row.querySelector('.edit-btn').style.display = 'inline-block';
                    return;
                }
                
                // Show loading
                saveBtn.disabled = true;
                cancelBtn.disabled = true;
                saveBtn.innerHTML = '<span class="loading-spinner"></span> Saving...';
                
                // Send AJAX request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/vehicles/' + vehicleId + '/update', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        saveBtn.disabled = false;
                        cancelBtn.disabled = false;
                        
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                // Update display
                                displaySpan.textContent = newValue || 'N/A';
                                displaySpan.setAttribute('data-value', newValue);
                                
                                wrapper.remove();
                                displaySpan.style.display = 'inline-block';
                                row.querySelector('.edit-btn').style.display = 'inline-block';
                                
                                // Show success message
                                if (typeof toastr !== 'undefined') {
                                    toastr.success('Field updated successfully!');
                                } else {
                                    alert('Field updated successfully!');
                                }
                            } else {
                                saveBtn.innerHTML = '<i class="fa fa-check"></i> Save';
                                alert('Error: ' + (response.message || 'Failed to update'));
                            }
                        } else {
                            saveBtn.innerHTML = '<i class="fa fa-check"></i> Save';
                            alert('Error: Failed to save changes');
                        }
                    }
                };
                
                xhr.send('field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(newValue));
            });
            
            // Enter key to save, Escape to cancel
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    saveBtn.click();
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    cancelBtn.click();
                }
            });
        });
    });
});
</script>
@endsection

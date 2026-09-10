@php
    $vehicle = $loan->vehicle;
@endphp
<div class="vehicle-detail-sheet" style="color: #333;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <h3 style="margin: 0; font-size: 20px;"><i class="fa fa-car"></i> Loan #{{ $loan->id }} — Vehicle Details</h3>
    </div>

    {{-- Photo Gallery --}}
    @if(!empty($vehicle) && $vehicle->photos->isNotEmpty())
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px;"><i class="fa fa-image"></i> Vehicle Photos</h4>
            <div style="display: flex; justify-content: center; align-items: center; min-height: 350px; position: relative; padding: 20px 60px; background: #f8f8f8; border-radius: 6px;">
                <img id="detailPhotoViewerImage"
                     src="{{ $vehicle->photos->first()->photo_url }}"
                     alt="Vehicle photo"
                     style="max-width: 100%; max-height: 350px; object-fit: contain; border-radius: 4px; opacity: 1;">

                @if($vehicle->photos->count() > 1)
                    <button type="button" class="btn btn-default btn-sm" id="detailPhotoPrev"
                            style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); opacity: 0.7;">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm" id="detailPhotoNext"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); opacity: 0.7;">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            @if($vehicle->photos->count() > 1)
                <div id="detailPhotoThumbs"
                     style="display: flex; justify-content: center; gap: 6px; margin-top: 10px; overflow-x: auto; padding: 6px 0;">
                    @foreach($vehicle->photos as $photo)
                        <img src="{{ $photo->photo_url }}"
                             style="height: 40px; width: auto; object-fit: cover; border-radius: 3px; cursor: pointer; opacity: 0.6;"
                             onclick="updateDetailPhoto(this)">
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Loan Information --}}
    <div style="margin-bottom: 25px;">
        <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #00a04a;"><i class="fa fa-file-text-o"></i> Loan Information</h4>
        <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
            <tr>
                <th style="width: 30%;">Loan ID</th>
                <td>{{ $loan->id }}</td>
            </tr>
            <tr>
                <th>Principal</th>
                <td>K{{ number_format($loan->principal, 2) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($loan->status == 'disbursed')
                        <span class="label label-success">Disbursed</span>
                    @elseif($loan->status == 'closed')
                        <span class="label label-danger">Closed</span>
                    @else
                        <span class="label label-warning">{{ ucfirst($loan->status) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Created Date</th>
                <td>{{ $loan->created_date }}</td>
            </tr>
            <tr>
                <th>Office / Branch</th>
                <td>{{ optional($loan->office)->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Client</th>
                <td>
                    {{ optional($loan->client)->first_name }} {{ optional($loan->client)->last_name }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Vehicle Information --}}
    @if(!empty($vehicle))
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #3c8dbc;"><i class="fa fa-car"></i> Vehicle Information</h4>
            <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                <tr>
                    <th style="width: 30%;">Vehicle Code</th>
                    <td>{{ $vehicle->vehicle_code }}</td>
                </tr>
                <tr>
                    <th>Make</th>
                    <td>{{ $vehicle->make }}</td>
                </tr>
                <tr>
                    <th>Model</th>
                    <td>{{ $vehicle->model }}</td>
                </tr>
                <tr>
                    <th>Year</th>
                    <td>{{ $vehicle->year }}</td>
                </tr>
                <tr>
                    <th>Color</th>
                    <td>{{ $vehicle->color }}</td>
                </tr>
                <tr>
                    <th>Registration Number</th>
                    <td>{{ $vehicle->registration_number }}</td>
                </tr>
                <tr>
                    <th>Engine Number</th>
                    <td>{{ $vehicle->engine_number }}</td>
                </tr>
                <tr>
                    <th>Chassis Number</th>
                    <td>{{ $vehicle->chassis_number }}</td>
                </tr>
                <tr>
                    <th>Mileage</th>
                    <td>{{ $vehicle->mileage }}</td>
                </tr>
                <tr>
                    <th>Fuel Type</th>
                    <td>{{ $vehicle->fuel_type }}</td>
                </tr>
                <tr>
                    <th>Transmission</th>
                    <td>{{ $vehicle->transmission }}</td>
                </tr>
                <tr>
                    <th>Market Value</th>
                    <td>K{{ number_format($vehicle->market_value, 2) }}</td>
                </tr>
                <tr>
                    <th>Ownership Type</th>
                    <td>{{ $vehicle->ownership_type }}</td>
                </tr>
            </table>
        </div>

        {{-- Insurance --}}
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #f39c12;"><i class="fa fa-shield-alt"></i> Insurance Policies</h4>
            @if($vehicle->insurancePolicies->isNotEmpty())
                <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Insurer</th>
                            <th>Policy Number</th>
                            <th>Start Date</th>
                            <th>Expiry Date</th>
                            <th>Insured Value</th>
                            <th>Cover Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle->insurancePolicies as $insurance)
                            <tr>
                                <td>{{ $insurance->insurer_name }}</td>
                                <td>{{ $insurance->policy_number }}</td>
                                <td>{{ $insurance->start_date }}</td>
                                <td>{{ $insurance->expiry_date }}</td>
                                <td>K{{ number_format($insurance->insured_value, 2) }}</td>
                                <td>{{ $insurance->cover_type }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted" style="font-size: 14px;">No insurance policies found.</p>
            @endif
        </div>

        {{-- Custody --}}
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #e74c3c;"><i class="fa fa-warehouse"></i> Vehicle Custody</h4>
            @if(!empty($vehicle->custody))
                <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                    <tr>
                        <th style="width: 30%;">Status</th>
                        <td>{{ ucfirst($vehicle->custody->status) }}</td>
                    </tr>
                    <tr>
                        <th>Received Date</th>
                        <td>{{ $vehicle->custody->received_at ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Received By</th>
                        <td>
                            {{ optional($vehicle->custody->receiver)->first_name }} {{ optional($vehicle->custody->receiver)->last_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>Keys Received</th>
                        <td>{{ $vehicle->custody->keys_received ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Key Tag Numbers</th>
                        <td>{{ $vehicle->custody->key_tag_numbers ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Garage / Storage Location</th>
                        <td>{{ $vehicle->custody->garage_location ?? $vehicle->custody->garage_name ?? 'N/A' }}</td>
                    </tr>
                    @if(!empty($vehicle->custody->garage_gps))
                        <tr>
                            <th>GPS Coordinates</th>
                            <td>
                                <a href="{{ $vehicle->custody->garage_gps }}" target="_blank" class="btn btn-primary btn-xs">
                                    <i class="fa fa-map-marker"></i> Open in Maps
                                </a>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Contact Person</th>
                        <td>{{ $vehicle->custody->garage_contact_person ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $vehicle->custody->garage_contact_phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td>{{ $vehicle->custody->remarks ?? 'N/A' }}</td>
                    </tr>
                </table>
            @else
                <p class="text-muted" style="font-size: 14px;">No custody records found.</p>
            @endif
        </div>

        {{-- Vehicle Intake & Condition Report --}}
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #00a04a;"><i class="fa fa-clipboard-check"></i> Vehicle Intake &amp; Condition Report</h4>
            <p style="font-size: 12px; color: #888; margin-bottom: 12px;">A vehicle inspection and condition report should be completed when the vehicle is received and again when it is released or sold.</p>

            @php
                $latestInspection = !empty($vehicle) && $vehicle->inspections ? $vehicle->inspections->sortByDesc('inspection_date')->first() : null;
                $custody = !empty($vehicle) ? $vehicle->custody : null;
                $intakePhotos = $custody && $custody->intake_photos ? (is_array($custody->intake_photos) ? $custody->intake_photos : json_decode($custody->intake_photos, true)) : [];
                $inspectionPhotos = $latestInspection && $latestInspection->inspection_photos ? (is_array($latestInspection->inspection_photos) ? $latestInspection->inspection_photos : json_decode($latestInspection->inspection_photos, true)) : [];
            @endphp

            <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                <tr>
                    <th style="width: 25%;">Intake Date</th>
                    <td>{{ $custody->intake_date ?? $custody->received_at ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Mileage</th>
                    <td>{{ $latestInspection->mileage ?? $vehicle->mileage ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Fuel Level</th>
                    <td>
                        @php
                            $fuelLevel = $custody->fuel_level ?? null;
                            if ($fuelLevel !== null) {
                                $fuelPct = is_numeric($fuelLevel) ? $fuelLevel : null;
                            } else {
                                $fuelPct = null;
                            }
                        @endphp
                        @if($fuelPct !== null)
                            {{ number_format($fuelLevel, 0) }}%
                            @if($fuelPct >= 25)
                                <span class="label label-success" style="margin-left: 8px;"><i class="fa fa-check"></i> Meets 1/4 tank minimum</span>
                            @else
                                <span class="label label-danger" style="margin-left: 8px;"><i class="fa fa-times"></i> Below 1/4 tank minimum</span>
                            @endif
                        @else
                            {{ $latestInspection->fuel_level ?? 'N/A' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Number of Keys</th>
                    <td>{{ $custody->keys_received ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Documents Received</th>
                    <td>{{ $custody->documents_received ? 'Yes' : ($custody->documents_received === false ? 'No' : 'N/A') }}</td>
                </tr>
                <tr>
                    <th>Accessories Received</th>
                    <td>{{ $custody->accessories_received ? 'Yes' : ($custody->accessories_received === false ? 'No' : 'N/A') }}</td>
                </tr>
                <tr>
                    <th>Visible Damages / Exterior Condition</th>
                    <td>{{ $latestInspection->exterior_condition ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Tyre Condition</th>
                    <td>{{ $latestInspection->tyres_condition ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Mechanical Condition</th>
                    <td>{{ $latestInspection->mechanical_condition ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Interior Condition</th>
                    <td>{{ $latestInspection->interior_condition ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Battery Condition</th>
                    <td>{{ $latestInspection->battery_condition ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Accessories Condition</th>
                    <td>{{ $latestInspection->accessories_condition ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>General Condition Rating</th>
                    <td>
                        @if(!empty($latestInspection))
                            {{ $latestInspection->condition_rating ?? $latestInspection->condition_score ?? 'N/A' }}
                            @if(!empty($latestInspection->condition_score))
                                <span class="text-muted" style="font-size: 12px;">(Score: {{ $latestInspection->condition_score }}/100)</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Overall Result</th>
                    <td>
                        @if(!empty($latestInspection))
                            <span class="label label-{{ $latestInspection->result == 'Passed' ? 'success' : ($latestInspection->result == 'Failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($latestInspection->result ?? 'N/A') }}
                            </span>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Condition Notes</th>
                    <td>{{ $latestInspection->condition_notes ?? $latestInspection->notes ?? 'N/A' }}</td>
                </tr>
            </table>

            {{-- Supporting Photographs --}}
            @if(!empty($intakePhotos) || !empty($inspectionPhotos))
                <div style="margin-top: 15px;">
                    <strong>Supporting Photographs</strong>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                        @foreach(array_slice($intakePhotos, 0, 8) as $photo)
                            <a href="{{ $photo }}" target="_blank">
                                <img src="{{ $photo }}" style="height: 50px; width: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            </a>
                        @endforeach
                        @foreach(array_slice($inspectionPhotos, 0, 8) as $photo)
                            <a href="{{ $photo }}" target="_blank">
                                <img src="{{ $photo }}" style="height: 50px; width: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Valuations --}}
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #9b59b6;"><i class="fa fa-tag"></i> Valuations</h4>
            @if($vehicle->valuations->isNotEmpty())
                <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Market Value</th>
                            <th>Forced Sale Value</th>
                            <th>Valuator</th>
                            <th>Valuation Company</th>
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle->valuations as $valuation)
                            <tr>
                                <td>{{ $valuation->valuation_date }}</td>
                                <td>K{{ number_format($valuation->market_value, 2) }}</td>
                                <td>K{{ number_format($valuation->forced_sale_value, 2) }}</td>
                                <td>{{ optional($valuation->valuator)->first_name }} {{ optional($valuation->valuator)->last_name }}</td>
                                <td>{{ $valuation->valuation_company ?? 'N/A' }}</td>
                                <td>
                                    @if(!empty($valuation->report_file_path))
                                        <a href="{{ $valuation->report_file_path }}" target="_blank" class="btn btn-xs btn-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted" style="font-size: 14px;">No valuations found.</p>
            @endif
        </div>

        {{-- Inspections --}}
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #e67e22;"><i class="fa fa-search"></i> Inspection History</h4>
            @if($vehicle->inspections->isNotEmpty())
                <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Inspector</th>
                            <th>Mileage</th>
                            <th>Result</th>
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle->inspections->sortByDesc('inspection_date') as $inspection)
                            <tr>
                                <td>{{ $inspection->inspection_date }}</td>
                                <td>{{ $inspection->inspector }}</td>
                                <td>{{ $inspection->mileage }}</td>
                                <td>{{ ucfirst($inspection->result) }}</td>
                                <td>
                                    @if(!empty($inspection->report_file_path))
                                        <a href="{{ $inspection->report_file_path }}" target="_blank" class="btn btn-xs btn-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted" style="font-size: 14px;">No inspections recorded.</p>
            @endif
        </div>

        {{-- Documents --}}
        <div style="margin-bottom: 25px;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #1abc9c;"><i class="fa fa-folder-open"></i> Documents</h4>
            @if($vehicle->documents->isNotEmpty())
                <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle->documents as $document)
                            <tr>
                                <td>{{ $document->document_type }}</td>
                                <td>
                                    <a href="{{ $document->document_file }}" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted" style="font-size: 14px;">No documents uploaded.</p>
            @endif
        </div>

        {{-- Ownership Records --}}
        @if($vehicle->ownershipRecords && $vehicle->ownershipRecords->isNotEmpty())
            <div style="margin-bottom: 25px;">
                <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #7f8c8d;"><i class="fa fa-id-card"></i> Ownership Records</h4>
                <table class="table table-bordered" style="margin-bottom: 0; font-size: 14px;">
                    <thead>
                        <tr>
                            <th>Verified At</th>
                            <th>Registered Owner</th>
                            <th>Seller NRC</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicle->ownershipRecords as $record)
                            <tr>
                                <td>{{ $record->verified_at ?? 'N/A' }}</td>
                                <td>{{ $record->registered_owner_name ?? 'N/A' }}</td>
                                <td>{{ $record->seller_nrc ?? 'N/A' }}</td>
                                <td>{{ ucfirst($record->ownership_type ?? 'N/A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

</div>

@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Details</h1>
</section>

<section class="content">

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-info-circle"></i> Vehicle Status
        </h3>

        @if($vehicle->status != 'sold')
            <button class="btn btn-danger pull-right"
                    data-toggle="modal"
                    data-target="#sellVehicleModal">
                <i class="fa fa-money"></i>
                Sell Car
            </button>
        @endif
    </div>

    <div class="box-body">

        <x-vehicle-timeline :currentStatus="$vehicle->status" />

        <hr>

        <div class="row">

            <div class="col-md-4">
                <strong>Status</strong><br>

                @if($vehicle->status=="sold")
                    <span class="label label-danger">
                        SOLD
                    </span>
                @elseif($vehicle->status=="in_custody" || $vehicle->custody)
                    <span class="label label-warning">
                        IN CUSTODY
                    </span>
                @else
                    <span class="label label-success">
                        AVAILABLE
                    </span>
                @endif
            </div>

            <div class="col-md-4">
                <strong>Sale Value</strong><br>

                @if($vehicle->forced_sale_value)
                    <span class="text-green">
                        K{{ number_format($vehicle->forced_sale_value,2) }}
                    </span>
                @else
                    -
                @endif
            </div>

            <div class="col-md-4">
                <strong>Date Sold</strong><br>

                {{ $vehicle->sold_at ?? '-' }}
            </div>

        </div>

    </div>
</div>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_vehicle_info" data-toggle="tab" aria-expanded="true">Vehicle Information</a></li>
        <li class=""><a href="#tab_insurance" data-toggle="tab" aria-expanded="false">Insurance</a></li>
        <li class=""><a href="#tab_custody" data-toggle="tab" aria-expanded="false">Vehicle Custody</a></li>
        <li class=""><a href="#tab_garage" data-toggle="tab" aria-expanded="false">Garage / Storage Facility</a></li>
        <li class=""><a href="#tab_documents" data-toggle="tab" aria-expanded="false">Documents</a></li>
        <li class=""><a href="#tab_photos" data-toggle="tab" aria-expanded="false">Vehicle Photos</a></li>
        <li class=""><a href="#tab_inspections" data-toggle="tab" aria-expanded="false">Inspection History</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_vehicle_info">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-car"></i> Vehicle Information
                    </h3>

                    <a
                        href="{{ url('vehicles/'.$vehicle->id.'/edit') }}"
                        class="btn btn-primary btn-xs pull-right"
                    >

                    Add Information

                    </a>
                </div>

                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tr>
                            <th width="35%">Vehicle Code</th>
                            <td>{{ $vehicle->vehicle_code }}</td>

                            <th width="20%">Registration</th>
                            <td>
                                <span class="label label-primary">
                                    {{ $vehicle->registration_number }}
                                </span>
                            </td>
                        </tr>

                        
                        <tr>
                            <th>Make</th>
                            <td >
                                    {{ $vehicle->make }}
                            </td>

                            <th>Model</th>
                            <td>
                                    {{ $vehicle->model }}
                            </td>

                        </tr>

                        <tr>
                            <th>Owner</th>
                            <td>
                                {{ optional($vehicle->client)->first_name }}
                                {{ optional($vehicle->client)->last_name }}
                            </td>

                            <th>Market Value</th>
                            <td>
                                <strong class="text-green">
                                    K{{ number_format($vehicle->market_value,2) }}
                                </strong>
                            </td>
                        </tr>


                        
                        <tr>
                            <th>Year</th>
                            <td >
                                    {{ $vehicle->year }}
                            </td>

                            <th>Color</th>
                            <td>
                                    {{ $vehicle->color }}
                            </td>

                        </tr>

                        <tr>
                            <th>Engine Number</th>
                            <td >
                                    {{ $vehicle->engine_number }}
                            </td>

                            <th>Chassis Number</th>
                            <td>
                                    {{ $vehicle->engine_number }}
                            </td>

                        </tr>

                           <tr>
                            <th>Insurance Policy #</th>
                            <td >
                                    {{ $vehicle->insurance_policy_number }}
                            </td>

                            <th>Mileage</th>
                            <td>
                                    {{ $vehicle->mileage }}
                            </td>

                        </tr>

                    

                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="tab_insurance">
            <div class="box box-success">

            <div class="box-header">

            <h3 class="box-title">
            Insurance
            </h3>

            <a
                href="{{ url('vehicles/'.$vehicle->id.'/insurance/create') }}"
                class="btn btn-success btn-xs pull-right"
            >

            Add Insurance

            </a>

            </div>

            <table class="table table-hover">

            <thead>
            <tr>
                <th>Insurer</th>
                <th>Value</th>
                <th>Policy Number</th>
                <th>Expiry</th>
            </tr>
            </thead>

            <tbody>

            @forelse($vehicle->insurancePolicies as $insurance)

            <tr>
                <td>{{ $insurance->insurer_name }}</td>
                <td>{{ $insurance->insured_value }}</td>
                <td>{{ $insurance->policy_number }}</td>
                <td>
                    <span class="label label-success">
                        {{ $insurance->expiry_date }}
                    </span>
                </td>
            </tr>

            @empty

            <tr>
                <td colspan="2" class="text-center text-muted">
                    No insurance found
                </td>
            </tr>

            @endforelse

            </tbody>

            </table>

            </div>
        </div>

        <div class="tab-pane" id="tab_custody">
            <div class="box box-danger">

            <div class="box-header with-border">

            <h3 class="box-title">

            Vehicle Custody

            </h3>

            <a
            href="{{ url('vehicles/'.$vehicle->id.'/custody/create') }}"
            class="btn btn-danger btn-xs pull-right">

            Receive Vehicle

            </a>

            </div>

            @if($vehicle->custody)
                <table class="table table-bordered">

                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="label label-success">
                                {{ ucfirst($vehicle->custody->status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Received</th>
                        <td>{{ $vehicle->custody->received_at }}</td>
                    </tr>

                    <tr>
                        <th>Received By</th>
                        <td>
                            {{ optional($vehicle->custody->receiver)->first_name }}
                            {{ optional($vehicle->custody->receiver)->last_name }}
                        </td>
                    </tr>

                    <tr>
                        <th>Key Received</th>
                        <td>{{ $vehicle->custody->keys_received }}</td>
                    </tr>

                    <tr>
                        <th>Key Tag Numbers</th>
                        <td>{{ $vehicle->custody->key_tag_numbers }}</td>
                    </tr>

                    <tr>
                        <th>Remarks</th>
                        <td>{{ $vehicle->custody->remarks }}</td>
                    </tr>

                </table>
            @endif

            <!-- <a class="btn btn-primary btn-block" href="{{ url('vehicles/'.$vehicle->id.'/custody') }}">
                <i class="fa fa-eye"></i>
                View Custody
            </a> -->

            </div>
        </div>

        <div class="tab-pane" id="tab_garage">
            <div class="box box-info">

            <div class="box-header with-border">

            <h3 class="box-title">

            Garage / Storage Facility

            </h3>

            </div>

            @if($vehicle->custody)

            <div class="box-body">

            @if($vehicle->custody->garage_name)

            <p>

            <strong>Garage Name</strong><br>

            {{ $vehicle->custody->garage_name }}

            </p>

            <p>

            <strong>Location</strong><br>

            {{ $vehicle->custody->garage_location }}

            </p>

            <p>
                <strong>GPS Coordinates</strong><br>

                @if($vehicle->custody->garage_gps)
                    <a href="{{ $vehicle->custody->garage_gps }}"
                       target="_blank"
                       class="btn btn-primary btn-sm">
                        <i class="fa fa-map-marker"></i>
                        Open in Google Maps
                    </a>
                @else
                    <span class="text-muted">Not available</span>
                @endif
            </p>

            <p>

            <strong>Contact Person</strong><br>

            {{ $vehicle->custody->garage_contact_person }}

            </p>

            <p>

            <strong>Phone</strong><br>

            {{ $vehicle->custody->garage_contact_phone }}

            </p>

            @else

            <div class="alert alert-info">

            No garage assigned.

            </div>

            @endif

            </div>

            @endif


            </div>
        </div>

        <div class="tab-pane" id="tab_documents">
            <div class="box">

            <div class="box-header">

            <h3 class="box-title">
            Documents
            </h3>

            <a
                href="{{ url('vehicles/'.$vehicle->id.'/documents/create') }}"
                class="btn btn-primary btn-xs pull-right">

            Upload Document

            </a>

            </div>

            <div class="box-body">

            <table class="table table-bordered">

            <thead>

            <tr>

            <th>Type</th>
            <th>File</th>

            </tr>

            </thead>

            <tbody>

            @forelse($vehicle->documents as $document)

            <tr>

            <td>
            {{ $document->document_type }}
            </td>

            <td>

            <a href="{{ $document->document_file }}"
               target="_blank"
               class="btn btn-xs btn-primary">

                View

            </a>

            <a href="{{ $document->document_file }}"
               target="_blank"
               class="btn btn-xs btn-success">

                Download

            </a>

            </td>

            </tr>

            @empty

            <tr>

            <td colspan="2">

            No documents uploaded.

            </td>

            </tr>

            @endforelse

            </tbody>

            </table>

            </div>

            </div>
        </div>

        <div class="tab-pane" id="tab_photos">
            <div class="box">

            <div class="box-header with-border">

            <h3 class="box-title">

            Vehicle Photos

            </h3>

            <button
                type="button"
                class="btn btn-primary btn-xs pull-right"
                id="openUploadPhotoSheet">

            Upload Photos

            </button>

            </div>

            <div class="box-body">

            <div class="row">

            @forelse(optional($vehicle)->photos as $photo)

            <div class="col-md-3">

            <div class="thumbnail" style="position: relative;">

                <img
                    src="{{ $photo->photo_url }}"
                    class="vehicle-photo-thumb img-responsive"
                    data-photos='@json(optional($vehicle)->photos->pluck("photo_url"))'
                    style="cursor: pointer;"
                    onclick="openPhotoBottomSheet(this)">

                <form method="POST"
                      action="{{ url('vehicles/'.$vehicle->id.'/photos/'.$photo->id.'/destroy') }}"
                      style="position: absolute; top: 5px; right: 5px;"
                      onsubmit="return confirm('Delete this photo?');">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-xs btn-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>

            <div class="caption">

            <strong>

            {{ $photo->photo_type }}

            </strong>

            <br>

            {{ $photo->caption }}

            </div>

            </div>

            </div>

            @empty

            <div class="col-md-12">

            <div class="alert alert-warning">

            No photos uploaded.

            </div>

            </div>

            @endforelse

            </div>

            </div>

            </div>
        </div>

        <div class="tab-pane" id="tab_inspections">
            <div class="box box-warning">

            <div class="box-header">

            <h3 class="box-title">

            Inspection History

            </h3>

            <a
                href="{{ url('vehicles/'.$vehicle->id.'/inspections/create') }}"
                class="btn btn-warning btn-xs pull-right">

            Add Inspection

            </a>

            </div>

            <div class="box-body">

            <table class="table table-bordered">

            <thead>

            <tr>

            <th>Date</th>
            <th>Inspector</th>
            <th>Mileage</th>
            <th>Fuel Level</th>
            <th>Result</th>
            <th>Report</th>

            </tr>

            </thead>

            <tbody>

            @forelse($vehicle->inspections as $inspection)

            <tr>

            <td>
            {{ $inspection->inspection_date }}
            </td>

            <td>
            {{ $inspection->inspector }}
            </td>

            <td>
            {{ $inspection->mileage }}
            </td>

            <td>
            {{ $inspection->fuel }}
            </td>

            <td>
            {{ ucfirst($inspection->result) }}
            </td>

            <td>
                @if($inspection->report_url)

                    <a
                        href="{{ $inspection->report_url }}"
                        target="_blank"
                        class="btn btn-xs btn-primary">

                        Report

                    </a>

                @endif
            </td>

            </tr>

            @empty

            <tr>

            <td colspan="3">

            No inspections recorded.

            </td>

            </tr>

            @endforelse

            </tbody>

            </table>

            </div>

            </div>
        </div>
    </div>
</div>

</section>


<div class="modal fade" id="sellVehicleModal">

    <div class="modal-dialog">

        <form method="POST"
              action="{{ url('vehicles/'.$vehicle->id.'/sell') }}">

            {{ csrf_field() }}

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        &times;

                    </button>

                    <h4 class="modal-title">
                        Sell Vehicle
                    </h4>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>Sale Value</label>

                        <input
                            type="number"
                            step="0.01"
                            name="sale_value"
                            class="form-control"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Buyer Full Name</label>

                        <input
                            type="text"
                            name="buyer_fullname"
                            class="form-control"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Buyer Phone</label>

                        <input
                            type="text"
                            name="buyer_phone"
                            class="form-control"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Buyer NRC Number</label>

                        <input
                            type="text"
                            name="buyer_nrc_number"
                            class="form-control"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Sex</label>

                        <select name="buyer_sex" class="form-control" required>

                            <option value="">Select Sex</option>

                            <option value="Male">Male</option>

                            <option value="Female">Female</option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Location</label>

                        <input
                            type="text"
                            name="buyer_location"
                            class="form-control"
                            required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger">

                        Sell Car

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- Upload Photo Bottom Sheet -->
<div class="bottom-sheet-overlay" id="uploadPhotoBottomSheetOverlay">
    <div class="bottom-sheet" id="uploadPhotoBottomSheet" style="max-height: 90vh;">
        <button class="bottom-sheet-close" id="closeUploadPhotoBottomSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content" style="padding: 20px;">
            <h3 class="bottom-sheet-title" style="margin-left: 40%; font-size: 20px; margin-bottom: 10px;">
                <i class="fa fa-camera"></i> Upload Vehicle Photo
            </h3>

            <div class="alert alert-info" style="margin-bottom: 20px;">
                <strong>{{ $vehicle->make }} {{ $vehicle->model }}</strong> ({{ $vehicle->registration_number }})
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ url('vehicles/'.$vehicle->id.'/photos/store') }}" style="max-width: 600px; margin: 0 auto;">
                @csrf

                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 8px;">Photo Type</label>
                    <select name="photo_type" class="form-control" required style="border-radius: 8px;">
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
                    <label style="font-weight: 600; margin-bottom: 8px;">Caption</label>
                    <input type="text" name="caption" class="form-control" style="border-radius: 8px;">
                </div>

                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 8px;">Select Photo</label>
                    <input type="file" name="photo" class="form-control" required style="border-radius: 8px;">
                </div>

                <div class="text-center" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; padding: 10px 30px; margin-right: 10px;">
                        <i class="fa fa-upload"></i> Upload Photo
                    </button>
                    <button type="button" id="cancelUploadPhoto" class="btn btn-default" style="border-radius: 25px; padding: 10px 30px;">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const overlay = document.getElementById('uploadPhotoBottomSheetOverlay');
    const sheet = document.getElementById('uploadPhotoBottomSheet');
    const openBtn = document.getElementById('openUploadPhotoSheet');
    const closeBtn = document.getElementById('closeUploadPhotoBottomSheet');
    const cancelBtn = document.getElementById('cancelUploadPhoto');

    function openSheet() {
        overlay.classList.add('active');
        sheet.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSheet() {
        overlay.classList.remove('active');
        sheet.classList.remove('active');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', openSheet);
    closeBtn.addEventListener('click', closeSheet);
    cancelBtn.addEventListener('click', closeSheet);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeSheet();
    });
})();
</script>

<div class="bottom-sheet-overlay" id="vehiclePhotoViewerOverlay">
    <div class="bottom-sheet" id="vehiclePhotoViewerSheet" style="background: #111; border-radius: 0; max-height: 90vh;">
        <div class="bottom-sheet-content" style="padding: 0; position: relative;">
            <button type="button" class="bottom-sheet-close" id="closeVehiclePhotoViewer" style="color: #fff;">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="bottom-sheet-handle" style="background: #333;"></div>

            <div style="display: flex; justify-content: center; align-items: center; min-height: 60vh; position: relative; padding: 20px 60px 80px 60px;">
                <img id="vehiclePhotoViewerImage"
                     src=""
                     alt="Vehicle photo"
                     style="max-width: 100%; max-height: 70vh; object-fit: contain; border-radius: 8px; opacity: 0; transition: opacity 0.25s ease;">

                <button type="button" class="btn btn-default btn-lg" id="vehiclePhotoViewerPrev"
                        style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.8; background: rgba(255,255,255,0.2); border: none; color: #fff;">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-default btn-lg" id="vehiclePhotoViewerNext"
                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); opacity: 0.8; background: rgba(255,255,255,0.2); border: none; color: #fff;">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>

            <div id="vehiclePhotoViewerThumbs"
                 style="display: flex; justify-content: center; gap: 8px; padding: 12px 0 20px 0; overflow-x: auto; background: #1a1a1a; border-top: 1px solid #333;">
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const overlay = document.getElementById('vehiclePhotoViewerOverlay');
    const sheet = document.getElementById('vehiclePhotoViewerSheet');
    const modalImg = document.getElementById('vehiclePhotoViewerImage');
    const prevBtn = document.getElementById('vehiclePhotoViewerPrev');
    const nextBtn = document.getElementById('vehiclePhotoViewerNext');
    const thumbsContainer = document.getElementById('vehiclePhotoViewerThumbs');
    const closeBtn = document.getElementById('closeVehiclePhotoViewer');

    let photos = [];
    let currentIndex = 0;

    function renderThumbs() {
        thumbsContainer.innerHTML = '';
        photos.forEach((url, idx) => {
            const thumb = document.createElement('img');
            thumb.src = url;
            thumb.style.height = '50px';
            thumb.style.width = 'auto';
            thumb.style.objectFit = 'cover';
            thumb.style.borderRadius = '4px';
            thumb.style.cursor = 'pointer';
            thumb.style.opacity = idx === currentIndex ? '1' : '0.5';
            thumb.style.transition = 'opacity 0.2s';
            thumb.onclick = () => updateImage(idx);
            thumbsContainer.appendChild(thumb);
        });
    }

    function updateImage(index) {
        if (!photos.length) return;
        currentIndex = (index + photos.length) % photos.length;
        modalImg.style.opacity = '0';
        setTimeout(() => {
            modalImg.src = photos[currentIndex];
            modalImg.onload = () => { modalImg.style.opacity = '1'; };
            renderThumbs();
        }, 250);
    }

    function openViewer(img) {
        try {
            photos = JSON.parse(img.getAttribute('data-photos') || '[]');
        } catch (e) {
            photos = [];
        }
        if (!photos.length) return;

        const clickedUrl = img.getAttribute('src');
        currentIndex = photos.indexOf(clickedUrl);
        if (currentIndex < 0) currentIndex = 0;

        overlay.classList.add('active');
        sheet.classList.add('active');
        document.body.style.overflow = 'hidden';
        updateImage(currentIndex);
    }

    function closeViewer() {
        overlay.classList.remove('active');
        sheet.classList.remove('active');
        document.body.style.overflow = '';
    }

    window.openPhotoBottomSheet = function(img) {
        openViewer(img);
    };

    closeBtn.addEventListener('click', closeViewer);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeViewer();
    });
    prevBtn.addEventListener('click', () => updateImage(currentIndex - 1));
    nextBtn.addEventListener('click', () => updateImage(currentIndex + 1));
})();
</script>

@endsection

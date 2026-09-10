@extends('layouts.master')
@section('title')
    Motor Vehicle Loans
@endsection

@section('content')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Motor Vehicle Loans Summary</h3>
            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('loans.create'))
                    <a href="{{ url('loan/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom: 20px;">

                <div class="col-md-2 col-sm-6">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>Total MVL Loans</p>
                            <p style="font-size: 14px; margin-bottom: 0;">K{{ number_format($stats['total_amount'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-folder-open-o"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3>{{ $stats['pending'] }}</h3>
                            <p>Pending</p>
                            <p style="font-size: 14px; margin-bottom: 0;">K{{ number_format($stats['pending_amount'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{ $stats['approved'] }}</h3>
                            <p>Approved</p>
                            <p style="font-size: 14px; margin-bottom: 0;">K{{ number_format($stats['approved_amount'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check-circle-o"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $stats['disbursed'] }}</h3>
                            <p>Disbursed</p>
                            <p style="font-size: 14px; margin-bottom: 0;">K{{ number_format($stats['disbursed_amount'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-send"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{ $stats['closed'] }}</h3>
                            <p>Closed</p>
                            <p style="font-size: 14px; margin-bottom: 0;">Collected: K{{ number_format($stats['total_collected'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-lock"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">

            <div class="box box-success">

                <div class="box-body table-responsive">

                <div class="box-body">

<form method="GET">

<div class="row">

<div class="col-md-3">
    <input type="text"
           class="form-control"
           name="search"
           placeholder="Search Loan ID or Client"
           value="{{ request('search') }}">
</div>

<div class="col-md-2">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>

        <option value="pending"
            {{ request('status')=='pending' ? 'selected' : '' }}>
            Pending
        </option>

        <option value="approved"
            {{ request('status')=='approved' ? 'selected' : '' }}>
            Approved
        </option>

        <option value="disbursed"
            {{ request('status')=='disbursed' ? 'selected' : '' }}>
            Disbursed
        </option>

        <option value="closed"
            {{ request('status')=='closed' ? 'selected' : '' }}>
            Closed
        </option>

    </select>
</div>

<div class="col-md-2">
    <input type="date"
           class="form-control"
           name="date"
           value="{{ request('date') }}">
</div>

<div class="col-md-3">

    <select name="office" class="form-control">

        <option value="">All Branches</option>

        @foreach($offices as $office)

            <option value="{{ $office->id }}"
                {{ request('office')==$office->id ? 'selected' : '' }}>
                {{ $office->name }}
            </option>

        @endforeach

    </select>

</div>

<div class="col-md-2">

    <button class="btn btn-success">
        <i class="fa fa-search"></i> Search
    </button>

    <a href="{{ url()->current() }}"
       class="btn btn-default">
       Reset
    </a>

</div>

</div>

</form>

</div>

                    <table class="table table-bordered table-striped">

                        <thead>

                         <tr>

                             <th>ID</th>
                             <th>Image</th>
                             <th>Client</th>
                             <th>Office</th>
                             <th>Principal</th>
                             <th>Created Date</th>
                             <th>Status</th>
                             <th>Received By</th>
                             <th>Inspector</th>
                             <th>Valuator</th>
                             <th>Custodian</th>
                             <th>Location</th>
                             <th>Onboarding Progress</th>
                             <th>Action</th>

                         </tr>

                        </thead>

                        <tbody>

                            @forelse($recentLoans as $loan)

                            <tr>

                                <td>
                               <a href="{{ url('loan/'.$loan->id.'/show') }}">
                                                {{$loan->id}}</a>
                                </td>

                                <td>
                                     @if(!empty($loan->vehicle) && $loan->vehicle->photos->isNotEmpty())
                                     <img id="vehicle-thumb-{{ $loan->id }}"
                                             src="{{ $loan->vehicle->photos->first()->photo_url }}"
                                             class="vehicle-photo-thumb"
                                             data-photos='@json($loan->vehicle->photos->pluck("photo_url"))'
                                             onclick="openLoanDetailSheet({{ $loan->id }})"
                                             style="height: 50px; width: auto; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                             alt="Vehicle photo">
                                     @else
                                         <img id="vehicle-thumb-{{ $loan->id }}"
                                             src="https://www.allthingsmotoringinternational.com/images/profile/230924/fairdrive-logo.jfif"
                                             class="vehicle-photo-thumb"
                                             data-photos='["https://www.allthingsmotoringinternational.com/images/profile/230924/fairdrive-logo.jfif"]'
                                             onclick="openLoanDetailSheet({{ $loan->id }})"
                                             style="height: 50px; width: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                             alt="No photo">
                                     @endif
                                 </td>

                               

                                <td>
                                    {{ optional($loan->client)->first_name }}
                                    {{ optional($loan->client)->last_name }}
                                </td>

                                    <td>
                                    {{ optional($loan->office)->name }}
                                </td>


                                <td>
                                    K{{ number_format($loan->principal,2) }}
                                </td>

                                   <td>
                                    {{$loan->created_date}}
                                </td>


                                <td>

                                    @if($loan->status == 'disbursed')
                                        <span class="label label-success">
                                            Disbursed
                                        </span>
                                    @elseif($loan->status == 'closed')
                                        <span class="label label-danger">
                                            Closed
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    @endif

                                </td>

                                <td>
                                    @if(!empty($loan->vehicle) && !empty($loan->vehicle->custody) && !empty($loan->vehicle->custody->receiver))
                                        {{ $loan->vehicle->custody->receiver->first_name }} {{ $loan->vehicle->custody->receiver->last_name }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $latestInspection = null;
                                        if (!empty($loan->vehicle) && $loan->vehicle->relationLoaded('inspections')) {
                                            $latestInspection = $loan->vehicle->inspections->sortByDesc('inspection_date')->first();
                                        }
                                    @endphp
                                    @if(!empty($latestInspection))
                                        {{ $latestInspection->inspector }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $latestValuation = null;
                                        if (!empty($loan->vehicle) && $loan->vehicle->relationLoaded('valuations')) {
                                            $latestValuation = $loan->vehicle->valuations->sortByDesc('valuation_date')->first();
                                        }
                                    @endphp
                                    @if(!empty($latestValuation) && !empty($latestValuation->valuator))
                                        {{ $latestValuation->valuator->first_name }} {{ $latestValuation->valuator->last_name }}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($loan->vehicle) && !empty($loan->vehicle->custody) && !empty($loan->vehicle->custody->receiver))
                                        {{ $loan->vehicle->custody->receiver->first_name }} {{ $loan->vehicle->custody->receiver->last_name }}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($loan->vehicle) && !empty($loan->vehicle->custody))
                                        {{ $loan->vehicle->custody->garage_location ?? $loan->vehicle->custody->garage_name ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>
                                    <x-onboarding-progress :status="$statuses[$loan->id] ?? ['kyc_completed' => null, 'compliance_screening_completed' => null, 'ownership_completed' => null]" :loan="$loan" />
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-xs" onclick="openLoanDetailSheet({{ $loan->id }})">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                </td>

                            </tr>

                            @empty

                                <tr>

                                    <td colspan="14" class="text-center">
                                        No vehicle loans found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>
</section>

{{-- Vehicle Detail Bottom Sheet --}}
<div class="bottom-sheet-overlay" id="vehicleDetailOverlay">
    <div class="bottom-sheet" id="vehicleDetailSheet" style="background: #fff; border-radius: 0 0 0 0; max-height: 95vh; max-width: 100vw;">
        <div class="bottom-sheet-content" style="padding: 0; position: relative;">
            <button type="button" class="bottom-sheet-close" id="closeVehicleDetailSheet" style="color: #333;">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="bottom-sheet-handle"></div>

            <div id="vehicleDetailContent" style="max-height: 85vh; overflow-y: auto; padding: 20px 30px 40px 30px;">
                <div class="text-center" style="padding: 40px;">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted" style="margin-top: 10px;">Loading vehicle details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const overlay = document.getElementById('vehicleDetailOverlay');
    if (!overlay) return;
    const sheet = document.getElementById('vehicleDetailSheet');
    const contentEl = document.getElementById('vehicleDetailContent');
    const closeBtn = document.getElementById('closeVehicleDetailSheet');

    function openSheet(loanId) {
        contentEl.innerHTML = '<div class="text-center" style="padding: 40px;"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i><p class="text-muted" style="margin-top: 10px;">Loading vehicle details...</p></div>';
        overlay.classList.add('active');
        sheet.classList.add('active');
        document.body.style.overflow = 'hidden';

        $.ajax({
            url: '/vehicles/loan-detail-sheet/' + loanId,
            method: 'GET',
            success: function(html) {
                contentEl.innerHTML = html;
                if (window.initDetailPhotoGallery) window.initDetailPhotoGallery();
            },
            error: function() {
                contentEl.innerHTML = '<div class="text-center" style="padding: 40px;"><p class="text-danger">Failed to load vehicle details.</p></div>';
            }
        });
    }

    function closeSheet() {
        overlay.classList.remove('active');
        sheet.classList.remove('active');
        document.body.style.overflow = '';
    }

    window.openLoanDetailSheet = function(loanId) {
        openSheet(loanId);
    };

    closeBtn.addEventListener('click', closeSheet);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeSheet();
    });
})();

window.updateDetailPhoto = function(img) {
    var photoImg = document.getElementById('detailPhotoViewerImage');
    var container = document.getElementById('detailPhotoThumbs');
    if (!photoImg || !container) return;
    var photos = Array.from(container.querySelectorAll('img')).map(function(i) { return i.src; });
    if (!photos.length) return;
    var idx = photos.indexOf(img.src);
    if (idx < 0) idx = 0;
    photoImg.src = photos[idx];
    attachGalleryNav();
};

window.initDetailPhotoGallery = function() {
    var photoImg = document.getElementById('detailPhotoViewerImage');
    var prevBtn = document.getElementById('detailPhotoPrev');
    var nextBtn = document.getElementById('detailPhotoNext');
    if (!photoImg) return;
    var container = document.getElementById('detailPhotoThumbs');
    if (!container) return;
    var photos = Array.from(container.querySelectorAll('img')).map(function(i) { return i.src; });
    if (!photos.length) return;
    var currentIndex = photos.indexOf(photoImg.src);
    if (currentIndex < 0) currentIndex = 0;
    if (prevBtn) prevBtn.onclick = function() {
        currentIndex = (currentIndex - 1 + photos.length) % photos.length;
        photoImg.src = photos[currentIndex];
    };
    if (nextBtn) nextBtn.onclick = function() {
        currentIndex = (currentIndex + 1) % photos.length;
        photoImg.src = photos[currentIndex];
    };
};

function attachGalleryNav() {
    var photoImg = document.getElementById('detailPhotoViewerImage');
    var prevBtn = document.getElementById('detailPhotoPrev');
    var nextBtn = document.getElementById('detailPhotoNext');
    if (!photoImg) return;
    var container = document.getElementById('detailPhotoThumbs');
    if (!container) return;
    var photos = Array.from(container.querySelectorAll('img')).map(function(i) { return i.src; });
    if (!photos.length) return;
    var currentIndex = photos.indexOf(photoImg.src);
    if (currentIndex < 0) currentIndex = 0;
    if (prevBtn) prevBtn.onclick = function() {
        currentIndex = (currentIndex - 1 + photos.length) % photos.length;
        photoImg.src = photos[currentIndex];
    };
    if (nextBtn) nextBtn.onclick = function() {
        currentIndex = (currentIndex + 1) % photos.length;
        photoImg.src = photos[currentIndex];
    };
}
</script>

@endsection

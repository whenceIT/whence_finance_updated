@extends('layouts.master')
@section('title')
    Motor Vehicle Loans
@endsection

@section('content')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Motor Vehicle Loans Summary</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info" style="margin-bottom: 20px;">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Total MVL Loans:</strong><br>
                                <span class="badge bg-blue">{{ $recentLoans->total() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Total Amount:</strong><br>
                                K{{ number_format($recentLoans->sum('principal'), 2) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Pending:</strong><br>
                                <span class="badge bg-yellow">{{ $recentLoans->where('status', 'pending')->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Approved:</strong><br>
                                <span class="badge bg-green">{{ $recentLoans->where('status', 'approved')->count() }}</span> loans
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">

            <div class="box box-success">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        Motor Vehicle Loans
                    </h3>
                </div>

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
                                        <img src="{{ $loan->vehicle->photos->first()->photo_url }}"
                                             class="vehicle-photo-thumb"
                                             data-photos='@json($loan->vehicle->photos->pluck("photo_url"))'
                                             style="height: 50px; width: auto; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                             alt="Vehicle photo">
                                    @else
                                        <img src="https://www.allthingsmotoringinternational.com/images/profile/230924/fairdrive-logo.jfif"
                                             class="vehicle-photo-thumb"
                                             data-photos='["https://www.allthingsmotoringinternational.com/images/profile/230924/fairdrive-logo.jfif"]'
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

                            </tr>

                            @empty

                                <tr>

                                    <td colspan="13" class="text-center">
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

<script>
(function() {
    const modal = document.getElementById('vehiclePhotoModal');
    if (!modal) return;
    const modalImg = document.getElementById('vehicleModalImage');
    const prevBtn = document.getElementById('vehicleModalPrev');
    const nextBtn = document.getElementById('vehicleModalNext');
    const thumbsContainer = document.getElementById('vehicleModalThumbs');
    let photos = [];
    let currentIndex = 0;

    function updateImage(index) {
        if (!photos.length) return;
        currentIndex = (index + photos.length) % photos.length;
        modalImg.style.transition = 'opacity 0.25s ease';
        modalImg.style.opacity = '0';
        setTimeout(() => {
            modalImg.src = photos[currentIndex];
            modalImg.onload = () => {
                modalImg.style.opacity = '1';
            };
        }, 250);
        updateThumbs();
    }

    function updateThumbs() {
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

    if (prevBtn) prevBtn.onclick = () => updateImage(currentIndex - 1);
    if (nextBtn) nextBtn.onclick = () => updateImage(currentIndex + 1);

    document.querySelectorAll('.vehicle-photo-thumb').forEach(img => {
        img.addEventListener('click', function() {
            try {
                photos = JSON.parse(this.getAttribute('data-photos') || '[]');
            } catch (e) {
                photos = [];
            }
            if (!photos.length) return;
            currentIndex = 0;
            updateImage(0);
            $(modal).modal('show');
        });
    });
})();
</script>

<div class="modal fade" id="vehiclePhotoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: auto; max-width: 90%;">
        <div class="modal-content" style="background: transparent; box-shadow: none; border: none;">
            <div class="modal-body" style="padding: 0; position: relative;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: -30px; right: 0; color: #fff; font-size: 30px; z-index: 10;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <img id="vehicleModalImage" src="" alt="Vehicle photo" style="width: 100%; max-height: 75vh; object-fit: contain; display: block; margin: 0 auto; border-radius: 8px;">
                <button type="button" class="btn btn-default btn-lg" id="vehicleModalPrev" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); opacity: 0.8;">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-default btn-lg" id="vehicleModalNext" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); opacity: 0.8;">
                    <i class="fa fa-chevron-right"></i>
                </button>
                <div id="vehicleModalThumbs" style="display: flex; justify-content: center; gap: 8px; margin-top: 12px; overflow-x: auto; padding: 8px 0;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

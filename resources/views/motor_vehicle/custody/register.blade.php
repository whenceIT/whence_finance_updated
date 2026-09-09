@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Vehicle Custody Register</h1>
</section>

<section class="content">

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Custody Summary</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Total Records:</strong><br>
                <span class="badge bg-blue">{{ $custodies->total() }}</span>
            </div>
            <div class="col-md-3">
                <strong>Approved:</strong><br>
                <span class="badge bg-green">{{ $totalApproved ?? 0 }}</span>
            </div>
            <div class="col-md-3">
                <strong>Pending Approval:</strong><br>
                <span class="badge bg-orange">{{ $totalPending ?? 0 }}</span>
            </div>
            <div class="col-md-3">
                <strong>Current Page:</strong><br>
                <span class="badge bg-purple">{{ $custodies->currentPage() }} / {{ $custodies->lastPage() }}</span>
            </div>
        </div>
    </div>
</div>

<div class="box">

    <div class="box-header">
        <h3 class="box-title">Custody Records</h3>
    </div>

    <div class="box-body">

        <table class="table table-bordered table-striped" id="custody-table">

            <thead>
            <tr>
                <th>Vehicle</th>
                <th>Image</th>
                <th>Loan Consultant</th>
                <th>Received By</th>
                <th>Inspector</th>
                <th>Valuator</th>
                <th>Custodian</th>
                <th>Location</th>
                <th>Storage Start Date</th>
                <th>Approved</th>
                <th>Onboarding Progress</th>
            </tr>
            </thead>

            <tbody>

            @forelse($custodies as $custody)
            <tr>
                <td>
                    @if($custody->vehicle)
                        <a href="{{ url('vehicles/'.$custody->vehicle->id) }}">{{ $custody->vehicle->registration_number }}</a>
                    @else
                        {{ __('N/A') }}
                    @endif
                </td>
                <td>
                    @if(!empty($custody->vehicle) && $custody->vehicle->photos->isNotEmpty())
                        <img src="{{ $custody->vehicle->photos->first()->photo_url }}"
                             class="vehicle-photo-thumb"
                             data-photos='@json($custody->vehicle->photos->pluck("photo_url"))'
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
                    @php
                        $loan = optional($custody->vehicle)->loan;
                    @endphp
                    @if(!empty($loan) && !empty($loan->loanConsultant))
                        {{ $loan->loanConsultant->first_name }} {{ $loan->loanConsultant->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($custody->receiver))
                        {{ $custody->receiver->first_name }} {{ $custody->receiver->last_name }}
                    @endif
                </td>
                <td>
                    @php
                        $latestInspection = null;
                        if (!empty($custody->vehicle) && $custody->vehicle->relationLoaded('inspections')) {
                            $latestInspection = $custody->vehicle->inspections->sortByDesc('inspection_date')->first();
                        }
                    @endphp
                    @if(!empty($latestInspection))
                        {{ $latestInspection->inspector }}
                    @endif
                </td>
                <td>
                    @php
                        $latestValuation = null;
                        if (!empty($custody->vehicle) && $custody->vehicle->relationLoaded('valuations')) {
                            $latestValuation = $custody->vehicle->valuations->sortByDesc('valuation_date')->first();
                        }
                    @endphp
                    @if(!empty($latestValuation) && !empty($latestValuation->valuator))
                        {{ $latestValuation->valuator->first_name }} {{ $latestValuation->valuator->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($custody->receiver))
                        {{ $custody->receiver->first_name }} {{ $custody->receiver->last_name }}
                    @endif
                </td>
                <td>{{ $custody->garage_location ?? $custody->garage_name ?? 'N/A' }}</td>
                <td>{{ $custody->received_at ? \Carbon\Carbon::parse($custody->received_at)->format('Y-m-d') : 'N/A' }}</td>
                <td>
                    @if($custody->custody_approved)
                        <span class="label label-success">Yes</span>
                    @else
                        <span class="label label-danger">No</span>
                    @endif
                </td>
                <td>
                    @if(!empty($loan))
                        <x-onboarding-progress :status="$statuses[$custody->id] ?? ['kyc_completed' => null, 'compliance_screening_completed' => null, 'ownership_completed' => null]" :loan="$loan" />
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">No custody records found.</td>
            </tr>
            @endforelse

            </tbody>

        </table>

        @if($custodies->hasPages())
            <div class="text-right">
                {{ $custodies->links() }}
            </div>
        @endif

    </div>

</div>

</section>

@endsection

@section('footer-scripts')
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

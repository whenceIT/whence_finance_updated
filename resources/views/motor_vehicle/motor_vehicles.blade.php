@extends('layouts.master')
@section('title')
    Motor Vehicles
@endsection 
@section('content')  
    <div class="row">

        <div class="col-md-12">

            <div class="box box-info">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        Vehicle Registry
                    </h3>

                </div>

                <div class="box-body table-responsive">

                <div class="box-body">

<form method="GET">

<div class="row">

    <div class="col-md-3">

        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search vehicle..."
               value="{{ request('search') }}">

    </div>

    <div class="col-md-2">

        <select name="status" class="form-control">

            <option value="">All Statuses</option>

            <option value="available"
                {{ request('status')=='available' ? 'selected' : '' }}>
                Available
            </option>

            <option value="pledged"
                {{ request('status')=='pledged' ? 'selected' : '' }}>
                Pledged
            </option>

            <option value="repossessed"
                {{ request('status')=='repossessed' ? 'selected' : '' }}>
                Repossessed
            </option>

        </select>

    </div>

    <div class="col-md-2">

        <input type="date"
               name="date"
               class="form-control"
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

        <button class="btn btn-info">
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
                            <th>Vehicle Code</th>
                            <th>Owner</th>
                            <th>Registration</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Market Value</th>
                            <th>Status</th>

                        </tr>

                        </thead>

                        <tbody>

                        @forelse($vehicles as $vehicle)

                            <tr>
                                
                            <td>
                                 <a href="{{ url('vehicles/'.$vehicle->id) }}">
    {{$vehicle->id}}
    </a>
                            </td>

                                <td>
                                    @if($vehicle && $vehicle->photos->isNotEmpty())
                                        <img src="{{ $vehicle->photos->first()->photo_url }}"
                                             class="vehicle-photo-thumb"
                                             data-photos='@json($vehicle->photos->pluck("photo_url"))'
                                             style="height: 50px; width: auto; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                             alt="Vehicle photo">
                                    @else
                                        <span class="text-muted">No photo</span>
                                    @endif
                                </td>

                                <td>
                                    
                                         {{ $vehicle->vehicle_code }}
                                 
                                 </td>

                                <td>
                                    {{ optional($vehicle->client)->first_name }}
                                    {{ optional($vehicle->client)->last_name }}
                                </td>

                                <td>
                                    {{ $vehicle->registration_number }}
                                </td>

                                <td>
                                    {{ $vehicle->make }}
                                </td>

                                <td>
                                    {{ $vehicle->model }}
                                </td>

                                <td>
                                    K{{ number_format($vehicle->market_value,2) }}
                                </td>

                                <td>

                                    @if($vehicle->status == 'available')
                                        <span class="label label-success">
                                            Available
                                        </span>
                                    @elseif($vehicle->status == 'pledged')
                                        <span class="label label-warning">
                                            Pledged
                                        </span>
                                    @elseif($vehicle->status == 'repossessed')
                                        <span class="label label-danger">
                                            Repossessed
                                        </span>
                                    @else
                                        <span class="label label-default">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center">
                                    No vehicles found.
                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

    </div>

</div>

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

<script>
(function() {
    const modal = document.getElementById('vehiclePhotoModal');
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

    prevBtn.onclick = () => updateImage(currentIndex - 1);
    nextBtn.onclick = () => updateImage(currentIndex + 1);

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

    document.addEventListener('keydown', function(e) {
        if (!$(modal).data('bs.modal')?.isShown) return;
        if (e.key === 'ArrowLeft') updateImage(currentIndex - 1);
        if (e.key === 'ArrowRight') updateImage(currentIndex + 1);
    });
})();
</script>

@endsection
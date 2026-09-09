@extends('layouts.master')
@section('title')
    Motor Vehicle Loans Pending Approval
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Motor Vehicle Loans Pending</h3>
            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('loans.create'))
                    <a href="{{ url('loan/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info" style="margin-bottom: 20px;">
                        <h4><i class="fa fa-car"></i> Vehicle & Loan Summary</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Total Pending:</strong><br>
                                <span class="badge bg-blue">{{ $data->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Total Amount:</strong><br>
                                KSh {{ number_format($data->sum('principal'), 2) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Pending Approval:</strong><br>
                                <span class="badge bg-yellow">{{ $data->where('status', 'pending')->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Approved:</strong><br>
                                <span class="badge bg-green">{{ $data->where('status', 'approved')->count() }}</span> loans
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Loans List</h3>
        </div>
        <div class="box-body">

            <form method="GET" class="form-horizontal">

                <div class="row">

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

                    <div class="col-md-3">
                        <select name="district" class="form-control">
                            <option value="">All Districts</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ request('district')==$district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="province" class="form-control">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}"
                                    {{ request('province')==$province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="staff" class="form-control">
                            <option value="">All Staff</option>
                            @foreach($staff as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('staff')==$user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">
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

        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>{{ trans_choice('general.account',1) }}#</th>
                        <th>{{ trans_choice('general.branch',1) }}</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>{{ trans_choice('general.client',1) }}</th>
                        <th>Loan Consultant</th>
                        <th>Received By</th>
                        <th>Inspector</th>
                        <th>Valuator</th>
                        <th>Custodian</th>
                        <th>{{ trans_choice('general.proposed',1) }} {{ trans_choice('general.amount',1) }}</th>
                        <th>{{ trans_choice('general.created_at',1) }}</th>
                        <th>Approval</th>
                        <th>Onboarding Progress</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                <tr>
                <td>
                    @if(!empty($key->vehicle) && $key->vehicle->photos->isNotEmpty())
                        <img src="{{ $key->vehicle->photos->first()->photo_url }}"
                             class="vehicle-photo-thumb"
                             data-photos='@json($key->vehicle->photos->pluck("photo_url"))'
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
                <td>{{ $key->id }}</td>
                <td>
                    @if(!empty($key->originatingBranch))
                        {{$key->originatingBranch->name}}
                    @endif
                </td>
                <td>
                    @if(!empty($key->originatingBranch->district))
                        {{$key->originatingBranch->district->name}}
                    @endif
                </td>
                <td>
                    @if(!empty($key->originatingBranch->province))
                        {{$key->originatingBranch->province->name}}
                    @endif
                </td>
                <td>
                    @if($key->client_type=="client")
                        @if(!empty($key->client))
                            @if($key->client->client_type=="individual")
                                {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                            @else
                                {{$key->client->full_name}}
                            @endif
                        @endif
                    @endif
                    @if($key->client_type=="group")
                        {{$key->group->name}}
                    @endif
                </td>
                <td>
                    @if(!empty($key->loanConsultant))
                        {{$key->loanConsultant->first_name}} {{$key->loanConsultant->last_name}}
                        <br><small class="text-muted">ID: {{$key->loanConsultant->id}}</small>
                    @endif
                </td>
                <td>
                    @if(!empty($key->created_by))
                        {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                    @endif
                </td>
                <td>
                    @php
                        $latestInspection = null;
                        if (!empty($key->vehicle) && $key->vehicle->relationLoaded('inspections')) {
                            $latestInspection = $key->vehicle->inspections->sortByDesc('inspection_date')->first();
                        }
                    @endphp
                    @if(!empty($latestInspection))
                        {{ $latestInspection->inspector }}
                    @endif
                </td>
                <td>
                    @php
                        $latestValuation = null;
                        if (!empty($key->vehicle) && $key->vehicle->relationLoaded('valuations')) {
                            $latestValuation = $key->vehicle->valuations->sortByDesc('valuation_date')->first();
                        }
                    @endphp
                    @if(!empty($latestValuation) && !empty($latestValuation->valuator))
                        {{ $latestValuation->valuator->first_name }} {{ $latestValuation->valuator->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($key->vehicle) && !empty($key->vehicle->custody) && !empty($key->vehicle->custody->receiver))
                        {{ $key->vehicle->custody->receiver->first_name }} {{ $key->vehicle->custody->receiver->last_name }}
                    @endif
                </td>
                <td>{{ number_format($key->principal, $key->decimals) }}</td>
                <td>{{ $key->created_date }}</td>
                <td>
                {{$key->status}}
            </td>
            <td>
                <x-onboarding-progress :status="$statuses[$key->id]" :loan="$key" />
            </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false"><i
                                    class="fa fa-navicon"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            @if(Sentinel::hasAccess('loans.view'))
                                <li>
                                    <a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                class="fa fa-search"></i>
                                        {{ trans_choice('general.detail',2) }}</a>
                                </li>
                            @endif
                            @if($key->status=="pending")
                                @if(Sentinel::hasAccess('loans.update'))
                                    <li>
                                        <a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                    class="fa fa-edit"></i>
                                            {{ trans('general.edit') }}</a>
                                    </li>
                                @endif
                                @if(Sentinel::hasAccess('loans.delete'))
                                    <li>
                                        <a href="{{ url('loan/'.$key->id.'/delete') }}"
                                           class="delete"><i
                                                    class="fa fa-trash"></i>
                                            {{ trans('general.delete') }}</a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[11, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });
    </script>

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
@extends('layouts.master')
@section('title')
    Disposal Register
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Disposal Register (Defaulted Motor Vehicle Loans)</h3>
        </div>
        <div class="box-body">
            <div class="row" style="margin-bottom: 20px;">

                <div class="col-md-3 col-sm-6">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>Total</p>
                            <p style="font-size: 14px; margin-bottom: 0;">K{{ number_format($stats['total_amount'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-folder-open-o"></i>
                        </div>
                    </div>
                </div>
                

                <div class="col-md-3 col-sm-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{ $stats['disbursed_overdue'] }}</h3>
                            <p>Disbursed (Overdue/Defaulted)</p>
                            <p style="font-size: 14px; margin-bottom: 0;">K{{ number_format($stats['disbursed_overdue_amount'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{ $stats['closed_overdue'] }}</h3>
                            <p>Recovered (Overdue/closed) <span class="fa fa-info-circle" style="color: #fff; cursor: help;" data-toggle="tooltip" data-placement="top" title="Total principal + initial interest recovered on Motor Vehicle Loans that have been overdue and closed/disposed."></span></p>
                            <p style="font-size: 14px; margin-bottom: 0;">K{{ number_format($stats['closed_overdue_amount'], 2) }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-ban"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Loans in more than 1 week and 1 month defualt</h5>
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
            <table class="table table-bordered table-hover table-striped" id="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Branch</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>Client</th>
                        <th>Loan Consultant</th>
                        <th>Received By</th>
                        <th>Inspector</th>
                        <th>Valuator</th>
                        <th>Custodian</th>
                        <th>Principal</th>
                        <th>Market Value</th>
                        <th>1st Repayment</th>
                        <th>Time Taken to Sale</th>
                        <th>Onboarding Progress</th>
                        <th>Recovery</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($loans as $loan)
                    @php
                        $status = $statuses[$loan->id] ?? ['kyc_completed' => null, 'compliance_screening_completed' => null, 'ownership_completed' => null];
                        $firstRepayment = $loan->first_repayment_date ? \Carbon\Carbon::parse($loan->first_repayment_date) : null;
                        $timeTaken = $firstRepayment ? \Carbon\Carbon::now()->diffInDays($firstRepayment) : null;
                        $timeTakenLabel = $timeTaken ? $timeTaken . ' days' : 'N/A';
                        if ($timeTaken >= 30) {
                            $months = floor($timeTaken / 30);
                            $days = $timeTaken % 30;
                            $timeTakenLabel = $months . ' month' . ($months > 1 ? 's' : '') . ($days > 0 ? ' ' . $days . ' day' . ($days > 1 ? 's' : '') : '');
                        }
                    @endphp
                    <tr>
                        <td>
                            @if(!empty($loan->vehicle) && $loan->vehicle->photos->isNotEmpty())
                                <img src="{{ $loan->vehicle->photos->first()->photo_url }}"
                                     class="vehicle-photo-thumb"
                                     data-photos='@json($loan->vehicle->photos->pluck("photo_url"))'
                                     style="height: 50px; width: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;"
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
                            @if(!empty($loan->originatingBranch))
                                {{$loan->originatingBranch->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->originatingBranch->district))
                                {{$loan->originatingBranch->district->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->originatingBranch->province))
                                {{$loan->originatingBranch->province->name}}
                            @endif
                        </td>
                        <td>
                            @if($loan->client_type=="client")
                                @if(!empty($loan->client))
                                    @if($loan->client->client_type=="individual")
                                        {{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}
                                    @else
                                        {{$loan->client->full_name}}
                                    @endif
                                @endif
                            @endif
                            @if($loan->client_type=="group")
                                {{$loan->group->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->loanConsultant))
                                {{$loan->loanConsultant->first_name}} {{$loan->loanConsultant->last_name}}
                                <br><small class="text-muted">ID: {{$loan->loanConsultant->id}}</small>
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->created_by))
                                {{$loan->created_by->first_name}} {{$loan->created_by->last_name}}
                            @endif
                        </td>
                        <td>
                            @php $latestInspection = !empty($loan->vehicle) ? $loan->vehicle->inspections->sortByDesc('inspection_date')->first() : null; @endphp
                            @if(!empty($latestInspection))
                                {{ $latestInspection->inspector }}
                            @endif
                        </td>
                        <td>
                            @php $latestValuation = !empty($loan->vehicle) ? $loan->vehicle->valuations->sortByDesc('valuation_date')->first() : null; @endphp
                            @if(!empty($latestValuation) && !empty($latestValuation->valuator))
                                {{ $latestValuation->valuator->first_name }} {{ $latestValuation->valuator->last_name }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->vehicle->custody) && !empty($loan->vehicle->custody->receiver))
                                {{ $loan->vehicle->custody->receiver->first_name }} {{ $loan->vehicle->custody->receiver->last_name }}
                            @endif
                        </td>
                        <td>{{ number_format($loan->principal, $loan->decimals) }}</td>
                        <td>
                            @if(!empty($loan->vehicle) && !empty($loan->vehicle->market_value))
                                K{{ number_format($loan->vehicle->market_value, 2) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($firstRepayment)
                                {{ \Carbon\Carbon::parse($loan->first_repayment_date)->diffForHumans() }}
                                <br><small class="text-muted">{{ $firstRepayment->format('Y-m-d') }}</small>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-red">{{ $timeTakenLabel }}</span>
                        </td>
                        <td>
                            <x-onboarding-progress :status="$status" :loan="$loan" />
                        </td>
                        <td>
                            <a href="javascript:void(0)" data-recovery-url="{{ url('vehicles/recovery-data/' . $loan->id) }}" class="recovery-btn">
                                <i class="fa fa-bullseye"></i> Recovery Status
                            </a>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li>
                                        <a href="{{ url('loan/'.$loan->id.'/show') }}"><i
                                                    class="fa fa-search"></i>
                                            Loan Details</a>
                                    </li>
                                    <li>
                                        @if(!empty($loan->vehicle))
                                        <a href="{{ url('vehicles/'.$loan->vehicle->id) }}">
                                            <i class="fa fa-eye"></i> Vehicle
                                        </a>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')
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
            "order": [[13, "desc"]],
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

<!-- Recovery Bottom Sheet -->
<div class="bottom-sheet-overlay" id="recoveryBottomSheetOverlay">
    <div class="bottom-sheet" id="recoveryBottomSheet" style="max-height: 90vh;">
        <button class="bottom-sheet-close" id="closeRecoveryBottomSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content" style="padding: 20px;">
            <div style="border-bottom: 3px solid #d9534f; padding-bottom: 15px; margin-bottom: 20px;">
                <h3 style="font-size: 22px; font-weight: 700; color: #333; margin: 0 0 5px 0;">
                    <i class="fa fa-bullseye" style="color: #d9534f;"></i> Recovery
                </h3>
                <p style="margin: 0; color: #777; font-size: 14px;">
                    <strong>Loan #<span id="recoveryLoanId"></span></strong>
                    &mdash; <span id="recoveryVehicleInfo"></span>
                </p>
            </div>

            <form id="recoveryForm" method="POST" action="" style="max-width: 100%; margin: 0;">
                @csrf

                <h4 style="margin: 0 0 15px 0; color: #d9534f; font-size: 16px; font-weight: 600;">Recovery Timeline</h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px 20px;">

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Contractual Due Date</label>
                        <input type="text" id="recoveryContractualDueDate" class="form-control" readonly style="background: #eee; border-radius: 4px;">
                    </div>

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Date of Default</label>
                        <input type="text" id="recoveryDateOfDefault" class="form-control" readonly style="background: #eee; border-radius: 4px;">
                    </div>

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Days Overdue</label>
                        <input type="text" id="recoveryDaysOverdue" class="form-control" readonly style="background: #eee; border-radius: 4px;">
                    </div>

                </div>

                <h4 style="margin: 20px 0 15px 0; color: #333; font-size: 16px; font-weight: 600;">Recovery Details</h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px 20px;">

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Storage Charges</label>
                        <input type="number" step="0.01" name="storage_charges" id="recoveryStorageCharges" class="form-control" style="border-radius: 4px;" placeholder="0.00">
                    </div>

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Valuation Costs</label>
                        <input type="number" step="0.01" name="valuation_costs" id="recoveryValuationCosts" class="form-control" style="border-radius: 4px;" placeholder="0.00">
                    </div>

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Repossession/Recovery Costs</label>
                        <input type="number" step="0.01" name="repossession_costs" id="recoveryRepossessionCosts" class="form-control" style="border-radius: 4px;" placeholder="0.00">
                    </div>

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Legal Costs</label>
                        <input type="number" step="0.01" name="legal_costs" id="recoveryLegalCosts" class="form-control" style="border-radius: 4px;" placeholder="0.00">
                    </div>

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Other Recovery Expenses</label>
                        <input type="number" step="0.01" name="other_expenses" id="recoveryOtherExpenses" class="form-control" style="border-radius: 4px;" placeholder="0.00">
                    </div>

                    <div>
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Applicable Penalties</label>
                        <input type="number" step="0.01" name="penalties" id="recoveryPenalties" class="form-control" style="border-radius: 4px;" placeholder="0.00">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Current Recovery Stage</label>
                        <input type="text" name="current_recovery_stage" id="recoveryCurrentStage" class="form-control" style="border-radius: 4px;" placeholder="e.g. Initial Notice, Legal Action, Auction">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Recovery Actions Taken</label>
                        <textarea name="recovery_actions" id="recoveryActions" class="form-control" rows="3" style="border-radius: 4px;" placeholder="Describe recovery actions taken"></textarea>
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Communications with Client</label>
                        <textarea name="communications" id="recoveryCommunications" class="form-control" rows="3" style="border-radius: 4px;" placeholder="Describe communications with client"></textarea>
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="font-weight: 600; margin-bottom: 5px; display: block; color: #555; font-size: 13px;">Promises/Arrangements Made</label>
                        <textarea name="promises_arrangements" id="recoveryPromises" class="form-control" rows="3" style="border-radius: 4px;" placeholder="Describe any promises or arrangements"></textarea>
                    </div>

                </div>

                <div style="border-top: 1px solid #ddd; margin-top: 20px; padding-top: 15px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="submit" class="btn btn-warning" style="border-radius: 4px; padding: 8px 24px;">
                        <i class="fa fa-save"></i> Save Recovery
                    </button>
                    <button type="button" id="closeRecoveryBtn" class="btn btn-default" style="border-radius: 4px; padding: 8px 24px;">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var recoveryOverlay = document.getElementById('recoveryBottomSheetOverlay');
    var recoverySheet = document.getElementById('recoveryBottomSheet');
    var closeRecoveryBtn = document.getElementById('closeRecoveryBottomSheet');
    var closeRecoveryBtn2 = document.getElementById('closeRecoveryBtn');

    function openRecoverySheet() {
        recoveryOverlay.classList.add('active');
        recoverySheet.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeRecoverySheet() {
        recoveryOverlay.classList.remove('active');
        recoverySheet.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.recovery-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('data-recovery-url');

            $.get(url, function(data) {
                document.getElementById('recoveryLoanId').textContent = data.loan_id;
                document.getElementById('recoveryVehicleInfo').textContent = data.vehicle_info || '';
                document.getElementById('recoveryContractualDueDate').value = data.contractual_due_date || '';
                document.getElementById('recoveryDateOfDefault').value = data.date_of_default || '';
                document.getElementById('recoveryDaysOverdue').value = data.days_overdue || 0;
                document.getElementById('recoveryStorageCharges').value = data.storage_charges || '';
                document.getElementById('recoveryValuationCosts').value = data.valuation_costs || '';
                document.getElementById('recoveryRepossessionCosts').value = data.recovery_data ? data.recovery_data.repossession_costs : '';
                document.getElementById('recoveryLegalCosts').value = data.recovery_data ? data.recovery_data.legal_costs : '';
                document.getElementById('recoveryOtherExpenses').value = data.recovery_data ? data.recovery_data.other_expenses : '';
                document.getElementById('recoveryPenalties').value = data.recovery_data ? data.recovery_data.penalties : '';
                document.getElementById('recoveryCurrentStage').value = data.recovery_data ? data.recovery_data.current_recovery_stage : '';
                document.getElementById('recoveryActions').value = data.recovery_data ? data.recovery_data.recovery_actions : '';
                document.getElementById('recoveryCommunications').value = data.recovery_data ? data.recovery_data.communications : '';
                document.getElementById('recoveryPromises').value = data.recovery_data ? data.recovery_data.promises_arrangements : '';

                document.getElementById('recoveryForm').action = '/vehicles/recovery/' + data.loan_id;
                openRecoverySheet();
            }).fail(function() {
                alert('Failed to load recovery data');
            });
        });
    });

    if (closeRecoveryBtn) closeRecoveryBtn.addEventListener('click', closeRecoverySheet);
    if (closeRecoveryBtn2) closeRecoveryBtn2.addEventListener('click', closeRecoverySheet);
    if (recoveryOverlay) recoveryOverlay.addEventListener('click', function(e) {
        if (e.target === recoveryOverlay) closeRecoverySheet();
    });

    document.getElementById('recoveryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        $.ajax({
            url: form.action,
            type: 'POST',
            data: $(form).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response) {
                alert(response.message || 'Recovery data saved successfully');
                closeRecoverySheet();
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to save recovery data'));
            }
        });
    });
    // Initialize tooltips
    $("[data-toggle='tooltip']").tooltip();
})();
</script>
@endsection

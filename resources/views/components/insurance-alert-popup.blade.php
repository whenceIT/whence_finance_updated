@if(!empty($bmInsuranceAlert ?? null))
{{-- =====================================================================
     Branch Insurance Alert Modal
     Shown automatically to branch managers (role 4) on their dashboard
     when one or more fleet vehicles assigned to their branch have an
     expired insurance_expire_date.
===================================================================== --}}
<div class="modal fade" id="bmInsuranceAlertModal" tabindex="-1" role="dialog"
     aria-labelledby="bmInsuranceAlertTitle" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(15,23,42,.25);">

            {{-- Header --}}
            <div class="modal-header" style="background:linear-gradient(135deg,#d97706 0%,#b45309 100%);border:none;padding:1.25rem 1.5rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span style="background:rgba(255,255,255,.2);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-shield" style="color:#fff;font-size:1.1rem;"></i>
                    </span>
                    <div>
                        <h5 class="modal-title" id="bmInsuranceAlertTitle"
                            style="color:#fff;font-weight:700;margin:0;font-size:1.1rem;line-height:1.3;">
                            Expired Vehicle Insurance
                        </h5>
                        <p style="color:rgba(255,255,255,.85);margin:0;font-size:.8rem;">
                            {{ $bmInsuranceAlert['office_name'] }}
                        </p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="color:#fff;opacity:.8;font-size:1.4rem;line-height:1;padding:0;margin:0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="modal-body" style="padding:1.5rem;">

                {{-- Count badge --}}
                <div style="text-align:center;margin-bottom:1.25rem;">
                    <div style="display:inline-flex;flex-direction:column;align-items:center;
                                background:#fffbeb;border:2px solid #fcd34d;border-radius:12px;
                                padding:.9rem 2rem;">
                        <span style="font-size:2.8rem;font-weight:800;color:#d97706;line-height:1;">
                            {{ $bmInsuranceAlert['count'] }}
                        </span>
                        <span style="font-size:.8rem;font-weight:600;color:#78350f;letter-spacing:.05em;text-transform:uppercase;">
                            {{ $bmInsuranceAlert['count'] === 1 ? 'Vehicle' : 'Vehicles' }} with Expired Insurance
                        </span>
                    </div>
                </div>

                <p style="color:#4a5568;font-size:.875rem;text-align:center;margin-bottom:1.1rem;">
                    The following {{ $bmInsuranceAlert['count'] === 1 ? 'vehicle' : 'vehicles' }} at your branch
                    {{ $bmInsuranceAlert['count'] === 1 ? 'has' : 'have' }} insurance that has already expired.
                    Please arrange renewal immediately.
                </p>

                {{-- Vehicle list --}}
                <div style="max-height:220px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px;">
                    @foreach($bmInsuranceAlert['vehicles'] as $fleet)
                        <div style="display:flex;align-items:center;justify-content:space-between;
                                    padding:.7rem 1rem;border-bottom:1px solid #f1f5f9;
                                    {{ $loop->last ? 'border-bottom:none;' : '' }}">
                            <div>
                                <div style="font-weight:700;color:#1e293b;font-size:.875rem;">
                                    {{ $fleet->vehicle_id }}
                                    <span style="font-weight:400;color:#64748b;font-size:.8rem;">
                                        — {{ $fleet->vehicle_type }} {{ $fleet->vehicle_model }}
                                    </span>
                                </div>
                                @if($fleet->user)
                                    <div style="font-size:.78rem;color:#64748b;">
                                        <i class="fa fa-user" style="margin-right:3px;"></i>{{ $fleet->user->first_name }} {{ $fleet->user->last_name }}
                                    </div>
                                @endif
                            </div>
                            <div style="text-align:right;flex-shrink:0;margin-left:.75rem;">
                                <span style="display:inline-block;background:#fef2f2;color:#dc2626;
                                             border:1px solid #fecaca;border-radius:6px;
                                             font-size:.72rem;font-weight:700;padding:2px 8px;
                                             text-transform:uppercase;letter-spacing:.04em;">
                                    Expired
                                </span>
                                @if($fleet->insurance_expire_date)
                                    <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">
                                        {{ $fleet->insurance_expire_date->format('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer" style="border-top:1px solid #e2e8f0;padding:1rem 1.5rem;gap:.5rem;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="border-radius:8px;font-weight:600;">
                    Dismiss
                </button>
                <a href="{{ route('goa.fleet-management') }}"
                   class="btn btn-warning btn-sm"
                   style="border-radius:8px;font-weight:600;color:#fff;background:#d97706;border-color:#d97706;">
                    <i class="fa fa-car"></i> View Fleet Management
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        setTimeout(function () {
            $('#bmInsuranceAlertModal').modal({ backdrop: true, keyboard: true });
            $('#bmInsuranceAlertModal').modal('show');
        }, 2000); {{-- slight offset from the vacancy modal (1.2 s) so they don't stack --}}
    });
</script>
@endif

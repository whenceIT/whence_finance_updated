@if(!empty($bmVacancyAlert ?? null))
{{-- =====================================================================
     Branch Vacancy Alert Modal
     Shown automatically to branch managers (role 4) when their branch
     has at least one vacancy (approved capacity > current personnel).
===================================================================== --}}
<div class="modal fade" id="bmVacancyAlertModal" tabindex="-1" role="dialog"
     aria-labelledby="bmVacancyAlertTitle" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:480px;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(15,23,42,.25);">

            {{-- Header --}}
            <div class="modal-header" style="background:linear-gradient(135deg,#e53e3e 0%,#c53030 100%);border:none;padding:1.25rem 1.5rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span style="background:rgba(255,255,255,.2);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-exclamation-triangle" style="color:#fff;font-size:1.1rem;"></i>
                    </span>
                    <div>
                        <h5 class="modal-title" id="bmVacancyAlertTitle"
                            style="color:#fff;font-weight:700;margin:0;font-size:1.1rem;line-height:1.3;">
                            Vacancy Alert
                        </h5>
                        <p style="color:rgba(255,255,255,.85);margin:0;font-size:.8rem;">
                            {{ $bmVacancyAlert['office_name'] }}
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

                {{-- Vacancy count badge --}}
                <div style="text-align:center;margin-bottom:1.25rem;">
                    <div style="display:inline-flex;flex-direction:column;align-items:center;
                                background:#fff5f5;border:2px solid #fed7d7;border-radius:12px;
                                padding:.9rem 2rem;">
                        <span style="font-size:2.8rem;font-weight:800;color:#e53e3e;line-height:1;">
                            {{ $bmVacancyAlert['vacancy'] }}
                        </span>
                        <span style="font-size:.8rem;font-weight:600;color:#742a2a;letter-spacing:.05em;text-transform:uppercase;">
                            {{ $bmVacancyAlert['vacancy'] === 1 ? 'Open Vacancy' : 'Open Vacancies' }}
                        </span>
                    </div>
                </div>

                <p style="color:#4a5568;font-size:.9rem;text-align:center;margin-bottom:1.25rem;">
                    Your branch is currently operating below its approved staffing capacity.
                    Please review the vacancy register and initiate the recruitment process.
                </p>

                {{-- Stats row --}}
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-bottom:1rem;">
                    <div style="background:#f7fafc;border-radius:8px;padding:.65rem .5rem;text-align:center;">
                        <div style="font-size:1.3rem;font-weight:700;color:#2d3748;">
                            {{ $bmVacancyAlert['approved'] }}
                        </div>
                        <div style="font-size:.72rem;color:#718096;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                            Approved
                        </div>
                    </div>
                    <div style="background:#f7fafc;border-radius:8px;padding:.65rem .5rem;text-align:center;">
                        <div style="font-size:1.3rem;font-weight:700;color:#2d3748;">
                            {{ $bmVacancyAlert['current'] }}
                        </div>
                        <div style="font-size:.72rem;color:#718096;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                            Current
                        </div>
                    </div>
                    <div style="background:#fff5f5;border-radius:8px;padding:.65rem .5rem;text-align:center;border:1px solid #fed7d7;">
                        <div style="font-size:1.3rem;font-weight:700;color:#e53e3e;">
                            {{ $bmVacancyAlert['vacancy_pct'] }}%
                        </div>
                        <div style="font-size:.72rem;color:#718096;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                            Vacant
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer" style="border-top:1px solid #e2e8f0;padding:1rem 1.5rem;gap:.5rem;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="border-radius:8px;font-weight:600;">
                    Dismiss
                </button>
                <!-- <a href="{{ route('goa.branch-staffing-capacity', ['office_id' => $bmVacancyAlert['office_id'], 'tab' => 'vacancies']) }}"
                   class="btn btn-danger btn-sm"
                   style="border-radius:8px;font-weight:600;background:#e53e3e;border-color:#e53e3e;">
                    <i class="fa fa-briefcase"></i> View Vacancy Register
                </a> -->
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Delay slightly so the page finishes rendering before the modal appears
        setTimeout(function () {
            $('#bmVacancyAlertModal').modal({ backdrop: true, keyboard: true });
            $('#bmVacancyAlertModal').modal('show');
        }, 1200);
    });
</script>
@endif

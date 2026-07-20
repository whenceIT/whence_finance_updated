@php
    $officeId = Sentinel::getUser()->office_id;
    $currentMonth = now()->format('Y-m');
    $baseQuery = App\Models\Advance::with(['office', 'user', 'transactions'])
        ->where('office_id', $officeId)
        ->where('created_at', '<', now()->startOfMonth());
    $thisMonthAdvances = App\Helpers\RedirectHelper::getThisMonthAdvances($baseQuery, $currentMonth);
    $pendingCount = $thisMonthAdvances->count();
@endphp


<div class="alert alert-warning advance-deduction-banner" style="margin-bottom: 0; border-radius: 0; background-color: #fff3cd; border: 1px solid #ffc107; display: {{ $pendingCount > 0 ? 'block' : 'none' }};">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 5px 10px;">
        <div class="row align-items-center">
            <div class="col-md-10">
                <strong><i class="fa fa-exclamation-triangle"></i> 
                    {{ $pendingCount }} {{ $pendingCount == 1 ? 'Advance pending to be deducted this month' : 'Advances pending to be deducted this month' }}
                </strong>
            </div>
            <div class="col-md-2 text-right">
                <a href="{{ url('advance/advance_deductions') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-arrow-right"></i> View Deductions
                </a>
            </div>
        </div>
    </div>
</div>
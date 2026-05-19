@if(count($scheduledAudits) === 0)
    <div class="alert alert-info">No scheduled audits found.</div>
@else
    <div class="row">
        @foreach($scheduledAudits as $branch)
            @include('risk.partials.audit-branch-card', ['branch' => $branch, 'ratingConfig' => $ratingConfig, 'sectionShorts' => $sectionShorts])
        @endforeach
    </div>
@endif

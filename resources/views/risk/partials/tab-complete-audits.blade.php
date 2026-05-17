@if(count($completeAudits) === 0)
    <div class="alert alert-info">No complete audits found.</div>
@else
    <div class="row">
        @foreach($completeAudits as $branch)
            @include('risk.partials.audit-branch-card', ['branch' => $branch, 'ratingConfig' => $ratingConfig, 'sectionShorts' => $sectionShorts])
        @endforeach
    </div>
@endif

@if(count($incompleteAudits) === 0)
    <div class="alert alert-info">No incomplete audits currently in progress.</div>
@else
    <div class="row">
        @foreach($incompleteAudits as $branch)
            @include('risk.partials.audit-branch-card', ['branch' => $branch, 'ratingConfig' => $ratingConfig, 'sectionShorts' => $sectionShorts, 'showDelete' => true])
        @endforeach
    </div>
@endif

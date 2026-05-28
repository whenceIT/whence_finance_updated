@extends('layouts.master')

@section('title', 'Policy of the Day')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 3rem 2rem; font-family: 'Georgia', serif;">
    <div style="border-bottom: 2px solid #00a04a; padding-bottom: 1.5rem; margin-bottom: 2rem;">
        <h1 style="font-size: 2.5rem; font-weight: 700; margin: 0; color: #1a1a1a; letter-spacing: -0.5px;">
            {{ $policyOfTheDay->title }}
        </h1>
        <p style="font-size: 1rem; color: #666; margin: 0.75rem 0 0 0; font-style: italic;">
            <i class="fa fa-calendar"></i> 
            {{ $policyOfTheDay->scheduled_date ? $policyOfTheDay->scheduled_date->format('F d, Y') : 'Random Selection' }}
            @if($policyOfTheDay->creator)
                | <i class="fa fa-user"></i> {{ $policyOfTheDay->creator->first_name }} {{ $policyOfTheDay->creator->last_name }}
            @endif
        </p>
    </div>

    @if($policyOfTheDay->content)
        <div style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #333; margin-bottom: 1rem; border-bottom: 1px solid #ddd; padding-bottom: 0.5rem;">
                Overview
            </h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #444; margin: 0;">
                {{ $policyOfTheDay->content }}
            </p>
        </div>
    @endif

    @if($policyOfTheDay->full_content)
        <div style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #333; margin-bottom: 1rem; border-bottom: 1px solid #ddd; padding-bottom: 0.5rem;">
                Full Content
            </h2>
            <div style="font-size: 1.1rem; line-height: 1.8; color: #444;">
                {{ $policyOfTheDay->full_content }}
            </div>
        </div>
    @endif

    @if($policyOfTheDay->policy)
        <div style="margin-top: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #333; margin-bottom: 1rem; border-bottom: 1px solid #ddd; padding-bottom: 0.5rem;">
                Policy Document
            </h2>
            <div style="border-left: 4px solid #00a04a; padding: 1.5rem; background: #f9f9f9;">
                <h3 style="font-size: 1.2rem; font-weight: 600; margin: 0 0 1rem 0; color: #1a1a1a;">
                    {{ $policyOfTheDay->policy->title }}
                </h3>
                @if($policyOfTheDay->policy->file_url)
                    <div style="border: 1px solid #ddd; background: white;">
                        <iframe src="{{ $policyOfTheDay->policy->file_url }}" 
                                style="width: 100%; height: 600px; border: none;"
                                allowfullscreen></iframe>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@section('footer-scripts')
<script>
    let viewStartTime = Date.now();
    let engagementInterval;

    function recordEngagement() {
        const elapsed = Math.floor((Date.now() - viewStartTime) / 1000);
        
        if (elapsed >= 180) {
            clearInterval(engagementInterval);
            
            const policyOfTheDayId = {{ $policyOfTheDay->id }};
            const userId = @json(\Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser()->id ?? 0);
            
            if (userId && userId !== 0) {
                $.post('{{ route('policies.track-engagement') }}', {
                    user_id: userId,
                    policy_of_the_day_id: policyOfTheDayId,
                    policy_id: @json($policyOfTheDay->policy_id ?? null),
                    engagement_time: elapsed,
                    _token: '{{ csrf_token() }}'
                });
            }
        }
    }

    engagementInterval = setInterval(recordEngagement, 10000);

    window.addEventListener('beforeunload', function() {
        const elapsed = Math.floor((Date.now() - viewStartTime) / 1000);
        
        if (elapsed >= 180) {
            const policyOfTheDayId = {{ $policyOfTheDay->id }};
            const userId = @json(\Cartalyst\Sentinel\Laravel\Facades\Sentinel::getUser()->id ?? 0);
            
            if (userId && userId !== 0) {
                $.post('{{ route('policies.track-engagement') }}', {
                    user_id: userId,
                    policy_of_the_day_id: policyOfTheDayId,
                    policy_id: @json($policyOfTheDay->policy_id ?? null),
                    engagement_time: elapsed,
                    _token: '{{ csrf_token() }}'
                });
            }
        }
    });
</script>
@endsection
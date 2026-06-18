@extends('layouts.master')

@section('title', 'Policy of the Day')

@section('content')
<div style="max-width: 100%; margin: 0 auto; padding: 3rem 2rem; font-family: 'Georgia', serif;">
    {{-- Policy Header Section --}}
    <div style="background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); padding: 3rem 2.5rem; border-radius: 16px; margin-bottom: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
        <div style="max-width: 900px;">
            <div style="display: inline-block; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 0.5rem 1.25rem; border-radius: 50px; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.2);">
                <span style="font-size: 0.9rem; color: #fff; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                    <i class="fa fa-file-text-o"></i> Policy of the Day
                </span>
            </div>
            <h1 style="font-size: 3rem; font-weight: 800; margin: 0 0 1.5rem 0; color: #ffffff; line-height: 1.2; letter-spacing: -1px;">
                {{ $policyOfTheDay->title }}
            </h1>
            <div style="display: flex; gap: 2rem; flex-wrap: wrap; font-size: 1rem; color: rgba(255,255,255,0.9);">
                <span style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa fa-calendar" style="font-size: 1.1rem;"></i>
                    <strong>Published:</strong> {{ $policyOfTheDay->scheduled_date ? $policyOfTheDay->scheduled_date->format('F d, Y') : 'Random Selection' }}
                </span>
                @if($policyOfTheDay->creator)
                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa fa-user-circle" style="font-size: 1.1rem;"></i>
                        <strong>Author:</strong> {{ $policyOfTheDay->creator->first_name }} {{ $policyOfTheDay->creator->last_name }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Overview Section --}}
    @if($policyOfTheDay->content)
        <div style="background: #ffffff; padding: 2.5rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-left: 5px solid #00a04a;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #00a04a 0%, #00c853 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-info-circle" style="color: white; font-size: 1.5rem;"></i>
                </div>
                <h2 style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a; margin: 0;">
                    Overview
                </h2>
            </div>
            <p style="font-size: 1.2rem; line-height: 2; color: #2d3748; margin: 0; text-align: justify;">
                {{ $policyOfTheDay->content }}
            </p>
        </div>
    @endif

    {{-- Full Content Section --}}
    @if($policyOfTheDay->full_content)
        <div style="background: #ffffff; padding: 2.5rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-left: 5px solid #2c5282;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #2c5282 0%, #3b82f6 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-align-left" style="color: white; font-size: 1.5rem;"></i>
                </div>
                <h2 style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a; margin: 0;">
                    Full Policy Details
                </h2>
            </div>
            <div style="font-size: 1.15rem; line-height: 2; color: #2d3748; text-align: justify;">
                {!! nl2br(e($policyOfTheDay->full_content)) !!}
            </div>
        </div>
    @endif

    {{-- Policy Document Section --}}
    @if($policyOfTheDay->policy)
        <div style="background: #ffffff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-left: 5px solid #f59e0b;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-file-pdf-o" style="color: white; font-size: 1.5rem;"></i>
                </div>
                <h2 style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a; margin: 0;">
                    Official Policy Document
                </h2>
            </div>

            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #dee2e6;">
                <h3 style="font-size: 1.4rem; font-weight: 700; margin: 0 0 0.5rem 0; color: #1a1a1a; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fa fa-bookmark" style="color: #f59e0b;"></i>
                    {{ $policyOfTheDay->policy->title }}
                </h3>
                @if($policyOfTheDay->policy->description)
                    <p style="font-size: 1rem; color: #6c757d; margin: 0; line-height: 1.6;">
                        {{ Illuminate\Support\Str::limit($policyOfTheDay->policy->description, 200) }}
                    </p>
                @endif
            </div>

            @if($policyOfTheDay->policy->file_url)
                <div style="border: 2px solid #dee2e6; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); border-bottom: 1px solid #1e3a5f; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fa fa-file-text" style="font-size: 1.5rem; color: #fbbf24;"></i>
                            <div>
                                <div style="font-size: 1.05rem; font-weight: 600; color: #ffffff;">
                                    {{ $policyOfTheDay->policy->file_name ?? 'Policy Document' }}
                                </div>
                                <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-top: 0.25rem;">
                                    Official Document
                                </div>
                            </div>
                        </div>
                        <a href="{{ $policyOfTheDay->policy->file_url }}" target="_blank" 
                           style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); color: white; padding: 0.6rem 1.25rem; border-radius: 8px; text-decoration: none; font-size: 0.95rem; font-weight: 600; border: 1px solid rgba(255,255,255,0.3); transition: all 0.3s ease;">
                            <i class="fa fa-external-link"></i> Open in New Tab
                        </a>
                    </div>
                    @php
                        $fileUrl = $policyOfTheDay->policy->file_url;
                        $fileMime = $policyOfTheDay->policy->file_mime ?? '';
                    @endphp
                    <div style="height: 900px; position: relative; background: #f8f9fa;">
                        @if(strpos($fileMime, 'pdf') !== false || strpos($fileUrl, '.pdf') !== false)
                            <embed src="{{ $fileUrl }}" width="100%" height="100%" type="application/pdf" style="display: block;">
                        @elseif(strpos($fileMime, 'word') !== false || strpos($fileMime, 'document') !== false || strpos($fileUrl, '.doc') !== false || strpos($fileUrl, '.docx') !== false)
                            <iframe src="https://docs.google.com/gview?url={{ urlencode($fileUrl) }}&embedded=true" width="100%" height="100%" style="border: none; display: block;"></iframe>
                        @else
                            <div style="padding: 3rem; text-align: center; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; border: 3px solid #dee2e6;">
                                    <i class="fa fa-file" style="font-size: 3.5rem; color: #adb5bd;"></i>
                                </div>
                                <h4 style="font-size: 1.3rem; color: #495057; margin: 0 0 0.75rem 0; font-weight: 600;">Preview Not Available</h4>
                                <p style="color: #6c757d; margin: 0 0 1.5rem 0; font-size: 1.05rem;">This file type cannot be previewed in the browser.</p>
                                <a href="{{ $fileUrl }}" target="_blank" 
                                   style="background: linear-gradient(135deg, #00a04a 0%, #00c853 100%); color: white; padding: 0.85rem 2rem; border-radius: 10px; text-decoration: none; font-size: 1.05rem; font-weight: 600; box-shadow: 0 4px 12px rgba(0,160,74,0.3); transition: all 0.3s ease;">
                                    <i class="fa fa-download"></i> Download Document
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div style="background: #fff3cd; border: 2px dashed #ffc107; padding: 2.5rem; border-radius: 12px; text-align: center;">
                    <i class="fa fa-exclamation-triangle" style="font-size: 3rem; color: #ff9800; margin-bottom: 1rem;"></i>
                    <p style="color: #856404; font-size: 1.15rem; font-weight: 600; margin: 0;">No document file available for this policy.</p>
                </div>
            @endif
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
@extends('layouts.learning')

@section('title', 'My Certificates - Whence Learn')

@section('content')
<div class="page-header">
    <h1>My Certificates</h1>
    <p>View and download your earned certificates</p>
</div>

<!-- Certificates Grid -->
@if(count($certificates) > 0)
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px;">
    @foreach($certificates as $certificate)
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); transition: transform 0.3s ease;">
        <!-- Certificate Header -->
        <div style="background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); padding: 30px; text-align: center; color: white;">
            <i class="fa fa-certificate" style="font-size: 64px; margin-bottom: 15px;"></i>
            <h2 style="font-size: 24px; font-weight: 700; margin: 0;">Certificate of Completion</h2>
        </div>
        
        <!-- Certificate Body -->
        <div style="padding: 30px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0 0 10px;">This certifies that</p>
                <h3 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin: 0 0 20px;">
                    {{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}
                </h3>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">has successfully completed</p>
            </div>
            
            <div style="background: var(--light-bg); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h4 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin: 0 0 10px;">
                    {{ $certificate['course_name'] }}
                </h4>
                <div style="display: flex; justify-content: space-between; color: var(--text-secondary); font-size: 14px;">
                    <span><i class="fa fa-calendar"></i> {{ date('F j, Y', strtotime($certificate['issue_date'])) }}</span>
                    <span><i class="fa fa-hashtag"></i> {{ $certificate['certificate_id'] }}</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 10px;">
                <button style="flex: 1; padding: 12px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
                    <i class="fa fa-download"></i> Download
                </button>
                <button style="flex: 1; padding: 12px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.3s;">
                    <i class="fa fa-share-alt"></i> Share
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<!-- Empty State -->
<div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
    <i class="fa fa-certificate" style="font-size: 64px; color: var(--text-secondary); margin-bottom: 20px;"></i>
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 15px; color: var(--text-primary);">
        No Certificates Yet
    </h2>
    <p style="color: var(--text-secondary); font-size: 16px; max-width: 600px; margin: 0 auto 30px;">
        Complete your first course to earn your certificate. Keep learning and achieve your goals!
    </p>
    <a href="{{ url('learning/courses') }}" style="display: inline-block; background: var(--primary-color); color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        Browse Courses
    </a>
</div>
@endif

<!-- Certificate Info -->
<div style="margin-top: 40px; background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow);">
    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">
        <i class="fa fa-info-circle" style="color: var(--primary-color); margin-right: 10px;"></i>
        About Certificates
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div>
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">Verification</h4>
            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
                Each certificate includes a unique ID that can be verified by employers or educational institutions.
            </p>
        </div>
        <div>
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">Digital Format</h4>
            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
                Certificates are available in high-resolution PDF format suitable for printing and digital sharing.
            </p>
        </div>
        <div>
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">LinkedIn Integration</h4>
            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.6;">
                Easily add your certificates to your LinkedIn profile to showcase your achievements.
            </p>
        </div>
    </div>
</div>
@endsection

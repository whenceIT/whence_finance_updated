@extends('layouts.learning')

@section('title', $material->title . ' - Whence Learn')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ url('/learning') }}" style="color: var(--primary-color); font-size: 24px;">
            <i class="fa fa-arrow-left"></i>
        </a>
        <div>
            <h1>{{ $material->title }}</h1>
            <p>{{ $material->description }}</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Course Content -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow);">
            <div class="panel-body" style="padding: 30px;">
                <!-- Material Preview -->
                <div style="background: var(--light-bg); border-radius: 8px; padding: 40px; text-align: center; margin-bottom: 24px;">
                    @if($material->material_type == 'video')
                        <i class="fa fa-play-circle" style="font-size: 64px; color: var(--primary-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">Video Content</p>
                    @elseif($material->material_type == 'document')
                        <i class="fa fa-file-pdf-o" style="font-size: 64px; color: var(--accent-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">Document</p>
                    @elseif($material->material_type == 'link')
                        <i class="fa fa-link" style="font-size: 64px; color: var(--secondary-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">External Link</p>
                    @else
                        <i class="fa fa-book" style="font-size: 64px; color: var(--primary-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">Training Material</p>
                    @endif
                </div>

                <!-- Material Details -->
                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px;">About This Course</h3>
                    <p style="color: var(--text-secondary); line-height: 1.8;">
                        {{ $material->description }}
                    </p>
                </div>

                <!-- Course Meta -->
                <div style="display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-clock-o" style="color: var(--primary-color);"></i>
                        <span>{{ $material->human_duration ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-folder" style="color: var(--primary-color);"></i>
                        <span>{{ $material->category ?? 'General' }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-building" style="color: var(--primary-color);"></i>
                        <span>{{ $material->department ?? 'All Departments' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Sidebar -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow);">
            <div class="panel-body" style="padding: 24px;">
                @if($isEnrolled)
                <!-- Enrolled User View -->
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Your Progress</h3>
                
                <!-- Progress Circle -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: conic-gradient(var(--secondary-color) {{ $progress }}%, var(--light-bg) 0); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                            <span style="font-size: 28px; font-weight: 700; color: var(--text-primary);">{{ $progress }}%</span>
                            <span style="font-size: 11px; color: var(--text-secondary);">Complete</span>
                        </div>
                    </div>
                </div>

                <!-- Download Materials Section -->
                @if(isset($material->file_path) && $material->file_path)
                <div style="margin-bottom: 12px;">
                    <a href="{{ asset($material->file_path) }}" class="btn btn-primary btn-block" style="margin-bottom: 12px;" target="_blank">
                        <i class="fa fa-download"></i> Download Material
                    </a>
                </div>
                @else
                <div style="text-align: center; padding: 20px; background: var(--light-bg); border-radius: 8px; margin-bottom: 16px;">
                    <i class="fa fa-info-circle" style="color: var(--text-secondary); font-size: 24px; margin-bottom: 8px;"></i>
                    <p style="color: var(--text-secondary); font-size: 13px; margin: 0;">No downloadable materials available</p>
                </div>
                @endif

                <!-- Classroom Button -->
                <a href="{{ url('/learning/course/' . $material->id . '/classroom') }}" class="btn btn-success btn-block" style="margin-bottom: 12px;">
                    <i class="fa fa-chalkboard-teacher"></i> Open Classroom
                </a>

                <button class="btn btn-default btn-block" onclick="markAsComplete()">
                    <i class="fa fa-check-circle"></i> Mark as Complete
                </button>
                @else
                <!-- Non-Enrolled User View -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <i class="fa fa-graduation-cap" style="font-size: 48px; color: var(--primary-color); margin-bottom: 16px;"></i>
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">Start Learning</h3>
                    <p style="color: var(--text-secondary); font-size: 13px;">
                        Enroll now to access this course and track your progress.
                    </p>
                </div>

                <!-- Enrollment Button -->
                <button class="btn btn-primary btn-block" style="margin-bottom: 12px;" onclick="enrollInCourse({{ $material->id }}, '{{ addslashes($material->title) }}')">
                    <i class="fa fa-plus-circle"></i> Enroll in This Course
                </button>

                <!-- Alternative Options -->
                <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <p style="color: var(--text-secondary); font-size: 12px; margin-bottom: 12px;">Or browse more courses</p>
                    <a href="{{ url('/learning') }}" class="btn btn-default btn-block">
                        <i class="fa fa-search"></i> Browse Courses
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Course Info -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow); margin-top: 16px;">
            <div class="panel-body" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Course Information</h3>
                
                <div style="margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 12px;">Views</span>
                    <div style="font-weight: 600;">{{ $material->view_count ?? 0 }}</div>
                </div>
                
                <div style="margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 12px;">Downloads</span>
                    <div style="font-weight: 600;">{{ $material->download_count ?? 0 }}</div>
                </div>

                @if(isset($material->created_at))
                <div>
                    <span style="color: var(--text-secondary); font-size: 12px;">Added</span>
                    <div style="font-weight: 600;">{{ $material->created_at->format('M d, Y') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function enrollInCourse(courseId, courseTitle) {
    $.ajax({
        url: '{{ url('learning/enroll') }}/' + courseId,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message, 'Success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(response.message, 'Error');
            }
        },
        error: function(xhr) {
            var message = 'An error occurred while enrolling';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            toastr.error(message, 'Error');
        }
    });
}

function markAsComplete() {
    toastr.success('Course marked as complete!', 'Success');
}
</script>
@endsection

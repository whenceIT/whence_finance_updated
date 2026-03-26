@extends('layouts.learning')

@section('title', $material->title . ' - Whence Learn')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1; min-width: 0;">
            <a href="{{ url('/learning') }}" style="color: var(--primary-color); font-size: 24px;">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div style="min-width: 0;">
                <h1 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $material->title }}</h1>
                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">by {{ $material->creator->first_name }} {{ $material->creator->last_name }}</p>
            </div>
        </div>
        
        <!-- Trainer Info Card in Header -->
        @if($material->creator)
        <div class="trainer-header-card" style="background: white; border-radius: 12px; box-shadow: var(--shadow); padding: 12px 16px; display: flex; align-items: center; gap: 12px; min-width: 200px; flex-shrink: 0;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; flex-shrink: 0;">
                <i class="fa fa-user"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 600; font-size: 13px; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $material->creator->first_name }} {{ $material->creator->last_name }}
                </div>
                @if($material->creator->roles && $material->creator->roles->first()) 
                <div style="font-size: 11px; color: var(--primary-color); font-weight: 500;">
                    {{ $material->creator->roles->first()->name }} (Instructor)
                </div>
                @endif
                <div style="font-size: 10px; color: var(--text-secondary); margin-top: 2px;">
                    <i class="fa fa-clock-o"></i> Since {{ $material->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
@media (max-width: 768px) {
    .page-header > div {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .trainer-header-card {
        min-width: auto !important;
        margin-left: 40px !important;
    }
    .page-header h1 {
        font-size: 18px !important;
    }
    .page-header p {
        font-size: 12px !important;
    }
}
</style>

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

                <!-- Topic View Count - Large and Sleek -->
                @php
                    $totalTopicViews = $material->allTopics ? $material->allTopics->sum('view_count') : 0;
                @endphp
                <div class="topic-views">
                    <i class="fa fa-eye"></i>
                    <span>{{ \App\Helpers\GeneralHelper::calculate_view_percentage($totalTopicViews) }}% views</span>
                </div>
                <div style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border-radius: 16px; padding: 32px; margin-bottom: 24px; text-align: center; color: white; box-shadow: 0 8px 32px rgba(74, 144, 226, 0.3);">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 16px; margin-bottom: 8px;">
                        <i class="fa fa-eye" style="font-size: 32px; opacity: 0.9;"></i>
                        <div style="font-size: 48px; font-weight: 700; line-height: 1;">{{ number_format($totalTopicViews) }}</div>
                    </div>
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 300;">Topic Views</div>
                    <div style="font-size: 12px; opacity: 0.7; margin-top: 4px;">Total engagement across all course topics</div>
                </div>
            </div>
        </div>

        <!-- Enrolled Users -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow); margin-top: 24px;">
            <div class="panel-body" style="padding: 30px;">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Enrolled Users ({{ count($enrolledUsers) }})</h3>
                
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Enrolled</th>
                                <th>Progress</th>
                                <th>Quiz Attempts</th>
                                <th>Average Score</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrolledUsers as $enrolledUser)
                                <tr>
                                    <td>{{ $enrolledUser['name'] }}</td>
                                    <td>{{ $enrolledUser['email'] }}</td>
                                    <td>{{ $enrolledUser['enrolled_at']->format('M d, Y') }}</td>
                                    <td>{{ $enrolledUser['progress'] }}%</td>
                                    <td>
                                        {{ count(array_filter($enrolledUser['quiz_stats'], function($stat) { return $stat['attempted']; })) }}/{{ count($enrolledUser['quiz_stats']) }}
                                    </td>
                                    <td>
                                        @php
                                            $scores = array_filter(array_column($enrolledUser['quiz_stats'], 'score'));
                                            $average = count($scores) > 0 ? round(array_sum($scores) / count($scores)) : 0;
                                        @endphp
                                        {{ $average }}%
                                    </td>
                                    <td>
                                        @if($enrolledUser['completed_all_quizzes'])
                                            <span class="label label-success">All Quizzes Completed</span>
                                        @else
                                            <span class="label label-warning">In Progress</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($enrolledUser['completed_all_quizzes'] || $isAdmin)
                                            <a href="{{ url('/learning/course/' . $material->id . '/certificate/' . $enrolledUser['id']) }}" 
                                               class="btn btn-sm btn-primary" 
                                               target="_blank">
                                                <i class="fa fa-certificate"></i> Certificate
                                            </a>
                                        @else
                                            <span class="text-muted">Complete quizzes first</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quiz Statistics -->
         
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow); margin-top: 24px;">
            <div class="panel-body" style="padding: 30px;">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Quiz Statistics</h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="background: var(--light-bg); padding: 20px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: var(--primary-color);">{{ $quizStats['topics_with_quizzes'] }}</div>
                        <div style="color: var(--text-secondary); font-size: 14px;">Topics with Quizzes</div>
                    </div>
                    
                    <div style="background: var(--light-bg); padding: 20px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: var(--primary-color);">{{ $quizStats['total_attempts'] }}</div>
                        <div style="color: var(--text-secondary); font-size: 14px;">Total Attempts</div>
                    </div>
                    
                    <div style="background: var(--light-bg); padding: 20px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: var(--success-color);">{{ $quizStats['passed_attempts'] }}</div>
                        <div style="color: var(--text-secondary); font-size: 14px;">Passed Attempts</div>
                    </div>
                    
                    <div style="background: var(--light-bg); padding: 20px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: var(--primary-color);">{{ $quizStats['average_score'] }}%</div>
                        <div style="color: var(--text-secondary); font-size: 14px;">Average Score</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Sidebar -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow);">
            <div class="panel-body" style="padding: 24px;">
                @if($isEnrolled || $isAdmin)
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

                    <!-- Certificate Button -->
                    @if($userCompletedAllQuizzes || $isAdmin)
                        <a href="{{ url('/learning/course/' . $material->id . '/certificate') }}" 
                           class="btn btn-primary btn-block" 
                           style="margin-bottom: 12px;"
                           target="_blank">
                            <i class="fa fa-certificate"></i> Generate Certificate
                        </a>
                        
                        @if($isAdmin && !$userCompletedAllQuizzes)
                            <p style="color: var(--text-secondary); font-size: 12px; text-align: center; margin-top: 8px;">
                                Preview certificate (Admin only)
                            </p>
                        @endif
                    @else
                        <div style="text-align: center; padding: 20px; background: var(--light-bg); border-radius: 8px; margin-bottom: 16px;">
                            <i class="fa fa-info-circle" style="color: var(--text-secondary); font-size: 24px; margin-bottom: 8px;"></i>
                            <p style="color: var(--text-secondary); font-size: 13px; margin: 0;">Complete all topic quizzes to generate certificate</p>
                        </div>
                    @endif

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
                
                @else
                    <!-- Non-Enrolled User View -->
                    <div style="text-align: center; margin-bottom: 24px;">
                        <i class="fa fa-graduation-cap" style="font-size: 48px; color: var(--primary-color); margin-bottom: 16px;"></i>
                        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">Start Learning</h3>
                        <p style="color: var(--text-secondary); font-size: 13px;">
                            Enroll now to access this course and track your progress.
                        </p>
                    </div>

                    <!-- Enrollment Form -->
                    <form action="{{ url('learning/enroll') }}/{{ $material->id }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block" style="margin-bottom: 12px;">
                            <i class="fa fa-plus-circle"></i> Enroll in This Course
                        </button>
                    </form>

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
                    <div style="font-weight: 600;">{{ $totalTopicViews ?? 0 }}</div>
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
function markAsComplete() {
    toastr.success('Course marked as complete!', 'Success');
}
</script>
@endsection

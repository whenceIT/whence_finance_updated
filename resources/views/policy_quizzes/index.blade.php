@extends('layouts.master')

@section('content')
@php
use App\Models\PolicyQuizAttempt;
use Illuminate\Support\Facades\Auth;
@endphp
<div class="content-wrapper">
    <section class="content-header">
        <h1>Policy Quizzes</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Available Quizzes</h3>
                    </div>
                    <div class="box-body">
                        @if($quizzes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Questions</th>
                                            <th>Time Limit</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quizzes as $quiz)
                                            @php
                                                $attempt = $activeAttempts[$quiz->id] ?? null;
                                                $completed = PolicyQuizAttempt::where('policy_quiz_id', $quiz->id)
                                                    ->where('user_id', Auth::id())
                                                    ->whereNotNull('completed_at')
                                                    ->exists();
                                            @endphp
                                            <tr>
                                                <td>{{ $quiz->title }}</td>
                                                <td>{{ $quiz->description ?? 'No description' }}</td>
                                                <td>{{ $quiz->max_questions }} questions</td>
                                                <td>{{ $quiz->time_limit_minutes }} minutes</td>
                                                <td>
                                                    @if($attempt)
                                                        <span class="label label-warning">In Progress</span>
                                                    @elseif($completed)
                                                        <span class="label label-success">Completed</span>
                                                    @elseif($quiz->isOpen())
                                                        <span class="label label-primary">Open</span>
                                                    @elseif($quiz->open_date > now())
                                                        <span class="label label-info">Opens {{ $quiz->open_date->format('M d, Y') }}</span>
                                                    @else
                                                        <span class="label label-default">Closed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attempt)
                                                        <a href="{{ route('policy.quizzes.question', ['id' => $quiz->id, 'question' => 1]) }}" 
                                                           class="btn btn-primary btn-sm">
                                                            <i class="fa fa-play"></i> Continue Quiz
                                                        </a>
                                                    @elseif($quiz->isOpen() && !$completed)
                                                        <a href="{{ route('policy.quizzes.start', $quiz->id) }}" 
                                                           class="btn btn-success btn-sm">
                                                            <i class="fa fa-play"></i> Start Quiz
                                                        </a>
                                                    @elseif($completed)
                                                        <a href="{{ route('policy.quizzes.results', $quiz->id) }}" 
                                                           class="btn btn-info btn-sm">
                                                            <i class="fa fa-eye"></i> View Results
                                                        </a>
                                                    @else
                                                        <button class="btn btn-default btn-sm" disabled>
                                                            Not Available
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No quizzes are currently available.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
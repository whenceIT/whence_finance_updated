<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\QuizAttempt;
use App\Models\CourseTopic;
use App\Models\TrainingMaterial;
use App\Models\Enrollment;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display trainer quiz management page.
     */
    public function index()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);
        $isTrainer = $user->istrainer == 1;

        if (!$isAdmin && !$isTrainer) {
            return redirect()->route('learning.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this page.');
        }

        // Get all topics with their quizzes
        $topics = CourseTopic::with('quiz')
            ->whereHas('trainingMaterial')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('learning.quizzes.index', compact('topics'));
    }

    /**
     * Show form to create/edit quiz for a topic.
     */
    public function manage($topicId)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);
        $isTrainer = $user->istrainer == 1;

        if (!$isAdmin && !$isTrainer) {
            return redirect()->route('learning.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission.');
        }

        $topic = CourseTopic::with('quiz.questions.options')->findOrFail($topicId);
        $quiz = $topic->quiz;

        return view('learning.quizzes.manage', compact('topic', 'quiz'));
    }

    /**
     * Store or update quiz.
     */
    public function save(Request $request, $topicId)
    {
        if (!Sentinel::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = Sentinel::getUser();
        $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);
        $isTrainer = $user->istrainer == 1;

        if (!$isAdmin && !$isTrainer) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $topic = CourseTopic::findOrFail($topicId);

        DB::transaction(function () use ($request, $topic) {
            // Create or update quiz
            $quiz = Quiz::updateOrCreate(
                ['course_topic_id' => $topic->id],
                [
                    'title' => $request->quiz_title,
                    'description' => $request->quiz_description,
                    'passing_score' => $request->passing_score ?? 70,
                    'time_limit' => $request->time_limit,
                    'is_active' => true,
                ]
            );

            // Delete existing questions and options
            QuizQuestion::where('quiz_id', $quiz->id)->delete();

            // Add new questions
            if ($request->has('questions')) {
                foreach ($request->questions as $qIndex => $questionData) {
                    $question = QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question' => $questionData['text'],
                        'question_type' => $questionData['type'] ?? 'multiple_choice',
                        'sort_order' => $qIndex,
                        'points' => $questionData['points'] ?? 1,
                        'explanation' => $questionData['explanation'] ?? null,
                    ]);

                    // Add options for multiple choice
                    if (isset($questionData['options']) && is_array($questionData['options'])) {
                        foreach ($questionData['options'] as $oIndex => $optionText) {
                            QuizOption::create([
                                'quiz_question_id' => $question->id,
                                'option_text' => $optionText,
                                'is_correct' => isset($questionData['correct_option']) && $questionData['correct_option'] == $oIndex,
                                'sort_order' => $oIndex,
                            ]);
                        }
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz saved successfully!'
        ]);
    }

    /**
     * Show quiz for student to take.
     */
    public function take($quizId)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $quiz = Quiz::with('questions.options', 'topic.trainingMaterial')->findOrFail($quizId);

        if (!$quiz->is_active) {
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'This quiz is not currently available.');
        }

        // Check if user has completed the topic
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $quiz->topic->trainingMaterial->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('learning.course', $quiz->topic->trainingMaterial->id)
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'Please enroll in this course first.');
        }

        $completedTopics = $enrollment->completed_topics ?? [];
        if (!in_array($quiz->topic->id, $completedTopics)) {
            return redirect()->route('learning.classroom', $quiz->topic->trainingMaterial->id)
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'Please complete the topic first before taking the quiz.');
        }

        // Get previous attempts
        $attempts = QuizAttempt::where('quiz_id', $quizId)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $passed = $attempts->where('passed', true)->first();

        return view('learning.quizzes.take', compact('quiz', 'attempts', 'passed'));
    }

    /**
     * Submit quiz answers.
     */
    public function submit(Request $request, $quizId)
    {
        if (!Sentinel::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = Sentinel::getUser();
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);

        $answers = $request->answers ?? [];
        $score = 0;
        $totalPoints = 0;
        $results = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $selectedOption = $answers[$question->id] ?? null;
            $isCorrect = false;

            if ($selectedOption) {
                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption && $correctOption->id == $selectedOption) {
                    $isCorrect = true;
                    $score += $question->points;
                }
            }

            $results[$question->id] = [
                'correct' => $isCorrect,
                'selected_option' => $selectedOption,
                'correct_option' => $question->options->where('is_correct', true)->first()?->id,
            ];
        }

        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100) : 0;
        $passed = $percentage >= $quiz->passing_score;

        // Create attempt record
        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'user_id' => $user->id,
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'passed' => $passed,
            'started_at' => now()->subMinutes($quiz->time_limit ?? 30),
            'completed_at' => now(),
            'answers' => $results,
        ]);

        return response()->json([
            'success' => true,
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'passed' => $passed,
            'passing_score' => $quiz->passing_score,
            'results' => $results,
            'attempt_id' => $attempt->id,
        ]);
    }

    /**
     * Delete quiz.
     */
    public function delete($quizId)
    {
        if (!Sentinel::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = Sentinel::getUser();
        $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);
        $isTrainer = $user->istrainer == 1;

        if (!$isAdmin && !$isTrainer) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $quiz = Quiz::findOrFail($quizId);
        $quiz->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quiz deleted successfully!'
        ]);
    }
}

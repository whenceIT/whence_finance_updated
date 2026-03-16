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
        try {
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
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error loading quiz manage page: ' . $e->getMessage(), [
                'topic_id' => $topicId,
                'user_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null
            ]);
            return redirect()->route('learning.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Unable to load quiz. Please try again later.');
        } catch (\Illuminate\Routing\Exceptions\UrlNotFoundException $e) {
            \Log::error('Topic not found for quiz manage: ' . $e->getMessage(), [
                'topic_id' => $topicId
            ]);
            return redirect()->route('learning.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Topic not found.');
        } catch (\Throwable $e) {
            \Log::error('Error loading quiz manage page: ' . $e->getMessage(), [
                'topic_id' => $topicId,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('learning.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'An error occurred while loading the quiz. Please try again.');
        }
    }

    /**
     * Store or update quiz.
     */
    public function save(Request $request, $topicId)
    {
        try {
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

            // Validate request
            $request->validate([
                'quiz_title' => 'required|string|max:255',
                'passing_score' => 'required|integer|min:0|max:100',
                'questions' => 'required|array|min:1',
            ], [
                'quiz_title.required' => 'Quiz title is required.',
                'passing_score.required' => 'Passing score is required.',
                'questions.required' => 'At least one question is required.',
                'questions.min' => 'At least one question is required.',
            ]);

            // Validate each question has options
            foreach ($request->questions as $index => $question) {
                if (empty($question['options']) || count($question['options']) < 2) {
                    return redirect()->back()
                        ->with('toastr_type', 'error')
                        ->with('toastr_message', 'Question ' . ($index + 1) . ' must have at least 2 options.')
                        ->withInput();
                }
                if (!isset($question['correct_option'])) {
                    return redirect()->back()
                        ->with('toastr_type', 'error')
                        ->with('toastr_message', 'Please select the correct answer for Question ' . ($index + 1) . '.')
                        ->withInput();
                }
            }

            $topic = CourseTopic::findOrFail($topicId);

            DB::transaction(function () use ($request, $topic, $user) {
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

                \Log::info('Quiz saved', ['quiz_id' => $quiz->id, 'topic_id' => $topic->id, 'user_id' => $user->id]);

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

            return redirect()->route('learning.training-materials.topics', ['materialId' => $topic->trainingMaterial->id])
                ->with('toastr_type', 'success')
                ->with('toastr_message', 'Quiz saved successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Please fill in all required fields correctly.')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error saving quiz: ' . $e->getMessage(), [
                'topic_id' => $topicId,
                'user_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null,
                'request' => $request->except(['_token'])
            ]);
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Unable to save quiz. Please try again later.')
                ->withInput();
        } catch (\Throwable $th) {
            \Log::error('Error saving quiz: ' . $th->getMessage(), [
                'topic_id' => $topicId,
                'user_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null,
                'trace' => $th->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'An error occurred while saving the quiz. Please try again.')
                ->withInput();
        }
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
        $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);
        $quiz = Quiz::with('questions.options', 'topic.trainingMaterial')->findOrFail($quizId);

        if (!$quiz->is_active) {
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'This quiz is not currently available.');
        }

        // Check if user has completed the topic (skip check for admins)
        if (!$isAdmin) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('training_material_id', $quiz->topic->trainingMaterial->id)
                ->first();

            if (!$enrollment) {
                return redirect()->route('learning.course', $quiz->topic->trainingMaterial->id)
                    ->with('toastr_type', 'warning')
                    ->with('toastr_message', 'Please enroll in this course first.');
            }

            $completedTopics = $enrollment->completed_topics ?? [];
            // if (!in_array($quiz->topic->id, $completedTopics)) {
            //     return redirect()->route('learning.classroom', $quiz->topic->trainingMaterial->id)
            //         ->with('toastr_type', 'warning')
            //         ->with('toastr_message', 'Please complete the topic first before taking the quiz.');
            // }
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
        try {
            if (!Sentinel::check()) {
                return redirect('login');
            }

            $user = Sentinel::getUser();
            $quiz = Quiz::with('questions.options', 'topic.trainingMaterial')->findOrFail($quizId);

            // Get answers from request
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

             // Generate PDF
             $pdf = \PDF::loadView('learning.quizzes.results-pdf', compact('quiz', 'score', 'totalPoints', 'percentage', 'passed', 'results', 'attempt'));
             
             // Return PDF for download
             $filename = 'quiz-results-' . $quiz->id . '-' . $attempt->id . '.pdf';
             return $pdf->download($filename);
            
        } catch (\Throwable $th) {
            \Log::error('Quiz submission error: ' . $th->getMessage(), ['quiz_id' => $quizId, 'user_id' => $user->id ?? null]);
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'An error occurred while processing your quiz submission');
        }
    }

    /**
     * Submit quiz from preview (returns JSON).
     */
    public function submitPreview(Request $request, $quizId)
    {
        try {
            if (!Sentinel::check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $user = Sentinel::getUser();
            $quiz = Quiz::with('questions.options', 'topic.trainingMaterial')->findOrFail($quizId);

            // Get answers from request
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

            \Log::info('Quiz preview attempt saved', ['quiz_id' => $quizId, 'attempt_id' => $attempt->id, 'user_id' => $user->id, 'score' => $score, 'total_points' => $totalPoints, 'percentage' => $percentage, 'passed' => $passed]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz preview completed!',
                'result' => [
                    'score' => $score,
                    'total_points' => $totalPoints,
                    'percentage' => $percentage,
                    'passing_score' => $quiz->passing_score,
                    'passed' => $passed,
                    'results' => $results,
                ]
            ]);

        } catch (\Throwable $th) {
            \Log::error('Quiz preview submission error: ' . $th->getMessage(), ['quiz_id' => $quizId, 'user_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your quiz submission'
            ], 500);
        }
    }

    /**
     * Delete quiz.
     */
    public function delete($quizId)
    {
        try {
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

            \Log::info('Quiz deleted', ['quiz_id' => $quizId, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz deleted successfully!'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error deleting quiz: ' . $e->getMessage(), [
                'quiz_id' => $quizId,
                'user_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete quiz. Please try again later.'
            ], 500);
        } catch (\Illuminate\Routing\Exceptions\UrlNotFoundException $e) {
            \Log::error('Quiz not found for deletion: ' . $e->getMessage(), [
                'quiz_id' => $quizId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Quiz not found.'
            ], 404);
        } catch (\Throwable $e) {
            \Log::error('Error deleting quiz: ' . $e->getMessage(), [
                'quiz_id' => $quizId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the quiz.'
            ], 500);
        }
    }
    
    /**
     * Get quiz questions for preview (JSON API).
     */
    public function getQuestions($quizId)
    {
        try {
            if (!Sentinel::check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $user = Sentinel::getUser();
            $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);
            $isTrainer = $user->istrainer == 1;

            if (!$isAdmin && !$isTrainer) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $quiz = Quiz::with('questions.options')->findOrFail($quizId);
            
            $questions = $quiz->questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'question_type' => $question->question_type,
                    'points' => $question->points,
                    'explanation' => $question->explanation,
                    'options' => $question->options->map(function($option) {
                        return [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                            'is_correct' => (bool) $option->is_correct,
                            'sort_order' => $option->sort_order,
                        ];
                    })->toArray(),
                    'correct_answer' => $question->options->where('is_correct', true)->first()?->option_text,
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'quiz' => [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'passing_score' => $quiz->passing_score,
                    'time_limit' => $quiz->time_limit,
                ],
                'questions' => $questions,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error getting quiz questions: ' . $e->getMessage(), [
                'quiz_id' => $quizId,
                'user_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to load quiz questions. Please try again later.'
            ], 500);
        } catch (\Illuminate\Routing\Exceptions\UrlNotFoundException $e) {
            \Log::error('Quiz not found: ' . $e->getMessage(), [
                'quiz_id' => $quizId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Quiz not found.'
            ], 404);
        } catch (\Throwable $e) {
            \Log::error('Error getting quiz questions: ' . $e->getMessage(), [
                'quiz_id' => $quizId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading the quiz.'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PolicyQuiz;
use App\Models\PolicyQuizAttempt;
use App\Models\PolicyQuizQuestion;
use App\Models\PolicyQuizUserAnswer;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class PolicyQuizController extends Controller
{
    /**
     * Display a listing of policy quizzes for users
     */
    public function index()
    {
        $quizzes = PolicyQuiz::where('active', true)
            ->where('close_date', '>=', now())
            ->orderBy('open_date', 'asc')
            ->get();

        // Check for any active attempts
        $activeAttempts = PolicyQuizAttempt::where('user_id', Sentinel::getUser()->id)
            ->whereNull('completed_at')
            ->get()
            ->keyBy('policy_quiz_id');

        return view('policy_quizzes.index', compact('quizzes', 'activeAttempts'));
    }

    /**
     * Start a new quiz attempt
     */
    public function start($id)
    {
        $quiz = PolicyQuiz::findOrFail($id);
        $user = Sentinel::getUser()->id;
        $isRetake = request('retake');

        if (!$isRetake && !$quiz->isOpen()) {
            return redirect()->route('policy.quizzes.index')
                ->with('error', 'This quiz is not currently available.');
        }

        $questions = $quiz->getActiveQuestions();
        if ($questions->isEmpty()) {
            return redirect()->route('policy.quizzes.index')
                ->with('error', 'This quiz has no questions available.');
        }

        $existingAttempt = PolicyQuizAttempt::where('policy_quiz_id', $id)
            ->where('user_id', $user)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->completed_at) {
                if ($isRetake) {
                    $existingAttempt->delete();
                    session()->forget('quiz_questions_' . $existingAttempt->id);
                    session(['policy_quiz_retake_' . $id => true]);
                } else {
                    return redirect()->route('policy.quizzes.results', $id)
                        ->with('error', 'You have already completed this quiz.');
                }
            } else {
                return redirect()->route('policy.quizzes.question', ['id' => $id, 'question' => 1]);
            }
        }

        try {
            $attempt = PolicyQuizAttempt::create([
                'policy_quiz_id' => $quiz->id,
                'user_id' => $user,
                'started_at' => now(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('policy.quizzes.index')
                ->with('error', 'Unable to start quiz. You may have already taken this quiz.');
        }

        return redirect()->route('policy.quizzes.question', ['id' => $id, 'question' => 1])
            ->with('success', 'Quiz started! You have ' . $quiz->time_limit_minutes . ' minutes to complete.');
    }


    /**
     * Display a single question
     */
    public function question($id, $question)
    {
        $quiz = PolicyQuiz::findOrFail($id);
        $user = Sentinel::getUser()->id;
        $attempt = PolicyQuizAttempt::where('policy_quiz_id', $id)
            ->where('user_id', $user)
            ->whereNull('completed_at')
            ->first();

        if (!$attempt) {
            return redirect()->route('policy.quizzes.start', $id)
                ->with('error', 'Please start the quiz before answering questions.');
        }

        $isRetake = session('policy_quiz_retake_' . $id);

        if (!$isRetake && $attempt->isTimeExpired()) {
            $attempt->calculateScore();
            return redirect()->route('policy.quizzes.results', $id)
                ->with('error', 'Quiz has expired');
        }

        if ($isRetake) {
            session()->forget('policy_quiz_retake_' . $id);
        }

        $questions = session('quiz_questions_' . $attempt->id);
        if (!$questions) {
            $questions = $quiz->getActiveQuestions();
            session(['quiz_questions_' . $attempt->id => $questions]);
        }

        if ($questions->isEmpty()) {
            $attempt->calculateScore();
            return redirect()->route('policy.quizzes.results', $id)
                ->with('error', 'This quiz has no questions available.');
        }

        if ($question < 1 || $question > count($questions)) {
            return redirect()->route('policy.quizzes.question', ['id' => $id, 'question' => 1]);
        }

        $currentQuestion = $questions[$question - 1];
        $totalQuestions = count($questions);
        $currentQuestionIndex = $question - 1;

        $userAnswer = $attempt->answers()->where('question_id', $currentQuestion->id)->first();

        $remainingSeconds = $attempt->getRemainingTimeSeconds();

        return view('policy_quizzes.question', compact(
            'quiz',
            'attempt',
            'questions',
            'currentQuestionIndex',
            'currentQuestion',
            'totalQuestions',
            'userAnswer',
            'remainingSeconds'
        ));
    }

    /**
     * Save user's answer
     */
    public function answer(Request $request, $id)
    {
        $request->validate([
            'question_id' => 'required|exists:policy_quiz_questions,id',
            'answer' => 'required|in:A,B,C,D',
        ]);

        $quiz = PolicyQuiz::findOrFail($id);
        $attempt = PolicyQuizAttempt::where('policy_quiz_id', $id)
            ->where('user_id', Sentinel::getUser()->id)
            ->whereNull('completed_at')
            ->first();

        if (!$attempt) {
            return redirect()->route('policy.quizzes.start', $id);
        }

        if ($attempt->isTimeExpired()) {
            $attempt->calculateScore();
            return redirect()->route('policy.quizzes.results', $id);
        }

        $question = PolicyQuizQuestion::find($request->question_id);
        $isCorrect = $question->isCorrectAnswer($request->answer);

        PolicyQuizUserAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'selected_answer' => strtoupper($request->answer),
                'is_correct' => $isCorrect,
                'answered_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'is_correct' => $isCorrect]);
    }

    /**
     * Submit quiz attempt
     */
    public function submit($id)
    {
        try {
            $quiz = PolicyQuiz::findOrFail($id);
            $attempt = PolicyQuizAttempt::where('policy_quiz_id', $id)
                ->where('user_id', Sentinel::getUser()->id)
                ->whereNull('completed_at')
                ->first();

            if (!$attempt) {
                return redirect()->route('policy.quizzes.index');
            }

            $attempt->calculateScore();

            // Clear session cache
            session()->forget('quiz_questions_' . $attempt->id);

            return redirect()->route('policy.quizzes.results', $id);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Display quiz results
     */
    public function results($id)
    {
        $quiz = PolicyQuiz::findOrFail($id);
        $attempt = PolicyQuizAttempt::where('policy_quiz_id', $id)
            ->where('user_id', Sentinel::getUser()->id)
            ->latest()
            ->first();

        if (!$attempt) {
            return redirect()->route('policy.quizzes.index')
                ->with('error', 'Quiz results not found or quiz expired timeout.');
        }

        $answers = $attempt->answers()->with('question')->get();
        $incorrectAnswers = $answers->where('is_correct', false);

        return view('policy_quizzes.results', compact('quiz', 'attempt', 'answers', 'incorrectAnswers'));
    }

    /**
     * ADMIN SECTION
     */

    /**
     * Display admin dashboard
     */
    public function adminIndex()
    {
        $quizzes = PolicyQuiz::orderBy('created_at', 'desc')->get();
        return view('policy_quizzes.admin.index', compact('quizzes'));
    }

    /**
     * Show form to create new quiz
     */
    public function create()
    {
        return view('policy_quizzes.admin.create');
    }

    /**
     * Store new quiz
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_threshold' => 'required|integer|min:1|max:100',
            'time_limit_minutes' => 'required|integer|min:1|max:180',
            'max_questions' => 'required|integer|min:1|max:50',
            'open_date' => 'required|date',
            'close_date' => 'required|date|after:open_date',
        ]);

        $quiz = PolicyQuiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'passing_threshold' => $request->passing_threshold,
            'time_limit_minutes' => $request->time_limit_minutes,
            'max_questions' => $request->max_questions,
            'open_date' => $request->open_date,
            'close_date' => $request->close_date,
            'created_by' => Sentinel::getUser()->id,
        ]);

        return redirect()->route('admin.policy-quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    /**
     * Show form to edit quiz
     */
    public function edit($id)
    {
        $quiz = PolicyQuiz::findOrFail($id);
        return view('policy_quizzes.admin.edit', compact('quiz'));
    }

    /**
     * Update quiz
     */
    public function update(Request $request, $id)
    {
        $quiz = PolicyQuiz::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_threshold' => 'required|integer|min:1|max:100',
            'time_limit_minutes' => 'required|integer|min:1|max:180',
            'max_questions' => 'required|integer|min:1|max:50',
            'open_date' => 'required|date',
            'close_date' => 'required|date|after:open_date',
            'active' => 'boolean',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'passing_threshold' => $request->passing_threshold,
            'time_limit_minutes' => $request->time_limit_minutes,
            'max_questions' => $request->max_questions,
            'open_date' => $request->open_date,
            'close_date' => $request->close_date,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.policy-quizzes.index')
            ->with('success', 'Quiz updated successfully.');
    }

    /**
     * Show CSV upload form
     */
    public function upload($id)
    {
        $quiz = PolicyQuiz::findOrFail($id);
        return view('policy_quizzes.admin.upload', compact('quiz'));
    }

    /**
     * Process CSV upload
     */
    public function uploadQuestions(Request $request, $id)
    {
        $quiz = PolicyQuiz::findOrFail($id);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $path = $file->store('temp');

        try {
            $csv = Reader::createFromPath(storage_path('app/' . $path), 'r');
            $csv->setHeaderOffset(0);
            
            $questions = [];
            foreach ($csv as $row) {
                // Validate row
                if (empty($row['Question']) || empty($row['Option A']) || empty($row['Option B']) || 
                    empty($row['Option C']) || empty($row['Option D']) || empty($row['Correct Answer'])) {
                    continue;
                }

                $correctAnswer = strtoupper(trim($row['Correct Answer']));
                if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                    continue;
                }

                $questions[] = [
                    'policy_quiz_id' => $quiz->id,
                    'question_text' => $row['Question'],
                    'option_a' => $row['Option A'],
                    'option_b' => $row['Option B'],
                    'option_c' => $row['Option C'],
                    'option_d' => $row['Option D'],
                    'correct_answer' => $correctAnswer,
                    'policy_link' => $row['Policy Link'] ?? null,
                    'explanation' => $row['Explanation'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Delete existing questions
            $quiz->questions()->delete();
            
            // Insert new questions
            if (!empty($questions)) {
                PolicyQuizQuestion::insert($questions);
            }

            Storage::delete($path);

            return redirect()->route('admin.policy-quizzes.index')
                ->with('success', count($questions) . ' questions uploaded successfully.');

        } catch (\Exception $e) {
            Storage::delete($path);
            return redirect()->back()
                ->with('error', 'Error processing CSV file: ' . $e->getMessage());
        }
    }

    /**
     * Generate completion report
     */
    public function report($id)
    {
        $quiz = PolicyQuiz::findOrFail($id);
        $attempts = PolicyQuizAttempt::with('user')
            ->where('policy_quiz_id', $id)
            ->whereNotNull('completed_at')
            ->get();

        $csvContent = "Name,Pass/Fail,Score %,Date Taken\n";
        foreach ($attempts as $attempt) {
            $csvContent .= '"' . $attempt->user->first_name . ' ' . $attempt->user->last_name . '",';
            $csvContent .= $attempt->passed ? 'Pass' : 'Fail';
            $csvContent .= ',' . $attempt->score_percentage . '%,';
            $csvContent .= $attempt->completed_at->format('Y-m-d H:i:s') . "\n";
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="quiz_' . $quiz->id . '_completion_report.csv"',
        ];

        return response($csvContent, 200, $headers);
    }

    /**
     * View completion dashboard
     */
    public function completionDashboard($id)
    {
        $quiz = PolicyQuiz::findOrFail($id);
        
        $totalUsers = \App\Models\User::count();
        $completedAttempts = PolicyQuizAttempt::where('policy_quiz_id', $id)
            ->whereNotNull('completed_at')
            ->count();
        
        $pendingUsers = $totalUsers - $completedAttempts;

        $attempts = PolicyQuizAttempt::with('user')
            ->where('policy_quiz_id', $id)
            ->whereNotNull('completed_at')
            ->get();

        return view('policy_quizzes.admin.report', compact(
            'quiz', 
            'totalUsers',
            'completedAttempts', 
            'pendingUsers', 
            'attempts'
        ));
    }
}
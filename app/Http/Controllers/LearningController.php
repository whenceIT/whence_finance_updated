<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\TrainingMaterial;
use App\Models\CourseTopic;
use App\Models\Office;
use App\Models\User;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;

class LearningController extends Controller
{
    /**
     * Display the learning dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Sentinel::getUser();
        
        // Get all active training materials
        $allMaterials = TrainingMaterial::where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get enrolled materials for current user
        $enrollments = Enrollment::where('user_id', $user->id)->get();
        $enrolledMaterialIds = $enrollments->pluck('training_material_id')->toArray();
        $enrollmentMap = $enrollments->keyBy('training_material_id');

        // Prepare courses data with enrollment status
        $courses = $allMaterials->map(function ($material) use ($enrolledMaterialIds, $enrollmentMap) {
            $isEnrolled = in_array($material->id, $enrolledMaterialIds);
            $progress = $isEnrolled ? $enrollmentMap[$material->id]->progress : 0;
            
            return [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'category' => $material->category ?? 'General',
                'icon' => $material->icon,
                'material_type' => $material->material_type,
                'duration' => $material->human_duration,
                'file_size' => $material->human_file_size,
                'view_count' => $material->view_count,
                'download_count' => $material->download_count,
                'department' => $material->department,
                'is_featured' => $material->is_featured,
                'enrolled' => $isEnrolled,
                'progress' => $progress,
                'lessons' => 1,
                'poster' => $material->poster,
            ];
        })->toArray();

        // Calculate statistics for current user
        $stats = [
            'total_courses' => $allMaterials->count(),
            'enrolled_courses' => count($enrolledMaterialIds),
            'completed_courses' => Enrollment::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
            'total_hours' => 0,
            'in_progress' => Enrollment::where('user_id', $user->id)
                ->where('progress', '>', 0)
                ->where('progress', '<', 100)
                ->count(),
        ];

        // Get unique categories from CourseCategory model
        $categories = CourseCategory::active()->ordered()->get();

        // Share categories with all views
        view()->share('categories', $categories);

        // Get general uploads
        $uploads = \App\Models\GeneralUpload::orderBy('created_at', 'desc')->get();

        return view('learning.dashboard', compact('courses', 'stats', 'uploads'));
    }

    /**
     * Display the user's enrolled courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function courses()
    {
        $user = Sentinel::getUser();
        $selectedCategory = request()->get('category');

        // Calculate statistics for current user
        $stats = [
            'total_courses' => TrainingMaterial::where('is_active', 1)->count(),
            'enrolled_courses' => Enrollment::where('user_id', $user->id)->count(),
            'completed_courses' => Enrollment::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
            'total_hours' => 0,
            'in_progress' => Enrollment::where('user_id', $user->id)
                ->where('progress', '>', 0)
                ->where('progress', '<', 100)
                ->count(),
        ];

        // If category is specified, show all available courses in that category
        if ($selectedCategory) {
            $category = CourseCategory::where('name', $selectedCategory)->first();
            
            $query = TrainingMaterial::active();
            
            // Apply category filter
            $query->where('category', $selectedCategory);
            
            $materials = $query->orderBy('created_at', 'desc')->get();

            // Prepare courses data
            $courses = $materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'title' => $material->title,
                    'description' => $material->description,
                    'category' => $material->category ?? 'General',
                    'icon' => $material->icon,
                    'material_type' => $material->material_type,
                    'duration' => $material->human_duration,
                    'file_size' => $material->human_file_size,
                    'view_count' => $material->view_count,
                    'download_count' => $material->download_count,
                    'department' => $material->department,
                    'is_featured' => $material->is_featured,
                    'enrolled' => false,
                    'progress' => 0,
                    'enrolled_at' => null,
                    'completed_at' => null,
                    'poster' => $material->poster,
                ];
            })->toArray();

            return view('learning.courses', compact('courses', 'selectedCategory', 'category', 'stats'));
        }

        // Otherwise, show only enrolled courses (original behavior)
        $enrollments = Enrollment::with('trainingMaterial')
            ->orderBy('enrolled_at', 'desc')
            ->get();

        // Prepare courses data
        $courses = $enrollments->map(function ($enrollment) {
            $material = $enrollment->trainingMaterial;
            
            // Skip if training material doesn't exist
            if (!$material) {
                return null;
            }
            
            return [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'category' => $material->category ?? 'General',
                'icon' => $material->icon,
                'material_type' => $material->material_type,
                'duration' => $material->human_duration,
                'file_size' => $material->human_file_size,
                'view_count' => $material->view_count,
                'download_count' => $material->download_count,
                'department' => $material->department,
                'is_featured' => $material->is_featured,
                'enrolled' => true,
                'progress' => $enrollment->progress,
                'enrolled_at' => $enrollment->enrolled_at,
                'completed_at' => $enrollment->completed_at,
                'poster' => $material->poster,
            ];
        })->toArray();

        // Filter out null entries (enrollments without materials)
        $courses = array_filter($courses);
        
        return view('learning.courses', compact('courses', 'stats'));
    }

    /**
     * Display the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar()
    {
        return view('learning.calendar');
    }

    /**
     * Display the progress page.
     *
     * @return \Illuminate\Http\Response
     */
    public function progress()
    {
        $user = Sentinel::getUser();
        
        // Calculate courses completed
        $coursesCompleted = Enrollment::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();
            
        // Calculate courses in progress
        $coursesInProgress = Enrollment::where('user_id', $user->id)
            ->where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->count();
            
        // Calculate certificates earned (courses completed with all quizzes passed)
        $certificatesEarned = 0;
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['trainingMaterial.topics.quiz.attempts'])
            ->get();
            
        foreach ($enrollments as $enrollment) {
            $material = $enrollment->trainingMaterial;
            $allQuizzesPassed = true;
            
            if (!$material) {
                continue;
            }
            
            foreach ($material->topics as $topic) {
                if ($topic->quiz) {
                    $hasPassed = $topic->quiz->attempts->where('user_id', $user->id)->where('passed', true)->count() > 0;
                    if (!$hasPassed) {
                        $allQuizzesPassed = false;
                        break;
                    }
                }
            }
            
            if ($allQuizzesPassed) {
                $certificatesEarned++;
            }
        }
            
        // Calculate total lessons completed
        $totalLessonsCompleted = 0;
        $enrollments = Enrollment::where('user_id', $user->id)->get();
        
        foreach ($enrollments as $enrollment) {
            $completedTopics = $enrollment->completed_topics ?? [];
            $totalLessonsCompleted += count($completedTopics);
        }
            
        // Calculate total lessons
        $totalLessons = 0;
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('trainingMaterial.topics')
            ->get();
            
        foreach ($enrollments as $enrollment) {
            if ($enrollment->trainingMaterial) {
                $totalLessons += $enrollment->trainingMaterial->topics->count();
            }
        }

        $progressData = [
            'courses_completed' => $coursesCompleted,
            'courses_in_progress' => $coursesInProgress,
            'certificates_earned' => $certificatesEarned,
            'total_lessons_completed' => $totalLessonsCompleted,
            'total_lessons' => $totalLessons,
        ];

        return view('learning.progress', compact('progressData'));
    }

    /**
     * Display the certificates page.
     *
     * @return \Illuminate\Http\Response
     */
    public function certificates()
    {
        return view('learning.certificates');
    }

    /**
     * Display a specific course.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showCourse($id)
    {
        
        $user = Sentinel::getUser();
        $material = TrainingMaterial::with(['topics.quiz.attempts', 'creator.roles'])->findOrFail($id);
        $isEnrolled = false;
        $progress = 0;
        $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);

        // Check if user is logged in and enrolled
        if (Sentinel::check()) {
            $user = Sentinel::getUser();
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('training_material_id', $id)
                ->first();
            
            if ($enrollment) {
                $isEnrolled = true;
                $progress = $enrollment->progress;
            }
        }

        // Get all enrolled users
        $enrolledUsers = Enrollment::with('user')
            ->where('training_material_id', $id)
            ->get()
            ->map(function($enrollment) use ($material) {
                // Check if user has completed all topic quizzes
                $userCompletedAllQuizzes = true;
                $userQuizStats = [];
                
                foreach ($material->topics as $topic) {
                    if ($topic->quiz) {
                        $attempt = $topic->quiz->attempts->where('user_id', $enrollment->user_id)->first();
                        $userQuizStats[$topic->id] = [
                            'quiz_id' => $topic->quiz->id,
                            'attempted' => $attempt ? true : false,
                            'passed' => $attempt ? $attempt->passed : false,
                            'score' => $attempt ? $attempt->percentage : 0,
                        ];
                        
                        if (!$attempt || !$attempt->passed) {
                            $userCompletedAllQuizzes = false;
                        }
                    }
                }

                return [
                    'id' => $enrollment->user_id,
                    'name' => $enrollment->user->first_name . ' ' . $enrollment->user->last_name,
                    'email' => $enrollment->user->email,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'completed_at' => $enrollment->completed_at,
                    'progress' => $enrollment->progress,
                    'completed_all_quizzes' => $userCompletedAllQuizzes,
                    'quiz_stats' => $userQuizStats,
                ];
            });

        // Calculate quiz statistics
        $quizStats = [
            'total_attempts' => 0,
            'passed_attempts' => 0,
            'average_score' => 0,
            'topics_with_quizzes' => 0,
        ];

        $totalScore = 0;
        $attemptCount = 0;

        foreach ($material->topics as $topic) {
            if ($topic->quiz) {
                $quizStats['topics_with_quizzes']++;
                $quizStats['total_attempts'] += $topic->quiz->attempts->count();
                $passedAttempts = $topic->quiz->attempts->where('passed', true)->count();
                $quizStats['passed_attempts'] += $passedAttempts;
                
                foreach ($topic->quiz->attempts as $attempt) {
                    $totalScore += $attempt->percentage;
                    $attemptCount++;
                }
            }
        }

        if ($attemptCount > 0) {
            $quizStats['average_score'] = round($totalScore / $attemptCount, 2);
        }

        // Check if current user has completed all quizzes (for certificate eligibility)
        $userCompletedAllQuizzes = false;
        if (Sentinel::check()) {
            $userCompletedAllQuizzes = true;
            foreach ($material->topics as $topic) {
                if ($topic->quiz) {
                    $attempt = $topic->quiz->attempts->where('user_id', $user->id)->where('passed', true)->first();
                    if (!$attempt) {
                        $userCompletedAllQuizzes = false;
                        break;
                    }
                }
            }
        }

        return view('learning.course', compact(
            'material', 
            'isEnrolled', 
            'progress', 
            'isAdmin', 
            'enrolledUsers', 
            'quizStats',
            'userCompletedAllQuizzes'
        ));
    }

    /**
     * Display the classroom view for a course.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function classroom($id)
    {
         $material = TrainingMaterial::with('topics.quiz.attempts')->findOrFail($id);
        $enrollment = null;
        $progress = 0;
        $completedTopics = [];
        $user = null;
        
        // Get enrollment details if user is logged in
        if (Sentinel::check()) {
            $user = Sentinel::getUser();
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('training_material_id', $id)
                ->first();
            
            if ($enrollment) {
                $progress = $enrollment->progress;
                $completedTopics = $enrollment->completed_topics ?? [];
            }
        }

        // Build phases from topics
        $topics = $material->topics;
        
        if ($topics->count() > 0) {
            // Create a single phase with all topics
            $phases = [[
                'id' => 1,
                'title' => $material->title,
                'description' => $material->description ?? 'Course Topics',
                'icon' => 'fa-book',
                 'topics' => $topics->map(function ($topic, $index) use ($completedTopics, $user) {
                    $quizPassed = false;
                    if ($topic->quiz) {
                        $quizPassed = $topic->quiz->attempts->where('user_id', $user->id)->where('passed', true)->count() > 0;
                    }
                    
                    return [
                        'id' => $topic->id,
                        'title' => $topic->topic_name,
                        'type' => $topic->topic_type,
                        'duration' => $topic->duration ? $topic->duration . ' min' : 'N/A',
                        'is_completed' => in_array($topic->id, $completedTopics),
                        'file_path' => $topic->file_path,
                        'video_file_path' => $topic->video_file_path,
                        'audio_file_path' => $topic->audio_file_path,
                        'pdf_file_path' => $topic->pdf_file_path,
                        'ppt_file_path' => $topic->ppt_file_path,
                        'document_file_path' => $topic->document_file_path,
                        'file_name' => $topic->file_name,
                        'sort_order' => $topic->sort_order,
                        'quiz_id' => $topic->quiz ? $topic->quiz->id : null,
                        'quiz_passed' => $quizPassed,
                    ];
                })->toArray(),
            ]];
        } else {
            // Fallback to single topic if no topics exist
             $phases = [
                 [
                     'id' => 1,
                     'title' => 'Main Material',
                     'description' => 'Get started with this course',
                     'icon' => 'fa-play-circle',
                     'topics' => [
                         [
                             'id' => 1,
                             'title' => $material->title,
                             'type' => $material->material_type,
                             'duration' => $material->human_duration ?? 'N/A',
                             'is_completed' => false,
                             'file_path' => $material->file_path,
                             'sort_order' => 0,
                             'quiz_id' => null,
                             'quiz_passed' => false,
                         ]
                     ]
                 ]
             ];
        }

        return view('learning.classroom', compact('material', 'enrollment', 'progress', 'phases'));
    }

    /**
     * Enroll a user in a course.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function enroll($id)
    {
        $material = TrainingMaterial::findOrFail($id);
        $user = Sentinel::getUser();

        // Check if user is already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'You are already enrolled in this course');
        }

        // Create new enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'training_material_id' => $id,
            'enrolled_at' => now(),
            'completed_at' => null,
            'progress' => 0,
            'completed_topics' => [],
        ]);

        // Redirect with success message
        return redirect()->back()
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Successfully enrolled in ' . $material->title);
    }

    /**
     * Unenroll a user from a course.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function unenroll($id)
    {
        $user = Sentinel::getUser();
        
        // Find and delete the enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $id)
            ->first();
        
        if ($enrollment) {
            $enrollment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Successfully unenrolled from course.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'You are not enrolled in this course.'
        ], 400);
    }

    /**
     * Mark a topic as complete.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeTopic($id, \Illuminate\Http\Request $request)
    {
        if (!Sentinel::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        }

        $user = Sentinel::getUser();
        $topicId = $request->input('topic_id');
        
        // Find the topic
        $topic = CourseTopic::with('quiz.attempts')->find($topicId);
        
        if (!$topic) {
            return response()->json([
                'success' => false,
                'message' => 'Topic not found.'
            ], 404);
        }

        // Check enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $id)
            ->first();
        
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ], 400);
        }

        // Get completed topic IDs from enrollment metadata or create new tracking
        $completedTopics = $enrollment->completed_topics ?? [];
        
        if (!in_array($topicId, $completedTopics)) {
            $completedTopics[] = $topicId;
        }
        
        // Update enrollment with completed topics
        $enrollment->update([
            'completed_topics' => $completedTopics,
        ]);
        
        // Calculate progress based on total topics
        $material = TrainingMaterial::with('topics')->find($id);
        $totalTopics = $material->topics->count();
        $completedCount = count($completedTopics);
        $progress = $totalTopics > 0 ? round(($completedCount / $totalTopics) * 100) : 0;
        
        // Update enrollment progress
        $enrollment->update([
            'progress' => $progress,
            'completed_at' => $progress >= 100 ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topic marked as complete!',
            'progress' => $progress,
            'completed_topics' => $completedTopics
        ]);
    }

    /**
     * Mark a course as complete.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeCourse($id)
    {
        if (!Sentinel::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        }

        $user = Sentinel::getUser();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course.'
            ], 400);
        }

        $enrollment->update([
            'progress' => 100,
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course marked as complete!'
        ]);
    }

    /**
     * Get progress for a specific enrollment.
     *
     * @param int $userId
     * @param int $materialId
     * @return int
     */
    private function getProgress($userId, $materialId)
    {
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('training_material_id', $materialId)
            ->first();

        return $enrollment ? $enrollment->progress : 0;
    }


    /**
     * Display the settings page.
     *
     * @return <Illuminate><Http><Response>
     */
    public function settings()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        // Get statistics for the dashboard
        $totalSettings = 5; // Total number of settings sections
        $totalCategories = CourseCategory::count();
        $totalStudents = 0; // Would need to query actual student count
        $totalTeachers = 0; // Would need to query actual teacher count

        return view('learning.settings', compact(
            'totalSettings', 
            'totalCategories', 
            'totalStudents', 
            'totalTeachers'
        ));
    }

    /**
     * Display the course categories settings page.
     *
     * @return <Illuminate><Http><Response>
     */
    public function settingsCategories()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this settings page.');
        }

        $categories = CourseCategory::orderBy('name', 'asc')->get();

        return view('learning.settings.categories', compact('categories'));
    }

    /**
     * Display the students settings page.
     *
     * @return <Illuminate><Http><Response>
     */
    public function settingsStudents()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this settings page.');
        }

        // Would need to query actual students data
        $students = [];

        return view('learning.settings.students', compact('students'));
    }

    /**
     * Display the teachers settings page.
     *
     * @return <Illuminate><Http><Response>
     */
    public function settingsTeachers()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this settings page.');
        }

        // Get all trainers with their office and roles
        $trainers = User::where('istrainer', 1)
            ->with(['office', 'roles'])
            ->get();

        // Get all offices
        $offices = Office::orderBy('name', 'asc')->get();

        return view('learning.settings.teachers', compact('trainers', 'offices'));
    }

    /**
     * Get roles by office.
     *
     * @param int $officeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRolesByOffice($officeId)
    {
        if (!Sentinel::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get users in this office
        $users = User::where('office_id', $officeId)
            ->with('roles')
            ->get();

        // Extract unique roles
        $roles = [];
        foreach ($users as $user) {
            foreach ($user->roles as $userRole) {
                $roleId = $userRole->id;
                if (!isset($roles[$roleId])) {
                    $roles[$roleId] = [
                        'id' => $roleId,
                        'name' => $userRole->name,
                        'slug' => $userRole->slug
                    ];
                }
            }
        }

        return response()->json(array_values($roles));
    }

    /**
     * Get users by office and role.
     *
     * @param int $officeId
     * @param int $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByOfficeRole($officeId, $roleId)
    {
        if (!Sentinel::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get users by office and role
        $users = User::where('office_id', $officeId)
            ->with('roles')
            ->get()
            ->filter(function($user) use ($roleId) {
                return $user->roles->contains('id', $roleId);
            });

        $userList = $users->map(function($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'designation' => $user->designation,
                'istrainer' => $user->istrainer
            ];
        })->values();

        return response()->json($userList);
    }

    /**
     * Update istrainer status for multiple users.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTrainerStatus(\Illuminate\Http\Request $request)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to perform this action.');
        }

        $request->validate([
            'user_ids' => 'required|string'
        ]);

        $userIds = explode(',', $request->user_ids);
        $count = 0;

        foreach ($userIds as $id) {
            $userToUpdate = User::find($id);
            if ($userToUpdate && $userToUpdate->istrainer != 1) {
                $userToUpdate->istrainer = 1;
                $userToUpdate->save();
                $count++;
            }
        }

        return redirect()->route('learning.settings.teachers')
            ->with('toastr_type', 'success')
            ->with('toastr_message', "Successfully granted trainer status to {$count} user(s).");
    }

    /**
     * Remove trainer status from a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeTrainerStatus($userId)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $currentUser = Sentinel::getUser();
        $role = $currentUser->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to perform this action.');
        }

        $userToUpdate = User::find($userId);
        
        if (!$userToUpdate) {
            return redirect()->route('learning.settings.teachers')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'User not found.');
        }

        $userToUpdate->istrainer = 0;
        $userToUpdate->save();

        return redirect()->route('learning.settings.teachers')
            ->with('toastr_type', 'success')
            ->with('toastr_message', "Successfully revoked trainer status from {$userToUpdate->first_name} {$userToUpdate->last_name}.");
    }

    /**
     * Generate certificate for a course.
     *
     * @param int $id
     * @param int|null $userId
     * @return \Illuminate\Http\Response
     */
    public function generateCertificate($id, $userId = null)
    {
        $material = TrainingMaterial::findOrFail($id);
        $user = Sentinel::getUser();
        $isAdmin = $user->roles->first() && in_array($user->roles->first()->id, ['1']);

        // Determine which user to generate certificate for
        if ($userId) {
            if (!$isAdmin) {
                return redirect()->back()
                    ->with('toastr_type', 'error')
                    ->with('toastr_message', 'You do not have permission to generate certificates for other users.');
            }
            
            $targetUser = User::findOrFail($userId);
        } else {
            $targetUser = $user;
        }

        // Check if user is eligible for certificate
        $eligible = false;
        if ($isAdmin) {
            $eligible = true; // Admin can generate any certificate
        } else {
            $eligible = true;
            foreach ($material->topics as $topic) {
                if ($topic->quiz) {
                    $attempt = $topic->quiz->attempts->where('user_id', $targetUser->id)->where('passed', true)->first();
                    if (!$attempt) {
                        $eligible = false;
                        break;
                    }
                }
            }
        }

        if (!$eligible) {
            return redirect()->back()
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You must complete all topic quizzes to generate a certificate.');
        }

        // Get enrollment details - create a dummy enrollment if none exists
        $enrollment = Enrollment::where('user_id', $targetUser->id)
            ->where('training_material_id', $id)
            ->first();
            
        // If no enrollment exists (shouldn't happen normally), create a dummy object
        if (!$enrollment) {
            $enrollment = (object)[
                'created_at' => now(),
                'completed_at' => null,
                'progress' => 0
            ];
        }

        return view('learning.certificate', compact(
            'material', 
            'targetUser', 
            'enrollment',
            'isAdmin'
        ));
    }

    /**
     * Calculate total learning hours for a user.
     *
     * @param int $userId
     * @return int
     */
    private function calculateTotalHours($userId)
    {
        $enrollments = Enrollment::where('user_id', $userId)->get();
        $totalSeconds = 0;

        foreach ($enrollments as $enrollment) {
            if ($enrollment->trainingMaterial && $enrollment->trainingMaterial->duration) {
                $totalSeconds += $enrollment->trainingMaterial->duration;
            }
        }

        return round($totalSeconds / 3600, 1);
    }
}

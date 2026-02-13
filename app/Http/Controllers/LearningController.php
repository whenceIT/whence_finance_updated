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
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $roleId = $role ? $role->id : null;
        $isAdmin = $role && in_array($role->id, ['1']);

        // Get all available materials
        $query = $isAdmin 
            ? TrainingMaterial::query()
            : TrainingMaterial::active();

        // Apply role-based filtering
        if ($roleId && !$isAdmin) {
            $query->forRole($roleId);
        }

        // Apply category filtering if specified
        if (request()->has('category') && !empty(request()->category)) {
            $category = CourseCategory::where('name', request()->category)->first();
            if ($category) {
                $query->where('category', $category->name);
            }
        }

        $allMaterials = $query->orderBy('created_at', 'desc')->get();

        // Get enrolled materials for current user
        $enrolledMaterialIds = Enrollment::where('user_id', $user->id)
            ->pluck('training_material_id')
            ->toArray();

        // Get unique categories from CourseCategory model
        $categories = CourseCategory::active()->ordered()->get();

        // Prepare courses data with enrollment status
        $courses = $allMaterials->map(function ($material) use ($user, $enrolledMaterialIds) {
            $isEnrolled = in_array($material->id, $enrolledMaterialIds);
            
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
                'progress' => $isEnrolled ? $this->getProgress($user->id, $material->id) : 0,
                'lessons' => 1,
            ];
        })->toArray();

        // Calculate statistics
        $stats = [
            'total_courses' => $allMaterials->count(),
            'enrolled_courses' => count($enrolledMaterialIds),
            'completed_courses' => Enrollment::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
            'total_hours' => $this->calculateTotalHours($user->id),
            'in_progress' => Enrollment::where('user_id', $user->id)
                ->where('progress', '>', 0)
                ->where('progress', '<', 100)
                ->count(),
        ];

        // Share categories with all views
        view()->share('categories', $categories);

        return view('learning.dashboard', compact('courses', 'stats'));
    }

    /**
     * Display the user's enrolled courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function courses()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);
        $selectedCategory = request()->get('category');

        // If category is specified, show all available courses in that category
        if ($selectedCategory) {
            $category = CourseCategory::where('name', $selectedCategory)->first();
            
            $query = TrainingMaterial::active();
            
            // Apply category filter
            $query->where('category', $selectedCategory);
            
            $materials = $query->orderBy('created_at', 'desc')->get();

            // Get enrolled material IDs
            $enrolledMaterialIds = Enrollment::where('user_id', $user->id)
                ->pluck('training_material_id')
                ->toArray();

            // Prepare courses data
            $courses = $materials->map(function ($material) use ($user, $enrolledMaterialIds) {
                $isEnrolled = in_array($material->id, $enrolledMaterialIds);
                
                $enrollment = Enrollment::where('user_id', $user->id)
                    ->where('training_material_id', $material->id)
                    ->first();
                
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
                    'progress' => $isEnrolled ? ($enrollment->progress ?? 0) : 0,
                    'enrolled_at' => $isEnrolled ? $enrollment->enrolled_at : null,
                    'completed_at' => $isEnrolled ? $enrollment->completed_at : null,
                ];
            })->toArray();

            return view('learning.courses', compact('courses', 'selectedCategory', 'category'));
        }

        // Otherwise, show only enrolled courses (original behavior)
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('trainingMaterial')
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
            ];
        })->toArray();

        // Filter out null entries (enrollments without materials)
        $courses = array_filter($courses);
        
        return view('learning.courses', compact('courses'));
    }

    /**
     * Display the calendar page.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        return view('learning.calendar');
    }

    /**
     * Display the progress page.
     *
     * @return \Illuminate\Http\Response
     */
    public function progress()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        
        // Get user's enrollments with training materials
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('trainingMaterial')
            ->get();
        
        // Calculate progress data
        $completedCount = 0;
        $inProgressCount = 0;
        $totalLessons = 0;
        $completedLessons = 0;
        $learningSeconds = 0;
        
        foreach ($enrollments as $enrollment) {
            $material = $enrollment->trainingMaterial;
            if (!$material) continue;
            
            $totalLessons += 1;
            $completedLessons += ($enrollment->progress / 100);
            $learningSeconds += $material->duration ?? 0;
            
            if ($enrollment->completed_at) {
                $completedCount++;
            } elseif ($enrollment->progress > 0) {
                $inProgressCount++;
            }
        }
        
        $progressData = [
            'courses_completed' => $completedCount,
            'courses_in_progress' => $inProgressCount,
            'certificates_earned' => $completedCount, // Using completed courses as certificates for now
            'streak_days' => 0, // Would need separate tracking logic
            'total_lessons_completed' => floor($completedLessons),
            'total_lessons' => $totalLessons,
            'learning_hours' => round($learningSeconds / 3600, 1),
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
        if (!Sentinel::check()) {
            return redirect('login');
        }

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
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $material = TrainingMaterial::findOrFail($id);

        // Check enrollment status
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $id)
            ->first();
        
        $isEnrolled = $enrollment !== null;
        $progress = $enrollment ? $enrollment->progress : 0;

        return view('learning.course', compact('material', 'isEnrolled', 'progress'));
    }

    /**
     * Display the classroom view for a course.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function classroom($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $material = TrainingMaterial::with('topics')->findOrFail($id);

        // Check enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $id)
            ->first();
        
        if (!$enrollment) {
            return redirect()->route('learning.course', $id)
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'Please enroll in this course first.');
        }

        // Get user's completed topics from enrollment
        $completedTopics = $enrollment->completed_topics ?? [];
        $progress = $enrollment->progress ?? 0;

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
                    $isCompleted = in_array($topic->id, $completedTopics);
                    $quiz = $topic->quiz;
                    
                    return [
                        'id' => $topic->id,
                        'title' => $topic->topic_name,
                        'type' => $topic->topic_type,
                        'duration' => $topic->duration ? $topic->duration . ' min' : 'N/A',
                        'is_completed' => $isCompleted,
                        'file_path' => $topic->file_path,
                        'sort_order' => $topic->sort_order,
                        'quiz_id' => $quiz ? $quiz->id : null,
                        'quiz_passed' => $quiz ? ($quiz->attempts->where('user_id', $user->id)->where('passed', true)->count() > 0) : false,
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
                            'is_completed' => $progress >= 100,
                            'file_path' => $material->file_path,
                            'sort_order' => 0,
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
        if (!Sentinel::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to enroll.'
            ], 401);
        }

        $user = Sentinel::getUser();
        $material = TrainingMaterial::findOrFail($id);

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('training_material_id', $id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ], 400);
        }

        // Create enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'training_material_id' => $id,
            'enrolled_at' => now(),
            'progress' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in ' . $material->title
        ]);
    }

    /**
     * Unenroll a user from a course.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function unenroll($id)
    {
        if (!Sentinel::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to unenroll.'
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

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully unenrolled from course.'
        ]);
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

        // Check if topic has a quiz
        $quiz = $topic->quiz;
        if ($quiz) {
            // Check if user has passed the quiz
            $hasPassed = $quiz->attempts->where('user_id', $user->id)->where('passed', true)->count() > 0;
            if (!$hasPassed) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must pass the quiz before completing this topic. Please take the quiz first.',
                    'quiz_required' => true,
                    'quiz_id' => $quiz->id
                ], 400);
            }
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

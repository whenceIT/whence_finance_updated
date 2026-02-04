<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\TrainingMaterial;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

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

        // Get only enrolled materials for current user
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with('trainingMaterial')
            ->orderBy('enrolled_at', 'desc')
            ->get();

        // Prepare courses data
        $courses = $enrollments->map(function ($enrollment) {
            $material = $enrollment->trainingMaterial;
            
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

        return view('learning.progress');
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

        // Check if user is enrolled
        $isEnrolled = $material->isEnrolled($user->id);

        if (!$isEnrolled) {
            return redirect()->route('learning.dashboard')
                ->with('toastr_type', 'warning')
                ->with('toastr_message', 'Please enroll in this course first.');
        }

        return view('learning.course', compact('material'));
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

        // Would need to query actual teachers data
        $teachers = [];

        return view('learning.settings.teachers', compact('teachers'));
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

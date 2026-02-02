<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Laracasts\Flash\Flash;
use App\Models\TrainingMaterial;

class LearningController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // No middleware - authentication handled in each method
    }

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

        // Check if user has access to learning module
        if (!$role || !in_array($role->id, ['1', '4', '6', '3', '5', '10'])) {
            Flash::warning('You do not have access to the Learning module.');
            return redirect('dashboard');
        }

        // Sample data for courses (this would typically come from a database)
        $courses = [
            [
                'id' => 1,
                'title' => 'Financial Management Fundamentals',
                'category' => 'Finance',
                'description' => 'Learn the basics of financial management, budgeting, and financial planning for business success.',
                'progress' => 75,
                'lessons' => 12,
                'duration' => '8 hours',
                'enrolled' => true,
                'icon' => 'fa-calculator'
            ],
            [
                'id' => 2,
                'title' => 'Leadership and Team Management',
                'category' => 'Leadership',
                'description' => 'Develop essential leadership skills and learn how to effectively manage and motivate teams.',
                'progress' => 45,
                'lessons' => 15,
                'duration' => '10 hours',
                'enrolled' => true,
                'icon' => 'fa-users'
            ],
            [
                'id' => 3,
                'title' => 'Customer Service Excellence',
                'category' => 'Business',
                'description' => 'Master the art of providing exceptional customer service and building lasting client relationships.',
                'progress' => 0,
                'lessons' => 8,
                'duration' => '5 hours',
                'enrolled' => false,
                'icon' => 'fa-comments'
            ],
            [
                'id' => 4,
                'title' => 'Digital Marketing Strategies',
                'category' => 'Business',
                'description' => 'Explore modern digital marketing techniques to grow your business and reach more customers.',
                'progress' => 20,
                'lessons' => 10,
                'duration' => '7 hours',
                'enrolled' => true,
                'icon' => 'fa-bullhorn'
            ],
            [
                'id' => 5,
                'title' => 'Data Analysis for Business',
                'category' => 'Technology',
                'description' => 'Learn how to analyze business data and make data-driven decisions for better outcomes.',
                'progress' => 0,
                'lessons' => 14,
                'duration' => '12 hours',
                'enrolled' => false,
                'icon' => 'fa-bar-chart'
            ],
            [
                'id' => 6,
                'title' => 'Risk Management Essentials',
                'category' => 'Finance',
                'description' => 'Understand key risk management principles and how to mitigate business risks effectively.',
                'progress' => 60,
                'lessons' => 9,
                'duration' => '6 hours',
                'enrolled' => true,
                'icon' => 'fa-shield'
            ]
        ];

        // Calculate statistics
        $enrolledCourses = collect($courses)->where('enrolled', true);
        $completedCourses = $enrolledCourses->where('progress', 100)->count();
        $inProgressCourses = $enrolledCourses->where('progress', '>', 0)->where('progress', '<', 100)->count();
        $averageProgress = $enrolledCourses->isNotEmpty() 
            ? round($enrolledCourses->avg('progress')) 
            : 0;

        $stats = [
            'total_courses' => count($courses),
            'enrolled_courses' => $enrolledCourses->count(),
            'completed_courses' => $completedCourses,
            'in_progress' => $inProgressCourses,
            'average_progress' => $averageProgress,
            'total_hours' => collect($courses)->sum(function($course) {
                return (int) filter_var($course['duration'], FILTER_SANITIZE_NUMBER_INT);
            })
        ];

        return view('learning.dashboard', compact('courses', 'stats'));
    }

    /**
     * Display all courses.
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

        if (!$role || !in_array($role->id, ['1', '4', '6', '3', '5', '10'])) {
            Flash::warning('You do not have access to the Learning module.');
            return redirect('dashboard');
        }

        // Fetch real courses from TrainingMaterial model
        $trainingMaterials = TrainingMaterial::active()
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();

        // Transform training materials to course format
        $courses = $trainingMaterials->map(function ($material) {
            return [
                'id' => $material->id,
                'title' => $material->title,
                'category' => $material->category ?? 'General',
                'description' => $material->description,
                'progress' => 0, // Progress tracking would need a separate enrollment table
                'lessons' => 1, // Each material is considered one lesson
                'duration' => $material->human_duration,
                'enrolled' => false, // Enrollment would need a separate enrollment table
                'icon' => $material->icon,
                'material_type' => $material->material_type,
                'file_path' => $material->file_path,
                'file_name' => $material->file_name,
                'file_size' => $material->human_file_size,
                'view_count' => $material->view_count,
                'download_count' => $material->download_count,
                'is_featured' => $material->is_featured,
                'department' => $material->department,
                'target_role' => $material->target_role,
            ];
        })->toArray();

        return view('learning.courses', compact('courses'));
    }

    /**
     * Display the calendar view.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        if (!$role || !in_array($role->id, ['1', '4', '6', '3', '5', '10'])) {
            Flash::warning('You do not have access to the Learning module.');
            return redirect('dashboard');
        }

        return view('learning.calendar');
    }

    /**
     * Display the progress view.
     *
     * @return \Illuminate\Http\Response
     */
    public function progress()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        if (!$role || !in_array($role->id, ['1', '4', '6', '3', '5', '10'])) {
            Flash::warning('You do not have access to the Learning module.');
            return redirect('dashboard');
        }

        // Sample progress data
        $progressData = [
            'courses_completed' => 2,
            'courses_in_progress' => 3,
            'total_lessons_completed' => 45,
            'total_lessons' => 68,
            'certificates_earned' => 2,
            'learning_hours' => 24,
            'streak_days' => 7
        ];

        return view('learning.progress', compact('progressData'));
    }

    /**
     * Display the certificates view.
     *
     * @return \Illuminate\Http\Response
     */
    public function certificates()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        if (!$role || !in_array($role->id, ['1', '4', '6', '3', '5', '10'])) {
            Flash::warning('You do not have access to the Learning module.');
            return redirect('dashboard');
        }

        // Sample certificates data
        $certificates = [
            [
                'id' => 1,
                'course_name' => 'Financial Management Fundamentals',
                'issue_date' => '2024-01-15',
                'certificate_id' => 'WF-LM-2024-001'
            ],
            [
                'id' => 2,
                'course_name' => 'Leadership and Team Management',
                'issue_date' => '2024-02-20',
                'certificate_id' => 'WF-LM-2024-002'
            ]
        ];

        return view('learning.certificates', compact('certificates'));
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
        $role = $user->roles->first();

        if (!$role || !in_array($role->id, ['1', '4', '6', '3', '5', '10'])) {
            Flash::warning('You do not have access to the Learning module.');
            return redirect('dashboard');
        }

        // Sample course data (in real app, fetch from database)
        $course = [
            'id' => $id,
            'title' => 'Financial Management Fundamentals',
            'category' => 'Finance',
            'description' => 'Learn the basics of financial management, budgeting, and financial planning for business success.',
            'progress' => 75,
            'lessons' => 12,
            'duration' => '8 hours',
            'enrolled' => true,
            'icon' => 'fa-calculator',
            'modules' => [
                [
                    'id' => 1,
                    'title' => 'Introduction to Financial Management',
                    'completed' => true,
                    'lessons' => 3
                ],
                [
                    'id' => 2,
                    'title' => 'Budgeting Basics',
                    'completed' => true,
                    'lessons' => 4
                ],
                [
                    'id' => 3,
                    'title' => 'Financial Planning',
                    'completed' => false,
                    'lessons' => 5
                ]
            ]
        ];

        return view('learning.course-detail', compact('course'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of course categories.
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
        $isAdmin = $role && in_array($role->id, ['1']);

        $categories = CourseCategory::ordered()->get();

        return view('course-categories.index', compact('categories', 'isAdmin'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to create categories
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('course-categories.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to create categories.');
        }

        return view('course-categories.create');
    }

    /**
     * Store a newly created category in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to create categories
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('course-categories.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to create categories.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:course_categories,name',
            'slug' => 'required|string|max:255|unique:course_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        CourseCategory::create($validated);

        return redirect()->route('course-categories.index')
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to edit categories
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('course-categories.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to edit categories.');
        }

        $category = CourseCategory::findOrFail($id);

        return view('course-categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to edit categories
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('course-categories.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to edit categories.');
        }

        $category = CourseCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:course_categories,name,' . $id,
            'slug' => 'required|string|max:255|unique:course_categories,slug,' . $id,
            'description' => 'nullable|string',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('course-categories.index')
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to delete categories
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('course-categories.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to delete categories.');
        }

        $category = CourseCategory::findOrFail($id);

        // Check if category has training materials
        if ($category->trainingMaterials()->count() > 0) {
            return redirect()->route('course-categories.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'Cannot delete category with existing training materials.');
        }

        $category->delete();

        return redirect()->route('course-categories.index')
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Category deleted successfully.');
    }

    /**
     * Toggle active status of a category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();

        // Check if user has permission to manage categories
        if (!$role || !in_array($role->id, ['1', '6', '4'])) {
            return redirect()->route('course-categories.index')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to manage categories.');
        }

        $category = CourseCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return redirect()->back()
            ->with('toastr_type', 'success')
            ->with('toastr_message', 'Category status updated successfully.');
    }
}

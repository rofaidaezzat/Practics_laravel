<?php

namespace App\Http\Controllers\Courses;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses (HTML view or JSON API).
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->query('format') === 'json') {
            return $this->apiIndex($request);
        }

        return view('courses.index');
    }

    /**
     * API: Returns all courses with pagination and student count.
     */
    public function apiIndex(Request $request)
    {
        $limit = max(1, (int) $request->input('limit', 1000));
        $page = max(1, (int) $request->input('page', 1));

        $courses = Course::withCount('students')->latest()->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Courses retrieved successfully',
            'results' => $courses->total(),
            'pagination' => [
                'currentPage' => $courses->currentPage(),
                'limit' => $courses->perPage(),
                'numberOfPages' => $courses->lastPage(),
            ],
            'data' => $courses->items(),
        ], 200);
    }

    /**
     * Show form to create course (HTML).
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created course in storage (JSON or Web).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:10',
        ]);

        $course = Course::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Course created successfully.',
                'data' => $course,
            ], 201);
        }

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course with enrolled students (HTML or JSON).
     */
    public function show(Request $request, Course $course)
    {
        $course->load('students');

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Course retrieved successfully',
                'data' => $course,
            ], 200);
        }

        return view('courses.show', compact('course'));
    }

    /**
     * Show form to edit course (HTML).
     */
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified course in storage (JSON or Web).
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'credits' => 'nullable|integer|min:1|max:10',
        ]);

        $course->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Course updated successfully.',
                'data' => $course,
            ], 200);
        }

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course from storage (JSON or Web).
     */
    public function destroy(Request $request, Course $course)
    {
        $course->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Course deleted successfully.',
            ], 200);
        }

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    /**
     * Allow a student to select/enroll in courses.
     * POST /courses/select
     */
    public function selectCourses(Request $request, Student $student)
    {
        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $student->courses()->sync($validated['course_ids']);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Courses selected successfully for student.',
                'data' => $student->load('courses'),
            ], 200);
        }

        return redirect()->back()->with('success', 'Courses selected successfully.');
    }
}

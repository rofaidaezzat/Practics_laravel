<?php

namespace App\Http\Controllers\StudentController;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * 1. Returns HTML View to the Browser, or JSON if requested
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->query('format') === 'json') {
            return $this->apiIndex($request);
        }

        $courses = Course::all();
        return view('students.index', compact('courses'));
    }

    /**
     * Show form to create student (HTML)
     */
    public function create()
    {
        $courses = Course::all();
        return view('students.create', compact('courses'));
    }

    /**
     * Show form to edit student (HTML)
     */
    public function edit(Student $student)
    {
        $courses = Course::all();
        $student->load('courses');
        return view('students.edit', compact('student', 'courses'));
    }

    /**
     * Display a single student (HTML or JSON)
     */
    public function show(Request $request, Student $student)
    {
        $student->load('courses');

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $student,
            ], 200);
        }

        return view('students.show', compact('student'));
    }

    /**
     * 2. API: Returns all students as JSON
     * GET /api/students
     */
    public function apiIndex(Request $request)
    {
        $limit = max(1, (int) $request->input('limit', 1000));
        $page = max(1, (int) $request->input('page', 1));

        $students = Student::with('courses')->latest()->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Students retrieved successfully',
            'results' => $students->total(),
            'pagination' => [
                'currentPage' => $students->currentPage(),
                'limit' => $students->perPage(),
                'numberOfPages' => $students->lastPage(),
            ],
            'data' => $students->items(),
        ], 200);
    }

    /**
     * API: Create a new student (JSON)
     * POST /api/students
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $student = Student::create($validated);

        if ($request->has('course_ids')) {
            $student->courses()->sync($request->input('course_ids', []));
        }

        $student->load('courses');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => 'Student created successfully.',
                'data' => $student,
            ], 201);
        }

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    /**
     * API: Get single student details (JSON)
     * GET /students/{id}
     */
    public function apiShow(Student $student)
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $student->load('courses'),
        ], 200);
    }

    /**
     * API: Update student (JSON or Web)
     * PUT /students/{id}
     */
    public function apiUpdate(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $student->update($validated);

        $student->courses()->sync($request->input('course_ids', []));

        $student->load('courses');

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Student updated successfully.',
                'data' => $student,
            ], 200);
        }

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Select / enroll courses for a student
     * POST /students/{student}/courses
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

    /**
     * API: Delete student (JSON or Web)
     * DELETE /students/{id}
     */
    public function apiDestroy(Request $request, Student $student)
    {
        $student->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Student deleted successfully.',
            ], 200);
        }

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}

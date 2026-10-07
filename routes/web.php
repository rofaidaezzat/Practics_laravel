<?php

use App\Http\Controllers\Courses\CourseController;
use App\Http\Controllers\Students\StudentController;
use Illuminate\Support\Facades\Route;

// Redirect home to students
Route::get('/', function () {
    return redirect()->route('students.index');
});

// Students routes
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'Store'])->name('students.store');
Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'Update'])->name('students.update');
Route::delete('/students/{student}', [StudentController::class, 'Destroy'])->name('students.destroy');
Route::post('/students/{student}/courses', [StudentController::class, 'selectCourses'])->name('students.courses.select');

// Courses CRUD routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
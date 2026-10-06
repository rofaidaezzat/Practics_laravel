<?php

use App\Http\Controllers\CoursesController\CoursesController;
use App\Http\Controllers\StudentController\StudentController;
use Illuminate\Support\Facades\Route;

// Redirect home to students
Route::get('/', function () {
    return redirect()->route('students.index');
});

// Students routes
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'apiStore'])->name('students.store');
Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'apiUpdate'])->name('students.update');
Route::delete('/students/{student}', [StudentController::class, 'apiDestroy'])->name('students.destroy');
Route::post('/students/{student}/courses', [StudentController::class, 'selectCourses'])->name('students.courses.select');

// Courses CRUD routes
Route::get('/courses', [CoursesController::class, 'index'])->name('courses.index');
Route::get('/courses/create', [CoursesController::class, 'create'])->name('courses.create');
Route::post('/courses', [CoursesController::class, 'store'])->name('courses.store');
Route::get('/courses/{course}', [CoursesController::class, 'show'])->name('courses.show');
Route::get('/courses/{course}/edit', [CoursesController::class, 'edit'])->name('courses.edit');
Route::put('/courses/{course}', [CoursesController::class, 'update'])->name('courses.update');
Route::delete('/courses/{course}', [CoursesController::class, 'destroy'])->name('courses.destroy');
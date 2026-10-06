@extends('layout.app')

@section('title', 'Edit Student')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Student #{{ $student->id }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('students.update', $student) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $student->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $student->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label fw-semibold">Age</label>
                            <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age', $student->age) }}" min="1" max="120">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-journal-check text-primary me-1"></i> Select Courses</span>
                            <small class="text-muted fw-normal">Optional</small>
                        </label>
                        <div class="border rounded p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                            @php
                                $enrolledIds = old('course_ids', $student->courses->pluck('id')->toArray());
                            @endphp
                            @forelse($courses as $course)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="course_ids[]" value="{{ $course->id }}" id="course_{{ $course->id }}" {{ in_array($course->id, $enrolledIds) ? 'checked' : '' }}>
                                    <label class="form-check-label w-100 cursor-pointer" for="course_{{ $course->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold text-dark">{{ $course->name }}</span>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $course->code }}</span>
                                        </div>
                                        <small class="text-muted d-block">{{ $course->credits }} Credits &bull; {{ Str::limit($course->description, 50) }}</small>
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted small fst-italic mb-0">No courses available. <a href="{{ route('courses.index') }}">Add a course</a></p>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat me-1"></i> Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layout.app')

@section('title', 'Course Details - ' . $course->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Course Info Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6">
                        {{ $course->code }}
                    </span>
                    <h5 class="mb-0 fw-bold text-dark">{{ $course->name }}</h5>
                </div>
                <span class="badge bg-light text-secondary border">Course ID: #{{ $course->id }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted d-block small">Credits</span>
                            <span class="fw-bold fs-5 text-dark">{{ $course->credits }} Credits</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted d-block small">Enrolled Students</span>
                            <span class="fw-bold fs-5 text-primary">{{ $course->students->count() }} Students</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted d-block small">Created At</span>
                            <span class="fw-semibold text-secondary">{{ $course->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-secondary mb-1">Description</h6>
                        <p class="text-dark mb-0">{{ $course->description ?: 'No description provided for this course.' }}</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-4 border-top mt-4">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Courses
                    </a>
                    <button class="btn btn-danger" onclick="deleteAndRedirect({{ $course->id }})">
                        <i class="bi bi-trash me-1"></i> Delete Course
                    </button>
                </div>
            </div>
        </div>

        <!-- Enrolled Students Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i>
                    <span>Enrolled Students</span>
                </h5>
                <span class="badge bg-primary rounded-pill">{{ $course->students->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($course->students->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                        <p class="text-muted mt-2 mb-0">No students are currently enrolled in this course.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" style="width: 80px;">#ID</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Age</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
                                    <tr>
                                        <td class="ps-4 fw-semibold text-secondary">#{{ $student->id }}</td>
                                        <td class="fw-semibold text-dark">{{ $student->name }}</td>
                                        <td>
                                            <a href="mailto:{{ $student->email }}" class="text-decoration-none text-muted">
                                                <i class="bi bi-envelope me-1"></i>{{ $student->email }}
                                            </a>
                                        </td>
                                        <td>{{ $student->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->age)
                                                <span class="badge bg-secondary-subtle text-secondary">{{ $student->age }} yrs</span>
                                            @else
                                                <span class="text-muted fst-italic">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-person me-1"></i> View Student
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    async function deleteAndRedirect(id) {
        if (!confirm('Are you sure you want to delete this course?')) return;
        try {
            const res = await fetch(`/courses/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (res.ok) {
                window.location.href = "{{ route('courses.index') }}";
            } else {
                alert('Could not delete course.');
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    }
</script>
@endsection

@extends('layout.app')

@section('title', 'Student Details - ' . $student->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Student Info Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-person-badge-fill text-primary me-2"></i>Student Details
                </h5>
                <span class="badge bg-light text-secondary border">ID: #{{ $student->id }}</span>
            </div>
            <div class="card-body p-4">
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between px-0 py-3">
                        <span class="text-muted fw-semibold">Full Name</span>
                        <span class="fw-bold text-dark">{{ $student->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-3">
                        <span class="text-muted fw-semibold">Email</span>
                        <a href="mailto:{{ $student->email }}" class="text-decoration-none">{{ $student->email }}</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-3">
                        <span class="text-muted fw-semibold">Phone Number</span>
                        <span>{{ $student->phone ?? 'Not provided' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-3">
                        <span class="text-muted fw-semibold">Age</span>
                        <span>{{ $student->age ? $student->age . ' years' : 'Not provided' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-3">
                        <span class="text-muted fw-semibold">Created At</span>
                        <span class="text-secondary">{{ $student->created_at->format('M d, Y - h:i A') }}</span>
                    </li>
                </ul>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                    <button class="btn btn-danger" onclick="deleteAndRedirect({{ $student->id }})">
                        <i class="bi bi-trash me-1"></i> Delete Student
                    </button>
                </div>
            </div>
        </div>

        <!-- Selected Courses Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-journal-check text-primary"></i>
                    <span>Selected Courses</span>
                </h5>
                <button class="btn btn-sm btn-primary" onclick="openSelectCoursesModal()">
                    <i class="bi bi-plus-circle me-1"></i> Select Courses
                </button>
            </div>
            <div class="card-body p-0">
                @if($student->courses->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x text-muted" style="font-size: 2.5rem;"></i>
                        <p class="text-muted mt-2 mb-3">This student has not selected any courses yet.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="openSelectCoursesModal()">
                            <i class="bi bi-plus-lg me-1"></i> Enroll in Courses
                        </button>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Code</th>
                                    <th>Course Name</th>
                                    <th>Credits</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->courses as $course)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                {{ $course->code }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $course->name }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                {{ $course->credits }} Credits
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye me-1"></i> View Course
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

<!-- Modal for Selecting Courses -->
<div class="modal fade" id="selectCoursesModal" tabindex="-1" aria-labelledby="selectCoursesTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="selectCoursesTitle">Select Courses for {{ $student->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="courseModalAlert" class="alert alert-danger d-none"></div>

                <div id="coursesLoading" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-2 text-muted">Loading available courses...</span>
                </div>

                <form id="selectCoursesForm" onsubmit="handleCoursesSubmit(event)">
                    <div id="coursesChecklist" class="d-none mb-3" style="max-height: 250px; overflow-y: auto;">
                        <!-- Checkboxes dynamically populated -->
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveCoursesBtn">
                            <i class="bi bi-check2-circle me-1"></i> Save Selection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const studentId = {{ $student->id }};
    const currentEnrolledIds = @json($student->courses->pluck('id'));

    function getCoursesModal() {
        return bootstrap.Modal.getOrCreateInstance(document.getElementById('selectCoursesModal'));
    }

    async function openSelectCoursesModal() {
        const loading = document.getElementById('coursesLoading');
        const checklist = document.getElementById('coursesChecklist');
        const alertBox = document.getElementById('courseModalAlert');

        alertBox.classList.add('d-none');
        loading.classList.remove('d-none');
        checklist.classList.add('d-none');

        getCoursesModal().show();

        try {
            const res = await fetch('/courses', {
                headers: { 'Accept': 'application/json' }
            });
            const result = await res.json();

            loading.classList.add('d-none');
            checklist.classList.remove('d-none');
            checklist.innerHTML = '';

            if (result.data && result.data.length > 0) {
                result.data.forEach(course => {
                    const isChecked = currentEnrolledIds.includes(course.id) ? 'checked' : '';
                    const item = `
                        <div class="form-check p-2 border-bottom">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="course_ids" value="${course.id}" id="course_${course.id}" ${isChecked}>
                            <label class="form-check-label w-100 cursor-pointer" for="course_${course.id}">
                                <div class="fw-bold text-dark">${escapeHtml(course.name)} <span class="badge bg-primary-subtle text-primary ms-1">${escapeHtml(course.code)}</span></div>
                                <small class="text-muted">${course.credits ?? 3} Credits &bull; ${escapeHtml(course.description || 'No description')}</small>
                            </label>
                        </div>
                    `;
                    checklist.insertAdjacentHTML('beforeend', item);
                });
            } else {
                checklist.innerHTML = '<p class="text-muted fst-italic mb-0">No courses available. Please create courses first.</p>';
            }
        } catch (err) {
            loading.classList.add('d-none');
            alertBox.innerText = 'Failed to load courses: ' + err.message;
            alertBox.classList.remove('d-none');
        }
    }

    async function handleCoursesSubmit(event) {
        event.preventDefault();
        const alertBox = document.getElementById('courseModalAlert');
        const saveBtn = document.getElementById('saveCoursesBtn');

        const checkboxes = document.querySelectorAll('input[name="course_ids"]:checked');
        const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

        alertBox.classList.add('d-none');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`/students/${studentId}/courses`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ course_ids: selectedIds })
            });

            const data = await response.json();

            if (response.ok) {
                getCoursesModal().hide();
                window.location.reload();
            } else {
                alertBox.innerText = data.message || 'Failed to update selected courses.';
                alertBox.classList.remove('d-none');
            }
        } catch (err) {
            alertBox.innerText = 'Error: ' + err.message;
            alertBox.classList.remove('d-none');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save Selection';
        }
    }

    async function deleteAndRedirect(id) {
        if (!confirm('Are you sure you want to delete this student?')) return;
        try {
            const res = await fetch(`/students/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (res.ok) {
                window.location.href = "{{ route('students.index') }}";
            } else {
                alert('Could not delete student.');
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection

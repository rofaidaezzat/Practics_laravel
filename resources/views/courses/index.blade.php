@extends('layout.app')

@section('title', 'Courses Management')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-journal-bookmark-fill text-primary"></i>
            <span>Courses List</span>
        </h5>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i> Add New Course
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2 mb-0">Loading courses...</p>
        </div>

        <!-- Table Container -->
        <div id="tableContainer" class="table-responsive d-none">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 80px;">#ID</th>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Credits</th>
                        <th>Description</th>
                        <th>Students Enrolled</th>
                        <th class="text-end pe-4" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="coursesTableBody">
                    <!-- Rows dynamically inserted from JSON -->
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5 d-none">
            <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-secondary">No courses found</h5>
            <p class="text-muted">Click the button below to add your first course.</p>
            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createCourseModal" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i> Add Course
            </button>
        </div>
    </div>
</div>

{{-- Modals included from partial files --}}
@include('courses._create_course_modal')
@include('courses._edit_course_modal')
@include('courses._show_course_modal')

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    function getModal(id) {
        const modalEl = document.getElementById(id);
        return bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    }

    function closeModal(id) {
        const modalEl = document.getElementById(id);
        const instance = bootstrap.Modal.getInstance(modalEl);
        if (instance) {
            instance.hide();
        }
        cleanupBackdrop();
    }

    function cleanupBackdrop() {
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }, 200);
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['createCourseModal', 'editCourseModal', 'showCourseModal'].forEach(id => {
            const modalEl = document.getElementById(id);
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', cleanupBackdrop);
            }
        });
        loadCourses();
    });

    // Fetch all courses from API (Returns JSON)
    async function loadCourses() {
        const spinner = document.getElementById('loadingSpinner');
        const container = document.getElementById('tableContainer');
        const emptyState = document.getElementById('emptyState');
        const tbody = document.getElementById('coursesTableBody');

        spinner.classList.remove('d-none');
        container.classList.add('d-none');
        emptyState.classList.add('d-none');

        try {
            const response = await fetch('/courses', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();

            spinner.classList.add('d-none');

            if (result.data && result.data.length > 0) {
                tbody.innerHTML = '';
                result.data.forEach(course => {
                    const courseJson = JSON.stringify(course).replace(/"/g, '&quot;');
                    const row = `
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">#${course.id}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6">
                                    ${escapeHtml(course.code)}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">${escapeHtml(course.name)}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                    ${course.credits ?? 3} Credits
                                </span>
                            </td>
                            <td class="text-muted small" style="max-width: 250px;">
                                ${course.description ? escapeHtml(course.description) : '<span class="fst-italic text-muted">No description</span>'}
                            </td>
                            <td>
                                <a href="/courses/${course.id}" class="badge bg-info-subtle text-info-emphasis text-decoration-none px-2 py-1">
                                    <i class="bi bi-people me-1"></i>${course.students_count ?? 0} Students
                                </a>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info" onclick='openShowModal(${courseJson})' title="Preview Course">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="/courses/${course.id}" class="btn btn-sm btn-outline-secondary" title="Full Details Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-warning" onclick='openEditModal(${courseJson})' title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCourse(${course.id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
                container.classList.remove('d-none');
            } else {
                emptyState.classList.remove('d-none');
            }
        } catch (error) {
            console.error('Error fetching courses:', error);
            spinner.classList.add('d-none');
            emptyState.classList.remove('d-none');
        }
    }

    // --- CREATE MODAL ---
    function openCreateModal() {
        document.getElementById('createCourseForm').reset();
        document.getElementById('create_credits').value = '3';
        document.getElementById('createModalAlert').classList.add('d-none');
    }

    async function handleCreateSubmit(event) {
        event.preventDefault();
        const alertBox = document.getElementById('createModalAlert');
        const saveBtn = document.getElementById('createSaveBtn');

        alertBox.classList.add('d-none');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const payload = {
            code: document.getElementById('create_code').value.trim(),
            name: document.getElementById('create_name').value.trim(),
            credits: document.getElementById('create_credits').value ? parseInt(document.getElementById('create_credits').value) : 3,
            description: document.getElementById('create_description').value.trim()
        };

        try {
            const response = await fetch('/courses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                closeModal('createCourseModal');
                loadCourses();
            } else {
                let errorMsg = data.message || 'Failed to create course.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                alertBox.innerHTML = errorMsg;
                alertBox.classList.remove('d-none');
            }
        } catch (err) {
            alertBox.innerText = 'Network error: ' + err.message;
            alertBox.classList.remove('d-none');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save Course';
        }
    }

    // --- EDIT MODAL ---
    function openEditModal(course) {
        document.getElementById('edit_courseId').value = course.id;
        document.getElementById('edit_code').value = course.code;
        document.getElementById('edit_name').value = course.name;
        document.getElementById('edit_credits').value = course.credits ?? 3;
        document.getElementById('edit_description').value = course.description || '';
        document.getElementById('editModalAlert').classList.add('d-none');
        getModal('editCourseModal').show();
    }

    async function handleEditSubmit(event) {
        event.preventDefault();
        const alertBox = document.getElementById('editModalAlert');
        const saveBtn = document.getElementById('editSaveBtn');
        const id = document.getElementById('edit_courseId').value;

        alertBox.classList.add('d-none');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        const payload = {
            code: document.getElementById('edit_code').value.trim(),
            name: document.getElementById('edit_name').value.trim(),
            credits: document.getElementById('edit_credits').value ? parseInt(document.getElementById('edit_credits').value) : 3,
            description: document.getElementById('edit_description').value.trim()
        };

        try {
            const response = await fetch(`/courses/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                closeModal('editCourseModal');
                loadCourses();
            } else {
                let errorMsg = data.message || 'Failed to update course.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                alertBox.innerHTML = errorMsg;
                alertBox.classList.remove('d-none');
            }
        } catch (err) {
            alertBox.innerText = 'Network error: ' + err.message;
            alertBox.classList.remove('d-none');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Update Course';
        }
    }

    // --- SHOW MODAL ---
    function openShowModal(course) {
        document.getElementById('show_courseId').innerText = `#${course.id}`;
        document.getElementById('show_courseCode').innerText = course.code;
        document.getElementById('show_courseName').innerText = course.name;
        document.getElementById('show_courseCredits').innerText = `${course.credits ?? 3} Credits`;
        document.getElementById('show_courseStudents').innerText = `${course.students_count ?? 0} Students`;
        document.getElementById('show_courseDescription').innerText = course.description || 'No description available.';
        document.getElementById('show_courseFullPageLink').href = `/courses/${course.id}`;
        getModal('showCourseModal').show();
    }

    // --- DELETE ---
    async function deleteCourse(id) {
        if (!confirm('Are you sure you want to delete this course?')) return;

        try {
            const response = await fetch(`/courses/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                loadCourses();
            } else {
                const data = await response.json();
                alert(data.message || 'Could not delete course.');
            }
        } catch (err) {
            alert('Error deleting course: ' + err.message);
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

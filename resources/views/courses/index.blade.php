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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#courseModal" onclick="openCreateModal()">
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
            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#courseModal" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i> Add Course
            </button>
        </div>
    </div>
</div>

<!-- Modal for Create / Edit Course -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalAlert" class="alert alert-danger d-none"></div>

                <form id="courseForm" onsubmit="handleFormSubmit(event)">
                    <input type="hidden" id="courseId">

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="code" class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" required placeholder="e.g. CS101">
                        </div>
                        <div class="col-md-7 mb-3">
                            <label for="name" class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" required placeholder="e.g. Database Systems">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="credits" class="form-label fw-semibold">Credits</label>
                        <input type="number" class="form-control" id="credits" min="1" max="10" value="3" placeholder="3">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Brief course overview..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <i class="bi bi-check2-circle me-1"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    function getModal() {
        return bootstrap.Modal.getOrCreateInstance(document.getElementById('courseModal'));
    }

    // Load courses on page ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadCourses);
    } else {
        loadCourses();
    }

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
                                    <a href="/courses/${course.id}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-warning" onclick='openEditModal(${JSON.stringify(course)})' title="Edit">
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

    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add New Course';
        document.getElementById('courseForm').reset();
        document.getElementById('courseId').value = '';
        document.getElementById('credits').value = '3';
        document.getElementById('modalAlert').classList.add('d-none');
    }

    function openEditModal(course) {
        document.getElementById('modalTitle').innerText = 'Edit Course';
        document.getElementById('courseId').value = course.id;
        document.getElementById('code').value = course.code;
        document.getElementById('name').value = course.name;
        document.getElementById('credits').value = course.credits ?? 3;
        document.getElementById('description').value = course.description || '';
        document.getElementById('modalAlert').classList.add('d-none');
        getModal().show();
    }

    async function handleFormSubmit(event) {
        event.preventDefault();
        const alertBox = document.getElementById('modalAlert');
        const saveBtn = document.getElementById('saveBtn');

        alertBox.classList.add('d-none');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const id = document.getElementById('courseId').value;
        const payload = {
            code: document.getElementById('code').value.trim(),
            name: document.getElementById('name').value.trim(),
            credits: document.getElementById('credits').value ? parseInt(document.getElementById('credits').value) : 3,
            description: document.getElementById('description').value.trim()
        };

        const isEdit = Boolean(id);
        const url = isEdit ? `/courses/${id}` : '/courses';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                getModal().hide();
                loadCourses();
            } else {
                let errorMsg = data.message || 'Operation failed.';
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
            saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save';
        }
    }

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

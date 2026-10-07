@extends('layout.app')

@section('title', 'Students Management')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-primary"></i>
            <span>Students List</span>
        </h5>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('students.export') }}" class="btn btn-success d-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('students.export-pdf') }}" class="btn btn-danger d-flex align-items-center gap-1">
                <i class="bi bi-file-earmark-pdf"></i> Download PDF
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="bi bi-plus-lg"></i> Add New Student
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2 mb-0">Loading students...</p>
        </div>

        <!-- Table Container -->
        <div id="tableContainer" class="table-responsive d-none">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 80px;">#ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>Enrolled Courses</th>
                        <th class="text-end pe-4" style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <!-- Rows dynamically inserted from JSON -->
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5 d-none">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-secondary">No students found</h5>
            <p class="text-muted">Click the button below to add your first student.</p>
            <a href="{{ route('students.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-person-plus me-1"></i> Add Student
            </a>
        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Load students on page ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadStudents);
    } else {
        loadStudents();
    }

    // Fetch all students from API (Returns JSON)
    async function loadStudents() {
        const spinner = document.getElementById('loadingSpinner');
        const container = document.getElementById('tableContainer');
        const emptyState = document.getElementById('emptyState');
        const tbody = document.getElementById('studentsTableBody');

        spinner.classList.remove('d-none');
        container.classList.add('d-none');
        emptyState.classList.add('d-none');

        try {
            const response = await fetch('/students', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();

            spinner.classList.add('d-none');

            if (result.data && result.data.length > 0) {
                tbody.innerHTML = '';
                result.data.forEach(student => {
                    const row = `
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">#${student.id}</td>
                            <td class="fw-semibold text-dark">${escapeHtml(student.name)}</td>
                            <td>
                                <a href="mailto:${escapeHtml(student.email)}" class="text-decoration-none text-muted">
                                    <i class="bi bi-envelope me-1"></i>${escapeHtml(student.email)}
                                </a>
                            </td>
                            <td>
                                ${student.phone 
                                    ? `<span class="text-secondary"><i class="bi bi-telephone me-1"></i>${escapeHtml(student.phone)}</span>` 
                                    : '<span class="text-muted fst-italic">N/A</span>'}
                            </td>
                            <td>
                                ${student.age 
                                    ? `<span class="badge badge-age">${student.age} yrs</span>` 
                                    : '<span class="text-muted fst-italic">N/A</span>'}
                            </td>
                            <td>
                                ${(student.courses && student.courses.length > 0)
                                    ? student.courses.map(c => `<a href="/courses/${c.id}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none me-1" title="${escapeHtml(c.name)}">${escapeHtml(c.code)}</a>`).join('')
                                    : '<span class="text-muted fst-italic small">None</span>'}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    <a href="/students/${student.id}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/students/${student.id}/edit" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent(${student.id})" title="Delete">
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
            spinner.classList.add('d-none');
            alert('Failed to load JSON data from /students: ' + error.message);
        }
    }

    // Delete Student via API (Fetch DELETE)
    async function deleteStudent(id) {
        if (!confirm('Are you sure you want to delete this student?')) return;

        try {
            const response = await fetch(`/students/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                loadStudents(); // Reload table data via JSON API
            } else {
                alert('Could not delete student');
            }
        } catch (error) {
            alert('Error deleting student: ' + error.message);
        }
    }

    // Utility: XSS prevention
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }
</script>
@endsection

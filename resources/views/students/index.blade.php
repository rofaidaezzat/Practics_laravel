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
            <button class="btn btn-warning d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-arrow-up"></i> Import Excel
            </button>
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
{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="importModalLabel">
                    <i class="bi bi-file-earmark-arrow-up text-warning"></i>
                    Import Students from Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pt-3">

                    {{-- Row-level import errors --}}
                    @if(session('import_errors'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Import Validation Errors:</div>
                            <ul class="mb-0 ps-3" style="font-size:0.85rem;">
                                @foreach(session('import_errors') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- File Drop Zone --}}
                    <div id="dropZone"
                         class="border border-2 border-dashed rounded-3 p-5 text-center"
                         style="border-color:#e2e8f0; cursor:pointer; transition:all 0.2s;"
                         onclick="document.getElementById('importFile').click()"
                         ondragover="event.preventDefault(); this.style.borderColor='#4f46e5'; this.style.background='#f0f0ff';"
                         ondragleave="this.style.borderColor='#e2e8f0'; this.style.background='';"
                         ondrop="handleDrop(event)">
                        <i class="bi bi-cloud-upload fs-1 text-muted mb-2 d-block"></i>
                        <p class="mb-1 fw-semibold text-dark">Drag & drop your file here</p>
                        <p class="text-muted small mb-0">or click to browse &mdash; <span class="text-primary">.xlsx, .xls, .csv</span> (max 5MB)</p>
                        <div id="fileName" class="mt-2 text-success fw-semibold small d-none"></div>
                    </div>

                    <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" class="d-none"
                           onchange="showFileName(this)">

                    @error('file')
                        <div class="text-danger small mt-2"><i class="bi bi-x-circle me-1"></i>{{ $message }}</div>
                    @enderror

                    <div class="mt-3 p-3 bg-light rounded-3">
                        <p class="small fw-semibold mb-2"><i class="bi bi-info-circle text-primary me-1"></i>File Format Requirements:</p>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>First row must contain headers: <code>name, email, phone, age</code></li>
                            <li><strong>name</strong> and <strong>email</strong> are required</li>
                            <li>Each email must be unique (not already in the system)</li>
                            <li><strong>phone</strong> and <strong>age</strong> are optional</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning d-flex align-items-center gap-1" id="importBtn">
                        <i class="bi bi-file-earmark-arrow-up"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showFileName(input) {
        const label = document.getElementById('fileName');
        if (input.files && input.files[0]) {
            label.textContent = '\u2713 ' + input.files[0].name;
            label.classList.remove('d-none');
        }
    }

    function handleDrop(event) {
        event.preventDefault();
        const zone = document.getElementById('dropZone');
        zone.style.borderColor = '#e2e8f0';
        zone.style.background = '';
        const dt = event.dataTransfer;
        if (dt.files && dt.files[0]) {
            const fileInput = document.getElementById('importFile');
            fileInput.files = dt.files;
            showFileName(fileInput);
        }
    }

    // Auto-open modal if there are import errors
    @if(session('import_errors'))
        document.addEventListener('DOMContentLoaded', function() {
            new bootstrap.Modal(document.getElementById('importModal')).show();
        });
    @endif
</script>
@endsection

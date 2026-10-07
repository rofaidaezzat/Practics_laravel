<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student & Course Management')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #4f46e5;
            --text-muted-sidebar: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .app-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #fff;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand:hover {
            color: #ffffff;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .brand-text h5 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .brand-text small {
            font-size: 0.75rem;
            color: var(--text-muted-sidebar);
            display: block;
        }

        .sidebar-menu {
            padding: 1.25rem 0.85rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 700;
            padding: 0.75rem 0.75rem 0.35rem;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0.9rem;
            color: var(--text-muted-sidebar);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.925rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .nav-item-link:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
        }

        .nav-item-link.active {
            color: #ffffff;
            background: var(--sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .nav-item-link i {
            font-size: 1.15rem;
        }

        .sidebar-footer {
            padding: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.15);
        }

        /* Layout Structure */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .content-body {
            padding: 2rem 1.75rem;
            flex-grow: 1;
        }

        /* Cards & Buttons & Tables */
        .card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.04);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.15s ease-in-out;
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #cbd5e1;
        }

        .badge-age {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        /* Mobile Trigger */
        .mobile-header {
            display: none;
            padding: 0.75rem 1.25rem;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .app-sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .app-sidebar.show {
                margin-left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1030;
                display: none;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Backdrop for Mobile -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <a href="{{ route('students.index') }}" class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="brand-text">
                <h5>EduPortal</h5>
                <small>Management System</small>
            </div>
        </a>

        <div class="sidebar-menu">
            <div class="menu-header">Navigation</div>

            <!-- Students Link -->
            <a href="{{ route('students.index') }}" class="nav-item-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Students</span>
            </a>

            <!-- Courses Link -->
            <a href="{{ route('courses.index') }}" class="nav-item-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark-fill"></i>
                <span>Courses</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary bg-opacity-25 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="text-white small fw-bold text-truncate">Administrator</div>
                    <div class="text-muted small" style="font-size: 0.725rem;">Active Session</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Mobile-Only Header for Toggling Sidebar -->
        <div class="mobile-header d-lg-none shadow-sm">
            <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 border-0" type="button" onclick="toggleSidebar()" aria-label="Toggle Navigation">
                <i class="bi bi-list fs-4"></i>
                <span class="fw-semibold">Menu</span>
            </button>
            <span class="fw-bold text-dark">EduPortal</span>
        </div>

        <!-- Main Body -->
        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Uni-Serve</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    @stack('styles')
</head>

<body data-barba="wrapper">
    <!-- Unique Loader -->
    <div id="loader-wrapper">
        <div class="uni-spinner">
            <div class="center-dot"></div>
        </div>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <a href="{{ route('admin.dashboard') }}"
            class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none logo-area">
            <span class="fs-4 fw-bold text-white tracking-wide">Uni-Serve</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i>
                    Users
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}"
                    class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-2"></i>
                    Reports
                </a>
            </li>
            <li>
                <a href="{{ route('admin.skills.index') }}"
                    class="nav-link {{ request()->routeIs('admin.skills.*') ? 'active' : '' }}">
                    <i class="bi bi-tools me-2"></i>
                    Skills & Categories
                </a>
            </li>
            <li>
                <a href="{{ route('admin.announcements.index') }}"
                    class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone me-2"></i>
                    Announcements
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                    style="width: 32px; height: 32px;">
                    {{ substr(Auth::user()->username ?? 'A', 0, 1) }}
                </div>
                <strong>{{ Auth::user()->username ?? 'Admin' }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small" aria-labelledby="dropdownUser1">
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" data-barba="container" data-barba-namespace="admin">
        <!-- Header -->
        <header class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top px-4 py-3">
            <div class="container-fluid p-0">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 fw-semibold text-secondary page-title">@yield('title')</h4>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-3 text-end d-none d-md-block">
                        <small class="text-muted d-block">Admin Panel</small>
                        <span class="fw-bold text-dark">{{ now()->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="container-fluid p-4 content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
            @stack('scripts')
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('load', function () {
            const loader = document.getElementById('loader-wrapper');
            loader.classList.add('loaded');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        });
    </script>
</body>

</html>
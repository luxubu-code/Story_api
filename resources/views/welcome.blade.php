@php
    use Illuminate\Support\Facades\Request;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Admin Dashboard')">
    <meta name="author" content="@yield('meta_author', 'Your Company Name')">
    <title>@yield('title', config('app.name', 'Dashboard'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #007bff;
            --hover-bg: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            background-color: var(--hover-bg);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .container-fluid {
            display: flex;
            min-height: 100vh;
            padding: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: #ffffff;
            border-right: 1px solid var(--border-color);
            padding: 1.5rem 1rem;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            background-color: var(--hover-bg);
            min-height: 100vh;
        }

        /* Navigation Styles */
        .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 1.5rem;
            text-align: center;
        }

        .nav-link:hover {
            background-color: var(--hover-bg);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        .nav-link.active:hover {
            transform: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: block;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 1001;
            }
        }

        /* Card Animations */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Mobile Toggle Button -->
    <button class="btn btn-primary toggle-sidebar d-md-none">
        <i class="bi bi-list"></i>
    </button>

    <div class="container-fluid">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="mb-4">
                <h5 class="text-muted mb-3">Dashboard</h5>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('stories.index') }}"
                        class="nav-link {{ Request::routeIs('stories.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Stories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.index') }}"
                        class="nav-link {{ Request::routeIs('admin.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        <span>VIP Statistics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('comment.index') }}"
                        class="nav-link {{ Request::routeIs('comment.index') ? 'active' : '' }}">
                        <i class="bi bi-chat"></i>
                        <span>Comments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rating.index') }}"
                        class="nav-link {{ Request::routeIs('rating.index') ? 'active' : '' }}">
                        <i class="bi bi-star"></i>
                        <span>Ratings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        document.querySelector('.toggle-sidebar')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>

</html>

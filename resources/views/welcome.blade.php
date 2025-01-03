@php
    use Illuminate\Support\Facades\Request;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Application')</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container-fluid {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            padding: 20px 10px;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
        }

        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 30px;
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .nav-link {
            color: #333;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: #f8f9fa;
            color: #007bff;
        }

        .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        .nav-link i {
            margin-right: 10px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('stories.index') }}"
                        class="nav-link {{ Request::routeIs('stories.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Stories
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.index') }}"
                        class="nav-link {{ Request::routeIs('admin.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Thống kê VIP</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('comment.index') }}"
                        class="nav-link {{ Request::routeIs('comment.index') ? 'active' : '' }}">
                        <i class="bi bi-chat"></i>
                        <span>Bình luận</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    @stack('scripts')
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
        }

        .page-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .nav-tabs {
            margin-bottom: 20px;
        }

        .user-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container mt-5">

        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1 class="h3">User Management</h1>

            {{-- <!-- Search Form -->
            <form class="d-flex" method="GET" action="{{ route('users.search') }}">
                <input class="form-control me-2" type="search" name="search" placeholder="Search users..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form> --}}
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Users List Section -->
        <div class="row">
            @if (empty($usersArray))
                <p class="text-center">No users found.</p>
            @else
                @foreach ($usersArray as $user)
                    <div class="col-md-4 mb-4">
                        <div class="card user-card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">{{ $user['name'] }}</h5>
                                <p class="card-text"><strong>Email:</strong> {{ $user['email'] }}</p>
                                <p class="card-text"><strong>Registered:</strong>
                                    {{ \Carbon\Carbon::parse($user['created_at'])->format('F d, Y') }}
                                </p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <small class="text-muted">
                                    {{ $user['google_id'] ? 'Google Account' : 'Standard Account' }}
                                </small>
                                <div>
                                    <a href="{{ route('users.edit', $user['id']) }}"
                                        cla     ss="btn btn-sm btn-outline-primary me-2">
                                        Edit
                                    </a>
                                    <form action="{{ route('users.delete', $user['id']) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>

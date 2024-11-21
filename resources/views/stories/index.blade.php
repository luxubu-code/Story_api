<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stories List</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for better appearance */
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

        .card {
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: #333;
        }

        .card-text {
            font-size: 0.95rem;
            color: #555;
        }

        .card-footer {
            background-color: #f8f9fa;
            border-top: none;
            padding-top: 10px;
            font-size: 0.85rem;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .form-control,
        .form-select {
            box-shadow: none;
            border-radius: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="container mt-5">

        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1 class="h3">Stories List</h1>

            <!-- Search Form -->
            <form class="d-flex" method="GET" action="{{ route('stories.search') }}">
                <input class="form-control me-2" type="search" name="search" placeholder="Search stories..."
                    aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>

            <!-- Button to trigger modal to add new story -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoryModal">
                Add New Story
            </button>
        </div>

        <!-- Flash Messages for Success or Error -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stories List Section -->
        <div class="row">
            @if (empty($storiesArray))
                <p class="text-center">No stories available.</p>
            @else
                @foreach ($storiesArray as $story)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <!-- Make the image clickable -->
                            <a href="{{ route('stories.show', $story['id']) }}">
                                <img src="{{ $story['image_path'] }}" class="card-img-top" alt="{{ $story['title'] }}"
                                    style="max-height: 200px; object-fit: cover;">
                            </a>
                            <div class="card-body">
                                <!-- Make the story title a clickable link to the show page -->
                                <h5 class="card-title">
                                    {{ $story['title'] }}</a>
                                </h5>
                                <p class="card-text"><strong>Author:</strong> {{ $story['author'] }}</p>
                                <p class="card-text"><strong>Views:</strong> {{ $story['views'] ?? 'N/A' }}</p>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($story['description'], 100) }}
                                </p>
                            </div>
                            <div class="card-footer text-muted">
                                <small>Rating: {{ $story['averageRating'] }}</small><br>
                                <small>Published on:
                                    {{ \Carbon\Carbon::parse($story['created_at'])->format('F d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Modal for Adding New Story -->
        <div class="modal fade" id="addStoryModal" tabindex="-1" aria-labelledby="addStoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStoryModalLabel">Add New Story</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form to add a new story -->
                        <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Story Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="author" class="form-label">Author</label>
                                <input type="text" class="form-control" id="author" name="author" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="categories" class="form-label">Categories</label>
                                <div id="categories">
                                    @foreach ($categories as $category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="category_{{ $category->category_id }}" name="categories[]"
                                                value="{{ $category->category_id }}">
                                            <label class="form-check-label"
                                                for="category_{{ $category->category_id }}">
                                                {{ $category->title }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" class="form-control" id="image" name="image" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Story</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>

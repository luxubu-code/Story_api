<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $story['title'] }} - Story Details</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f8ff;
            /* Nền xanh nhạt */
            color: #333;
        }

        .story-details {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            transition: box-shadow 0.3s ease-in-out;
        }

        .story-details h1 {
            font-size: 2.7rem;
            font-weight: 800;
            color: #007bff;
            /* Màu xanh nổi bật */
        }

        .story-details p {
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
            line-height: 1.6;
        }

        .story-details img {
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .story-details p strong {
            color: #0056b3;
            /* Màu chữ đậm */
        }

        .badge {
            background-color: #17a2b8;
            /* Đổi màu huy hiệu */
            color: #ffffff;
            padding: 0.6rem;
            font-size: 1rem;
            font-weight: 500;
            margin-right: 7px;
            border-radius: 5px;
        }

        .chapter-list-header h2 {
            font-size: 2rem;
            color: #007bff;
            font-weight: 700;
        }

        .list-group-item {
            padding: 18px;
            border-radius: 10px;
            background-color: #f9f9f9;
            margin-bottom: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #f0f8ff;
            transform: scale(1.01);
            transition: transform 0.2s ease-in-out;
        }

        .btn-danger {
            padding: 0.35rem 0.8rem;
            font-size: 0.875rem;
            background-color: #dc3545;
        }

        .btn-primary {
            padding: 0.5rem 1rem;
            background-color: #007bff;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- Back and Delete Buttons -->
        <div class="d-flex justify-content-between mb-3">
            <!-- Back Button -->
            <a href="{{ route('stories.index') }}" class="btn btn-secondary">Back</a>

            <!-- Delete Button -->
            <form method="POST" action="{{ route('stories.destroy', $story['id']) }}"
                onsubmit="return confirm('Are you sure you want to delete this story?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Story</button>
            </form>
        </div>
        <!-- Story Details -->
        <div class="row story-details">
            <div class="col-md-4">
                <img src="{{ $story['image_path'] }}" alt="{{ $story['title'] }}" class="img-fluid">
            </div>
            <div class="col-md-8">
                <h1>{{ $story['title'] }}</h1>
                <p><strong>Author:</strong> {{ $story['author'] }}</p>
                <p><strong>Description:</strong> {{ $story['description'] }}</p>
                <p><strong>Views:</strong> {{ $story['views'] }}</p>
                <p><strong>Average Rating:</strong> {{ $story['averageRating'] }}</p>
                <p><strong>Categories:</strong>
                    @foreach ($story['categories'] as $category)
                        <span class="badge">{{ $category['title'] }}</span>
                    @endforeach
                </p>
                <p><strong>Status:</strong> {{ $story['status'] }}</p>
            </div>
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

        <!-- Chapters List -->
        <div class="chapter-list-header">
            <h2>Chapters</h2>
        </div>
        <ul class="list-group mb-4">
            @foreach ($story['chapter'] as $chapter)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $chapter['title'] }}</span>
                    <small>Views: {{ $chapter['views'] }} | Created:
                        {{ \Carbon\Carbon::parse($chapter['created_at'])->format('F d, Y') }}</small>
                    <form method="POST" action="{{ route('stories.chapters.destroy', $chapter['id']) }}"
                        onsubmit="return confirm('Are you sure you want to delete this chapter?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>

        <!-- Upload New Chapter -->
        <div class="chapter-list-header">
            <h2>Upload New Chapter</h2>
        </div>
        <form method="POST" action="{{ route('stories.upload', $story['id']) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Chapter Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="images_zip" class="form-label">Upload Images (ZIP File)</label>
                <input type="file" class="form-control" id="images_zip" name="images_zip" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Chapter</button>
        </form>

    </div>

    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>

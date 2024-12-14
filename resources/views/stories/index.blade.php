<!-- stories/index.blade.php -->
@extends('welcome')

@section('title', 'Stories')

@section('content')
    <!-- Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Stories List</h1>
        <!-- Tìm kiếm -->
        <form class="d-flex" method="GET" action="{{ route('stories.search') }}">
            <input class="form-control me-2" type="search" name="search" placeholder="Search stories..." aria-label="Search">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
        <!-- Nút thêm Story -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoryModal">
            Add New Story
        </button>
    </div>

    <!-- Danh sách Stories -->
    <div class="row">
        @if (empty($storiesArray))
            <p class="text-center">No stories available.</p>
        @else
            @foreach ($storiesArray as $story)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('stories.show', $story['id']) }}">
                            <img src="{{ $story['image_path'] }}" class="card-img-top" alt="{{ $story['title'] }}"
                                style="max-height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">{{ $story['title'] }}</h5>
                            <p class="card-text"><strong>Author:</strong> {{ $story['author'] }}</p>
                            <p class="card-text"><strong>Views:</strong> {{ $story['views'] ?? 'N/A' }}</p>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($story['description'], 100) }}</p>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Rating: {{ $story['averageRating'] }}</small><br>
                            <small>Published on: {{ \Carbon\Carbon::parse($story['created_at'])->format('F d, Y') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="container mt-5">
        <!-- Modal for Adding New Story -->
        <div class="modal fade" id="addStoryModal" tabindex="-1" aria-labelledby="addStoryModalLabel" aria-hidden="true">
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
                                            <label class="form-check-label" for="category_{{ $category->category_id }}">
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Story</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

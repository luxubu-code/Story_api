@extends('welcome')

@section('title', 'Story Management')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Header section with search bar and add new button -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Manage Stories</h1>
                <div class="d-flex gap-3">
                    <!-- Enhanced search form -->
                    <form class="d-flex" method="GET" action="{{ route('stories.search') }}">
                        <div class="input-group">
                            <input class="form-control border-end-0" type="search" name="search"
                                placeholder="Search stories..." aria-label="Search">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <!-- Add new button with hover effect -->
                    <button type="button" class="btn btn-primary hover-shadow" data-bs-toggle="modal"
                        data-bs-target="#addStoryModal">
                        <i class="fas fa-plus-circle me-1"></i> Add New Story
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table to display stories in a management view -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if (empty($storiesArray))
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i> No stories available.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Views</th>
                                <th>Rating</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($storiesArray as $index => $story)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <img src="{{ $story['image_path'] }}" alt="{{ $story['title'] }}"
                                            class="img-thumbnail" style="width: 100px; height: 75px; object-fit: cover;">
                                    </td>
                                    <td>{{ $story['title'] }}</td>
                                    <td>{{ $story['author'] }}</td>
                                    <td>{{ $story['views'] ?? '0' }}</td>
                                    <td>{{ number_format($story['averageRating'], 1) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($story['created_at'])->format('F d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Nút sửa -->
                                            <a href="{{ route('stories.show', $story['id']) }}"
                                                class="btn btn-outline-info btn-sm transition" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit Story">
                                                <i class="fas fa-pen me-1"></i> Edit
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Story Modal -->
    <div class="modal fade" id="addStoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-book-medical me-2"></i>Add New Story
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Story Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Author</label>
                            <input type="text" class="form-control" name="author" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Categories</label>
                            <div class="row row-cols-2 row-cols-md-3 g-3">
                                @foreach ($categories as $category)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="category_{{ $category->category_id }}" name="categories[]"
                                                value="{{ $category->category_id }}">
                                            <label class="form-check-label" for="category_{{ $category->category_id }}">
                                                {{ $category->title }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Upload Image</label>
                            <input type="file" class="form-control" name="image" required accept="image/*"
                                onchange="previewImage(event)">
                            <div id="imagePreview" class="mt-2 d-none">
                                <img src="" class="img-thumbnail" style="max-height: 200px">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Story
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .hover-shadow:hover {
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }

            .transition {
                transition: all 0.3s ease;
            }

            table th,
            table td {
                text-align: center;
                vertical-align: middle;
            }

            .btn-outline-info:hover {
                color: #fff;
                background-color: #17a2b8;
                border-color: #17a2b8;
            }

            .btn-outline-danger:hover {
                color: #fff;
                background-color: #dc3545;
                border-color: #dc3545;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function previewImage(event) {
                const preview = document.querySelector('#imagePreview img');
                const previewDiv = document.getElementById('imagePreview');
                preview.src = URL.createObjectURL(event.target.files[0]);
                previewDiv.classList.remove('d-none');
            }
        </script>
    @endpush

@endsection

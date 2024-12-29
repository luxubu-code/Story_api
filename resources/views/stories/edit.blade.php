<!-- resources/views/stories/edit.blade.php -->
@extends('app')

@section('title', 'Edit Story - ' . $story['title'])

@section('content')
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Edit Story: {{ $story['title'] }}</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('stories.update', $story['id']) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title', $story['title']) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="4" required>{{ old('description', $story['description']) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                            name="category_id" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $story['category_id']) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="ongoing" {{ old('status', $story['status']) == 'ongoing' ? 'selected' : '' }}>
                                Ongoing
                            </option>
                            <option value="completed"
                                {{ old('status', $story['status']) == 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail</label>
                        <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail"
                            name="thumbnail">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($story['thumbnail'])
                            <div class="mt-2">
                                <img src="{{ $story['thumbnail'] }}" alt="Current thumbnail" class="img-thumbnail"
                                    style="max-height: 200px">
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stories.show', $story['id']) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Story</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

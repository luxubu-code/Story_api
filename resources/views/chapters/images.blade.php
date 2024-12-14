@extends('app')
@section('title', 'Danh sách ảnh')

@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('stories.show', $chapter->story_id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <h1 class="h3 mb-0 text-center flex-grow-1">Danh sách ảnh của chương: {{ $chapter->title }}</h1>
            <div></div> <!-- Empty div để giữ căn giữa -->
        </div>

        <!-- Image Grid -->
        <div class="row row-cols-1 row-cols-md-3 g-3">
            @foreach ($chapter->images as $image)
                <div class="col">
                    <div class="card shadow-sm border-0">
                        <img src="{{ $image->base_url }}/{{ $image->file_name }}" alt="Image"
                            class="card-img-top img-fluid rounded-top"
                            style="object-fit: contain; height: 200px; cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#imageModal-{{ $image->image_id }}">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <p class="card-text text-center text-secondary small mb-2">Ảnh ID: {{ $image->image_id }}</p>
                            <a href="{{ route('images.edit', $image->image_id) }}"
                                class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-pencil"></i> Chỉnh sửa
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modal for Enlarged Image -->
                <div class="modal fade" id="imageModal-{{ $image->image_id }}" tabindex="-1"
                    aria-labelledby="imageModalLabel-{{ $image->image_id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel-{{ $image->image_id }}">Ảnh ID:
                                    {{ $image->image_id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img src="{{ $image->base_url }}/{{ $image->file_name }}" alt="Image" class="img-fluid"
                                    style="object-fit: contain; width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

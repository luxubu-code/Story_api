@extends('app')
@section('title', 'Chỉnh sửa ảnh')

@section('content')
    <div class="container">

        <h1>Chỉnh sửa ảnh</h1>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('chapters.images', $image->chapter_id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('images.update', $image->image_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="file_name" class="form-label">Chọn ảnh mới</label>
                <input type="file" name="file_name" id="file_name" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-success">Lưu thay đổi</button>
        </form>
    </div>
@endsection

@extends('welcome')
@section('title', 'Bình luận')

@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-chat"></i> Danh Sách Bình Luận
                </h1>
                <div class="col-md-3">

                    <label class="form-label">Sắp xếp theo : </label>
                    <select class="form-select" name="sort_by">
                        <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="comment_high" {{ request('sort_by') == 'likes_high' ? 'selected' : '' }}>Lượt thích
                            cao
                            nhất</option>
                        <option value="comment_low" {{ request('sort_by') == 'likes_low' ? 'selected' : '' }}>Lượt thích
                            thấp
                            nhất</option>
                    </select>
                    {{-- <select class="form-select" name="sort_by">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                        <option value="comment_high">Đánh giá cao nhất</option>
                        <option value="comment_low">Đánh giá thấp nhất</option>
                    </select> --}}
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Truyện được bình luận</th>
                            <th>Nội dung bình luận</th>
                            <th>Người dùng bình luận</th>
                            <th>Ngày bình luận</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $index => $comment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $comment->story->title }}
                                <td>
                                    {{ $comment->content }}
                                </td>
                                <td>
                                    {{ $comment->user->name }}
                                </td>
                                <td>
                                    {{ $comment->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <form action="{{ route('comment.delete', $comment->id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm transition"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Rating"
                                                onclick="return confirm('Are you sure you want to delete this comment?')">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .modal-header {
            background-color: #f8f9fa;
        }
    </style>
@endpush
@push('scripts')
    <script>
        // Lắng nghe sự kiện thay đổi giá trị của select box
        document.querySelector('select[name="sort_by"]').addEventListener('change', function() {
            // Khi người dùng chọn option mới, chuyển hướng trang với tham số sort_by
            // Ví dụ: /ratings?sort_by=oldest
            window.location.href = `${window.location.pathname}?sort_by=${this.value}`;
        });
    </script>
@endpush

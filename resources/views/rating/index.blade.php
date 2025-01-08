@extends('welcome')
@section('title', 'Đánh giá')

@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-star"></i> Danh Sách Đánh Giá
                </h1>
                <div class="col-md-3">
                    <label class="form-label">Sắp xếp theo : </label>
                    <select class="form-select" name="sort_by">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                        <option value="rating_high">Đánh giá cao nhất</option>
                        <option value="rating_low">Đánh giá thấp nhất</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nội dung đánh giá</th>
                            <th>Số sao đánh giá</th>
                            <th>Người dùng đánh giá</th>
                            <th>Truyện được đánh giá</th>
                            <th>Thời gian</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ratings as $index => $rating)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $rating->title }}
                                </td>
                                <td>
                                    {{ $rating->rating }} <i class="fas fa-star text-warning"></i>
                                </td>
                                <td>
                                    {{ $rating->user->name }}
                                </td>
                                <td>
                                    @if ($rating->story->title)
                                        {{ $rating->story->title }}
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        {{ $rating->created_at->format('d/m/Y H:i') }}
                                        @if ($rating->updated_at->ne($rating->created_at))
                                            <br>
                                            {{ $rating->updated_at->format('d/m/Y H:i') }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <form action="{{ route('rating.delete', $rating->user_id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm transition"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Rating"
                                                onclick="return confirm('Are you sure you want to delete this rating?')">
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

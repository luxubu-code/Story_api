@extends('welcome')
@section('title', 'Bình luận')

@section('content')
    <div class="container">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-chat"></i> Danh Sách Bình Luận
                </h1>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nội dung bình luận</th>
                            <th>Người dùng bình luận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $index => $comment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $comment['content'] }}
                                </td>
                                <td>
                                    {{ $comment['user']['name'] }}
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

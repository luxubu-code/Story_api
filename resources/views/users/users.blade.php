@extends('welcome')

@section('title', 'Users')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-primary">
                        <i class="fas fa-users me-2"></i>Danh Sách Người Dùng
                    </h1>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>

                <!-- User List -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        @if ($users->isEmpty())
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i>Không có người dùng nào.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tên</th>
                                            <th>Email</th>
                                            <th>Ngày Sinh</th>
                                            <th>Ngày Đăng Ký</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->date_of_birth ? date('d/m/Y', strtotime($user->date_of_birth)) : 'Chưa cập nhật' }}
                                                </td>
                                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editUser{{ $user->id }}">
                                                        <i class="fas fa-edit me-1"></i>Sửa
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1"
                                                aria-labelledby="editUserLabel{{ $user->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('users.update', $user->id) }}" method="POST"
                                                            class="user-update-form">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editUserLabel{{ $user->id }}">Cập Nhật Thông
                                                                    Tin
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="name{{ $user->id }}"
                                                                        class="form-label">Tên</label>
                                                                    <input type="text" class="form-control"
                                                                        id="name{{ $user->id }}" name="name"
                                                                        value="{{ $user->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="date_of_birth{{ $user->id }}"
                                                                        class="form-label">Ngày Sinh</label>
                                                                    <input type="date" class="form-control"
                                                                        id="date_of_birth{{ $user->id }}"
                                                                        name="date_of_birth"
                                                                        value="{{ $user->date_of_birth }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="avatar{{ $user->id }}"
                                                                        class="form-label">Ảnh Đại Diện</label>
                                                                    <input type="file" class="form-control"
                                                                        id="avatar{{ $user->id }}" name="avatar"
                                                                        accept="image/*">
                                                                    @if ($user->avatar_url)
                                                                        <div class="mt-2">
                                                                            <img src="{{ $user->avatar_url }}"
                                                                                alt="Avatar" class="rounded-circle"
                                                                                style="width: 64px; height: 64px;">
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Lưu Thay
                                                                    Đổi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form submission
            const forms = document.querySelectorAll('.user-update-form');
            forms.forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const userId = this.action.split('/').pop();

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            alert('Cập nhật thành công!');
                            window.location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + data.message);
                        }
                    } catch (error) {
                        alert('Có lỗi xảy ra khi cập nhật thông tin');
                    }
                });
            });
        });
    </script>
@endpush

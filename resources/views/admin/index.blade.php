@extends('welcome')

@section('title', 'Thống kê Gói VIP')

@push('styles')
    <style>
        .dashboard-container {
            padding: 2rem;
            background-color: #f8f9fa;
        }

        .page-title {
            color: #2d3436;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .stat-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card .card-body {
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            margin: 1rem 0;
            letter-spacing: -0.5px;
        }

        .stat-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        .data-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .data-card .card-title {
            color: #2d3436;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            background-color: #fff;
            margin: 0;
        }

        .data-card .card-body {
            padding: 1.5rem;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            border-top: none;
            font-weight: 600;
            color: #2d3436;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            color: #4a4a4a;
        }

        .bg-primary {
            background: linear-gradient(45deg, #4a90e2, #357abd) !important;
        }

        .bg-success {
            background: linear-gradient(45deg, #2ecc71, #27ae60) !important;
        }

        .bg-info {
            background: linear-gradient(45deg, #3498db, #2980b9) !important;
        }

        .bg-warning {
            background: linear-gradient(45deg, #f1c40f, #f39c12) !important;
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-container">
        <h1 class="page-title">Thống kê Đăng ký Gói VIP</h1>

        <!-- Thẻ Thống kê Tổng quan -->
        <div class="row g-4 mb-4">
            @foreach ([['title' => 'Gói VIP Đang Hoạt động', 'value' => number_format($totalStats['total_active_subscriptions']), 'class' => 'bg-primary'], ['title' => 'Tổng Doanh Thu', 'value' => number_format($totalStats['total_revenue']) . 'đ', 'class' => 'bg-success'], ['title' => 'Doanh Thu 30 Ngày', 'value' => number_format($totalStats['last_30_days_revenue']) . 'đ', 'class' => 'bg-info'], ['title' => 'Tỷ Lệ Chuyển Đổi', 'value' => $totalStats['conversion_rate'] . '%', 'class' => 'bg-warning']] as $stat)
                <div class="col-md-3">
                    <div class="card stat-card {{ $stat['class'] }} text-white">
                        <div class="card-body">
                            <div class="stat-label">{{ $stat['title'] }}</div>
                            <div class="stat-value">{{ $stat['value'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Thống kê Gói -->
        <div class="row">
            <div class="col-md-6">
                <div class="data-card">
                    <h5 class="card-title">Lượt Đăng Ký Từng Gói</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên Gói</th>
                                        <th>Lượt Đăng Ký</th>
                                        <th>Doanh Thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packageStats as $package)
                                        <tr>
                                            <td>{{ $package->name }}</td>
                                            <td>{{ number_format($package->total_subscriptions) }}</td>
                                            <td>{{ number_format($package->total_revenue) }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="data-card">
                    <h5 class="card-title">Người Dùng Đăng Ký</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Người Dùng</th>
                                        <th>Gói</th>
                                        <th>Thời Gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentSubscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->user->name }}</td>
                                            <td>{{ $subscription->package->name }}</td>
                                            <td>{{ $subscription->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

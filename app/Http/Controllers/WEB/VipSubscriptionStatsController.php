<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\UserVipSubscription;
use App\Models\VipPackage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VipSubscriptionStatsController extends Controller
{
    public function index()
    {
        // Lấy thống kê tổng quan
        $totalStats = $this->getOverallStats();

        // Lấy dữ liệu doanh thu theo tháng cho biểu đồ
        $monthlyRevenue = $this->getMonthlyRevenue();

        // Lấy thống kê về độ phổ biến của các gói
        $packageStats = $this->getPackageStats();

        // Lấy danh sách đăng ký gần đây
        $recentSubscriptions = $this->getRecentSubscriptions();

        return view('admin.index', compact(
            'totalStats',
            'monthlyRevenue',
            'packageStats',
            'recentSubscriptions'
        ));
    }

    private function getOverallStats()
    {
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        return [
            'total_active_subscriptions' => UserVipSubscription::where('payment_status', 'completed')
                ->where('end_date', '>', now())
                ->count(),

            'total_revenue' => UserVipSubscription::where('payment_status', 'completed')
                ->join('vip_packages', 'user_vip_subscriptions.vip_package_id', '=', 'vip_packages.id')
                ->sum('vip_packages.price'),

            // Sửa lỗi bằng cách thêm tên bảng vào trước created_at
            'last_30_days_revenue' => UserVipSubscription::where('payment_status', 'completed')
                ->where('user_vip_subscriptions.created_at', '>=', $thirtyDaysAgo)
                ->join('vip_packages', 'user_vip_subscriptions.vip_package_id', '=', 'vip_packages.id')
                ->sum('vip_packages.price'),

            'conversion_rate' => $this->calculateConversionRate()
        ];
    }

    private function getMonthlyRevenue()
    {
        // Sửa cả phần monthly revenue để tránh lỗi tương tự
        return UserVipSubscription::where('payment_status', 'completed')
            ->join('vip_packages', 'user_vip_subscriptions.vip_package_id', '=', 'vip_packages.id')
            ->select(
                DB::raw('DATE_FORMAT(user_vip_subscriptions.created_at, "%Y-%m") as month'),
                DB::raw('SUM(vip_packages.price) as revenue'),
                DB::raw('COUNT(*) as subscriptions')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
    }
    private function getPackageStats()
    {
        return VipPackage::select('vip_packages.*')
            ->addSelect([
                'total_subscriptions' => UserVipSubscription::selectRaw('COUNT(*)')
                    ->whereColumn('vip_package_id', 'vip_packages.id')
                    ->where('payment_status', 'completed'),
                'total_revenue' => UserVipSubscription::selectRaw('COUNT(*) * vip_packages.price')
                    ->whereColumn('vip_package_id', 'vip_packages.id')
                    ->where('payment_status', 'completed')
            ])
            ->get();
    }

    private function getRecentSubscriptions()
    {
        return UserVipSubscription::with(['user', 'package'])
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function calculateConversionRate()
    {
        $totalAttempts = UserVipSubscription::count();
        $successfulSubscriptions = UserVipSubscription::where('payment_status', 'completed')->count();

        return $totalAttempts > 0
            ? round(($successfulSubscriptions / $totalAttempts) * 100, 2)
            : 0;
    }
}
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VipSubscriptionResource;
use App\Models\UserVipSubscription;
use Illuminate\Http\Request;
use App\Http\Helpers\ResponseHelper;
use App\Http\Helpers\ErrorHelper;
use Carbon\Carbon;

class VipSubscriptionHistoryController extends Controller
{

    public function index()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return ErrorHelper::unauthorized('Người dùng chưa đăng nhập');
            }
            $subscriptions = UserVipSubscription::with('package')
                ->where('user_id', $user->id)
                ->orderBy('start_date', 'desc')
                ->get();

            $subscriptions = $subscriptions->map(function ($subscription) {
                $data = new VipSubscriptionResource($subscription);
                return array_merge($data->resolve(), [
                    'is_active' => $subscription->isActive(),
                    'days_remaining' => $subscription->getDaysRemaining(),
                    'status' => $this->getSubscriptionStatus($subscription)
                ]);
            });

            return ResponseHelper::success([
                'subscriptions' => $subscriptions,
                'total_subscriptions' => $subscriptions->count(),
                'active_subscriptions' => $subscriptions->where('is_active', true)->count()
            ], 'Lấy lịch sử đăng ký VIP thành công');
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi lấy lịch sử đăng ký VIP');
        }
    }


    public function show($id)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return ErrorHelper::unauthorized('Người dùng chưa đăng nhập');
            }

            $subscription = UserVipSubscription::with('package')
                ->where('user_id', $user->id)
                ->findOrFail($id);

            $data = new VipSubscriptionResource($subscription);
            $subscriptionData = array_merge($data->resolve(), [
                'is_active' => $subscription->isActive(),
                'days_remaining' => $subscription->getDaysRemaining(),
                'status' => $this->getSubscriptionStatus($subscription)
            ]);

            return ResponseHelper::success($subscriptionData, 'Lấy chi tiết đăng ký VIP thành công');
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi lấy chi tiết đăng ký VIP');
        }
    }


    public function getActiveSubscription()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return ErrorHelper::unauthorized('Người dùng chưa đăng nhập');
            }

            $activeSubscription = UserVipSubscription::with('package')
                ->where('user_id', $user->id)
                ->where('payment_status', 'completed')
                ->where('end_date', '>', now())
                ->orderBy('end_date', 'desc')
                ->first();

            if (!$activeSubscription) {
                return ResponseHelper::success(null, 'Không có gói VIP đang hoạt động');
            }

            $data = new VipSubscriptionResource($activeSubscription);
            return ResponseHelper::success(array_merge($data->resolve(), [
                'days_remaining' => $activeSubscription->getDaysRemaining()
            ]), 'Lấy thông tin gói VIP đang hoạt động thành công');
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi lấy thông tin gói VIP đang hoạt động');
        }
    }

    private function getSubscriptionStatus($subscription)
    {
        if ($subscription->payment_status === 'pending') {
            return 'Chờ thanh toán';
        }

        if ($subscription->payment_status === 'failed') {
            return 'Thanh toán thất bại';
        }

        if ($subscription->payment_status === 'completed') {
            if ($subscription->end_date->isFuture()) {
                return 'Đang hoạt động';
            }
            return 'Đã hết hạn';
        }

        return 'Không xác định';
    }
}
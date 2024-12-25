<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\VipPackage;
use App\Services\VNPayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    protected $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    public function getCurrentSubscription()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chưa đăng nhập',
                    'auth_status' => 'unauthenticated'
                ], 401);
            }

            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('package')
                ->first();

            return response()->json([
                'status' => 'success',
                'auth_status' => 'authenticated',
                'data' => $subscription ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'auth_status' => 'authenticated',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chưa đăng nhập',
                    'auth_status' => 'unauthenticated'
                ], 401);
            }

            $history = UserSubscription::where('user_id', $user->id)
                ->with('package')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'auth_status' => 'authenticated',
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function purchase(Request $request)
    {
        try {
            $validated = $request->validate([
                'package_id' => 'required|exists:vip_packages,id',
            ]);

            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vui lòng đăng nhập để thực hiện giao dịch',
                    'auth_status' => 'unauthenticated'
                ], 401);
            }

            $package = VipPackage::find($request->package_id);

            if (!$package) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy gói VIP'
                ], 404);
            }

            $activeSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($activeSubscription) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn đang có gói đang hoạt động',
                    'current_subscription' => $activeSubscription
                ], 400);
            }

            return DB::transaction(function () use ($user, $package) {
                $subscription = UserSubscription::create([
                    'user_id' => $user->id,
                    'vip_package_id' => $package->id,
                    'start_date' => now(),
                    'end_date' => now()->addMonths($package->duration),
                    'status' => 'pending',
                    'price' => $package->price,
                    'package_name' => $package->name,
                    'package_duration' => $package->duration
                ]);

                $paymentUrl = $this->vnpayService->createPaymentUrl([
                    'id' => $subscription->id,
                    'total' => $package->price,
                    'description' => "Thanh toán gói {$package->name}"
                ]);

                Log::info('Created subscription successfully', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'package_id' => $package->id
                ]);

                return response()->json([
                    'status' => 'success',
                    'auth_status' => 'authenticated',
                    'data' => [
                        'payment_url' => $paymentUrl,
                        'subscription' => $subscription->load('package')
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Purchase failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth('api')->id(),
                'package_id' => $request->package_id
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau',
                'debug_message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    protected function updateOrderStatus($orderId, $status, $transactionId = null)
    {
        try {
            $subscription = UserSubscription::findOrFail($orderId);

            // Cập nhật trạng thái subscription
            $subscription->status = ($status === 'success') ? 'active' : 'failed';

            // Nếu có mã giao dịch, lưu lại
            if ($transactionId) {
                $subscription->transaction_id = $transactionId;
            }

            // Nếu thanh toán thành công, cập nhật ngày bắt đầu từ thời điểm hiện tại
            if ($status === 'success') {
                $subscription->start_date = now();
                $subscription->end_date = now()->addMonths($subscription->package_duration);
            }

            $subscription->save();

            // Ghi log
            Log::info('Subscription status updated', [
                'subscription_id' => $orderId,
                'status' => $status,
                'transaction_id' => $transactionId
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update subscription status', [
                'subscription_id' => $orderId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
    public function vnpayReturn(Request $request)
    {
        // Gọi hàm verifyPayment để xác thực dữ liệu trả về từ VNPAY
        $vnpayService = new VNPayService();
        $isValid = $vnpayService->verifyPayment($request);

        if ($isValid) {
            if ($request->vnp_ResponseCode == '00') {
                // Thanh toán thành công
                $orderId = $request->vnp_TxnRef; // ID của đơn hàng
                $transactionId = $request->vnp_TransactionNo; // Mã giao dịch tại VNPAY

                // Cập nhật trạng thái đơn hàng trong database
                $this->updateOrderStatus($orderId, 'success', $transactionId);

                // Ghi log thông tin thanh toán
                Log::info('Thanh toán thành công', [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                    'amount' => $request->vnp_Amount / 100, // Chuyển về VNĐ
                    'time' => $request->vnp_PayDate
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Thanh toán thành công!',
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId
                ]);
            } else {
                // Thanh toán thất bại hoặc bị hủy
                $orderId = $request->vnp_TxnRef;
                $this->updateOrderStatus($orderId, 'failed');

                Log::warning('Thanh toán thất bại hoặc bị hủy', [
                    'order_id' => $orderId,
                    'response_code' => $request->vnp_ResponseCode
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Thanh toán không thành công!'
                ]);
            }
        } else {
            Log::error('Xác thực thanh toán VNPAY thất bại');
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ!'
            ]);
        }
    }
}

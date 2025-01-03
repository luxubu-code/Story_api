<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserVipSubscription;
use App\Models\VipPackage;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VipSubscriptionController extends Controller
{
    private $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    public function showPackages()
    {
        // Thay vì trả về view, chúng ta cần trả về JSON cho API
        $packages = VipPackage::all();
        return response()->json([
            'packages' => $packages
        ]);
    }

    public function subscribe(Request $request, VipPackage $package)
    {
        $user = auth('api')->user();
        $subscription = UserVipSubscription::create([
            'user_id' => $user->id,
            'vip_package_id' => $package->id,
            'start_date' => now(),
            'end_date' => now()->addDays($package->duration_days),
            'payment_status' => 'pending'
        ]);

        $paymentUrl = $this->vnpayService->createPaymentUrl($subscription);
        Log::info('Generated Payment URL: ' . $paymentUrl);
        return response()->json([
            'payment_url' => urlencode($paymentUrl)
        ]);
    }
    public function handleVnPayReturn(Request $request)
    {
        try {
            // Validate required parameters
            $vnp_ResponseCode = $request->vnp_ResponseCode;
            $vnp_TxnRef = $request->vnp_TxnRef;
            $vnp_TransactionNo = $request->vnp_TransactionNo;

            if (!$vnp_TxnRef || !$vnp_ResponseCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameters',
                    'data' => null
                ], 400);
            }

            // Extract subscription ID and find the subscription
            $subscriptionId = explode('_', $vnp_TxnRef)[0];
            $subscription = UserVipSubscription::findOrFail($subscriptionId);

            if ($vnp_ResponseCode == '00') {
                // Payment successful
                $subscription->update([
                    'payment_status' => 'completed',
                    'vnpay_transaction_id' => $vnp_TransactionNo
                ]);
                $user = $subscription->user;
                $user->update([
                    'is_vip' => true,
                    'vip_expires_at' => $subscription->end_date
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'data' => [
                        'subscription_id' => $subscription->id,
                        'status' => 'completed',
                        'transaction_id' => $vnp_TransactionNo,
                        'is_vip' => true,
                        'vip_expires_at' => $subscription->end_date
                    ]
                ]);
            }

            // Payment failed
            $subscription->update(['payment_status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => 'Payment failed',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'status' => 'failed',
                    'error_code' => $vnp_ResponseCode
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('VNPay Return Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment',
                'data' => null
            ], 500);
        }
    }
    public function handleVnPayIPN(Request $request)
    {
        // Xác thực chữ ký
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, config('vnpay.hash_secret'));

        if ($secureHash == $request->vnp_SecureHash) {
            $vnp_TxnRef = $request->vnp_TxnRef;
            $subscriptionId = explode('_', $vnp_TxnRef)[0];
            $subscription = UserVipSubscription::findOrFail($subscriptionId);

            if ($request->vnp_ResponseCode == '00') {
                $subscription->update([
                    'payment_status' => 'completed',
                    'vnpay_transaction_id' => $request->vnp_TransactionNo
                ]);

                return response()->json([
                    'RspCode' => '00',
                    'Message' => 'Confirm Success'
                ]);
            }
        }
        Log::info('Input Data: ' . json_encode($inputData));
        Log::info('Hash Data: ' . $hashData);
        Log::info('Generated Hash: ' . $secureHash);
        Log::info('Received Hash: ' . $request->vnp_SecureHash);
        return response()->json([
            'RspCode' => '97',
            'Message' => 'Invalid Signature'
        ]);
    }
}
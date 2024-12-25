<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VNPayService
{
    /**
     * Create payment URL for VNPAY
     * @param array $order
     * @return string
     */
    public function createPaymentUrl($order)
    {
        try {
            // Extract required information
            $vnp_TxnRef = $order['id'];
            $vnp_OrderInfo = "Payment for order #" . $order['id'];
            $vnp_Amount = $order['total'] * 100;

            // Prepare input data
            $inputData = [
                "vnp_Version"    => "2.1.0",
                "vnp_TmnCode"    => config('vnpay.vnp_TmnCode'),
                "vnp_Amount"     => $vnp_Amount,
                "vnp_Command"    => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode"   => "VND",
                "vnp_IpAddr"     => request()->ip(),
                "vnp_Locale"     => "vn",
                "vnp_OrderInfo"  => $vnp_OrderInfo,
                "vnp_OrderType"  => "other",
                "vnp_ReturnUrl"  => config('vnpay.vnp_ReturnUrl'),
                "vnp_TxnRef"     => $vnp_TxnRef,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+30 minutes')),
            ];

            // Sort and build query string
            ksort($inputData);
            $queryString = http_build_query($inputData);

            // Generate secure hash
            $vnp_HashSecret = config('vnpay.vnp_HashSecret');
            if ($vnp_HashSecret) {
                $vnpSecureHash = hash_hmac('sha512', $queryString, $vnp_HashSecret);
                $queryString .= '&vnp_SecureHash=' . $vnpSecureHash;
            }

            // Final VNPAY URL
            $vnp_Url = config('vnpay.vnp_Url') . "?" . $queryString;

            // Log success
            Log::info('VNPay payment URL created successfully', [
                'order_id' => $vnp_TxnRef,
                'vnp_url'  => $vnp_Url,
            ]);
            Log::info('VNPAY Transaction Time', [
                'vnp_CreateDate' => Carbon::createFromFormat('YmdHis', $inputData['vnp_CreateDate'])->toDateTimeString(),
                'vnp_ExpireDate' => Carbon::createFromFormat('YmdHis', $inputData['vnp_ExpireDate'])->toDateTimeString(),
            ]);

            return $vnp_Url;
        } catch (\Exception $e) {
            // Log error
            Log::error('VNPay payment URL creation failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment response from VNPAY
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function verifyPayment($request)
    {
        try {
            $vnp_SecureHash = $request->input('vnp_SecureHash');
            $inputData = [];

            // Extract only vnp_ prefixed data
            foreach ($request->all() as $key => $value) {
                if (str_starts_with($key, "vnp_")) {
                    $inputData[$key] = $value;
                }
            }

            unset($inputData['vnp_SecureHash']); // Remove hash from data to verify
            ksort($inputData);

            // Generate hash for verification
            $hashData = http_build_query($inputData);
            $secureHash = hash_hmac('sha512', $hashData, config('vnpay.vnp_HashSecret'));

            // Compare hash
            $isValid = hash_equals($secureHash, $vnp_SecureHash);

            // Log verification result
            Log::info('VNPay payment verification', [
                'is_valid'   => $isValid,
                'order_id'   => $inputData['vnp_TxnRef'] ?? null,
                'response'   => $inputData,
            ]);

            return $isValid;
        } catch (\Exception $e) {
            // Log error
            Log::error('VNPay payment verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
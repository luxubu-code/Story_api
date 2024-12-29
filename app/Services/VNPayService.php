<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VNPayService
{
    private $tmnCode;
    private $hashSecret;
    private $baseUrl;

    public function __construct()
    {
        $this->tmnCode = config('vnpay.tmn_code');
        $this->hashSecret = config('vnpay.hash_secret');
        $this->baseUrl = config('vnpay.base_url');
    }

    public function createPaymentUrl($subscription)
    {
        $orderId = $subscription->id . '_' . time();
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $subscription->package->price * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan goi VIP: " . $subscription->package->name,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => route('vnpay.return'),
            "vnp_TxnRef" => $orderId,
        ];

        ksort($inputData);
        $hashData = http_build_query($inputData);
        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        $inputData['vnp_SecureHash'] = $vnpSecureHash;
        $paymentUrl = $this->baseUrl . "?" . http_build_query($inputData);

        return $paymentUrl;
    }
}
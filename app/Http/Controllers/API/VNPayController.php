<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VNPayService;

class VNPayController extends Controller
{
    public function handleReturn(Request $request)
    {
        $vnpService = new VNPayService();
        $isValid = $vnpService->verifyPayment($request);

        if ($isValid) {
            return response()->json(['status' => 'success', 'message' => 'Payment verified']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid payment']);
        }
    }
}
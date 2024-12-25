<?php

namespace App\Http\Helpers;

class ResponseHelper
{
    /**
     * Trả về phản hồi thành công
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param string|null $token
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, $message = 'Thành công', $code = 200, $token = null)
    {
        $response = [
            'response_code' => $code,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];

        // Chỉ thêm token nếu nó tồn tại
        if ($token) {
            $response['token'] = $token;
        }

        return response()->json($response, $code);
    }
}
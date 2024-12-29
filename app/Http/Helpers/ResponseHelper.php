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
        ];

        if ($data) {
            $response['data'] = $data;
        }
        if ($message) {
            $response['message'] = $message;
        }
        if ($token) {
            $response['access_token'] = $token;
        }

        return response()->json($response, $code);
    }
}
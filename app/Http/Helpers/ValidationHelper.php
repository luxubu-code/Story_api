<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationHelper
{
    public static function make(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            // Tự động trả về JSON phản hồi lỗi
            throw new ValidationException($validator, response()->json([
                'response_code' => '422',
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422));
        }

        return $data;
    }
}
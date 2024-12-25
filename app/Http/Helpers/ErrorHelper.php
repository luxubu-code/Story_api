<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ErrorHelper
{
    // Định nghĩa các mã HTTP thông dụng
    private const HTTP_CODES = [
        'SUCCESS' => 200,
        'CREATED' => 201,
        'BAD_REQUEST' => 400,
        'UNAUTHORIZED' => 401,
        'FORBIDDEN' => 403,
        'NOT_FOUND' => 404,
        'VALIDATION_ERROR' => 422,
        'SERVER_ERROR' => 500
    ];

    /**
     * Tạo phản hồi lỗi chuẩn hóa
     *
     * @param string $message Thông báo lỗi
     * @param int $code Mã HTTP
     * @param mixed $errors Chi tiết lỗi (tùy chọn)
     * @param bool $shouldLog Có nên ghi log không
     * @return JsonResponse
     */
    public static function response(
        string $message,
        int $code = 400,
        $errors = null,
        bool $shouldLog = false
    ): JsonResponse {
        $response = [
            'response_code' => (string)$code,
            'status' => 'error',
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if ($shouldLog) {
            self::logError($message, $code, $errors);
        }

        return response()->json($response, $code);
    }

    /**
     * Tạo phản hồi lỗi validation
     *
     * @param mixed $errors Các lỗi validation
     * @param string $message Thông báo tùy chỉnh (tùy chọn)
     * @return JsonResponse
     */
    public static function validationError($errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
    {
        return self::response(
            $message,
            self::HTTP_CODES['VALIDATION_ERROR'],
            $errors
        );
    }

    /**
     * Tạo phản hồi lỗi không được phép truy cập
     *
     * @param string $message Thông báo tùy chỉnh
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Không có quyền truy cập'): JsonResponse
    {
        return self::response($message, self::HTTP_CODES['UNAUTHORIZED']);
    }

    /**
     * Tạo phản hồi lỗi không tìm thấy tài nguyên
     *
     * @param string $message Thông báo tùy chỉnh
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Không tìm thấy tài nguyên'): JsonResponse
    {
        return self::response($message, self::HTTP_CODES['NOT_FOUND']);
    }

    /**
     * Tạo phản hồi lỗi server
     *
     * @param \Exception $exception Exception gốc
     * @param string $message Thông báo tùy chỉnh
     * @return JsonResponse
     */
    public static function serverError(\Exception $exception, string $message = 'Đã xảy ra lỗi hệ thống'): JsonResponse
    {
        $errorDetails = config('app.debug') ? $exception->getMessage() : 'Internal Server Error';

        return self::response(
            $message,
            self::HTTP_CODES['SERVER_ERROR'],
            $errorDetails,
            true
        );
    }

    /**
     * Tạo phản hồi lỗi yêu cầu không hợp lệ
     *
     * @param string $message Thông báo tùy chỉnh
     * @param mixed $errors Chi tiết lỗi (tùy chọn)
     * @return JsonResponse
     */
    public static function badRequest(string $message, $errors = null): JsonResponse
    {
        return self::response($message, self::HTTP_CODES['BAD_REQUEST'], $errors);
    }

    /**
     * Ghi log lỗi với thông tin chi tiết
     *
     * @param string $message Thông báo lỗi
     * @param int $code Mã HTTP
     * @param mixed $errors Chi tiết lỗi
     * @return void
     */
    private static function logError(string $message, int $code, $errors = null): void
    {
        $logContext = [
            'code' => $code,
            'message' => $message
        ];

        if ($errors !== null) {
            $logContext['errors'] = $errors;
        }

        Log::error('API Error', $logContext);
    }
}
<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ErrorHelper
{
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


    public static function validationError($errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
    {
        return self::response(
            $message,
            self::HTTP_CODES['VALIDATION_ERROR'],
            $errors
        );
    }


    public static function unauthorized(string $message = 'Không có quyền truy cập'): JsonResponse
    {
        return self::response($message, self::HTTP_CODES['UNAUTHORIZED']);
    }

    public static function notFound(string $message = 'Không tìm thấy tài nguyên'): JsonResponse
    {
        return self::response($message, self::HTTP_CODES['NOT_FOUND']);
    }

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

    public static function badRequest(string $message, $errors = null): JsonResponse
    {
        return self::response($message, self::HTTP_CODES['BAD_REQUEST'], $errors);
    }

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
<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FavoriteStoriesController;
use App\Http\Controllers\API\ReadingHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\ImageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
// Route cho việc lấy danh sách và tạo mới câu chuyện
Route::get('stories', [StoryController::class, 'index']);
Route::post('stories', [StoryController::class, 'store']);

// Route cho việc lấy chi tiết, cập nhật và xóa câu chuyện cụ thể
Route::get('stories/{id}', [StoryController::class, 'show']);
Route::put('stories/{id}', [StoryController::class, 'update']);
Route::delete('stories/{id}', [StoryController::class, 'destroy']);

Route::post('/chapters/{chapterId}/upload', [ImageController::class, 'upload']);  

// Route tìm kiếm câu chuyện
Route::get('search', [StoryController::class, 'search']);

// Route cho việc tải ảnh
Route::get('/images/{filename}', [ImageController::class, 'show']);
Route::get('/image/{id}', [ImageController::class, 'find']);

// Route cho việc đăng ký và đăng nhập
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route cho việc thêm và xóa yêu thích và lịch sử đọc
Route::middleware('auth:api')->group(function () {
    Route::get('/favourite', [FavoriteStoriesController::class, 'index']);
    Route::post('/favourite', [FavoriteStoriesController::class, 'store']);
    Route::get('/exists/{id}', [FavoriteStoriesController::class, 'exists']);
    Route::delete('/favouritedestroy/{id}', [FavoriteStoriesController::class, 'destroy']);
    Route::post('/history', [ReadingHistoryController::class, 'store']);
    Route::get('/history', [ReadingHistoryController::class, 'index']);
});

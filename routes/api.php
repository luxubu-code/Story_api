<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FavoriteStoriesController;
use App\Http\Controllers\API\ReadingHistoryController;
use App\Http\Controllers\API\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\ForgotPasswordController;

Route::post('/images/{id}', [ImageController::class, 'upload']);
Route::get('/images/{id}', [ImageController::class, 'index']);
Route::get('/search', [StoryController::class, 'search']);
Route::get('/comment/{id}', [CommentController::class, 'index']);
Route::post('password', [ForgotPasswordController::class,'sendNewPassWord']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('comment')->group(function () {
        Route::post('/', [CommentController::class, 'store']);
    });
    Route::post('send-fcmToken', [AuthController::class, 'store']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Routes for favorite stories
    Route::prefix('favourite')->group(function () {
        Route::get('/', [FavoriteStoriesController::class, 'index']);
        Route::post('/', [FavoriteStoriesController::class, 'store']);
        Route::get('/exists/{id}', [FavoriteStoriesController::class, 'exists']);
        Route::delete('/{id}', [FavoriteStoriesController::class, 'destroy']);
    });

    // Routes for reading history
    Route::prefix('history')->group(function () {
        Route::post('/', [ReadingHistoryController::class, 'store']);
        Route::get('/', [ReadingHistoryController::class, 'index']);
    });
});

// Routes for stories
Route::prefix('stories')->group(function () {
    Route::get('/', [StoryController::class, 'index']);
    Route::post('/', [StoryController::class, 'store']);
    Route::get('/{id}', [StoryController::class, 'show']);
    Route::put('/{id}', [StoryController::class, 'update']);
    Route::delete('/{id}', [StoryController::class, 'destroy']);
    Route::get('/search', [StoryController::class, 'search']);
});


// Route for image upload

// Routes for authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [AuthController::class, 'googleAuth']);
});

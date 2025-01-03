<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FavoriteStoriesController;
use App\Http\Controllers\API\ReadingHistoryController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\API\RatingController;
use App\Http\Controllers\API\VipSubscriptionController;
use App\Http\Controllers\API\VipSubscriptionHistoryController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\VipPackageController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\VNPayController;


// Routes for Authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [AuthController::class, 'googleAuth']);
});

// Routes for User Profile and FCM Token
Route::middleware('auth')->group(function () {

    Route::get('/user', [AuthController::class, 'userInfo']);
    Route::post('/user', [AuthController::class, 'update']);
    Route::post('/send-fcmToken', [AuthController::class, 'store']);



    Route::get('/vip/packages', [VipSubscriptionController::class, 'showPackages'])->name('vip.packages');
    Route::post('/vip/subscribe/{package}', [VipSubscriptionController::class, 'subscribe'])->name('vip.subscribe');
});
Route::post('/vnpay/ipn', [VipSubscriptionController::class, 'handleVnPayIPN'])->name('vnpay.ipn');
Route::get('/vnpay/return', [VipSubscriptionController::class, 'handleVnPayReturn'])->name('vnpay.return');

// Routes for Password Management
Route::post('/password', [ForgotPasswordController::class, 'sendNewPassWord']);

// Routes for Stories
Route::prefix('stories')->group(function () {
    Route::get('/', [StoryController::class, 'index']);
    Route::post('/', [StoryController::class, 'store']);
    Route::get('/{id}', [StoryController::class, 'show']);
    Route::put('/{id}', [StoryController::class, 'update']);
    Route::delete('/{id}', [StoryController::class, 'destroy']);
    Route::get('/search', [StoryController::class, 'search']);
});
Route::get('most', [StoryController::class, 'getMostFavorited']);
Route::post('/view/{id}', [StoryController::class, 'read']);

Route::get('/comment/{id}', [CommentController::class, 'index']);
Route::middleware('auth:api')->prefix('comment')->group(function () {
    Route::post('/', [CommentController::class, 'store']);
    Route::delete('/{id}', [CommentController::class, 'delete']);
});

// Routes for Ratings
Route::middleware('auth:api')->post('/rating', [RatingController::class, 'ratings']);
Route::get('/rating/{id}', [RatingController::class, 'index']);
Route::delete('/rating/{id}', [RatingController::class, 'delete']);
// Routes for Favorite Stories
Route::middleware('auth:api')->prefix('favourite')->group(function () {
    Route::get('/', [FavoriteStoriesController::class, 'index']);
    Route::post('/', [FavoriteStoriesController::class, 'store']);
    Route::get('/exists/{id}', [FavoriteStoriesController::class, 'exists']);
    Route::delete('/{id}', [FavoriteStoriesController::class, 'destroy']);
});

// Routes for Reading History
Route::middleware('auth:api')->prefix('history')->group(function () {
    Route::get('/', [ReadingHistoryController::class, 'index']);
    Route::post('/', [ReadingHistoryController::class, 'store']);
});
// Routes for Image Management
Route::prefix('images')->group(function () {
    Route::post('/{id}', [ImageController::class, 'upload']);
    Route::get('/{id}', [ImageController::class, 'index']);
});


// VIP Subscription History Routes
Route::middleware('auth:api')->prefix('vip/history')->group(function () {
    Route::get('/', [VipSubscriptionHistoryController::class, 'index']);
    Route::get('/active', [VipSubscriptionHistoryController::class, 'getActiveSubscription']);
    Route::get('/{id}', [VipSubscriptionHistoryController::class, 'show']);
});


Route::get('/all-comment', [CommentController::class, 'getAllComment']);

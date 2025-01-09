<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $now = now();
    Log::info("Scheduler Test Task", [
        'time' => $now->format('Y-m-d H:i:s'),
        'timezone' => $now->timezone->getName(),
        'offset' => $now->timezone->getOffset($now),
    ]);
})->everyMinute();


// Cập nhật trạng thái VIP hàng ngày lúc 00:00
Schedule::command('users:update-vip-status')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/vip-status-update.log'));
Schedule::command('chapters:check-vip')
    ->before(function () {
        Log::info('Scheduler đang chuẩn bị chạy command vào: ' . now());
    })
    ->everyMinute();
// Thêm vào Kernel.php
Schedule::call(function () {
    Log::info('Thời gian hệ thống: ' . date('Y-m-d H:i:s'));
    Log::info('Thời gian Laravel: ' . now()->format('Y-m-d H:i:s'));
})->everyMinute();

// Schedule::command('backup:clean')->dailyAt('01:30');
// Schedule::command('backup:run')->dailyAt('02:00');
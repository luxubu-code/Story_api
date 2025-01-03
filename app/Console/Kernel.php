<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     * Định nghĩa lịch chạy các command của ứng dụng
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cập nhật trạng thái VIP hàng ngày lúc 00:00
        $schedule->command('users:update-vip-status')
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/vip-status-update.log'));

        // Dọn dẹp file logs cũ hơn 7 ngày
        $schedule->command('log:clear --days=7')
            ->weekly()
            ->sundays()
            ->at('01:00');

        // Backup database hàng ngày
        $schedule->command('backup:clean')->dailyAt('01:30');
        $schedule->command('backup:run')->dailyAt('02:00');

        // Xóa các session hết hạn
        $schedule->command('session:gc')->daily();

        // Xóa các temporary files
        $schedule->command('temp:clean')->daily();
    }

    /**
     * Register the commands for the application.
     * Đăng ký các command cho ứng dụng
     */
    protected function commands(): void
    {
        // Load các commands từ thư mục Commands
        $this->load(__DIR__ . '/Commands');

        // Load các commands từ file routes/console.php
        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     * Lấy timezone mặc định cho các scheduled events
     */
    protected function scheduleTimezone(): string
    {
        return config('app.timezone', 'Asia/Ho_Chi_Minh');
    }
}
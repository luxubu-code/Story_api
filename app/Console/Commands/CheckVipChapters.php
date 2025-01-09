<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use Illuminate\Console\Command;
use App\Events\ChapterBecameFree;
use Illuminate\Support\Facades\Log;

class CheckVipChapters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapters:check-vip';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra vip cho các chương';

    public function handle()
    {
        logger('Command chapters:check-vip is running.');

        $expiredVipChapters = Chapter::where('is_vip', true)
            ->where('vip_expiration', '<=', now())
            ->get();

        if ($expiredVipChapters->isEmpty()) {
            logger('No expired VIP chapters found.');
            return;
        }

        foreach ($expiredVipChapters as $chapter) {
            broadcast(new ChapterBecameFree($chapter));
            $chapter->update(['is_vip' => false]);
            $this->info("Chapter ID {$chapter->id} đã chuyển sang trạng thái miễn phí.");
        }
        Log::info('Tìm thấy ' . $expiredVipChapters->count() . ' chapters cần xử lý');

        $this->info("Quá trình kiểm tra các chapter VIP đã hoàn tất.");
    }
}
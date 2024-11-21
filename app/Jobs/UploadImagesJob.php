<?php

namespace App\Jobs;

use App\Models\Image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UploadImagesJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    protected $chapter;
    protected $tempFilePath;

    /**
     * Create a new job instance.
     *
     */
    public function __construct($chapter, $tempFilePath)
    {
        $this->chapter = $chapter;
        $this->tempFilePath = $tempFilePath;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        try {
    
            $imageUrl = Cloudinary::upload(Storage::disk('local')->path($this->tempFilePath))->getSecurePath();
            Image::create([
                'chapter_id' => $this->chapter->chapter_id,
                'base_url' => dirname($imageUrl) . '/',
                'file_name' => basename($imageUrl),
            ]);

            Storage::disk('local')->delete($this->tempFilePath);
        } catch (\Exception $e) {
            Log::error("Lỗi xảy ra trong quá trình upload ảnh: " . $e->getMessage());
        }
    }
}

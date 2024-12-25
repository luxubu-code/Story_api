<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateUserProfileImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $imagePath;

    /**
     * Khởi tạo job.
     *
     * @param User $user
     * @param string $imagePath
     */
    public function __construct(User $user, $imagePath)
    {
        $this->user = $user;
        $this->imagePath = $imagePath;
    }

    /**
     * Xử lý job.
     */
    public function handle()
    {
        try {
            // Xóa ảnh cũ trên Cloudinary nếu có
            if ($this->user->public_id) {
                try {
                    Cloudinary::destroy($this->user->public_id);
                    Log::info("Ảnh cũ (public_id: {$this->user->public_id}) đã được xóa.");
                } catch (\Exception $e) {
                    Log::warning("Không thể xóa ảnh cũ trên Cloudinary: " . $e->getMessage());
                }
            }

            // Upload ảnh mới từ file tạm lên Cloudinary
            $uploadedFile = Cloudinary::upload(Storage::path($this->imagePath), [
                'folder' => 'user_avatars',
                'overwrite' => true,
                'resource_type' => 'image',
            ]);

            // Cập nhật thông tin ảnh trong user
            $this->user->avatar_url = $uploadedFile->getSecurePath();
            $this->user->public_id = $uploadedFile->getPublicId();
            $this->user->save();

            Log::info("Upload ảnh mới thành công: {$uploadedFile->getSecurePath()}");
        } catch (\Exception $e) {
            Log::error("Lỗi khi upload ảnh lên Cloudinary: " . $e->getMessage());
            throw $e;
        } finally {
            // Xóa file tạm sau khi xử lý xong
            if (Storage::exists($this->imagePath)) {
                Storage::delete($this->imagePath);
                Log::info("Đã xóa file tạm: {$this->imagePath}");
            }
        }
    }

    /**
     * Xử lý nếu job thất bại.
     *
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        Log::error("Job UpdateUserProfileImage thất bại: " . $exception->getMessage());
    }
}
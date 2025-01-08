<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ValidationHelper;
use App\Jobs\UploadImagesJob;
use App\Models\Chapter;
use App\Models\FavoriteStories;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\User;
use App\Services\ZipFileService;
use Illuminate\Support\Facades\Log;
use App\Services\SendNotificationService;
use App\Http\Helpers\ResponseHelper;
use App\Http\Helpers\ErrorHelper;

class ImageController extends Controller
{
    public function index($chapter_id)
    {
        $image = Image::where('chapter_id', $chapter_id)->get();
        $data = $image->map(function ($image) {
            return [
                'image_id' => $image->image_id,
                'file_name' => $image->file_name,
                'base_url' => $image->base_url,
            ];
        });
        return ResponseHelper::success($data, 'Images retrieved successfully.');
    }
    public function upload(Request $request, $story_id, ZipFileService $zipFileService)

    {
        $story = Story::findOrFail($story_id);
        ValidationHelper::make($request->all(), [
            'title' => 'required',
            'file_zip' => 'required|file|mimes:zip|max:10240',
        ]);
        // Làm sạch tiêu đề, loại bỏ khoảng trắng thừa
        $cleanTitle = trim($request->title);

        // Kiểm tra trùng lặp không phân biệt hoa thường
        $duplicateCheck = Chapter::where('story_id', $story_id)
            ->whereRaw('LOWER(TRIM(title)) = ?', [strtolower($cleanTitle)])
            ->first();
        // $duplicateCheck = Chapter::where('title', $request->title)->where('story_id', $story_id)->first();
        if ($duplicateCheck) {
            return ErrorHelper::badRequest('Chapter title already exists.');
        }
        // Log what was found
        Log::info('Duplicate check result', [
            'duplicate_found' => !is_null($duplicateCheck),
            'existing_chapter' => $duplicateCheck
        ]);
        try {
            $chapter = Chapter::create([
                'title' => $request->title,
                'story_id' => $story_id,
            ]);
            $favoriteStories = FavoriteStories::where('story_id', $story->story_id)->with('user')->get();
            foreach ($favoriteStories as $favorite) {
                $notificationSent = SendNotificationService::sendNotification(
                    'Chương mới đã được thêm vào: ' . $story->title,
                    'Tên chương mới: ' . $chapter->title,
                    $favorite->user->fcm_token
                );

                if (json_decode($notificationSent)->status !== 'success') {
                    Log::error('Failed to send notification to user: ' . $favorite->user->id);
                }
            }
            if (!$chapter) {
                return ErrorHelper::serverError(new \Exception('Failed to create chapter.'));
            }
            $zipFile = $request->file('file_zip');
            $validMimeTypes = ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed'];
            if (!in_array($zipFile->getClientMimeType(), $validMimeTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File is not a ZIP format.',
                ], 404);
            }

            $imagePaths = $zipFileService->extractImages($zipFile->getRealPath(), 'temp_images');
            if (empty($imagePaths)) {
                return ErrorHelper::badRequest('No images found in the ZIP file.');
            }
            foreach ($imagePaths as $imagePath) {
                UploadImagesJob::dispatch($chapter, $imagePath)->onQueue('high');
            }
            return ResponseHelper::success(null, 'Chapter created and images uploaded successfully.');
        } catch (\Exception $e) {
            Log::error("Error occurred during image upload: " . $e->getMessage(), [
                'story_id' => $story_id,
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString()
            ]);
            return ErrorHelper::serverError($e, 'An error occurred during the upload process.');
        }
    }
    public function destroy($chapter_id)
    {
        $chapter = Chapter::findOrFail($chapter_id);
        if ($chapter->images()->count() > 0) {
            $chapter->images()->delete();
        }
        $chapter->delete();
        return ResponseHelper::success(null, 'Chapter deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
    public function upload(Request $request, $story_id, ZipFileService $zipFileService)
    
    {
        $story = Story::findOrFail($story_id);
        $request->validate([
            'title' => 'required',
            'images_zip' => 'required|file|mimes:zip|max:10240',
        ]);
        try {
            $chapter = Chapter::create([
                'title' => $request->title,
                'story_id' => $story_id,
            ]);
            $favoriteStories = FavoriteStories::where('story_id', $story->story_id)->with('user')->get();
            foreach ($favoriteStories as $favorite) {
                $notificationSent =  SendNotificationService::sendNotification(
                    'A new chapter has been added to the story: ' . $story->title,
                    'A new chapter title :'.$chapter->title,
                    $favorite->user->fcm_token
                );
    
                if (json_decode($notificationSent)->status !== 'success') {
                    Log::error('Failed to send notification to user: ' . $favorite->user->id);
                }
                
            }
            if (!$chapter) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create chapter.',
                ], 404);
            }
            $zipFile = $request->file('images_zip');
            $validMimeTypes = ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed'];
            if (!in_array($zipFile->getClientMimeType(), $validMimeTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File is not a ZIP format.',
                ], 404);
            }
            
            $imagePaths = $zipFileService->extractImages($zipFile->getRealPath(), 'temp_images');
            if (empty($imagePaths)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No images found in the ZIP file.',
                ], 404);
            }
            foreach ($imagePaths as $imagePath) {
                UploadImagesJob::dispatch($chapter, $imagePath)->onQueue('high');
            }

            return response()->json([
                'status' => 'success',
                'data' => 'Chapter created successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error occurred during image upload: " . $e->getMessage(), [
                'story_id' => $story_id,
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the upload process: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function destroy($chapter_id)
    {
        $chapter = Chapter::findOrFail($chapter_id);    
        if($chapter->images()->count() > 0){
            $chapter->images()->delete();
        }
        $chapter->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Chapter deleted successfully',
        ], 200);
    }
}

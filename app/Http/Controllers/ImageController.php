<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Models\Image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageController extends Controller
{
    public function show($filename)
    {
        $path = public_path('images/' . $filename);
        if (!File::exists($path)) {
            return response()->json(['error' => 'File not found.'], 404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }
    public function upload(Request $request, $chapterId)
    {
        // Validate the image
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $chapter = Chapter::findOrFail($chapterId);

        try {
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

            $chapterImage = Image::create([
                'chapter_id' => $chapter->chapter_id,
                'image_url' => $uploadedFileUrl,
            ]);

            return response()->json([
                'status' => 'success',
                'image_url' => $chapterImage->image_url,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during image upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function find($id)
    {
        $image = Image::where('chapter_id', $id)->get();
        if (!$image) {
            return response()->json(['status' => 'error', 'message' => 'Image not found'], 404);
        }
        $detail = $image->map(function ($image) {
            return [
                'id_image' => $image->id_image,
                'image_path' => $image->image_path,
            ];
        });
        return response()->json(['status' => 'success', 'data' => $detail]);
    }
}

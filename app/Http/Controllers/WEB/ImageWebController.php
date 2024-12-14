<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Image;
use App\Models\Story;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageWebController extends Controller
{
    // Display images of a specific chapter     
    public function show($chapter_id)
    {
        $chapter = Chapter::with('images')->findOrFail($chapter_id);
        $story = Story::findOrFail($chapter->story_id);

        return view('chapters.images', [
            'chapter' => $chapter,
            'story' => [
                'id' => $story->id,
                'title' => $story->title,
                'image_path' => $story->image_path ?? null  // Use null if no image path exists
            ]
        ]);
    }
    // Edit image     
    public function edit($image_id)
    {
        $image = Image::findOrFail($image_id);
        return view('chapters.edit', ['image' => $image]);
    }

    public function update(Request $request, $image_id)
    {
        $request->validate([
            'file_name' => 'required|file|mimes:jpg,jpeg,png',
        ]);

        $image = Image::findOrFail($image_id);

        // Update image in Cloudinary         
        $cloudinaryResponse = Cloudinary::upload($request->file('file_name')->getRealPath(), [
            'folder' => 'chapter_images',
        ]);
        $imageUrl = $cloudinaryResponse->getSecurePath();

        $lastSlashPos = strrpos($imageUrl, '/');
        $baseUrl = substr($imageUrl, 0, $lastSlashPos + 1);
        $fileName = substr($imageUrl, $lastSlashPos + 1);

        $image->update([
            'base_url' => $baseUrl,
            'file_name' => $fileName,
        ]);

        return redirect()->route('chapters.images', $image->chapter_id)->with('success', 'Ảnh đã được cập nhật thành công trên Cloudinary!');
    }
}
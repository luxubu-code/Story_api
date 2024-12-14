<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\API\StoryController;
use App\Models\Category;
use App\Models\Story;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Chapter;
use App\Services\ZipFileService;
use App\Http\Controllers\API\ImageController;
use Illuminate\Http\Request; // This line was missing before
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryWebController extends StoryController
{

    protected $imageController;

    public function __construct(ImageController $imageController)
    {
        $this->imageController = $imageController;
    }
    public function showAll()
    {
        $response = parent::index();
        $storiesArray = json_decode(json_encode($response->getData()->data), true);
        $categories = Category::all();
        return view('stories.index', compact('storiesArray', 'categories'));
    }

    public function store(Request $request)
    {
        $response = parent::store($request);

        if ($response->status() === 200) {
            return redirect()->route('stories.store')->with('success', 'Story added successfully!');
        } else {
            return redirect()->route('stories.store')->with('error', $response->getData()->message);
        }
    }
    public function show($story_id)
    {
        // Gọi phương thức show từ class cha
        $response = parent::show($story_id);
        $story = json_decode(json_encode($response->getData()->data), true);

        // Lấy dữ liệu câu chuyện từ database với quan hệ comments và ratings
        // $story = Story::with(['comments.user', 'ratings.user'])->find($story_id);
        if (!$story) {
            abort(404, 'Story not found');
        }
        return view('stories.show', compact('story'));
    }

    public function upload(Request $request, $story_id, ZipFileService $zipFileService)
    {
        $response = $this->imageController->upload($request, $story_id, $zipFileService);
        try {
            if ($response->status() === 200) {
                return redirect()->route('stories.show', $story_id)->with('success', 'Images uploaded successfully!');
            } else {
                return redirect()->back()->with('error', 'An unexpected error occurred.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function destroyChapter($id)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            $chapter = Chapter::findOrFail($id);
            $storyId = $chapter->story_id;

            $imageUrls = $chapter->images()
                ->get(['file_name'])
                ->map(function ($image) {
                    return  $image->file_name;
                })->toArray();

            // Delete images from CloudBinary
            foreach ($imageUrls as $imageUrl) {

                try {
                    // Use CloudBinary SDK to delete the image
                    $result = Cloudinary::destroy($imageUrls);

                    // Check if deletion was successful
                    if (!$result['result'] === 'ok') {
                        throw new \Exception("Failed to delete image: {$imageUrls}");
                    }
                } catch (\Exception $imageDeleteError) {
                    // Log individual image deletion errors but continue processing
                    Log::warning('Image deletion error: ' . $imageDeleteError->getMessage(), [
                        'public_id' => $imageUrls,
                        'chapter_id' => $id
                    ]);
                }
            }

            $chapter->images()->delete();
            $chapter->delete();
            DB::commit();
            return redirect()->route('stories.show', $storyId)
                ->with('success', 'Chapter and associated images deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Chapter deletion error: ' . $e->getMessage(), [
                'chapter_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stories.show', $storyId ?? null)
                ->with('error', 'Unable to delete chapter: ' . $e->getMessage());
        }
    }
    // public function destroyChapter($id)
    // {
    //     try {
    //         // Begin a database transaction
    //         DB::beginTransaction();

    //         // Find the chapter to get story_id and associated image information
    //         $chapter = Chapter::findOrFail($id);
    //         $storyId = $chapter->story_id;

    //         // Retrieve all image URLs associated with this chapter
    //         $imageUrls = $chapter->images()
    //             ->get(['base_url', 'file_name'])
    //             ->map(function ($image) {
    //                 return $image->base_url . '/' . $image->file_name;
    //             })->toArray();

    //         // Delete images from CloudBinary
    //         foreach ($imageUrls as $imageUrl) {
    //             // Extract the public ID from the CloudBinary URL
    //             $publicId = $this->extractCloudinaryPublicId($imageUrl);

    //             try {
    //                 // Use CloudBinary SDK to delete the image
    //                 $result = Cloudinary::destroy($publicId);

    //                 // Check if deletion was successful
    //                 if (!$result['result'] === 'ok') {
    //                     throw new \Exception("Failed to delete image: {$publicId}");
    //                 }
    //             } catch (\Exception $imageDeleteError) {
    //                 // Log individual image deletion errors but continue processing
    //                 Log::warning('Image deletion error: ' . $imageDeleteError->getMessage(), [
    //                     'public_id' => $publicId,
    //                     'chapter_id' => $id
    //                 ]);
    //             }
    //         }

    //         $chapter->images()->delete();
    //         $chapter->delete();
    //         DB::commit();
    //         return redirect()->route('stories.show', $storyId)
    //             ->with('success', 'Chapter and associated images deleted successfully!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Chapter deletion error: ' . $e->getMessage(), [
    //             'chapter_id' => $id,
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return redirect()->route('stories.show', $storyId ?? null)
    //             ->with('error', 'Unable to delete chapter: ' . $e->getMessage());
    //     }
    // }



    public function destroyStory($id)
    {
        $response = parent::destroy($id);
        if ($response->status() == 200) {
            return redirect()->route('stories.index')->with('success', 'Story deleted successfully!');
        } else {
            return redirect()->back()->with('error', $response->getData()->message);
        }
    }
    public function searchStory(Request $request)
    {
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $response = parent::search($request);
            $storiesArray = json_decode(json_encode($response->getData()->data), true);
        } else {
            $storiesArray = [];
        }
        $categories = Category::all();

        return view('stories.index', compact('storiesArray', 'categories'));
    }
}
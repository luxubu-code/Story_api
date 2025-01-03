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
        try {
            $response = parent::store($request);
            $responseData = json_decode($response->getContent());

            // Check if the response contains a validation error for duplicate story
            if ($response->status() === 422 && isset($responseData->errors->duplicate_story)) {
                $duplicateStory = $responseData->errors->duplicate_story;

                // Create a more user-friendly error message
                $errorMessage = sprintf(
                    'A story with similar details already exists: "%s" by %s (ID: %s)',
                    $duplicateStory->title,
                    $duplicateStory->author,
                    $duplicateStory->id
                );

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            if ($response->status() === 200) {
                return redirect()->route('stories.index')
                    ->with('success', 'Story added successfully!');
            }

            // Handle other types of errors
            return redirect()->back()
                ->withInput()
                ->with('error', $responseData->message ?? 'An error occurred while adding the story.');
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
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
            DB::beginTransaction();
            $chapter = Chapter::with('images')->findOrFail($id);
            $storyId = $chapter->story_id;
            $imageUrls = $chapter->images->pluck('file_name')->toArray();
            foreach ($imageUrls as $imageUrl) {
                try {
                    $public_id = $this->extractPublicIdFromUrl($imageUrl);
                    $result = Cloudinary::destroy($public_id);
                    if ($result['result'] !== 'ok') {
                        Log::warning("Cloudinary deletion failed for image", [
                            'public_id' => $public_id,
                            'chapter_id' => $id,
                            'result' => $result
                        ]);
                    }
                } catch (\Exception $imageDeleteError) {
                    Log::warning('Image deletion error: ' . $imageDeleteError->getMessage(), [
                        'public_id' => $public_id ?? $imageUrl,
                        'chapter_id' => $id,
                        'error' => $imageDeleteError->getTraceAsString()
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
            Log::error('Chapter deletion failed', [
                'chapter_id' => $id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('stories.show', $storyId ?? null)
                ->with('error', 'Unable to delete chapter. Please try again later.');
        }
    }
    private function extractPublicIdFromUrl($url)
    {
        $withoutExtension = preg_replace('/\.[^.]+$/', '', $url);
        $public_id = preg_replace('/^.*\//', '', $withoutExtension);

        return $public_id;
    }

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


    public function updateStory(Request $request, $id)
    {
        $response = parent::update($request, $id);

        if ($response->status() === 200) {
            return redirect()->route('stories.show', $id)->with('success', 'Story updated successfully!');
        } else {
            return redirect()->back()->with('error', $response->getData()->message);
        }
    }
    public function getMost()
    {
        $response = parent::getMostFavorited();
        $storiesArray = json_decode(json_encode($response->getData()->data), true);
        $categories = Category::all();

        return view('favorite.favorite', compact('storiesArray', 'categories'));
    }
}

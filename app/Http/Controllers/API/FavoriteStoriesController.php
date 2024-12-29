<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\FavoriteStories;
use App\Models\Story;
use Carbon\Carbon;

class FavoriteStoriesController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();
        $favourite_stories = FavoriteStories::with('story')->where('user_id', $user->id)->get();

        $favourite_stories_data = $favourite_stories->map(function ($favouriteStory) {
            return [
                'id' => $favouriteStory->story_id,
                'title' => $favouriteStory->story->title,
                'image_path' => $favouriteStory->base_url . $favouriteStory->file_name,
                'read_at' => Carbon::parse($favouriteStory->story->read_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($favouriteStory->story->updated_at)->format('Y-m-d')
            ];
        });
        return ResponseHelper::success($favourite_stories_data, 'Success get favourite stories');
    }
    public function exists($id)
    {
        $user = auth('api')->user();
        $exists = FavoriteStories::where('user_id', $user->id)->where('story_id', $id)->exists();
        return response()->json([
            'exists' => $exists,
        ]);
    }
    public function store(Request $request)
    {
        $user = auth('api')->user();
        $validatedData = $request->validate([
            'story_id' => 'required|exists:stories,story_id',
        ]);
        $story = Story::findOrFail($validatedData['story_id']);
        $exists = FavoriteStories::where('user_id', $user->id)->where('story_id', $request->story_id)->exists();
        if ($exists) {
            return ErrorHelper::badRequest('Story already exists in favourite');
        } else {
            $favourite_story = FavoriteStories::create([
                'user_id' => $user->id,
                'story_id' => $validatedData['story_id'],
                'read_at' => Carbon::now(),
                'base_url' => $story->base_url,
                'file_name' => $story->file_name,
            ]);
            return ResponseHelper::success($favourite_story, 'Story added to favourites successfully');
        }
    }
    public function destroy($id)
    {
        $user = auth('api')->user();

        $favoriteStory  = FavoriteStories::where('user_id', $user->id)->where('story_id', $id)->first();
        if ($favoriteStory) {
            $favoriteStory->delete();
            return ResponseHelper::success(null, 'Favorite story deleted successfully');
        } else {
            return ErrorHelper::notFound('Favorite story not found');
        }
    }
}
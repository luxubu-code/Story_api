<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\FavoriteStories;
use App\Models\Story;
use Carbon\Carbon;

class FavoriteStoriesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $favourite_stories = FavoriteStories::with('story')->where('user_id', $user->id)->get();

        $favourite_stories_data = $favourite_stories->map(function ($favouriteStory) {
            return [
                'image_path' => $favouriteStory->story->image_path,
                'story_id' => $favouriteStory->story_id,
                'title' => $favouriteStory->story->title,
                'read_at' => Carbon::parse($favouriteStory->story->read_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($favouriteStory->story->updated_at)->format('Y-m-d')
            ];
        });

        return response()->json([
            'response_code' => '200',
            'status'        => 'success',
            'message'       => 'success get favourite stories',
            'data'          => $favourite_stories_data,
        ]);
    }
    public function exists($id)
    {
        $user = Auth::user();
        $exists = FavoriteStories::where('user_id', $user->id)->where('story_id', $id)->exists();
        return response()->json([
            'exists' => $exists, 
        ]);
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'story_id' => 'required|exists:stories,story_id',
        ]);
        $exists = FavoriteStories::where('user_id', $user->id)->where('story_id', $request->story_id)->exists();
        if ($exists) {
            return response()->json([
                'response_code' => '400',
                'status'        => 'error',
                'message'       => 'story already exists in favourite',
            ]);
        } else {
            $favourite_story = FavoriteStories::create([
                'user_id' => $user->id,
                'story_id' => $validatedData['story_id'],
            ]);
            return response()->json([
                'response_code' => '200',
                'status'        => 'success',
                'data'          => $favourite_story,
            ]);
        }
    }
    public function destroy($id)
    {
        $user = Auth::user();

        $favoriteStory  = FavoriteStories::where('user_id', $user->id)->where('story_id', $id)->first();
        if ($favoriteStory) {
            $favoriteStory->delete();
            return response()->json([
                'status' => 'success',  
                'message' => 'Favorite Stories deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Favorite Story not found.'
            ], 404);
        }
    }
}

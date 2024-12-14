<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\RatingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating;
use App\Models\Story;
use App\Models\Ratings;

class RatingController extends Controller
{
    public function index($id)
    {
        try {
            $rating = Ratings::where('story_id', $id)->with('user')->get();
            return response()->json([
                'response_code' => '200',
                'status' => 'success',
                'data' => RatingResource::collection($rating)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => '500',
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function ratings(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'response_code' => '401',
                    'status'        => 'error',
                    'message'       => 'User not authenticated',
                ], 401);
            }
            $validatedData = $request->validate(
                [
                    'story_id' => 'required|exists:stories,story_id',
                    'title' => 'required',
                    'rating'   => 'required|integer|min:1|max:5'
                ]
            );
            $story = Story::findOrFail($validatedData['story_id']);
            $isPosted = Ratings::where('user_id', $user->id)
                ->where('story_id', $story->story_id)->first();
            if ($isPosted) {
                return response()->json([
                    'response_code' => '400',
                    'status'        => 'error',
                    'message'       => 'You have already rated this story',
                ], 400);
            }
            $rating = Ratings::create([
                'user_id' => $user->id,
                'story_id' => $story->story_id,
                'rating' => $validatedData['rating'],
                'title' => $validatedData['title']
            ]);
            return response()->json([
                'response_code' => '200',
                'status' => 'success',
                'data' => $rating
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => '500',
                'status'        => 'error',
                'message'       => $e->getMessage(),
            ]);
        }
    }
}
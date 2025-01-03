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
use Illuminate\Support\Facades\Log;

class FavoriteStoriesController extends Controller
{
    public function index()
    {
        try {
            // Kiểm tra xác thực người dùng
            $user = auth('api')->user();
            if (!$user) {
                Log::error('User authentication failed in FavoriteStories index');
                return ErrorHelper::unauthorized('Unauthorized access', 401);
            }

            // Log thông tin để debug
            Log::info('Fetching favorites for user: ' . $user->id);

            // Lấy danh sách truyện yêu thích với relationship
            $favourite_stories = FavoriteStories::with(['story' => function ($query) {
                $query->select('story_id', 'title', 'updated_at');
            }])->where('user_id', $user->id)->get();

            // Kiểm tra nếu không có dữ liệu
            if ($favourite_stories->isEmpty()) {
                return ResponseHelper::success([], 'No favorite stories found');
            }

            // Transform dữ liệu với xử lý lỗi
            $favourite_stories_data = $favourite_stories->map(function ($favouriteStory) {
                try {
                    // Kiểm tra story relationship
                    if (!$favouriteStory->story) {
                        Log::warning('Story not found for favorite ID: ' . $favouriteStory->id);
                        return null;
                    }

                    return [
                        'id' => $favouriteStory->story_id,
                        'title' => $favouriteStory->story->title,
                        'image_path' => $favouriteStory->base_url . $favouriteStory->file_name,
                        'read_at' => $favouriteStory->read_at ?
                            Carbon::parse($favouriteStory->story->read_at)->format('Y-m-d') : null,
                        'updated_at' => $favouriteStory->story->updated_at ?
                            Carbon::parse($favouriteStory->story->updated_at)->format('Y-m-d') : null,
                        'categories' => $favouriteStory->story->categories->map(
                            function ($categories) {
                                return [
                                    'title' => $categories->title,
                                ];
                            }
                        ),
                    ];
                } catch (\Exception $e) {
                    Log::error('Error transforming favorite story: ' . $e->getMessage(), [
                        'story_id' => $favouriteStory->story_id,
                        'user_id' => $favouriteStory->user_id
                    ]);
                    return null;
                }
            })->filter(); // Lọc bỏ các giá trị null

            // Log kết quả để debug
            Log::info('Successfully fetched favorites', [
                'count' => $favourite_stories_data->count()
            ]);

            return ResponseHelper::success($favourite_stories_data, 'Success get favourite stories');
        } catch (\Exception $e) {
            Log::error('Error in FavoriteStories index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return ErrorHelper::serverError($e, 'Error fetching favorite stories');
        }
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

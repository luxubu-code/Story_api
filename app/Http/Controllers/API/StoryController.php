<?php

namespace App\Http\Controllers\API;

use App\Models\Chapter;
use App\Models\FavoriteStories;
use App\Models\Ratings;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoryController extends Controller

{

    public function index(): JsonResponse
    {
        try {
            $datastories = Story::with(['ratings', 'favorites', 'categories', 'chapters'])->get();
            $stories = $datastories->map(function ($story) {
                $averageRating = $story->ratings->avg('rating') ?? 0;
                return [
                    'id' => $story->story_id,
                    'title' => $story->title,
                    'author' => $story->author,
                    'views' => $story->views ?? 0,
                    'description' => $story->description,
                    'image_path' => $story->base_url . $story->file_name,
                    'created_at' => $story->created_at,
                    'averageRating' => $averageRating,
                    'totalChapter' => $story->chapters->count(),
                    'favourite' => $story->favorites->count(),
                    'categories' => $story->categories->map(function ($categories) {
                        return [
                            'title' => $categories->title
                        ];
                    })
                ];
            });
            return response()->json([
                'status' => 'success',
                'data' => $stories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while retrieving stories.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $story = Story::with('chapters', 'categories', 'ratings', 'favorites')->find($id);
            if (!$story) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Story not found.'
                ], 404);
            }

            $averageRating = $story->ratings->avg('rating') ?? 0;
            $favouriteCount = $story->favorites->count() ?? 0;
            $views = $story->chapters->sum('views');
            $detailedstorsy = [
                'id' => $story->story_id,
                'title' => $story->title,
                'author' => $story->author,
                'description' => $story->description,
                'status' => $story->status ?? 0,
                'views' => $views ?? 0,
                'image_path' => $story->base_url . $story->file_name ?? 'null',
                'averageRating' => $averageRating,
                'favourite' => $favouriteCount,
                'chapter' => $story->chapters->map(
                    function ($chapter) {
                        return [
                            'id' => $chapter->chapter_id,
                            'title' => $chapter->title,
                            'views' => $chapter->views ?? 0,
                            'created_at' => $chapter->created_at
                        ];
                    }
                ),
                'categories' => $story->categories->map(
                    function ($categories) {
                        return [
                            'title' => $categories->title,
                        ];
                    }
                ),
                'ratings' => $story->ratings() ? $story->ratings->map(
                    function ($rating) {
                        return [
                            'user_id' => $rating->user_id,
                            'rating' => $rating->rating,
                            'created_at' => $rating->created_at
                        ];
                    }
                ) : []
            ];
            return response()->json([
                'status' => 'success',
                'data' => $detailedstorsy
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while retrieving story.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function read($id)
    {
        Chapter::findOrFail($id)->increment('views');
        return response()->json([
            'status' => 'success',
        ]);
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'author' => 'required',
                'description' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,category_id',
            ]);
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath());
            $imageUrl = $uploadedFileUrl->getSecurePath();

            $lastSlashPos = strrpos($imageUrl, '/');  // tìm vị trí cuối cùng của dấu gạch chéo "/"
            $baseUrl = substr($imageUrl, 0, $lastSlashPos + 1);  //  chứa phần từ đầu đến ký tự "/" cuối cùng
            $fileName = substr($imageUrl, $lastSlashPos + 1);  //  chứa phần sau dấu gạch chéo "/"

            $story = Story::create([
                'title' => $request->title,
                'author' => $request->author,
                'description' => $request->description,
                'base_url' => $baseUrl,
                'file_name' => $fileName
            ]);
            $story->categories()->attach($request->categories);
            return response()->json([
                'status' => 'store success',
                'data' => $story,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while storing story.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $story = Story::find($id);

            if (!$story) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Story not found.'
                ], 404);
            }

            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'author' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath());
                $imageUrl = $uploadedFileUrl->getSecurePath();

                $lastSlashPos = strrpos($imageUrl, '/');
                $baseUrl = substr($imageUrl, 0, $lastSlashPos + 1);
                $fileName = substr($imageUrl, $lastSlashPos + 1);

                $story->update([
                    'title' => $request->input('title', $story->title),
                    'author' => $request->input('author', $story->author),
                    'description' => $request->input('description', $story->description),
                    'base_url' => $baseUrl,
                    'file_name' => $fileName
                ]);
            } else {
                $story->update($request->only(['title', 'author', 'description']));
            }

            return response()->json([
                'status' => 'success',
                'data' => $story
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while updating the story.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id): JsonResponse
    {
        try {
            $story = Story::findOrFail($id);
            DB::beginTransaction();
            try {
                Cloudinary::destroy($story->file_name);
                // Xóa tất cả quan hệ và dữ liệu liên quan
                $story->chapters()->each(function ($chapter) {
                    $chapter->images()->delete();
                    $chapter->delete();
                });
                // Xóa các quan hệ một-nhiều sử dụng delete() trực tiếp
                $story->favorites()->delete();
                $story->ratings()->delete();
                $story->comments()->delete();
                $story->readingHistory()->delete();
                // Xóa quan hệ nhiều-nhiều
                $story->categories()->detach();
                // Xóa story chính
                $story->delete();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Xóa truyện thành công'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy truyện'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi xóa truyện',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function search(Request $request): JsonResponse
    {
        try {
            $query = Story::query();
            $searchInput = $request->input('search');
            if (!empty($searchInput)) {
                $query->where(function ($q) use ($searchInput) {
                    $q->where('title', 'like', '%' . $searchInput . '%')
                        ->orWhere('author', 'like', '%' . $searchInput . '%');
                });
            }
            $dataSearch = $query->with(['ratings', 'favorites', 'categories'])->get();
            $stories = $dataSearch->map(function ($story) {
                $averageRating = $story->ratings->avg('rating') ?? 0;
                return [
                    'id' => $story->story_id,
                    'title' => $story->title,
                    'author' => $story->author,
                    'views' => $story->views,
                    'description' => $story->description,
                    'image_path' => $story->base_url . $story->file_name,
                    'created_at' => $story->created_at,
                    'averageRating' => $averageRating,
                    'categories' => $story->categories->map(function ($category) {
                        return [
                            'id' => $category->category_id,
                            'title' => $category->title
                        ];
                    })
                ];
            });
            return response()->json([
                'status' => 'success',
                'data' => $stories
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while searching stories.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
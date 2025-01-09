<?php

namespace App\Http\Controllers\API;

use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use App\Http\Helpers\ValidationHelper;
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
    // public function index(): JsonResponse
    // {
    //     try {
    //         $datastories = Story::with(['ratings', 'favorites', 'categories', 'chapters'])->get();
    //         $stories = $datastories->map(function ($story) {
    //             $averageRating = $story->ratings->avg('rating') ?? 0;
    //             return [
    //                 'id' => $story->story_id,
    //                 'title' => $story->title,
    //                 'author' => $story->author,
    //                 'views' => $story->views ?? 0,
    //                 'description' => $story->description,
    //                 'image_path' => $story->base_url . $story->file_name,
    //                 'created_at' => $story->created_at,
    //                 'averageRating' => $averageRating,
    //                 'totalChapter' => $story->chapters->count(),
    //                 'favourite' => $story->favorites->count(),
    //                 'categories' => $story->categories->map(function ($categories) {
    //                     return [
    //                         'title' => $categories->title
    //                     ];
    //                 })
    //             ];
    //         });
    //         return ResponseHelper::success($stories, 'Lấy danh sách truyện thành công');
    //     } catch (\Exception $e) {
    //         return ErrorHelper::serverError($e, 'Lỗi khi tải danh sách truyện');
    //     }
    // }

    public function index(Request $request): JsonResponse
    {
        try {
            // Start with a base query including essential relationships
            $storiesQuery = Story::with([
                'ratings',
                'favorites',
                'categories',
                'chapters'
            ]);

            // Get the raw data first before applying transformations
            $stories = $storiesQuery->get();

            // Transform the stories data with calculated fields
            $transformedStories = $stories->map(function ($story) {
                return [
                    'id' => $story->story_id,
                    'title' => $story->title,
                    'author' => $story->author,
                    'views' => $story->chapters->sum('views') ?? 0,
                    'description' => $story->description,
                    'image_path' => $story->base_url . $story->file_name,
                    'is_vip' => $story->is_vip,
                    'created_at' => $story->created_at,
                    'averageRating' => $story->ratings->avg('rating') ?? 0,
                    'totalChapter' => $story->chapters->count(),
                    'favouriteCount' => $story->favorites->count(),
                    'categories' => $story->categories->map(function ($categories) {
                        return [
                            'title' => $categories->title
                        ];
                    })
                ];
            });

            // Apply sorting based on request parameter
            $sortedStories = $this->applySorting($transformedStories, $request->sort_by);

            return ResponseHelper::success(
                $sortedStories,
                'Stories retrieved successfully'
            );
        } catch (\Exception $e) {
            return ErrorHelper::serverError(
                $e,
                'Error occurred while retrieving stories'
            );
        }
    }
    private function applySorting($stories, $sortBy)
    {
        switch ($sortBy) {
            case 'oldest':
                return $stories->sortBy('created_at')->values(); // chuyển từ collection sang array để trả về dữ liệu ko có số ở đầu 

            case 'likes_high':
                return $stories->sortByDesc('favouriteCount')->values();

            case 'likes_low':
                return $stories->sortBy('favouriteCount')->values();

            case 'rating_high':
                return $stories->sortByDesc('averageRating')->values();

            case 'rating_low':
                return $stories->sortBy('averageRating')->values();

            case 'newest':
            default:
                return $stories->sortByDesc('created_at')->values();
        }
    }

    private function extractPublicIdFromUrl($url)
    {
        $withoutExtension = preg_replace('/\.[^.]+$/', '', $url);
        $public_id = preg_replace('/^.*\//', '', $withoutExtension);

        return $public_id;
    }

    public function show($id)
    {
        try {
            $story = Story::with('chapters', 'categories', 'ratings', 'favorites')->find($id);
            if (!$story) {
                return ErrorHelper::notFound('Không tìm thấy truyện');
            }
            $averageRating = $story->ratings->avg('rating') ?? 0;
            $favouriteCount = $story->favorites->count() ?? 0;
            $views = $story->chapters->sum('views');
            $detailedStory = [
                'id' => $story->story_id,
                'title' => $story->title,
                'author' => $story->author,
                'description' => $story->description,
                'is_vip' => $story->is_vip,
                'status' => $story->status ?? 0,
                'views' => $views ?? 0,
                'image_path' => $story->base_url . $story->file_name ?? 'null',
                'averageRating' => $averageRating,
                'favouriteCount' => $favouriteCount,
                'chapter' => $story->chapters->map(
                    function ($chapter) {
                        return [
                            'id' => $chapter->chapter_id,
                            'title' => $chapter->title,
                            'views' => $chapter->views ?? 0,
                            'is_vip' => $chapter->is_vip,
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
            return ResponseHelper::success($detailedStory, 'Lấy thông tin truyện thành công');
        } catch (Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi tải thông tin truyện');
        }
    }
    public function read($id)
    {
        Chapter::findOrFail($id)->increment('views');
        return ResponseHelper::success('Lấy thông tin truyện thành công');
    }
    public function store(Request $request)
    {
        try {
            ValidationHelper::make($request->all(), [
                'title' => 'required',
                'author' => 'required',
                'description' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,category_id',
                'is_vip' => 'boolean'
            ]);
            $duplicateCheck = Story::where(function ($query) use ($request) {
                $query->where('title', $request->title)
                    ->orWhere(function ($q) use ($request) {
                        $q->where('author', $request->author)
                            ->whereRaw('LOWER(title) = ?', [strtolower($request->title)]);
                    });
            })->first();

            if ($duplicateCheck) {
                return ErrorHelper::validationError(
                    [
                        'duplicate_story' => [
                            'id' => $duplicateCheck->story_id,
                            'title' => $duplicateCheck->title,
                            'author' => $duplicateCheck->author
                        ]
                    ],
                    'Truyện đã tồn tại'
                );
            }
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
                'file_name' => $fileName,
                'is_vip' => $request->is_vip ?? false
            ]);
            $story->categories()->attach($request->categories);
            return ResponseHelper::success($story, 'Thêm truyện mới thành công');
        } catch (Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi thêm truyện');
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
            return ResponseHelper::success($story, 'Cập nhật truyện thành công');
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi cập nhật truyện');
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $story = Story::findOrFail($id);
            DB::beginTransaction();
            try {
                $public_id = preg_replace('/\.[^.]+$/', '', $story->file_name);

                Cloudinary::destroy($public_id);
                $story->chapters()->each(function ($chapter) {
                    $chapter->images->each(function ($image) {
                        $public_id = $this->extractPublicIdFromUrl($image->file_name);
                        Cloudinary::destroy($public_id);
                    });
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

                return ResponseHelper::success('Xóa truyện thành công');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return ErrorHelper::notFound('Không tìm thấy truyện');
        } catch (Exception $e) {
            DB::rollBack();
            return ErrorHelper::serverError($e, 'Lỗi khi xóa truyện');
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
            return ResponseHelper::success($stories, 'Tìm kiếm truyện thành công');
        } catch (Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi tìm kiếm truyện');
        }
    }
}
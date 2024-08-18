<?php

namespace App\Http\Controllers;

use App\Models\Ratings;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{


    public function index(): JsonResponse
    {
        try {
            $datastories = Story::with('ratings')->get();
            $stories = $datastories->map(function ($story) {
                $averageRating = $story->ratings->avg('rating');
                return [
                    'id' => $story->story_id,
                    'title' => $story->title,
                    'author' => $story->author,
                    'description' => $story->description,
                    'image_path' => $story->image_path,
                    'created_at' => $story->created_at,
                    'updated_at' => $story->updated_at,
                    'averageRating' => $averageRating,
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
            $story = Story::with('chapters', 'categories', 'ratings')->find($id);
            $averageRating = $story->ratings->avg('rating');

            $detailedstorsy = [
                'id' => $story->story_id,
                'title' => $story->title,
                'author' => $story->author,
                'description' => $story->description,
                'image_path' => $story->image_path,
                'averageRating' => $averageRating,
                'chapter' => $story->chapters->map(
                    function ($chapter) {
                        return [
                            'id' => $chapter->chapter_id,
                            'title' => $chapter->title,
                            'created_at' => $chapter->created_at
                        ];
                    }
                ),
                'categories' => $story->categories->map(
                    function ($categories) {
                        return [
                            'id' => $categories->story_id,
                            'title' => $categories->title,
                        ];
                    }
                ),
                'ratings' => $story->ratings() ? $story->ratings->map(
                    function ($rating) {
                        return [
                            'id' => $rating->id,
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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'author' => 'required',
                'description' => 'required',
                'image_path' => 'required'
            ]);
            $story = Story::create($request->all());
            return response()->json([
                'status' => 'store success',
                'data' => $story
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
            $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'description' => 'required|string',
                'image_path' => 'required|string|max:255'
            ]);

            $story = Story::find($id);

            if (!$story) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Story not found.'
                ], 404);
            }

            $story->update($request->all());

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
            $story = Story::find($id);

            if (!$story) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Story not found.'
                ], 404);
            }

            $story->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Story deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while deleting the story.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function search(Request $request): JsonResponse
    {
        try {
            // Lấy tham số tìm kiếm từ request
            $search = $request->query('title');

            // Eager load mối quan hệ 'ratings' để tránh lỗi khi truy cập thuộc tính
            $datastories = Story::with('ratings')
                ->where('title', 'like', '%' . $search . '%')
                ->get();

            // Kiểm tra nếu không tìm thấy câu chuyện nào
            if ($datastories->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => []
                ], 200);
            }
            // Xử lý dữ liệu
            $stories = $datastories->map(function ($story) {
                // Tính giá trị trung bình của các đánh giá, kiểm tra trước khi tính
                $averageRating = $story->ratings ? $story->ratings->avg('rating') : null;
                return [
                    'id' => $story->story_id,
                    'title' => $story->title,
                    'author' => $story->author_name, // Đảm bảo bạn sử dụng đúng tên cột
                    'description' => $story->description,
                    'image_path' => $story->image_path,
                    'created_at' => $story->created_at,
                    'updated_at' => $story->updated_at,
                    'averageRating' => $averageRating,
                    'ratings' => $story->ratings ? $story->ratings->map(function ($rating) {
                        return [
                            'id' => $rating->id,
                            'user_id' => $rating->user_id,
                            'rating' => $rating->rating,
                            'created_at' => $rating->created_at
                        ];
                    }) : []
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $stories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Thêm stack trace để xem chi tiết lỗi
            ], 400);
        }
    }
    // Admin Methods
    public function adminIndex()
    {
        $stories = Story::paginate(5);
        return view('admin.stories.index', compact('stories'));
    }

    public function createW()
    {
        return view('admin.stories.create');
    }

    public function storeW(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'image_path' => 'required|string|max:255'
        ]);

        Story::create($request->all());

        return redirect()->route('admin.stories')->with('success', 'Story created successfully.');
    }

    public function editW($story_id)
    {
        $story = Story::find($story_id);

        if (!$story) {
            return redirect()->route('admin.stories')->with('error', 'Story not found.');
        }

        return view('admin.stories.edit', compact('story'));
    }

    public function updateW(Request $request, $story_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'image_path' => 'required|string|max:255'
        ]);

        $story = Story::find($story_id);

        if (!$story) {
            return redirect()->route('admin.stories')->with('error', 'Story not found.');
        }

        $story->update($request->all());

        return redirect()->route('admin.stories')->with('success', 'Story updated successfully.');
    }

    public function destroyW($story_id)
    {
        $story = Story::find($story_id);

        if (!$story) {
            return redirect()->route('admin.stories')->with('error', 'Story not found.');
        }

        $story->delete();

        return redirect()->route('admin.stories')->with('success', 'Story deleted successfully.');
    }
}

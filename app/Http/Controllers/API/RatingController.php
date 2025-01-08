<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\RatingResource;
use Illuminate\Http\Request;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use App\Models\Rating;
use App\Models\Story;
use App\Models\Ratings;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function index($id)
    {
        try {
            $ratings = Ratings::where('story_id', $id)->with('user')->get();
            return ResponseHelper::success(
                RatingResource::collection($ratings),
                'Lấy danh sách đánh giá thành công'
            );
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e);
        }
    }
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            if (!$user) {
                return ErrorHelper::unauthorized('Người dùng chưa được xác thực');
            }
            $rating = Ratings::find($id);
            if ($rating->user_id !== $user->id) {
                return ErrorHelper::response(
                    'Bạn không có quyền xóa đánh giá này',
                    403
                );
            }
            $rating->delete();
            DB::commit();
            return ResponseHelper::success(
                'Xóađánh giá thành công'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHelper::serverError($e, 'Không thể xóa đánh giá');
        }
    }
    public function ratings(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return ErrorHelper::unauthorized('Người dùng chưa được xác thực');
            }

            $validatedData = $request->validate([
                'story_id' => 'required|exists:stories,story_id',
                'title'    => 'required',
                'rating'   => 'required|integer|min:1|max:5'
            ]);

            $story = Story::findOrFail($validatedData['story_id']);
            $isPosted = Ratings::where('user_id', $user->id)
                ->where('story_id', $story->story_id)->first();

            if ($isPosted) {
                return ErrorHelper::badRequest(
                    'Bạn đã đánh giá câu chuyện này',
                    null
                );
            }

            $rating = Ratings::create([
                'user_id'  => $user->id,
                'story_id' => $story->story_id,
                'rating'   => $validatedData['rating'],
                'title'    => $validatedData['title']
            ]);

            return ResponseHelper::success(
                $rating,
                'Đánh giá thành công'
            );
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e);
        }
    }
    public function getAllRating(Request $request)
    {
        try {
            // Tạo query lấy dữ liệu ratings kèm theo thông tin story và user liên quan
            $query = Ratings::with(['story', 'user']);

            // Xử lý sắp xếp dựa trên giá trị sort_by từ request
            switch ($request->sort_by) {
                case 'oldest': // Cũ nhất
                    $query->oldest();  // Sắp xếp theo created_at tăng dần
                    break;
                case 'rating_high': // Đánh giá cao nhất
                    $query->orderBy('rating', 'desc'); // Sắp xếp rating giảm dần
                    break;
                case 'rating_low': // Đánh giá thấp nhất  
                    $query->orderBy('rating', 'asc'); // Sắp xếp rating tăng dần
                    break;
                default: // Mặc định - mới nhất
                    $query->latest(); // Sắp xếp theo created_at giảm dần
                    break;
            }
            $ratings = $query->get();
            return RatingResource::collection($ratings);
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e);
        }
    }
}

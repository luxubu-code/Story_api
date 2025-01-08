<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use App\Http\Helpers\ValidationHelper;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\be;

class CommentController extends Controller
{
    public function index($id)
    {
        try {
            $comments = Comment::where('story_id', $id)
                ->with(['user', 'story', 'replies'])
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            return ResponseHelper::success(CommentResource::collection($comments), 200);
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Không thể lấy danh sách bình luận');
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
            $comment = Comment::find($id);
            if ($comment->user_id !== $user->id) {
                return ErrorHelper::response(
                    'Bạn không có quyền xóa bình luận này',
                    403
                );
            }
            $comment->replies()->delete();
            $comment->delete();
            DB::commit();
            return ResponseHelper::success(
                'Xóa bình luận thành công'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ErrorHelper::serverError($e, 'Không thể xóa bình luận');
        }
    }
    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();
            ValidationHelper::make($request->all(), [
                'content' => 'required',
                'story_id' => 'required|exists:stories,story_id',
                'parent_id' => 'nullable|exists:comments,id',
            ]);
            $comment = Comment::create([
                'content' => $request->input('content'),
                'story_id' => $request->input('story_id'),
                'parent_id' => $request->input('parent_id'),
                'user_id' => $user->id,
            ]);
            return ResponseHelper::success(
                $comment,
                'Bình luận thành công'
            );
        } catch (\Exception $e) {
            return ErrorHelper::serverError(
                $e,
                'Không thể tạo bình luận'
            );
        }
    }
    public function getAllComment(Request $request)
    {
        // $comment = Comment::all();
        try {
            $query = Comment::with('user');
            // Xử lý sắp xếp dựa trên giá trị sort_by từ request
            switch ($request->sort_by) {
                case 'oldest': // Cũ nhất
                    $query->oldest();  // Sắp xếp theo created_at tăng dần
                    break;
                case 'comment_high': // bình luận 
                    $query->orderBy('likes', 'desc'); // Sắp xếp rating giảm dần
                    break;
                case 'comment_low': // bình luận
                    $query->orWhereBetween('created_at', ['2025-01-01', '2025-01-05']);
                    break;
                default: // Mặc định - mới nhất
                    $query->orderBy('created_at', 'desc');
                    // $query->latest(); // Sắp xếp theo created_at giảm dần
                    break;
            }

            $comment = $query->get();
            return CommentResource::collection($comment);
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Không thể lấy danh sách bình luận');
        }
    }
}
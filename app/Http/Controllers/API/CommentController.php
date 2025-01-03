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
                ->with('user')
                ->with('replies')
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
    public function getAllComment()
    {
        // $comment = Comment::all();
        $comment = Comment::with('user')->get();
        return ResponseHelper::success($comment, 'Lấy dữ liệu thành công');
    }
}

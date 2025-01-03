<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use App\Http\Helpers\ValidationHelper;
use App\Http\Resources\HistoryResource;
use App\Models\ReadingChapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ReadingHistory;
use App\Models\Story;
use App\Models\User;
use Google\Api\ResourceDescriptor\History;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReadingHistoryController extends Controller
{
    // Trong controller, thay thế đoạn code hiện tại bằng:
    public function index()
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                Log::error('User not authenticated');
                return ErrorHelper::unauthorized('Người dùng chưa đăng nhập', 401);
            }

            Log::info('User ID: ' . $user->id); // Thêm log để debug

            $history = ReadingHistory::where('user_id', $user->id)
                ->with(['story' => function ($query) {
                    $query->select('story_id', 'title', 'base_url', 'file_name');
                }])
                ->get();

            if ($history->isEmpty()) {
                return ResponseHelper::success([], 'Không có lịch sử đọc');
            }

            return ResponseHelper::success(
                HistoryResource::collection($history),
                'Lấy lịch sử đọc thành công'
            );
        } catch (\Exception $e) {
            Log::error('Reading History Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return ErrorHelper::serverError($e, 'Lỗi khi lấy lịch sử đọc');
        }
    }
    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();
            ValidationHelper::make($request->all(), [
                'id' => 'nullable',
                'story_id' => 'required|exists:stories,story_id',
                'chapter_id' => 'required|exists:chapters,chapter_id',
            ]);
            $story = Story::findOrFail($request['story_id']);
            $history = ReadingHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'story_id' => $request['story_id'],
                    'base_url' => $story->base_url,
                    'file_name' => $story->file_name,
                ],
                [
                    'chapter_id' => $request['chapter_id'],
                    'read_at' => now()
                ]
            );
            return ResponseHelper::success($history, 'Lịch sử đọc được lưu thành công');
        } catch (\Exception $e) {
            return ErrorHelper::serverError($e, 'Lỗi khi lưu lịch sử đọc');
        }
    }
}
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
use Illuminate\Validation\ValidationException;

class ReadingHistoryController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();
        $history = ReadingHistory::where('user_id', $user->id)->with(['story', 'chapters'])->get();
        return ResponseHelper::success(
            HistoryResource::collection($history),
            'Lấy lịch sử đọc thành công'
        );
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
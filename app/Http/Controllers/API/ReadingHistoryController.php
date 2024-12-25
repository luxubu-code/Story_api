<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        return response()->json([
            'response_code' => '200',
            'status'        => 'success',
            'message'       => 'success get reading history',
            'data'          => HistoryResource::collection($history),
        ]);
    }
    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();
            $validatedData = $request->validate([
                'id' => 'nullable',
                'story_id' => 'required|exists:stories,story_id',
                'chapter_id' => 'required|exists:chapters,chapter_id',
            ]);
            $story = Story::findOrFail($validatedData['story_id']);
            $history = ReadingHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'story_id' => $validatedData['story_id'],
                    'base_url' => $story->base_url,
                    'file_name' => $story->file_name,
                ],
                [
                    'chapter_id' => $validatedData['chapter_id'],
                    'read_at' => now()
                ]
            );

            return response()->json([
                'response_code' => '200',
                'status'        => 'success',
                'data'          => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => '500',
                'status'        => 'error',
                'message'       => $e->getMessage(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => '422',
                'status'        => 'error',
                'message'       => $e->getMessage(),
            ]);
        }
    }
}
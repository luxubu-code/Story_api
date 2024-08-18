<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReadingChapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ReadingHistory;


class ReadingHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $history = ReadingHistory::where('user_id', $user->id)->with('readingchapters')->get();
        return response()->json([
            'response_code' => '200',
            'status'        => 'success',
            'data'          => $history,
        ]);
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'story_id' => 'required|exists:stories,story_id',
            'chapter_id' => 'required|exists:chapters,chapter_id',
        ]);
        $history = ReadingHistory::firstOrCreate(
            [
                'user_id' => $user->id,
                'story_id' => $validatedData['story_id']
            ],
            [
                'read_at' => now()
            ]
        );
        $readingChapter = ReadingChapter::firstOrCreate(
            [
                'user_id' => $history->user_id,
                'story_id' => $history->story_id,
                'chapter_id' => $validatedData['chapter_id']
            ],
        );


        return response()->json([
            'response_code' => '200',
            'status'        => 'success',
            'data'          => $history,
            'readingChapter'          => $readingChapter,
        ]);
    }
}

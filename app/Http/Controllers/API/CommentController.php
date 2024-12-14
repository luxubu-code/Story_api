<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            return response()->json([
                'message' => 'Success',
                'data' => CommentResource::collection($comments),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'content' => 'required',
                'story_id' => 'required|exists:stories,story_id',
                'parent_id' => 'nullable|exists:comments,id'
            ]);
            $comment = Comment::create([
                'content' => $request->input('content'),
                'story_id' => $request->input('story_id'),
                'parent_id' => $request->input('parent_id'),
                'user_id' => $user->id,
            ]);
            return response()->json([
                'message' => 'Comment created successfully',
                'status'        => 'success',
                'data' => $comment
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Failed to create comment',
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
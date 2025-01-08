<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentWebController extends CommentController
{
    public function getAll(Request $request)
    {
        $comments = parent::getAllComment($request);
        return view("comment.index", compact("comments"));
    }
    public function deleteComment($id)
    {
        $comment = Comment::find($id);
        if ($comment->delete()) {
            return redirect()->route('comment.index')->with('success', 'Comment deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Comment could not be found or deleted!');
        }
    }
}

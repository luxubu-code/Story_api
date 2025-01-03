<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentWebController extends CommentController
{
    public function getAll()
    {
        $response = parent::getAllComment();
        $comments = $response->original['data'];
        return view("comment.index", compact("comments"));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;

class PublishCommentController extends Controller
{

    public function store(Comment $comment)
    {
        $comment->publish();

        session()->flash('status', [
            'type' => 'success',
            'message' => 'کامنت با موفقیت منتشر شد.'
        ]);
        return back();
    }

    public function destroy(Comment $comment)
    {
        $comment->unpublish();

        session()->flash('status', [
            'type' => 'success',
            'message' => 'کامنت با موفقیت از حالت انتشار در آمد.'
        ]);
        return back();
    }
}

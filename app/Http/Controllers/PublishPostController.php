<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PublishPostController extends Controller {

    public function store(Post $post)
    {
        $post->publish();

        session()->flash('status', [
            'type' => 'success',
            'message' => 'پست با موفقیت منتشر شد.'
        ]);

        return back();
    }

    public function destroy(Post $post)
    {
        $post->unpublish();

        session()->flash('status', [
            'type' => 'success',
            'message' => 'پست با موفقیت از حالت انتشار در آمد.'
        ]);

        return back();
    }
}

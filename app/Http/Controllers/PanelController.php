<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;

class PanelController extends Controller
{

    public function dashboard()
    {
        $posts = Post::withoutGlobalScopes()->count();
        $categories = Category::withoutGlobalScopes()->count();
        $comments = Comment::count();
        $tags = Tag::count();
        return view('panel.dashboard',compact('posts','categories','comments','tags'));
    }
}

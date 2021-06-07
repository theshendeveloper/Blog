<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class TagPostsController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function __invoke($tag)
    {

        $tag = Tag::where("slug->fa", $tag)->first();
        $current_page = request()->get('page') ?: "1";
        $posts = Cache::tags('tag-'.$tag->id.'-posts')->rememberForever('tag-'.$tag->id.'-posts-'.$current_page,function () use($tag) {
            return $tag->posts()->with(['author','comments'])->latest()->paginate(3);
        });
        return view('blog.category-posts',compact('posts','tag'));
    }

}

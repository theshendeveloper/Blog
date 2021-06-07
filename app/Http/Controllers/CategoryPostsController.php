<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryPostsController extends Controller
{

    public function __invoke(Category $category)
    {
        $current_page = request()->get('page') ?: "1";
        $posts = Cache::tags('category-'.$category->id.'-posts')->rememberForever('category-'.$category->id.'-posts-'.$current_page,function () use($category) {
            return $category->posts()->with(['author','comments','categories'])->latest()->paginate(3);
        });
        return view('blog.category-posts',compact('posts','category'));
    }
}

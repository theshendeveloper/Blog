<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{

    private $post;

    public function __construct(PostRepository $post)
    {
        $this->post = $post;
    }

    public function index(Request $request)
    {
        if ($request->has('search'))
        {
            // Get the search value from the request
            $search = $request->search;

            // Search in the title and body columns from the posts table
            $posts = Post::where('title', 'LIKE', "%{$search}%")
                ->orWhere('content', 'LIKE', "%{$search}%")
                ->with(['author','comments','categories'])
                ->paginate(3);
            $posts->appends(['search' => $search]);
        }
        else{
            $current_page = request()->get('page') ?: "1";
            $posts = Cache::tags('posts')->rememberForever('posts-' . $current_page, function () {
                return $this->post->getPublishedWith(['author','comments','categories'], 3);
            });
        }


        return view('blog.index', compact('posts'));
    }
}

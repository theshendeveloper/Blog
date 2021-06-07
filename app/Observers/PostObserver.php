<?php

namespace App\Observers;

use App\Events\PostDeleted;
use App\Events\PostPublished;
use App\Events\PostUpdated;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function __construct()
    {
        Cache::tags('panel-posts')->flush();
        Cache::forget('latest-posts');
        Cache::forget('popular-tags');
    }

    /**
     * Handle the Post "created" event.
     *
     * @param Post $post
     * @return void
     */
    public function created(Post $post)
    {
        if ($post->is_published == true){
            PostPublished::dispatch($post);
        }
    }

    /**
     * Handle the Post "updated" event.
     *
     * @param Post $post
     * @return void
     */
    public function updated(Post $post)
{
    // Clean Caches Related To Blog Posts And Category Posts And The Post Itself
PostUpdated::dispatch($post);
}

/**
 * Handle the Post "deleted" event.
 *
 * @param Post $post
 * @return void
 */
public function deleted(Post $post)
{
    if($post->is_published){
        PostDeleted::dispatch($post);
    }
}

}

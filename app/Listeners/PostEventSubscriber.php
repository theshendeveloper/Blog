<?php

namespace App\Listeners;

use App\Models\Post;
use App\Models\Subscriber;
use App\Notifications\PersonSubscribedNotification;
use App\Notifications\PostPublishedNotification;
use Illuminate\Support\Facades\Cache;

class PostEventSubscriber
{
    protected function clearPostsCache($event){
        Cache::tags('posts')->flush();
        foreach ($event->post->categories as $category){
            Cache::tags('category-'.$category->id.'-posts')->flush();
        }
        foreach ($event->post->tags as $tag){
            Cache::tags('tag-'.$tag->id.'-posts')->flush();
        }
    }

    /**
     * Handle post publish events.
     *
     * @param  object  $event
     * @return void
     */
    public function handlePostPublished($event)
    {
        $this->clearPostsCache($event);
        foreach (Subscriber::all() as $subscriber){
            $subscriber->notify(new PostPublishedNotification($event->post));
        }
    }

    /**
     * Handle post update events.
     *
     * @param  object  $event
     * @return void
     */
    public function handlePostUpdated($event)
    {
        Cache::forget('post-'.$event->post->id);
        $this->clearPostsCache($event);

    }

    /**
     * Handle post update events.
     *
     * @param  object  $event
     * @return void
     */
    public function handlePostDeleted($event)
    {
        Cache::forget('post-'.$event->post->id);
        $this->clearPostsCache($event);
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\PostUpdated',
            'App\Listeners\PostEventSubscriber@handlePostUpdated'
        );

        $events->listen(
            'App\Events\PostPublished',
            'App\Listeners\PostEventSubscriber@handlePostPublished'
        );

        $events->listen(
            'App\Events\PostDeleted',
            'App\Listeners\PostEventSubscriber@handlePostDeleted'
        );
    }
}

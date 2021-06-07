<?php

namespace App\Providers;

use App\Listeners\PostEventSubscriber;
use App\Models\Post;
use App\Observers\PostObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
      PostEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Post::observe(PostObserver::class);
    }
}

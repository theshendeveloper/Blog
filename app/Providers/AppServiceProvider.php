<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('categories'))
        {
            View::share('categories', Category::withPostsCount()->get());
            View::share('tags', Tag::withPostsCount()->get());
        }

            View::composer('*', function ($view) {
                $latest = Cache::rememberForever('latest-posts',function (){
                     return Post::latest()->get()->take(3);
                });
                $view->with('latest_posts', $latest);
                $popular = Cache::rememberForever('popular-tags',function (){
                    return Tag::Popular()->get()->take(5);
                });
                $view->with('popular_tags', $popular);
            });

    }
}

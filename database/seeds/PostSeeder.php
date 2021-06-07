<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Post::class,10)->create()->each(function ($post) {
            $post->comments()->saveMany(factory(Comment::class,2)->create());
            $post->categories()->saveMany(factory(Category::class,2)->create());
            $post->tags()->saveMany(factory(Tag::class,2)->create());
        });
    }
}

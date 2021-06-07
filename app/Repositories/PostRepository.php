<?php


namespace App\Repositories;


use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostRepository extends Repository {

    public function model()
    {
        return Post::class;
    }
    public function getPublishedWith($relation,$limit=5)
    {
        return Post::with($relation)->latest()->paginate($limit);


    }

    public function create(array $data) {
        $post = Auth::user()->posts()->create($data);
        $post->categories()->sync($data['categories']);
        $post->attachTags($data['tags']);
        return $post;
    }

    public function update($post,array $data)
    {
        $post->update($data);
        $post->categories()->sync($data['categories']);
        $post->syncTags($data['tags']);


    }
}

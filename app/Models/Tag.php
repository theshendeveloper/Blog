<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use Spatie\Tags\Tag as SpatieTag;
use function _HumbugBox373c0874430e\RingCentral\Psr7\try_fopen;

class Tag extends SpatieTag
{
    use HasTags;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class,'taggable');
    }

    public function scopeWithPostsCount($query)
    {
        return $query->withCount('posts');
    }
    public function scopePopular($query)
    {
        return $query->withCount('posts')->orderBy('posts_count','desc');
    }
}

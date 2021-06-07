<?php

namespace App\Models;

use App\Http\Traits\HasPublished;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasPublished,HasTags;

    public static function getTagClassName(): string
    {
        return Tag::class;
    }
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable')
            ->ordered();
    }
    public function setTagsAttribute($tags)
    {
        if (app()->environment() =='testing'){
            $this->attributes['tags'] = $tags;
        }
        if (! $this->exists) {
            $this->queuedTags = $tags;

            return;
        }

        $this->syncTags($tags);
    }

    protected $fillable=[
      'title',
      'slug',
      'banner',
      'content',
      'summary',
      'author_id'
    ];

    protected $casts = ['is_published' => 'boolean'];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function resolveRouteBinding($value)
    {
        if (Auth::check()){
            return $this->where($this->getRouteKeyName(), $value)->withoutGlobalScope('published')->first();
        }
        return $this->where($this->getRouteKeyName(), $value)->first();
    }


    public function setAttributeContent($value)
    {
        $this->attributes['content'] = Purifier::clean($value);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class,'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function path()
    {
        return route('posts.show',$this);
    }
}

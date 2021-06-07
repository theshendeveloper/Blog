<?php


namespace App\Http\Traits;


use App\Events\PostPublished;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

trait HasPublished {

    public static function bootHasPublished()
    {
        static::addGlobalScope('published',function (Builder $builder){
            $builder->where('is_published', true);
        });
    }

    public function publish()
    {
        $this->is_published = true;
        if ($this instanceof Post){
            PostPublished::dispatch($this);
        }
        $this->save();
    }
    public function unpublish()
    {
        $this->is_published = false;
        $this->save();
    }
}

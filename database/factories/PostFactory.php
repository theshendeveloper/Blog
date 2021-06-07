<?php
/** @var Factory $factory */

use App\Models\Post;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(Post::class, function (Faker $faker) {

    $title = implode(' ',$faker->words(5));
    $content = $faker->paragraph(7);
    return [
        'title' => $title,
        'content' => $content,
        'summary' => Str::words($content,15),
        'slug' => Str::slug($title),
        'banner' => $faker->imageUrl(),
        'author_id' => User::WhereHas('roles',function ($q){
            $q->where('name','Writer');
        })->inRandomOrder()->first(),
        'is_published' => $faker->boolean,

    ];
});

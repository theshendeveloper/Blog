<?php
/** @var Factory $factory */

use App\Models\Comment;
use App\Models\Post;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'author' => $faker->name,
        'phone' => $faker->regexify('{?(0?9[0-9]{9,9}}?)$'),
        'content' => $faker->paragraph(2),
        'avatar' => $faker->imageUrl(50,50),
        'post_id' => Post::inRandomOrder()->first(),
        'is_published' => $faker->boolean,
    ];
});

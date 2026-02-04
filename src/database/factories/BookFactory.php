<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Book;
use Faker\Generator as Faker;

$factory->define(Book::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(3),
        'description' => $faker->paragraph,
        'published_date' => $faker->date(),
        'author_id' => function () {
            return factory(App\Models\Author::class)->create()->id;
        },
    ];
});

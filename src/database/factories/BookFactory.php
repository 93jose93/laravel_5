<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Book;
use App\Models\Author;
use Faker\Generator as Faker;

$factory->define(Book::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(3),
        'description' => $faker->paragraph,
        'published_date' => $faker->date(),
        'author_id' => function () {
            // Reutilizar autor existente o crear uno nuevo
            $existingAuthor = Author::first();
            return $existingAuthor ? $existingAuthor->id : factory(Author::class)->create()->id;
        },
    ];
});

<?php

use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Book;

class AuthorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 10 authors, each with 2 books
        factory(Author::class, 10)->create()->each(function ($author) {
            // Pass author_id to prevent factory from creating a new random author for each book
            $books = factory(Book::class, 2)->make(['author_id' => $author->id]);
            $author->books()->saveMany($books);

            // Update logic for books_count
            $author->books_count = $books->count();
            $author->save();
        });
    }
}

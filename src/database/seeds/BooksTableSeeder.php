<?php

use Illuminate\Database\Seeder;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Books are currently seeded via AuthorsTableSeeder to maintain relationships easier.
        // However, we can add standalone books here if needed.
    }
}

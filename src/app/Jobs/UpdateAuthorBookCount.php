<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use App\Models\Author;
use App\Models\Book;

class UpdateAuthorBookCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $authorId;

    public function __construct($authorId)
    {
        $this->authorId = $authorId;
    }

    public function handle()
    {
        $count = Book::where('author_id', $this->authorId)->count();
        Author::where('id', $this->authorId)->update(['books_count' => $count]);
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use Illuminate\Support\Facades\DB;

use App\Models\Author;
use App\Models\Book;

class UpdateAuthorBookCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $authorId;

    public function __construct($authorId)
    {
        $this->authorId = $authorId;
    }

    public function handle()
    {
        DB::transaction(function () {
            // Lock del registro del autor para evitar race conditions
            $author = Author::lockForUpdate()->find($this->authorId);

            if ($author) {
                $count = Book::where('author_id', $this->authorId)->count();
                $author->update(['books_count' => $count]);
            }
        });
    }
}

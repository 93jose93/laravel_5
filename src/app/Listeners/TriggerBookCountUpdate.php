<?php

namespace App\Listeners;

use App\Events\BookCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\Jobs\UpdateAuthorBookCount;

class TriggerBookCountUpdate
{
    public function __construct()
    {
        //
    }

    public function handle(BookCreated $event)
    {
        dispatch(new UpdateAuthorBookCount($event->book->author_id));
    }
}

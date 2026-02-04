<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['name', 'surname', 'books_count'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}

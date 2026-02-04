<?php

namespace App\Exports\Sheets;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class BooksSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        return Book::all();
    }

    public function title(): string
    {
        return 'Books';
    }
}

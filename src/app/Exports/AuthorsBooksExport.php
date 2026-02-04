<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;


use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\AuthorsSheet;
use App\Exports\Sheets\BooksSheet;

class AuthorsBooksExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new AuthorsSheet(),
            new BooksSheet(),
        ];
    }
}

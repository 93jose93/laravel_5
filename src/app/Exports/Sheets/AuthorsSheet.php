<?php

namespace App\Exports\Sheets;

use App\Models\Author;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class AuthorsSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        return Author::all();
    }

    public function title(): string
    {
        return 'Authors';
    }
}

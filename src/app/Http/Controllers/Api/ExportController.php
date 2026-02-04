<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Exports\AuthorsBooksExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function export()
    {
        return Excel::download(new AuthorsBooksExport, 'authors_books.xlsx');
    }
}

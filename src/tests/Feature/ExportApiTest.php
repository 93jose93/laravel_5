<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Author;
use App\Models\Book;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuthorsBooksExport;

class ExportApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->token = auth('api')->login($this->user);
    }

    public function test_can_export_authors_books()
    {
        Excel::fake();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/v1/export');

        $response->assertStatus(200);

        Excel::assertDownloaded('authors_books.xlsx', function (AuthorsBooksExport $export) {
            return true;
        });
    }

    public function test_export_requires_authentication()
    {
        auth('api')->logout();
        Excel::fake();
        $response = $this->getJson('/api/v1/export');

        $response->assertStatus(401);
    }
}

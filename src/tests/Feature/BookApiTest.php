<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Author;
use App\Models\Book;
use App\User;
use Illuminate\Support\Facades\Event;
use App\Events\BookCreated;

class BookApiTest extends TestCase
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

    protected function withAuth()
    {
        return $this->withHeader('Authorization', "Bearer {$this->token}");
    }

    public function test_can_list_books()
    {
        factory(Book::class, 3)->create();

        $response = $this->withAuth()->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_book_and_dispatches_event()
    {
        Event::fake();

        $author = factory(Author::class)->create();
        $data = [
            'title' => 'Sample Book',
            'published_date' => '2023-01-01',
            'author_id' => $author->id,
            'description' => 'A nice book',
        ];

        $response = $this->withAuth()->postJson('/api/v1/books', $data);

        $response->assertStatus(201);

        Event::assertDispatched(BookCreated::class);
        $this->assertDatabaseHas('books', ['title' => 'Sample Book']);
    }

    public function test_can_show_book()
    {
        $book = factory(Book::class)->create();

        $response = $this->withAuth()->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => $book->title]);
    }

    public function test_can_update_book()
    {
        $book = factory(Book::class)->create();
        $data = ['title' => 'Updated Title'];

        $response = $this->withAuth()->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);
    }

    public function test_can_delete_book()
    {
        $book = factory(Book::class)->create();

        $response = $this->withAuth()->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_cannot_list_books_without_token()
    {
        auth('api')->logout();
        $response = $this->getJson('/api/v1/books');
        $response->assertStatus(401);
    }
}

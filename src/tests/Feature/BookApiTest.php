<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Models\Author;
use App\Models\Book;
use App\User;
use Illuminate\Support\Facades\Event;
use App\Events\BookCreated;
use App\Jobs\UpdateAuthorBookCount;

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

    public function test_job_updates_author_book_count()
    {
        // Crear autor con 0 libros
        $author = factory(Author::class)->create(['books_count' => 0]);

        // Crear 3 libros para el autor
        factory(Book::class, 3)->create(['author_id' => $author->id]);

        // Ejecutar el Job manualmente
        $job = new UpdateAuthorBookCount($author->id);
        $job->handle();

        // Verificar que books_count se actualizó a 3
        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'books_count' => 3
        ]);
    }

    public function test_cannot_create_book_with_invalid_author_id()
    {
        $data = [
            'title' => 'Sample Book',
            'published_date' => '2023-01-01',
            'author_id' => 9999, // Autor que no existe
            'description' => 'A nice book',
        ];

        $response = $this->withAuth()->postJson('/api/v1/books', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['author_id']);
    }

    public function test_complete_flow_creates_book_and_updates_count_via_job()
    {
        Queue::fake();

        $author = factory(Author::class)->create(['books_count' => 0]);

        $data = [
            'title' => 'Complete Flow Book',
            'published_date' => '2023-01-01',
            'author_id' => $author->id,
            'description' => 'Testing complete flow',
        ];

        // Crear libro
        $response = $this->withAuth()->postJson('/api/v1/books', $data);
        $response->assertStatus(201);

        // Verificar que el Job fue despachado
        Queue::assertPushed(UpdateAuthorBookCount::class, function ($job) use ($author) {
            return $job->authorId === $author->id;
        });

        // Ejecutar el Job
        $job = new UpdateAuthorBookCount($author->id);
        $job->handle();

        // Verificar que el contador se actualizó
        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'books_count' => 1
        ]);
    }
}

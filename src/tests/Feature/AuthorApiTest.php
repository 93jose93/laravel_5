<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Author;
use App\User;

class AuthorApiTest extends TestCase
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

    public function test_can_list_authors()
    {
        factory(Author::class, 3)->create();

        $response = $this->withAuth()->getJson('/api/v1/authors');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_author()
    {
        $data = [
            'name' => 'Gabriel',
            'surname' => 'Garcia Marquez',
        ];

        $response = $this->withAuth()->postJson('/api/v1/authors', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('authors', $data);
    }

    public function test_can_show_author()
    {
        $author = factory(Author::class)->create();

        $response = $this->withAuth()->getJson("/api/v1/authors/{$author->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $author->name]);
    }

    public function test_can_update_author()
    {
        $author = factory(Author::class)->create();
        $data = ['name' => 'Updated Name'];

        $response = $this->withAuth()->putJson("/api/v1/authors/{$author->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('authors', $data);
    }

    public function test_can_delete_author()
    {
        $author = factory(Author::class)->create();

        $response = $this->withAuth()->deleteJson("/api/v1/authors/{$author->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    }

    public function test_cannot_list_authors_without_token()
    {
        auth('api')->logout();
        $response = $this->getJson('/api/v1/authors');
        $response->assertStatus(401);
    }
}

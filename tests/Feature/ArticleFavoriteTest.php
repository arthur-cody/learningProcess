<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleFavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up a common user for all tests
        $this->user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

    }

    public function test_user_can_favorite_article()
    {
        $articles = Article::factory()->count(5)->create();
        $articleSlug = $articles->first();
        $this->actingAs($this->user)
            ->postJson('/api/articles/'.$articleSlug->slug.'/favorite')
            ->assertStatus(201);
    }

    public function test_guest_cannot_favorite_article()
    {
        $articles = Article::factory()->count(5)->create();
        $article = $articles->first();

        $response = $this->postJson('/api/articles/'.$article->slug.'/favorite')
            ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response['message']);
    }

    public function test_guest_cannot_unfavorite_article()
    {
        $articles = Article::factory()->count(5)->create();
        $article = $articles->first();

        $response = $this->deleteJson('/api/articles/'.$article->slug.'/favorite')
            ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response['message']);
    }

    public function test_user_can_unfavorite_favorited_article()
    {
        $articles = Article::factory()->count(5)->create();
        $articleSlug = $articles->first();
        $this->actingAs($this->user)
            ->deleteJson('/api/articles/'.$articleSlug->slug.'/favorite')
            ->assertStatus(201);
    }
}

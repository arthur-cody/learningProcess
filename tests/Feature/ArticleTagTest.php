<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Database\Factories\ArticleFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTagTest extends TestCase
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

    public function test_get_article_tag()
    {
        ArticleFactory::new()->hasTags();
        $this->actingAs($this->user)
             ->getJson('/api/tags')
             ->assertStatus(200);
    }
}

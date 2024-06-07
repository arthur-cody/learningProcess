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

    public function test_user_can_get_tags()
    {
        ArticleFactory::new()->hasTags();
        $this->actingAs($this->user)
             ->getJson('/api/tags')
             ->assertStatus(200);
    }

    public function test_guest_cannot_get_tags()
    {
        $response = $this->getJson('/api/tags')
             ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response['message']);
    }
}

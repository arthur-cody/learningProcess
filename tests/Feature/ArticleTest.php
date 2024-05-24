<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_articles()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $response = $this->get('/api/articles');

        $response->assertJsonStructure([
            'articles' => [
                '*' => [
                    'title',
                    'slug',
                    'author' => [
                        'username',
                        'bio',
                        'image',
                        'following',
                    ],
                    'description',
                    'body',
                    'tagList',
                    'favorited',
                    'favoritesCount',
                    'createdAt',
                    'updatedAt',
                ],
            ],
        ]);

        $response->assertStatus(200);

        $this->assertAuthenticated();
    }
}

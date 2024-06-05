<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleCommentTest extends TestCase
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

    public function test_guest_cannot_add_comment_to_article()
    {
        $article = Article::factory()->createOne();
        $data = [
                "comment" => [
                  "body"=> "Comment body for this article"
                ]
            ];
       $response = $this->postJson('/api/articles/'.$article->slug.'/comments', $data)
            ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response['message']);
    }

    public function test_user_can_add_comment_to_article()
    {
        $articles = Article::factory()->count(5)->create();
        $article = $articles->first();
        $data = [
            "comment" => [
              "body"=> "Comment body for this article"
            ]
        ];
        
        $this->actingAs($this->user)
                ->postJson('/api/articles/'.$article->slug.'/comments', $data)
                ->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'body' => 'Comment body for this article',
            'users_id' => $this->user->id,
            'articleSlug' => $article->slug,
        ]);

    }

    public function test_user_cannot_add_empty_comment_to_article()
    {
        $articles = Article::factory()->count(5)->create();
        $article = $articles->first();
        $data = [
            "comment" => [
              "body"=> ""
            ]
        ];
        
        $this->actingAs($this->user)
                ->postJson('/api/articles/'.$article->slug.'/comments', $data)
                ->assertStatus(422);
    }

    public function test_guest_can_get_comments_from_article()
    {
        Article::factory()->count(5)->create();
        $comments = Comment::factory()->count(5)->create();
        $comment = $comments->first();
        $this->getJson('/api/articles/'.$comment->articleSlug.'/comments')
        ->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'articleSlug' => $comment->articleSlug,
        ]);
    }

    public function test_user_can_get_comments_from_article()
    {
        Article::factory()->count(5)->create();
        $comments = Comment::factory()->count(5)->create();
        $comment = $comments->first();
        $this->actingAs($this->user)->getJson('/api/articles/'.$comment->articleSlug.'/comments')
        ->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'users_id' => $this->user->id,
            'articleSlug' => $comment->articleSlug,
        ]);
    }

    public function test_guest_cannot_delete_comment_from_article(){

        Article::factory()->count(5)->create();
        $comments = Comment::factory()->count(5)->create();

        $comment = $comments->first();

        $response = $this->deleteJson('/api/articles/' . $comment->articleSlug)
            ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response['message']);
    }

    public function test_user_can_delete_his_own_comment()
    {
        Article::factory()->count(5)->create();
        $comments = Comment::factory()->count(5)->create();

        $comment = $comments->first();

        $this->actingAs($this->user)
            ->deleteJson('/api/articles/' . $comment->articleSlug)
            ->assertStatus(200);
    }

    public function test_user_cannot_delete_other_user_comment(){

        Article::factory()->count(5)->create();
        $comments = Comment::factory()->count(5)->create();

        $nonAuthor = User::factory()->create();

        $this->actingAs($nonAuthor);
    
        $comment = $comments->first();

        $response = $this->deleteJson('/api/articles/' . $comment->articleSlug);

        $response->assertStatus(401);

    }
}

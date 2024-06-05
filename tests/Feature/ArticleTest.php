<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tags;
use App\Models\User;
use Database\Factories\ArticleFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class ArticleTest extends TestCase
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


    public function test_user_can_create_article(){
        $data = [
            "article" => [
                "title" => "How to train your dragons",
                "description" => "Ever wonder how?",
                "body" => "You have to believe",
                "tagList" => ["reactjs", "angularjs", "dragons"]
            ]
        ];
        $this->actingAs($this->user);

        $this->assertAuthenticated();

        $response = $this->post('/api/articles/',$data);

        $this->assertDatabaseHas('articles',[
            "title" => $data['article']['title'],
        ]);

        $response->assertStatus(200);
        
    }

    public function test_user_cannot_create_article_without_required_fields(){
        $data = [
            "article" => [
                "description" => "Ever wonder how?",
                "body" => "You have to believe",
                "tagList" => ["reactjs", "angularjs", "dragons"]
            ]
        ];

        $this->actingAs($this->user);

        $this->assertAuthenticated();

        $response = $this->postJson('/api/articles', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('article.title');

    }

    public function test_user_cannot_get_non_existing_article()
    {
        
        $nonExistingSlug = 'non-existing-slug';

        $this->actingAs($this->user);

        $this->assertAuthenticated();

        $response = $this->postJson('/api/articles/' . $nonExistingSlug);

        $response->assertStatus(404);
    }

    public function test_user_can_view_article()
    {
        $this->actingAs($this->user);
        ArticleFactory::new()->create();

        $this->get('/api/articles')
            ->assertStatus(200);

        $this->assertAuthenticated();
    }

    public function test_get_article_by_slug(){
        
        // $this->actingAs($this->createUser());
        $article = ArticleFactory::new()->create();
        $this->post("/api/articles/$article->slug")
            ->assertStatus(200);
    }

    public function guest_cannot_create_article(){
        $data = [
            "article" => [
                "title" => "How to train your dragons",
                "description" => "Ever wonder how?",
                "body" => "You have to believe",
                "tagList" => ["reactjs", "angularjs", "dragons"]
            ]
        ];

        $this->postJson('/api/artciles', $data)
                    ->assertStatus(403)
                    ->assertJsonStructure([
                        "message"=>"Unauthenticated.",
                    ]); 

    }

    public function test_guest_can_view_articles(){

        $this->actingAs($this->user);

        $this->assertAuthenticated();

        $articles = ArticleFactory::new()->count(5)->create();

        $response = $this->get('/api/articles');
        
        $response->assertStatus(200);
        
        $response->assertJsonCount(5, 'articles');

        foreach ($articles as $article) {
            $response->assertJsonFragment(['title' => $article->title]);
        }

    }

    public function test_user_can_view_articles_most_recent_first()
    {
        $articles = ArticleFactory::new()->count(5)->create()->sortByDesc('created_at');

        $this->actingAs($this->user);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200);

        $response->assertJsonCount(5, 'articles');

        $responseArticles = collect($response->json('articles'));

        $responseTimestamps = $responseArticles->pluck('created_at');

        $this->assertEquals($responseTimestamps->sortDesc()->values()->all(), $responseTimestamps->values()->all());

        foreach ($articles as $article) {
            $response->assertJsonFragment(['title' => $article->title]);
        }
    }

    public function test_user_can_view_articles_filtered_by_tag()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticated();

        $tags = Tags::factory()->count(3)->create();
        $articles = ArticleFactory::new()
            ->count(5)
            ->create()
            ->each(function ($article) use ($tags) {
                $article->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')->toArray()
                );
            });
        
        $filterTag = $tags->first();

        $response = $this->getJson('/api/articles?filterByTag=' . $filterTag->name);

        $response->assertStatus(200);

        $filteredArticles = Article::whereHas('tags', function ($query) use ($filterTag) {
            $query->where('name', $filterTag->name);
        })->get();

        foreach ($filteredArticles as $article) {
            $response->assertJsonFragment(['title' => $article->title]);
        }
    }

    public function test_user_can_view_articles_filtered_by_author()
    {
        Article::factory()->count(5)->create();

        $filterAuthorId = User::inRandomOrder()->first()->id;

        $this->actingAs($this->user);

        $response = $this->getJson('/api/articles?filterByAuthor=' . $filterAuthorId);

        $response->assertStatus(200);

    }

    public function test_user_can_view_articles_favorited_by_user(){
        
        $user = $this->user;

        $articles = ArticleFactory::new()->count(5)->create();

        $favoriteArticles = $articles->random(2);

        $user->favoriteArticles()->attach($favoriteArticles);

        $this->actingAs($user);

        $response = $this->getJson('/api/articles?favoritedByUser='.$user->id);

        $response->assertStatus(200);
        $this->assertAuthenticated();
    }

    public function test_user_can_view_articles_filtered_by_limit()
    {
        $this->actingAs($this->user);

        Article::factory()->count(45)->create();

        $response = $this->getJson('/api/articles?limit=30');
  
        $response->assertStatus(200);
          
        $this->assertCount(30, $response->json('articles'));

        $this->assertAuthenticated();

    }

    public function test_user_can_view_articles_filtered_by_offset()
    {
        $this->actingAs($this->user);

        Article::factory()->count(45)->create();

        $response = $this->getJson('/api/articles?limit=30&offset=30');
  
        $response->assertStatus(200);
          
        $this->assertCount(15, $response->json('articles'));

        $this->assertAuthenticated();
    }

    public function test_guest_cannot_update_article()
    {
        $article = Article::factory()->createOne();
        $data = [
                "article" => [
                  "title"=> "Did you train your dragon2?"
                ]
            ];
       $response = $this->putJson('/api/articles/'.$article->slug, $data)
            ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response['message']);
    }

    public function test_user_can_update_article_he_authored()
    {
        $articles = Article::factory()->count(5)->create();

        $this->actingAs($this->user);

        $article = $articles->first();
        $article->users_id = $this->user->id;
        $article->save();

        $data = [
            "article" => [
                "title" => "Did you train your dragon2?"
            ]
        ];

        $response = $this->putJson('/api/articles/' . $article->slug, $data);

        $response->assertStatus(200);

        $response->assertJson([
            "article" => [
                "title" => "Did you train your dragon2?"
            ]
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'users_id' => $this->user->id,
            'title' => 'Did you train your dragon2?'
        ]);
    }

    public function test_user_cannot_update_article_of_other_authors()
    {
        $articles = Article::factory()->count(5)->create();
    
        $nonAuthor = User::factory()->create();
        $this->actingAs($nonAuthor);
    
        $article = $articles->first();
    
        $data = [
            "article" => [
                "title" => "Did you train your dragon2?"
            ]
        ];
    
        $response = $this->putJson('/api/articles/' . $article->slug, $data);
    
        $response->assertStatus(401);
    }

    public function test_guest_cannot_delete_article()
    {
        $articles = Article::factory()->count(5)->create();

        $article = $articles->first();

        $response = $this->deleteJson('/api/articles/' . $article->slug);

        $response->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $response['message']);
    }

    public function test_user_can_delete_article()
    {
        $articles = Article::factory()->count(5)->create();

        $user = User::factory()->create();
        $this->actingAs($user);

        $article = $articles->first();
        $article->users_id = $user->id;
        $article->save();

        $response = $this->deleteJson('/api/articles/' . $article->slug);

        $response->assertStatus(200);

        $this->assertSoftDeleted('articles', [
            'id' => $article->id,
        ]);
        $this->assertEquals('Article Deleted Successfully!', $response['message']);
    }

    public function test_user_cannot_delete_articles_of_other_authors()
    {
        $articles = Article::factory()->count(5)->create();

        $nonAuthor = User::factory()->create();

        $this->actingAs($nonAuthor);
    
        $article = $articles->first();

        $response = $this->deleteJson('/api/articles/' . $article->slug);

        $response->assertStatus(401);

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

}

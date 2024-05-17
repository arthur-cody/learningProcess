<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleService extends ServiceProvider
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


    public function createArticle(array $data)
    {
        return Article::create([
            'title' => $data['article']['title'],
            'users_id' => Auth::user()->id,
            'slug' => Str::slug($data['article']['title']),
            'description' => $data['article']['description'],
            'body' => $data['article']['body'],
            'tagList' => $data['article']['tagList']
        ]);
        
        // Logic to create an article
    }

    public function updateArticle(Article $article, array $data)
    {
        $article->update($data);
        return $article;
        
    }

    public function deleteArticle(Article $article) : void 
    {
         $article->delete();
    }

    public function getArticle(string $slug)
    {
        // Logic to get an article by ID
    }

    public function getAllArticles()
    {
        return Article::all();
    }
}

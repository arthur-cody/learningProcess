<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\ArticleFavorite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class ArticleFavoriteService extends ServiceProvider
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    
    /**
     * Register services.
     */
    public function createFavorite($data)
    {
        $article = Article::where('slug', $data['slug'])->first();
        $existingFavorite = ArticleFavorite::where('user_id', auth()->id())
        ->where('article_id', $article->id)->where('deleted_at', NULL)
        ->first();

        if ($existingFavorite) {
            return null;
        }
        $favorite =  ArticleFavorite::create([
            'user_id' => auth()->id(),
            'article_id' => $data['id'],
        ]);

        return $favorite ? Article::where('id', $article->id)->get() : null;
    }


    public function unFavorite($data)
    {
        $existingFavorite = ArticleFavorite::where('user_id', auth()->id())
        ->where('article_id', $data['id'])
        ->first();

        if ($existingFavorite) {
            $deleted = $existingFavorite->delete();
            return $deleted ? Article::where('slug', $data['slug'])->get() : null;
        }

        return null;
    }
}
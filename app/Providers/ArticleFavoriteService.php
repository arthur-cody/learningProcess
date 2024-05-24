<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\ArticleFavorite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

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
        $existingFavorite = ArticleFavorite::where('user_id', auth()->id())
        ->where('article_slug', $data['slug'])->where('deleted_at', NULL)
        ->first();

        if ($existingFavorite) {
            return null;
        }
        $favorite =  ArticleFavorite::create([
            'user_id' => auth()->id(),
            'article_id' => $data['id'],
            'article_slug' => $data['slug']
        ]);

        return $favorite ? Article::where('slug', $favorite->article_slug)->get() : null;
    }


    public function unFavorite($data)
    {
        $existingFavorite = ArticleFavorite::where('user_id', auth()->id())
        ->where('article_slug', $data['slug'])
        ->first();

        if ($existingFavorite) {
            $deleted = $existingFavorite->delete();
            return $deleted ? Article::where('slug', $data['slug'])->get() : null;
        }

        return null;
    }
}
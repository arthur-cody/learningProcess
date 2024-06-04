<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Tags;
use Illuminate\Database\Eloquent\Collection;
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
        $articleCreated = Article::create([
            'title' => $data['article']['title'],
            'users_id' => Auth::user()->id,
            'slug' => Str::slug($data['article']['title']),
            'description' => $data['article']['description'],
            'body' => $data['article']['body'],
            
        ]);
        
        foreach ($data['article']['tagList'] as $tagName) {
            $existingTag = Tags::where('name', $tagName)->first();

            if ($existingTag) {
                // If tag exists, attach it to the article
                $articleCreated->tags()->attach($existingTag->id);
            } else {
                // If tag does not exist, create and attach it to the article
                $newTag = Tags::create([
                    'name' => $tagName,
                    'slug' => Str::slug($tagName),
                ]);

                $articleCreated->tags()->attach($newTag->id);
            }
        }
        return $articleCreated;
        
        // Logic to create an article
    }

    public function updateArticle(Article $article, array $data)
    {
        $updateArticle = $article->where('slug',$article->slug)
        ->where('users_id', Auth::user()->id)
        ->update([
            'title' => $data['article']['title'],
            'slug' => Str::slug($data['article']['title'])
        ]);
        return $updateArticle ? Article::where('slug',Str::slug($data['article']['title']))->first() : null;
    }

    public function deleteArticle(Article $article) 
    {
       $deleted = Article::where('users_id', Auth::user()->id)->where('slug', $article->slug)->delete();
       if($deleted)
       {
        return response()->json([
            'message' => "Article Deleted Successfully!"
        ]);
       }else{
        return response()->json([
            'message' => "Unauthorized to delete this article"
        ],401);
       }
    }

    public function getArticle(string $slug)
    {
        // Logic to get an article by ID
    }

    public function getAllArticles($request): Collection
    {
        return  Article::with(['author'])
                    ->when($request->filterByAuthor, function ($query) use ($request) {
                        $query->whereHas('author', function ($authorQuery) use ($request) {
                            $authorQuery->where('id', $request->filterByAuthor ?? null);
                        });
                    })->when($request->favoritedByUser, function ($query) use ($request) {
                        $query->whereHas('authorFavorited', function ($authorFavoritedQuery) use ($request) {
                            $authorFavoritedQuery->where('user_id', $request->favoritedByUser ?? null);
                        });
                    })->when($request->filterByTag, function ($query) use ($request) {
                        $query->whereHas('tags', function ($tagQuery) use ($request) {
                            $tagQuery->where('name', $request->filterByTag ?? null);
                        });
                    })->when($request->limit, function ($query) use ($request) {
                        $query->limit($request->limit ?? 15);
                    })->when($request->offset, function ($query) use ($request) {
                        $query->offset($request->offset ?? 15);
                    })
                    ->get();
    }
}

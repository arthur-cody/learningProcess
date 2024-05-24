<?php

namespace App\Providers;

use App\Models\{
    Comment,
    Article
};
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class CommentService extends ServiceProvider
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function createComment(array $data)
    {
        return Comment::create([
            'users_id' => Auth::user()->id,
            'articleSlug' => $data['article']['slug'],
            'body' => $data['comment']['body'],
        ]);
    }

    public function getAllArticles(Article $article)
    {
        return $article->comment()->get();
    }

    public function deleteComment(Article $article, $commentId) 
    {
        $deleted = Comment::where('article_slug', $article->slug)
        ->where('id', $commentId)
        ->delete();


        if ($deleted) {
            return response()->json(['message' => 'Comment deleted successfully.'], 200);
        } else {
            return response()->json(['error' => 'Comment not found or does not belong to the specified article.'], 404);
        }
         $article->delete();
    }

    public function getCommentFromArtcle(string $slug){

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

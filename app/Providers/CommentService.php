<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
            'article_id' => $data['article']['id'],
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
        $deleted = Comment::where('article_id', $article->id)
            ->where('id', $commentId)
            ->where('users_id', Auth::user()->id)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Comment deleted successfully.'], 200);
        } else {
            return response()->json(['error' => 'Something wrong'], 404);
        }
        $article->delete();
    }

    public function getCommentFromArtcle(string $slug)
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

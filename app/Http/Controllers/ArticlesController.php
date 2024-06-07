<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListArticleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Providers\ArticleService;
use Illuminate\Http\JsonResponse;

class ArticlesController extends Controller
{
    //
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(ListArticleRequest $request): JsonResponse
    {
        try {

            $articles = $this->articleService->getAllArticles($request);

            return response()->json([
                'articles' => ArticleResource::collection($articles),
                'articlesCount' => count($articles),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch data', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        try {
            $articles = $this->articleService->updateArticle($article, $request->validated());

            return response()->json([
                'article' => ArticleResource::titleOnly($articles),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch data', 'message' => $e->getMessage()], 401);
        }
    }

    public function show(Article $article): JsonResponse
    {
        try {
            return response()->json([
                'article' => new ArticleResource($article),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch data', 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Article $article): JsonResponse
    {
        try {
            return $this->articleService->deleteArticle($article);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch data', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        try {
            $createdArticle = $this->articleService->createArticle($request->validated());

            return response()->json([
                'articles' => new ArticleResource($createdArticle),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch data', 'message' => $e->getMessage()], 500);
        }
    }
}

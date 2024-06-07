<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Providers\ArticleFavoriteService;
use Illuminate\Http\Request;

class ArticleFavoriteController extends Controller
{
    protected $articleFavoriteService;

    public function __construct(ArticleFavoriteService $articleFavoriteService)
    {
        $this->articleFavoriteService = $articleFavoriteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Article $article)
    {
        try {
            $favoriteCreated = $this->articleFavoriteService->createFavorite($article);

            if ($favoriteCreated != null) {
                return response()->json([
                    'articles' => ArticleResource::collection($favoriteCreated),
                ], 201);
            } else {
                return response()->json([
                    'message' => 'You already follow this account!',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to process request', 'message' => $e->getMessage()], 500);
        }

        return $article;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        try {

            $unFavorite = $this->articleFavoriteService->unFavorite($article);

            if ($unFavorite != null) {
                return response()->json([
                    'articles' => ArticleResource::collection($unFavorite),
                ], 201);
            } else {
                return response()->json([
                    'message' => 'No credentials found!',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to process request', 'message' => $e->getMessage()], 500);
        }
    }
}

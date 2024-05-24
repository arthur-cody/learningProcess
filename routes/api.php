<?php

use App\Http\Controllers\ArticleFavoriteController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\ArticleTagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {

    Route::get('/tags', [TagsController::class,'index']);

    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticlesController::class, 'index']);
        Route::post('/', [ArticlesController::class, 'store']);

        // Article Slug
        Route::prefix("{article:slug}")
            ->group(function(){
                Route::post('/',[ArticlesController::class,'show']);
                Route::put('/', [ArticlesController::class, 'update']);
                Route::delete('/', [ArticlesController::class, 'delete']);

                // Comments Route
                    Route::prefix('comments')->group(function(){
                        Route::post('/', [CommentController::class,'store']);
                        Route::get('/', [CommentController::class,'index']);
                        Route::delete('/{id}', [CommentController::class,'destroy']);
                });

                Route::prefix('favorite')->group(function(){
                    Route::post('/', [ArticleFavoriteController::class,'store']);
                    Route::delete('/', [ArticleFavoriteController::class,'destroy']);
                });
            });
    });

    Route::prefix("{user}")->group(function(){
        Route::get('/', [UserController::class, 'index']);
        Route::put('/', [UserController::class, 'update']);
    });

    Route::prefix('profiles')->group(function(){
        Route::get('/{username}', [UserController::class, 'show']);
        Route::post('/{username}/follow', [FollowerController::class, 'store']);
        Route::delete('/{username}/follow', [FollowerController::class, 'destroy']);
    });
});
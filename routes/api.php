<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticlesController::class, 'index']);
        Route::post('/', [ArticlesController::class, 'store']);

        Route::prefix("{article:slug}")
            ->group(function(){
                Route::post('/',[ArticlesController::class,'show']);
                Route::middleware(['auth:sanctum'])
                    ->group(function(){
                        Route::put('/', [ArticlesController::class, 'update']);
                        Route::delete('/', [ArticlesController::class, 'delete']);
                        Route::prefix('comments')->group(function(){
                            Route::post('/', [CommentController::class,'store']);
                        });
                    });
            });
    });
});
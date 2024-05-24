<?php

namespace App\Providers;

use App\Models\ArticleTag;
use App\Models\Tags;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class ArticleTagService extends ServiceProvider
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    /**
     * Register services.
     */
    public function getArticlesTag()
    {
        return Tags::get();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

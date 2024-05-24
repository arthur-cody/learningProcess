<?php

namespace App\Providers;

use App\Models\Tags;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TagService extends ServiceProvider
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    /**
     * Register services.
     */
    public function getTags()
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

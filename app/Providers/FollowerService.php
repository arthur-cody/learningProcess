<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FollowerService extends ServiceProvider
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * Register services.
     */
    public function createFollower($data)
    {
        $user = auth()->user();
        if ($user->follow($data)) {
            return $data;
        }

        return null;

    }

    public function unfollowUser($data)
    {

        $user = auth()->user();
        if ($user->unfollow($data)) {
            return $data;
        }

        return null;
    }
}

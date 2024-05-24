<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService extends ServiceProvider
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function updateUserInfo($data)
    {
        $userId = Auth::user()->id;
        $updatedUser = User::where('id', $userId)
                    ->update([
                        'email' => $data['user']['email'],
                        'bio' => $data['user']['bio'],
                        'image' => $data['user']['image']
                    ]);
    return $updatedUser ? Auth::user() : null;
    }

    public function getUserInfo()
    {
        return Auth::user();
    }

    public function getUserProfile($username)
    {
        return User::where('name', $username)->first();

    }
}

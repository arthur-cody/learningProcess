<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
    use RefreshDatabase;

    /**
     * Create and return a new user.
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }
}

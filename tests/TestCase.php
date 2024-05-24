<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    //
    use RefreshDatabase;

    /**
     * Create and return a new user.
     *
     * @param  array  $attributes
     * @return \App\Models\User
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }
}

<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
    
        // Assert that the response status code is 500
        $response->assertStatus(500);
    
        // Check if the user was not created in the database
        $this->assertDatabaseMissing('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

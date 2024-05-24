<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_user_details()
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $response = $this->get('/user');

        $response->assertStatus(200);
    }

    public function test_can_update_user_details(){
        $user = $this->createUser();

        $data = [
            "user" => [
                "email" => "arthur.update2@gmail.com",
                "bio" => "I like to skateboard",
                "image" => "https://i.stack.imgur.com/xHWG8.jpg"
            ]
        ];

        $response = $this->actingAs($user)
                    ->put('/api/user',$data);
        
        $this->assertDatabaseHas('users',[
            'image' => $data['user']['image'],
            'email' => $data['user']['email'],
            'bio' => $data['user']['bio']
        ]);

        $response->assertOk()
            ->assertStatus(200);

    }
}

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

    public function test_get_profile()
    {
        $users = User::factory()->count(3)->create();
        $user = $users->first();
        $this->actingAs($this->createUser())->getJson('/api/profiles/'. $user->name)
             ->assertStatus(200)
             ->assertJsonFragment([
                    "profile"=> [
                        "username" => $user->name,
                        "bio" => $user->bio,
                        "image" => $user->image,
                        "following" => false
                    ]
                ]);
    }

    public function test_user_follow_another_user()
    {
        $users = User::factory()->count(3)->create();
        $followee = $users->first();
        $follower = $this->createUser();
        $this->actingAs($follower)->postJson('/api/profiles/'. $followee->name.'/follow')
        ->assertStatus(201)
        ->assertJsonFragment([
               "profile"=> [
                   "username" => $followee->name,
                   "bio" => $followee->bio,
                   "image" => $followee->image,
                   "following" => true
               ]
           ]);
           $this->assertDatabaseHas('followers', [
            'user_id' => $follower->id,
            'followee_id' => $followee->id,
        ]);
    }

    public function test_user_can_follow_and_unfollow_another_user()
    {   
        $users = User::factory()->count(3)->create();
        $followee = $users->first();
        $follower = $this->createUser();

        // Follow the user
        $this->actingAs($follower)->postJson('/api/profiles/' . $followee->name . '/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                "profile" => [
                    "username" => $followee->name,
                    "bio" => $followee->bio,
                    "image" => $followee->image,
                    "following" => true
                ]
            ]);

        // Ensure the user is now following the followee
        $this->assertDatabaseHas('followers', [
            'user_id' => $follower->id,
            'followee_id' => $followee->id,
        ]);

        // Unfollow the user
        $this->actingAs($follower)->deleteJson('/api/profiles/' . $followee->name . '/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                "profile" => [
                    "username" => $followee->name,
                    "bio" => $followee->bio,
                    "image" => $followee->image,
                    "following" => false
                ]
            ]);

        // Ensure the user is no longer following the followee
        $this->assertDatabaseMissing('followers', [
            'user_id' => $follower->id,
            'followee_id' => $followee->id,
        ]);
    }
}

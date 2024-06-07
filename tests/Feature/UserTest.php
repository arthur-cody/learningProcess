<?php

namespace Tests\Feature;

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

    public function test_can_update_user_details()
    {
        $user = $this->createUser();

        $data = [
            'user' => [
                'email' => 'arthur.update2@gmail.com',
                'bio' => 'I like to skateboard',
                'image' => 'https://i.stack.imgur.com/xHWG8.jpg',
            ],
        ];

        $response = $this->actingAs($user)
            ->put('/api/user', $data);

        $this->assertDatabaseHas('users', [
            'image' => $data['user']['image'],
            'email' => $data['user']['email'],
            'bio' => $data['user']['bio'],
        ]);

        $response->assertOk()
            ->assertStatus(200);

    }

    public function test_get_profile()
    {
        $users = User::factory()->count(3)->create();
        $user = $users->first();
        $this->getJson('/api/profiles/'.$user->name)
            ->assertStatus(200)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $user->name,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ],
            ]);
    }

    public function test_user_follow_another_user()
    {
        $users = User::factory()->count(3)->create();
        $followee = $users->first();
        $follower = $this->createUser();
        $this->actingAs($follower)->postJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $followee->name,
                    'bio' => $followee->bio,
                    'image' => $followee->image,
                    'following' => true,
                ],
            ]);
        $this->assertDatabaseHas('followers', [
            'user_id' => $follower->id,
            'followee_id' => $followee->id,
        ]);
    }

    public function test_guest_cannot_follow_and_unfollow_user()
    {
        $users = User::factory()->count(3)->create();
        $followee = $users->first();

        // Follow the user
        $responseFollow = $this->postJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $responseFollow['message']);

        // Unfollow the user
        $responseUnfollow = $this->deleteJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(401);
        $this->assertEquals('Unauthenticated.', $responseUnfollow['message']);
    }

    public function test_user_can_follow_and_unfollow_another_user()
    {
        $users = User::factory()->count(3)->create();
        $followee = $users->first();
        $follower = $this->createUser();

        // Follow the user
        $this->actingAs($follower)->postJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $followee->name,
                    'bio' => $followee->bio,
                    'image' => $followee->image,
                    'following' => true,
                ],
            ]);

        // Ensure the user is now following the followee
        $this->assertDatabaseHas('followers', [
            'user_id' => $follower->id,
            'followee_id' => $followee->id,
        ]);

        // Unfollow the user
        $this->actingAs($follower)->deleteJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $followee->name,
                    'bio' => $followee->bio,
                    'image' => $followee->image,
                    'following' => false,
                ],
            ]);

        // Ensure the user is no longer following the followee
        $this->assertDatabaseMissing('followers', [
            'user_id' => $follower->id,
            'followee_id' => $followee->id,
        ]);
    }

    public function test_guest_can_get_current_user()
    {
        $users = User::factory()->count(3)->create();
        $user = $users->first();
        $this->getJson('/api/profiles/'.$user->name)
            ->assertStatus(200)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $user->name,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ],
            ]);
    }

    public function test_user_cannot_follow_already_follow_user()
    {
        $users = User::factory()->count(3)->create();
        $followee = $users->first();
        $follower = $this->createUser();

        // First follow action
        $this->actingAs($follower)->postJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $followee->name,
                    'bio' => $followee->bio,
                    'image' => $followee->image,
                    'following' => true,
                ],
            ]);

        // Attempt to follow the same user again
        $this->actingAs($follower)->postJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201) // or appropriate status code
            ->assertJsonFragment([
                'message' => 'You already follow this account!',
            ]);

        // Ensure only one follow relationship exists in the database
        $this->assertDatabaseCount('followers', 1);
    }

    public function test_user_cannot_unfollow_already_unfollowed_user()
    {
        $users = User::factory()->count(3)->create();
        $followee = $users->first();
        $follower = $this->createUser();

        // First follow action
        $this->actingAs($follower)->postJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $followee->name,
                    'bio' => $followee->bio,
                    'image' => $followee->image,
                    'following' => true,
                ],
            ]);

        // First unfollow action
        $this->actingAs($follower)->deleteJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201)
            ->assertJsonFragment([
                'profile' => [
                    'username' => $followee->name,
                    'bio' => $followee->bio,
                    'image' => $followee->image,
                    'following' => false,
                ],
            ]);

        // Attempt to unfollow the same user again
        $this->actingAs($follower)->deleteJson('/api/profiles/'.$followee->name.'/follow')
            ->assertStatus(201) // or appropriate status code
            ->assertJsonFragment([
                'message' => 'No credentials found!',
            ]);

        // Ensure no follow relationship exists in the database
        $this->assertDatabaseMissing('followers', [
            'user_id' => $follower->id,
            'followee_id' => $followee->id,
        ]);
    }
}

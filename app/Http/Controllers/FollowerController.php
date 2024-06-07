<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFollowerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Providers\FollowerService;

class FollowerController extends Controller
{
    public function __construct(protected FollowerService $followerService)
    {
        $this->followerService = $followerService;
    }

    public function store(StoreFollowerRequest $request)
    {
        try {

            $validatedData = $request->validated();
            $user = auth()->user();
            $followee = User::findOrFail($validatedData['followee_id']);

            if ($user->id === $followee->id) {
                return response()->json(['message' => 'You cannot follow yourself'], 400);
            }

            $followCreated = $this->followerService->createFollower($followee);
            if ($followCreated != null) {
                return response()->json([
                    'profile' => new UserResource($followCreated),
                ], 201);
            } else {
                return response()->json([
                    'message' => 'You already follow this account!',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to process request', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($username)
    {
        try {

            $user = auth()->user();
            $followee = User::where('name', $username)->firstOrFail();

            if ($user->id === $followee->id) {
                return response()->json(['message' => 'You cannot unfollow yourself'], 400);
            }

            $followCreated = $this->followerService->unfollowUser($followee);
            if ($followCreated != null) {
                return response()->json([
                    'profile' => new UserResource($followCreated),
                ], 201);
            } else {
                return response()->json([
                    'message' => 'No credentials found!',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to process request', 'message' => $e->getMessage()], 500);
        }
        //
    }
}

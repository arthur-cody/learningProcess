<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'username' => $this->name,
            'bio' => $this->bio,
            "image" => $this->image,
            'following' => $this->isFollowedByAuthUser()
        ];
    }

    public static function getUser($request)
    {
        return [
            'email' => $request->email,
            'bio' => $request->bio,
            "username" => $request->name,
            "token" => $request->token,
            "image" => $request->image ? $request->image : null
        ];
    }
}

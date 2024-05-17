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
            "following" => $this->following
        ];
    }
}

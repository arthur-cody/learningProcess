<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string,mixed>
     */
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => UserResource::make($this->author),
            'description' => $this->description,
            'body' => $this->body,
            'tagList' => TagResource::getArray($this->tags),
            'favorited' => $this->authorFavorited()->where('user_id', auth()->id())->exists(),
            'favoritesCount' => $this->authorFavorited()->count(),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }

    public static function titleOnly($res)
    {
        return [
            'title' => $res['title'],
        ];
    }
}

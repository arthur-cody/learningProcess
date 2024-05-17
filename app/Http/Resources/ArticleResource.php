<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
        /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string,mixed>
     */
    public function toArray($request):array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => UserResource::make($this->users),
            'description' => $this->description,
            'body' => $this->body,
            'tagList' => json_decode($this->tagList, true),
            'favorited' => $this->favorited,    
            'favoritesCount' => $this->favoritesCount,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}

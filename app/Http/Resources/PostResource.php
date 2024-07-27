<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'likes_count' => $this->likes->count()
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $name = isset($this->username) ? $this->username : (isset($this->full_name) ? $this->full_name : $this->name);
        return [
            'id' => $this->id,
            'name' => $name,
            'email' => $this->email,
            'photo' => $this->profile_photo ? asset('storage/' . $this->photo) : null,
            'posts' => PostResource::collection($this->whenLoaded('posts', $this->posts))
        ];
    }
    }


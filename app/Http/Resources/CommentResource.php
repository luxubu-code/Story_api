<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CommentResource extends JsonResource
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
            'content' => $this->content,
            'story_id' => $this->story_id,
            'like' => $this->likes,
            'parent_id' => $this->parent_id,
            'created_at' => Carbon::parse($this->create_at)->format('Y-m-d'),
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar_url' => $this->user->avatar_url,
            ],
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
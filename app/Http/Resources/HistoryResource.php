<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->story_id,
            'user_id' => $this->user_id,
            'chapter_id' => $this->chapter_id,
            'title'=> $this->story->title,
            'read_at' => Carbon::parse($this->read_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'image_path' => $this->base_url . $this->file_name,

        ];
    }
}

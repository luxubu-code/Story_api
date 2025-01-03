<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        try {
            $story = $this->story;
            return [
                'id' => $this->story_id,
                'user_id' => $this->user_id,
                'chapter_id' => $this->chapter_id,
                'title' => $story ? $story->title : null,
                'read_at' => $this->read_at ? Carbon::parse($this->read_at)->format('Y-m-d') : null,
                'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('Y-m-d') : null,
                'image_path' => $story ? ($story->base_url . $story->file_name) : null,
                'categories' => $story && $story->categories ? $story->categories->map(
                    function ($category) {
                        return ['title' => $category->title];
                    }
                ) : [],
                'chapter' => $story->chapters->map(
                    function ($chapter) {
                        return [
                            'id' => $chapter->chapter_id,
                            'title' => $chapter->title,
                            'views' => $chapter->views ?? 0,
                            'created_at' => $chapter->created_at
                        ];
                    }
                ),
            ];
        } catch (\Exception $e) {
            Log::error('HistoryResource Error: ' . $e->getMessage());
            Log::error('Resource Data: ' . json_encode($this->resource));
            return [];
        }
    }
}

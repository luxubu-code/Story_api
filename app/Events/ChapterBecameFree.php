<?php

namespace App\Events;

use App\Models\Chapter;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChapterBecameFree
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chapter;
    public function __construct(Chapter $chapter)
    {
        $this->chapter = $chapter;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chapter.' . $this->chapter->id),
        ];
    }
    public function broadcastWith(): array
    {
        return [
            'id' => $this->chapter->chapter_id,
            'views' => $this->chapter->views,
            'is_vip' => $this->chapter->is_vip,
        ];
    }
}
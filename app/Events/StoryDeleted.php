<?php

namespace App\Events;

use App\Story;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class StoryDeleted implements ShouldBroadcast
{
    use SerializesModels;

    public $story;

    public function __construct(Story $story)
    {
        $this->story = $story;
    }

    public function broadcastOn()
    {
        return [
            new Channel('board.'.$this->story->sprint_id),
        ];
    }

    public function broadcastAs()
    {
        return 'story.deleted';
    }
}

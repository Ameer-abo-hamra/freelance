<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public $commentID, $whoInteracted;
    public function __construct($commentID, $whoInteracted)
    {
        $this->commentID = $commentID;
        $this->whoInteracted = $whoInteracted;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('Comments' . $this->commentID),
        ];
    }

    public function broadcatWith()
    {

        return [
            "commentID" => $this->commentID,
            "whoInteracted" => $this->whoInteracted,
        ];
    }
}

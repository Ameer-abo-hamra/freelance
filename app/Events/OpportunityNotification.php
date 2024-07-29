<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OpportunityNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $opportunityOwnerID, $position, $opportunityOwnerName;
    public function __construct($opportunityOwner, $position, $opportunityOwnerName)
    {
        $this->opportunityOwner = $opportunityOwner;
        $this->position = $position;
        $this->opportunityOwnerName = $opportunityOwnerName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('Opportunities' . $this->opportunityOwnerID),
        ];
    }
    public function broadcatWith()
    {

        return [
            "opportunityOwnerID" => $this->opportunityOwnerID,
            "position" => $this->position,
            "opportunityOwnerName" => $this->opportunityOwnerName,
        ];
    }
}

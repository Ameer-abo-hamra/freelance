<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RespondApplicants implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $companyName, $isAccepted, $message;

    public function __construct($companyName, $isAccepted, $message)
    {
        $this->companyName = $companyName;
        $this->message = $message;
        $this->isAccepted = $isAccepted;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('Respond' . $this->companyName),
        ];
    }

    public function broadcastWith()
    {
        return [
            "companyName" => $this->companyName,
            "isAccepted" => $this->isAccepted,
            "message" => $this->message
        ];

    }
}

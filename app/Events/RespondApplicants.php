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
    public $job_seeker_id, $isAccepted, $message,$a;

    public function __construct($job_seeker_id, $isAccepted, $message)
    {
        $this->job_seeker_id = $job_seeker_id;
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
            new Channel('Respond' . $this->job_seeker_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            "job_seeker_id" => $this->job_seeker_id,
            "isAccepted" => $this->isAccepted,
            "message" => $this->message
        ];

    }
}

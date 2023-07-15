<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUserJoinedPresence implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $action ;
    public $user;
    /**
     * Create a new event instance.
     */
    public function __construct($action,$user)
    {
        $this->user = $user;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return [
            new PresenceChannel('presence-user')
        ];
    }

    public function broadcastAs(): string
    {
        return 'presenceAction';
    }

    public function broadcastWith()
    {
        return [
            'action' => $this->action,
            'user' => $this->user
        ];
    }
}

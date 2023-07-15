<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $user;
    public $conversation;
    /**
     * Create a new event instance.
     */
    public function __construct($message,$conversation,$user)
    {
        $this->message = $message;
        $this->conversation = $conversation;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn() : PrivateChannel
    {
        return new PrivateChannel('user-'.$this->user->id);
    }

    public function broadcastAs(): string
    {
        return 'newMessage';
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'conversation' => $this->conversation,
            'message' => $this->message
        ];
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomChatBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $last_updated;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($last_updated)
    {
        $this->last_updated     = $last_updated;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('last_updated_chat');
    }
    public function broadcastWith()
    {
        return [
            'last_updated' => $this->last_updated,
        ];
    }
    public function broadcastAs()
    {
        return 'last_updated';
    }

}

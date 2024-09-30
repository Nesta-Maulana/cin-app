<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatRoomDetailBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $last_updated, $chat_room_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chat_room_id, $last_updated)
    {
        $this->chat_room_id = $chat_room_id;
        $this->last_updated = $last_updated;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("updated_chat");
    }
    public function broadcastWith()
    {
        return [
            'last_updated' => $this->last_updated,
            'chat_room_id' => $this->chat_room_id,
        ];
    }
    public function broadcastAs()
    {
        return 'updated_room_chat';
    }

}

<?php

namespace App\Repositories\Transaction\ChatRoomDetail;

use App\Models\ChatRoomDetail;
use App\Repositories\BaseRepository;
use Illuminate\Support\Carbon;

class ChatRoomDetailRepository extends BaseRepository implements ChatRoomDetailRepositoryInterface
{
    protected $model;

    public function __construct(ChatRoomDetail $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
    public function storeMessage($chat_room, $message)
    {
        $check_message = $this->model->byMessageId($message['id'])->first();
        if (is_null($check_message)) {
            $chat_room_detail_data = [
                'message_id' => $message['id']['_serialized'],
                'type' => $message['type'],
                'chat_room_id' => $chat_room->id,
                'is_from_me' => $message['id']['fromMe'],
                'message_time' => Carbon::createFromTimestamp($message['t'])
            ];
            $chat_room_detail_data['message'] = ($message['type'] == 'chat') ? $message['body'] : ($message['caption'] ?? null);
            if (in_array($message['type'], ['image', 'video', 'document'])) {
                $chat_room_detail_data['mimetype'] = $message['mimetype'];
            }
            $this->create($chat_room_detail_data);
        }
    }
}

<?php

namespace App\Repositories\Transaction\ChatRoomDetail;

use App\Events\RoomChatBroadcast;
use App\Models\ChatRoomDetail;
use App\Events\ChatRoomDetailBroadcast;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

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
        try {
            $check_message = $this->model->byMessageId($message['id'])->first();
            if (is_null($check_message)) {
                if (in_array($message['type'], ['chat', 'image', 'video', 'document'])) {
                    $chat_room_detail_data = [
                        'message_id' => $message['id'],
                        'type' => $message['type'],
                        'chat_room_id' => $chat_room->id,
                        'is_from_me' => $message['fromMe'],
                        'message_time' => Carbon::createFromTimestamp($message['t'])
                    ];
                    $chat_room_detail_data['message'] = ($message['type'] == 'chat') ? $message['body'] : ($message['caption'] ?? null);
                    if (in_array($message['type'], ['image', 'video', 'document'])) {
                        $chat_room_detail_data['mimetype'] = $message['mimetype'];
                    }
                    $this->create($chat_room_detail_data);
                }
            }
            $date = Carbon::parse($message['t']);

            // Convert to a specific timezone (e.g., Asia/Jakarta)
            $date->setTimezone('Asia/Jakarta');

            // Format the date to the desired format
            $formattedDate = $date->format('Y-m-d H:i:s');

            RoomChatBroadcast::dispatch($formattedDate);
            ChatRoomDetailBroadcast::dispatch($chat_room->id, $formattedDate);
        } catch (Exception $th) {
            Log($message);
            Log($th->getMessage());
        }
    }
}

<?php

namespace App\Repositories\Transaction\ChatRoomDetail;
use App\Repositories\BaseRepositoryInterface;

interface ChatRoomDetailRepositoryInterface extends BaseRepositoryInterface
{
    public function storeMessage($chat_room, $message);
}

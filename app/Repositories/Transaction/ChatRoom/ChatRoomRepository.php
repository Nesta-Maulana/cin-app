<?php

namespace App\Repositories\Transaction\ChatRoom;

use App\Models\ChatRoom;
use App\Repositories\BaseRepository;

class ChatRoomRepository extends BaseRepository implements ChatRoomRepositoryInterface
{
    protected $model;

    public function __construct(ChatRoom $model)
    {
        parent::__construct($model);
    }

}

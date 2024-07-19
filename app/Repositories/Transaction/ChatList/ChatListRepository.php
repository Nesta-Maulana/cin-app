<?php

namespace App\Repositories\Transaction\ChatList;

use App\Models\ChatList;
use App\Repositories\BaseRepository;

class ChatListRepository extends BaseRepository implements ChatListRepositoryInterface
{
    protected $model;

    public function __construct(ChatList $model)
    {
        parent::__construct($model);
    }

}

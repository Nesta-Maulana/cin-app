<?php

namespace App\Repositories\Master\BotService;

use App\Models\BotService;
use App\Repositories\BaseRepository;

class BotServiceRepository extends BaseRepository implements BotServiceRepositoryInterface
{
    protected $model;

    public function __construct(BotService $model)
    {
        parent::__construct($model);
    }

}

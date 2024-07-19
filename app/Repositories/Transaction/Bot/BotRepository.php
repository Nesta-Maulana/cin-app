<?php

namespace App\Repositories\Transaction\Bot;

use App\Models\Bot;
use App\Repositories\BaseRepository;

class BotRepository extends BaseRepository implements BotRepositoryInterface
{
    protected $model;

    public function __construct(Bot $model)
    {
        parent::__construct($model);
    }

}

<?php

namespace App\Repositories\Master\Unit;

use App\Models\Unit;
use App\Repositories\BaseRepository;
use App\Repositories\Master\Unit\UnitRepositoryInterface;

class UnitRepository extends BaseRepository implements UnitRepositoryInterface
{
    protected $model;

    public function __construct(Unit $model)
    {
        parent::__construct($model);
    }

}

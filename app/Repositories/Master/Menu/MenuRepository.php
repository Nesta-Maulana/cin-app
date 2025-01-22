<?php

namespace App\Repositories\Master\Menu;

use App\Models\Menu;
use App\Repositories\BaseRepository;

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    protected $model;

    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }

}

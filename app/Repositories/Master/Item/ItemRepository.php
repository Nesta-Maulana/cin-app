<?php
namespace App\Repositories\Master\Item;

use App\Models\Item;
use App\Repositories\BaseRepository;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    protected $model;

    public function __construct(Item $model)
    {
        parent::__construct($model);
    }

}

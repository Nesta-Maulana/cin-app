<?php
namespace App\Repositories\Master\ItemType;

use App\Models\ItemType;
use App\Repositories\BaseRepository;

class ItemTypeRepository extends BaseRepository implements ItemTypeRepositoryInterface
{
    protected $model;

    public function __construct(ItemType $model)
    {
        parent::__construct($model);
    }

}

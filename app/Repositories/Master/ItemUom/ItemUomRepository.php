<?php
namespace App\Repositories\Master\ItemUom;

use App\Models\ItemUom;
use App\Repositories\BaseRepository;

class ItemUomRepository extends BaseRepository implements ItemUomRepositoryInterface
{
    protected $model;

    public function __construct(ItemUom $model)
    {
        parent::__construct($model);
    }

}

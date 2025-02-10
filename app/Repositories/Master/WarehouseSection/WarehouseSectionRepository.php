<?php
namespace App\Repositories\Master\WarehouseSection;

use App\Models\WarehouseSection;
use App\Repositories\BaseRepository;

class WarehouseSectionRepository extends BaseRepository implements WarehouseSectionRepositoryInterface
{
    protected $model;

    public function __construct(WarehouseSection $model)
    {
        parent::__construct($model);
    }

}

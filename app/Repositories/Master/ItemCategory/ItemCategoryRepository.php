<?php
namespace App\Repositories\Master\ItemCategory;

use App\Models\ItemCategory;
use App\Repositories\BaseRepository;

class ItemCategoryRepository extends BaseRepository implements ItemCategoryRepositoryInterface
{
    protected $model;

    public function __construct(ItemCategory $model)
    {
        parent::__construct($model);
    }

}

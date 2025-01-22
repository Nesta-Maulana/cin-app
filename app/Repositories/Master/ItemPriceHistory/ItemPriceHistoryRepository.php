<?php
namespace App\Repositories\Master\ItemPriceHistory;

use App\Models\ItemPriceHistory;
use App\Repositories\BaseRepository;

class ItemPriceHistoryRepository extends BaseRepository implements ItemPriceHistoryRepositoryInterface
{
    protected $model;

    public function __construct(ItemPriceHistory $model)
    {
        parent::__construct($model);
    }

}

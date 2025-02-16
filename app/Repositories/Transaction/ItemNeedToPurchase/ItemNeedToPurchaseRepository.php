<?php
namespace App\Repositories\Transaction\ItemNeedToPurchase;

use App\Models\ItemNeedToPurchase;
use App\Repositories\BaseRepository;

class ItemNeedToPurchaseRepository extends BaseRepository implements ItemNeedToPurchaseRepositoryInterface
{
    protected $model;

    public function __construct(ItemNeedToPurchase $model)
    {
        parent::__construct($model);
    }

}

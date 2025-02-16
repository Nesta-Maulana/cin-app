<?php
namespace App\Repositories\Transaction\ItemNeedToPurchaseDetail;

use App\Models\ItemNeedToPurchaseDetail;
use App\Repositories\BaseRepository;

class ItemNeedToPurchaseDetailRepository extends BaseRepository implements ItemNeedToPurchaseDetailRepositoryInterface
{
    protected $model;

    public function __construct(ItemNeedToPurchaseDetail $model)
    {
        parent::__construct($model);
    }

}

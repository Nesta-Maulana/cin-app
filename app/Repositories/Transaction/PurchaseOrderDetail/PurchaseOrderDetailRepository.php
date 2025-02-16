<?php
namespace App\Repositories\Transaction\PurchaseOrderDetail;

use App\Models\PurchaseOrderDetail;
use App\Repositories\BaseRepository;

class PurchaseOrderDetailRepository extends BaseRepository implements PurchaseOrderDetailRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderDetail $model)
    {
        parent::__construct($model);
    }

}

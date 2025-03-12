<?php
namespace App\Repositories\Transaction\PrePurchaseOrderDetail;

use App\Models\PrePurchaseOrderDetail;
use App\Repositories\BaseRepository;

class PrePurchaseOrderDetailRepository extends BaseRepository implements PrePurchaseOrderDetailRepositoryInterface
{
    protected $model;

    public function __construct(PrePurchaseOrderDetail $model)
    {
        parent::__construct($model);
    }

}

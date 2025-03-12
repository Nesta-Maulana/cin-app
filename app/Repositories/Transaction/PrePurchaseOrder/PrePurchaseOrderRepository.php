<?php
namespace App\Repositories\Transaction\PrePurchaseOrder;

use App\Models\PrePurchaseOrder;
use App\Repositories\BaseRepository;

class PrePurchaseOrderRepository extends BaseRepository implements PrePurchaseOrderRepositoryInterface
{
    protected $model;

    public function __construct(PrePurchaseOrder $model)
    {
        parent::__construct($model);
    }

}

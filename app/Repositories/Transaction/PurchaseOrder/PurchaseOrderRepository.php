<?php
namespace App\Repositories\Transaction\PurchaseOrder;

use App\Models\PurchaseOrder;
use App\Repositories\BaseRepository;

class PurchaseOrderRepository extends BaseRepository implements PurchaseOrderRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrder $model)
    {
        parent::__construct($model);
    }

}

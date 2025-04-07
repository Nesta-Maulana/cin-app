<?php
namespace App\Repositories\Transaction\PrePurchaseOrderItemSelection;

use App\Models\PrePurchaseOrderItemSelection;
use App\Repositories\BaseRepository;

class PrePurchaseOrderItemSelectionRepository extends BaseRepository implements PrePurchaseOrderItemSelectionRepositoryInterface
{
    protected $model;

    public function __construct(PrePurchaseOrderItemSelection $model)
    {
        parent::__construct($model);
    }

}

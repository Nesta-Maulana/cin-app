<?php
namespace App\Repositories\Transaction\PurchaseOrderNewAdditionalCost;

use App\Models\PurchaseOrderNewAdditionalCost;
use App\Repositories\BaseRepository;

class PurchaseOrderNewAdditionalCostRepository extends BaseRepository implements PurchaseOrderNewAdditionalCostRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderNewAdditionalCost $model)
    {
        parent::__construct($model);
    }

}
